<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rabs;
use App\Models\Divisies;
use App\Models\Periode;
use App\Models\Sodaqoh;
use App\Models\SodaqohHistoris;
use App\Models\Settings;
use App\Models\SpWhatsappPhoneNumbers;
use App\Helpers\WaSchedules;
use App\Helpers\CommonHelpers;
use App\Models\Jurnals;
use App\Models\Banks;
use App\Models\Poses;
use App\Models\Santri;
use App\Models\RabManagBuildingDetails;
use App\Models\RabManagBuildings;
use App\Models\RabKegiatanDetails;
use App\Models\RabKegiatans;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tagihan()
    {
        $need_approval = null;
        $historis = null;
        $tagihans = null; 
        if(auth()->user()->hasRole('superadmin') || (auth()->user()->hasRole('ku') && !isset(auth()->user()->santri))){
            $need_approval = SodaqohHistoris::where('status','pending')->get();
            $historis = SodaqohHistoris::where('status','!=','pending')->orderBy('id','DESC')->get();
        }else{
            $tagihans = Sodaqoh::where('fkSantri_id',auth()->user()->santri->id)->get();
        }
        
        return view('keuangan.tagihan', [
            'tagihans' => $tagihans,
            'need_approval' => $need_approval,
            'historis' => $historis,
        ]);
    }

    public function list_sodaqoh($periode = null, $angkatan = null, $status = 2)
    {
        if(isset(auth()->user()->santri)){
            return redirect()->route('dashboard');
        }

        if ($status == 0) {
            $status = null;
        }
        $datax = [];
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'ASC')
            ->get();
        $list_periode = Sodaqoh::select('periode')->groupBy('periode')->get();
        $last_update = Sodaqoh::select('updated_at')->orderBy('updated_at', 'DESC')->limit(1)->first();

        $st = null;
        if ($status == 1) {
            $st = 1;
        }

        if (($periode == '-' && $angkatan == '-') || ($periode == null && $angkatan == null)) {
            if ($status == 2) {
                $datax = Sodaqoh::get();
            } else {
                $datax = Sodaqoh::where('status_lunas', $st)->get();
            }
            $periode = '-';
            $angkatan = '-';
        } elseif ($periode != '-' && $angkatan != '-') {
            if ($status == 2) {
                $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->where('periode', $periode)->get();
            } else {
                $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->where('periode', $periode)->where('status_lunas', $st)->get();
            }
        } elseif ($angkatan != null && ($periode == null || $periode == '-')) {
            if ($status == 2) {
                $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->get();
            } else {
                $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->where('status_lunas', $st)->get();
            }
        } elseif ($periode != null && ($angkatan == null || $angkatan == '-')) {
            if ($status == 2) {
                $datax = Sodaqoh::where('periode', $periode)->get();
            } else {
                $datax = Sodaqoh::where('periode', $periode)->where('status_lunas', $st)->get();
            }
        }

        return view('keuangan.list_sodaqoh', [
            'datax' => $datax,
            'select_angkatan' => $angkatan,
            'list_periode' => $list_periode,
            'list_angkatan' => $list_angkatan,
            'select_lunas' => $status == null ? 0 : $status,
            'periode' => $periode,
            'last_update' => $last_update
        ]);
    }

    public function store_sodaqoh(Request $request)
    {
        $check = Sodaqoh::find($request->input('id'));
        if ($check) {
            if ($check->fkSantri_id == $request->input('fkSantri_id')) {
                if ($request->hasFile('bukti_transfer')) {
                    $request->validate([
                        'bukti_transfer' => 'mimes:jpeg,png' // Only allow .jpg and .png file types.
                    ]);
                    $request->bukti_transfer->store('bukti_transfer', 'public');
                }

                $status = 'pending';
                $created = SodaqohHistoris::create([
                    'fkSodaqoh_id' => $request->input('id'),
                    'fkSantri_id' => $request->input('fkSantri_id'),
                    'nominal' => $request->input('nominal_bayar'),
                    'bukti_transfer' => $request->hasFile('bukti_transfer') ? $request->bukti_transfer->hashName() : null,
                    'status' => $status,
                    'pay_date' => $request->input('date'),
                    'updated_by' => auth()->user()->fullname,
                ]);

                if ($created) {
                    // kirim WA
                    if (auth()->user()->hasRole('santri')) {
                        $santri = Santri::find($request->input('fkSantri_id'));
                        $setting = Settings::find(1);
                        $caption = '*[SODAQOH TAHUNAN]* Pembayaran sodaqoh tahunan dari *'.$santri->user->fullname.'* sejumlah *Rp '.number_format($request->input('nominal_bayar'),0).'*.
Bukti Transfer: '.$setting->host_url.'/storage/bukti_transfer/'.$created->bukti_transfer;
                        WaSchedules::save('Pembayaran Sodaqoh dari '.$santri->user->fullname, $caption, $setting->wa_keuangan_group_id, null, true);
                    }
                    return json_encode(array("status" => true, "message" => 'Pembayaran sodaqoh berhasil diajukan'));
                } else {
                    return json_encode(array("status" => false, "message" => 'Pembayaran sodaqoh gagal diajukan'));
                }
            } else {
                return json_encode(array("status" => false, "message" => 'ID Mahasiswa tidak valid'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Data tidak ditemukan'));
        }
    }

    public function approve_payment(Request $request)
    {
        $check = SodaqohHistoris::find($request->input('id'));
        if ($check) {
            $check->status = $request->input('tipe');
            if($check->save()){
                if($request->input('tipe')=="approved"){
                    // start jurnal
                    $jurnal = Jurnals::create([
                        'fkBank_id' => 2,
                        'fkPos_id' => 1,
                        'periode_tahun' => CommonHelpers::periode(),
                        'fkSodaqoh_id' => $check->sodaqoh->id,
                        'tanggal' => $check->pay_date,
                        'jenis' => 'in',
                        'qty' => 1,
                        'uraian' => 'Sodaqoh Tahunan '.$check->sodaqoh->periode.': '.$check->santri->user->fullname,
                        'nominal' => $check->nominal,
                        'tipe_penerimaan' => 'Sodaqoh Tahunan'
                    ]);
                    // end jurnal

                    $update_payment = SodaqohHistoris::where('fkSodaqoh_id',$check->fkSodaqoh_id)->where('fkSantri_id',$check->fkSantri_id)->where('status','approved')->get();
                    if($update_payment){
                        $total = 0;
                        $history_payment = '
*Riwayat Pembayaran:*';
                        foreach($update_payment as $up){
                            $total = $total + intval($up->nominal);
                            $history_payment = $history_payment . '
- Rp ' . number_format(intval($up->nominal), 0);
                        }
                        $check_nominal = Sodaqoh::where('fkSantri_id',$check->fkSantri_id)->where('id',$check->fkSodaqoh_id)->first();
                        if($check_nominal->nominal<=$total){
                            $check_nominal->status_lunas = 1;
                            $check_nominal->save();
                        }
                        $nominal_kekurangan = $check_nominal->nominal - $total;
                        $text_kekurangan = '';
                        $status_lunas = '*[LUNAS]*';
                        if ($nominal_kekurangan > 0) {
                            $text_kekurangan = '
Adapun kekurangannya masih senilai: *Rp ' . number_format($nominal_kekurangan, 0) . ',-*';
                            $status_lunas = '*[BELUM LUNAS]*';
                        }
                    }
                    // kirim WA
                    $santri = Santri::find($request->input('fkSantri_id'));
                    $setting = Settings::find(1);
                    $caption = $status_lunas . ' Pembayaran Sodaqoh Tahunan ' . $setting->org_name . ' Periode ' . $check_nominal->periode . ' an. *' . $check_nominal->santri->user->fullname . '* senilai *Rp ' . number_format($check->nominal, 0) . '* sudah dikonfirmasi.
' . $history_payment . $text_kekurangan;
                    WaSchedules::save('[ORTU] Sodaqoh: [' . $check_nominal->santri->angkatan . '] ' . $check_nominal->santri->user->fullname . ' - ' . $check_nominal->periode, $caption, WaSchedules::getContactId($check_nominal->santri->nohp_ortu));
                    WaSchedules::save('[SANTRI] Sodaqoh: [' . $check_nominal->santri->angkatan . '] ' . $check_nominal->santri->user->fullname . ' - ' . $check_nominal->periode, $caption, WaSchedules::getContactId($check_nominal->santri->user->nohp));
                    return json_encode(array("status" => true, "message" => 'Pembayaran berhasil dikonfirmasi dengan status "Approved"'));
                }else{
                    return json_encode(array("status" => false, "message" => 'Pembayaran berhasil dikonfirmasi dengan status "Rejected"'));
                }
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Data tidak ditemukan'));
        }
    }

    public function delete_sodaqoh($id, $periode, $angkatan, $select_lunas)
    {
        $data = Sodaqoh::find($id);
        if (!$data)
            return redirect()->route('list periode sodaqoh', [$periode, $angkatan, $select_lunas])->withErrors(['periode_not_found' => 'Sodaqoh tidak ditemukan.']);
        $data->delete();
        return redirect()->route('list periode sodaqoh', [$periode, $angkatan, $select_lunas])->with('success', 'Berhasil menghapus sodaqoh');
    }

    public function reminder_sodaqoh(Request $request)
    {
        $id = $request->input('id');
        $check = Sodaqoh::find($id);
        $setting = Settings::find(1);
        
        if ($check) {
            $get_historis = SodaqohHistoris::where('fkSodaqoh_id',$check->id)->where('status','approved')->get();
            $terbayar = 0;
            $history_payment = '
*Riwayat Pembayaran:*';
            foreach ($get_historis as $b) {
                $terbayar = $terbayar + intval($b->nominal);
                $history_payment = $history_payment . '
- Rp ' . number_format(intval($b->nominal), 0);
            }
            $nominal_kekurangan = $check->nominal - $terbayar;
            $text_kekurangan = '';
            $status_lunas = '*[LUNAS]*';
            if ($nominal_kekurangan > 0) {
                $text_kekurangan = '
Masih memiliki kekurangannya senilai: *Rp ' . number_format($nominal_kekurangan, 0) . ',-*';
                $status_lunas = '*[BELUM LUNAS]*';
            } 
            
            // kirim wa
            $caption = $status_lunas . ' Mengingatkan Kewajiban Pembayaran Sodaqoh Tahunan ' . $setting->org_name . ' Periode ' . $check->periode . ' an. *' . $check->santri->user->fullname . '*.
' . $history_payment . $text_kekurangan;
            WaSchedules::save('Sodaqoh: [' . $check->santri->angkatan . '] ' . $check->santri->user->fullname . ' - ' . $check->periode, $caption, WaSchedules::getContactId($check->santri->nohp_ortu));
            // end kirim wa
            return json_encode(array("status" => true, "message" => 'Berhasil diinput'));
        } else {
            return json_encode(array("status" => false, "message" => 'ID tidak ditemukan'));
        }
    }

    public function rab_tahunan($select_periode = null)
    {
        if ($select_periode == null) {
            $select_periode = CommonHelpers::periode();
        }
        if(auth()->user()->hasRole('ku') && isset(auth()->user()->santri)){
            $rabs = Rabs::where('periode_tahun', $select_periode)->where('id','!=', 13)->orderBy('fkDivisi_id','ASC')->get();
        }else{
            $rabs = Rabs::where('periode_tahun', $select_periode)->orderBy('fkDivisi_id','ASC')->get();
        }
        $divisis = Divisies::where('active', 1)->get();
        $periodes = Periode::get();

        return view('keuangan.rab', [
            'rabs' => $rabs,
            'divisis' => $divisis,
            'periodes' => $periodes,
            'select_periode' => $select_periode,
        ]);
    }

    public function rab_tahunan_store(Request $request)
    {
        if ($request->input('rab_id') == '') {
            $data = Rabs::create([
                'periode_tahun' => $request->input('periode_tahun'),
                'fkDivisi_id' => $request->input('divisi'),
                'keperluan' => $request->input('keperluan'),
                'periode' => $request->input('periode'),
                'biaya' => $request->input('biaya'),
                'bulan_1' => $request->input('bulan_1'),
                'bulan_2' => $request->input('bulan_2'),
                'bulan_3' => $request->input('bulan_3'),
                'bulan_4' => $request->input('bulan_4'),
                'bulan_5' => $request->input('bulan_5'),
                'bulan_6' => $request->input('bulan_6'),
                'bulan_7' => $request->input('bulan_7'),
                'bulan_8' => $request->input('bulan_8'),
                'bulan_9' => $request->input('bulan_9'),
                'bulan_10' => $request->input('bulan_10'),
                'bulan_11' => $request->input('bulan_11'),
                'bulan_12' => $request->input('bulan_12'),
            ]);
        } else {
            $data = Rabs::find($request->input('rab_id'));
            $data->periode_tahun = $request->input('periode_tahun');
            $data->fkDivisi_id = $request->input('divisi');
            $data->keperluan = $request->input('keperluan');
            $data->periode = $request->input('periode');
            $data->biaya = $request->input('biaya');
            $data->bulan_1 = $request->input('bulan_1');
            $data->bulan_2 = $request->input('bulan_2');
            $data->bulan_3 = $request->input('bulan_3');
            $data->bulan_4 = $request->input('bulan_4');
            $data->bulan_5 = $request->input('bulan_5');
            $data->bulan_6 = $request->input('bulan_6');
            $data->bulan_7 = $request->input('bulan_7');
            $data->bulan_8 = $request->input('bulan_8');
            $data->bulan_9 = $request->input('bulan_9');
            $data->bulan_10 = $request->input('bulan_10');
            $data->bulan_11 = $request->input('bulan_11');
            $data->bulan_12 = $request->input('bulan_12');
            $data->save();
        }

        if ($data) {
            return json_encode(array("status" => true, "message" => 'Berhasil menyimpan RAB'));
        } else {
            return json_encode(array("status" => false, "message" => 'Gagal menyimpan RAB'));
        }
    }

    public function set_create_rab(Request $request)
    {
        $data = Rabs::find($request->input('rab_id'));
        $data->create_rab = !$data->create_rab;
        if($data->save()){
            return json_encode(array("status" => true));
        }else{
            return json_encode(array("status" => false));
        }
    }

    public function rab_tahunan_delete($id)
    {
        $data = Rabs::find($id);
        if ($data) {
            if ($data->delete()) {
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus RAB'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus RAB'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'RAB tidak ditemukan'));
        }
    }

    public function jurnal($select_bank = 'all', $select_divisi = 'all', $select_rab = 'all', $select_bulan = null, $select_penerimaan = 'all')
    {

        $sodaqohs = DB::table('v_user_santri')->orderBy('fullname','ASC')->get();
        $divisis = Divisies::where('active', 1)->get();
        $banks = Banks::get();
        $poses = Poses::get();

        $bulans = DB::table('jurnals')
                ->select(DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as ym'))
                ->groupBy('ym')
                ->orderBy('ym', 'DESC')
                ->get();

        if ($select_bulan == null) {
            $select_bulan = date('Y-m');
        }

        if(auth()->user()->hasRole('ku') && isset(auth()->user()->santri)){
            $select_bank = 1;
            $banks = Banks::where('id',1)->get();
        }

        if($select_bulan=="all"){
            $jurnals = Jurnals::orderBy('tanggal','ASC')->get();
        }else{
            $jurnals = Jurnals::where('tanggal', 'like', $select_bulan . '%')->orderBy('tanggal','ASC')->get();
        }
        if($select_bank!="all"){
            $jurnals = $jurnals->where('fkBank_id',$select_bank);
        }
        if($select_divisi!="all"){
            $jurnals = $jurnals->where('fkDivisi_id',$select_divisi);
        }
        if($select_rab!="all"){
            $jurnals = $jurnals->where('fkRab_id',$select_rab);
        }
        if($select_penerimaan!="all"){
            $jurnals = $jurnals->where('tipe_penerimaan',$select_penerimaan);
        }
        
        $saldo = 0;
        if($select_bulan!='all'){
            $saldo_jurnal = Jurnals::where('tanggal', '<', $select_bulan.'-01')->orderBy('tanggal','ASC')->get();
            if($select_bank!="all"){
                $saldo_jurnal = $saldo_jurnal->where('fkBank_id',$select_bank);
            }
            if($saldo_jurnal!=null){
                foreach($saldo_jurnal as $j){
                    if($j->jenis=="in"){
                        $saldo = $saldo + ($j->qty*$j->nominal);
                    }else if($j->jenis=="out"){
                        $saldo = $saldo - ($j->qty*$j->nominal);
                    }
                }
            }
        }

        if($select_divisi!="all"){
            $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->where('fkDivisi_id',$select_divisi)->get();
        }else{
            $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->get();
        }

        return view('keuangan.jurnal', [
            'saldo' => $saldo,
            'rabs' => $rabs,
            'jurnals' => $jurnals,
            'sodaqohs' => $sodaqohs,
            'divisis' => $divisis,
            'banks' => $banks,
            'poses' => $poses,
            'bulans' => $bulans,
            'select_bulan' => $select_bulan,
            'select_divisi' => $select_divisi,
            'select_rab' => $select_rab,
            'select_bank' => $select_bank,
            'select_penerimaan' => $select_penerimaan,
        ]);
    }

    public function jurnal_store(Request $request)
    {
        if ($request->input('jurnal_id') == '') {
            if ($request->input('jenis') == 'out') {
                if($request->input('fkDivisi_id')==13){
                    $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->where('fkDivisi_id',13)->get();
                    foreach($rabs as $ukhro){
                        $data = Jurnals::create([
                            'fkBank_id' => $request->input('fkBank_id'),
                            'fkPos_id' => $request->input('fkPos_id'),
                            'fkDivisi_id' => $request->input('fkDivisi_id'),
                            'fkRab_id' => $ukhro->id,
                            'tanggal' => $request->input('tanggal'),
                            'jenis' => $request->input('jenis'),
                            'uraian' => $request->input('keterangan'),
                            'qty' => 1,
                            'nominal' => $ukhro->biaya,
                            'created_by' => auth()->user()->fullname,
                            'tipe_pengeluaran' => $request->input('tipe_pengeluaran')
                        ]);
                    }
                }else{
                    $data = Jurnals::create([
                        'fkBank_id' => $request->input('fkBank_id'),
                        'fkPos_id' => $request->input('fkPos_id'),
                        'fkDivisi_id' => $request->input('fkDivisi_id'),
                        'fkRab_id' => $request->input('fkRab_id'),
                        'tanggal' => $request->input('tanggal'),
                        'jenis' => $request->input('jenis'),
                        'uraian' => $request->input('keterangan'),
                        'qty' => $request->input('qty'),
                        'nominal' => $request->input('nominal'),
                        'created_by' => auth()->user()->fullname,
                        'tipe_pengeluaran' => $request->input('tipe_pengeluaran')
                    ]);
                }
            } elseif ($request->input('jenis') == 'in') {
                $data = Jurnals::create([
                    'fkBank_id' => $request->input('fkBank_id'),
                    'fkPos_id' => $request->input('fkPos_id'),
                    'fkDivisi_id' => $request->input('fkDivisi_id'),
                    'fkSodaqoh_id' => $request->input('fkSodaqoh_id'),
                    'tanggal' => $request->input('tanggal'),
                    'jenis' => $request->input('jenis'),
                    'uraian' => $request->input('keterangan'),
                    'nominal' => $request->input('nominal'),
                    'created_by' => auth()->user()->fullname,
                    'tipe_penerimaan' => $request->input('tipe_penerimaan')
                ]);
            } elseif ($request->input('jenis') == 'kuop') {
                $data = Jurnals::create([
                    'fkBank_id' => $request->input('fkBank_id'),
                    'fkPos_id' => $request->input('fkPos_id'),
                    'fkDivisi_id' => $request->input('fkDivisi_id'),
                    'fkRab_id' => $request->input('fkRab_id'),
                    'tanggal' => $request->input('tanggal'),
                    'jenis' => $request->input('status'),
                    'sub_jenis' => $request->input('jenis'),
                    'uraian' => $request->input('keterangan'),
                    'nominal' => $request->input('nominal'),
                    'created_by' => auth()->user()->fullname
                ]);
            }
            $data = Jurnals::find($data);
        }else{
            $data = Jurnals::find($request->input('jurnal_id'));
            if ($request->input('jenis') == 'out') {
                $data->fkBank_id = $request->input('fkBank_id');
                $data->fkPos_id = $request->input('fkPos_id');
                $data->fkDivisi_id = $request->input('fkDivisi_id');
                $data->fkRab_id = $request->input('fkRab_id');
                $data->tanggal = $request->input('tanggal');
                $data->jenis = $request->input('jenis');
                $data->uraian = $request->input('keterangan');
                $data->qty = $request->input('qty');
                $data->nominal = $request->input('nominal');
                $data->created_by = auth()->user()->fullname;
                $data->tipe_pengeluaran = $request->input('tipe_pengeluaran');
            } elseif ($request->input('jenis') == 'in') {
                $data->fkBank_id = $request->input('fkBank_id');
                $data->fkPos_id = $request->input('fkPos_id');
                $data->fkDivisi_id = $request->input('fkDivisi_id');
                $data->fkSodaqoh_id = $request->input('fkSodaqoh_id');
                $data->tanggal = $request->input('tanggal');
                $data->jenis = $request->input('jenis');
                $data->uraian = $request->input('keterangan');
                $data->nominal = $request->input('nominal');
                $data->created_by = auth()->user()->fullname;
                $data->tipe_penerimaan = $request->input('tipe_penerimaan');
            }
            $data->save();
        }

        if ($data) {
            return json_encode(array("status" => true, "message" => 'Berhasil menyimpan jurnal'));
        } else {
            return json_encode(array("status" => false, "message" => 'Gagal menyimpan jurnal'));
        }
    }

    public function jurnal_delete(Request $request)
    {
        $data = Jurnals::find($request->input('id'));
        if ($data) {
            if ($data->delete()) {
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus jurnal'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus jurnal'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Jurnal tidak ditemukan'));
        }
    }

    public function lock_unlock(Request $request)
    {
        $get_rab = Rabs::where('periode_tahun',$request->input('periode_tahun'))->get();
        foreach($get_rab as $rab){
            $lock_unlock = Rabs::find($rab->id);
            $lock = 1;
            if($lock_unlock->is_lock==1){
                $lock = 0;
            }
            $lock_unlock->is_lock = $lock;
            $lock_unlock->save();
        }
        $text = "Mengunci";
        if($request->input('lock')){
            $text = "Membuka";
        }
        return json_encode(array("status" => true, "message" => 'Berhasil '.$text.' RAB Tahunan'));
    }

    public function duplicate_rab(Request $request)
    {
        $year1 = date('Y');
        $year2 = date('Y') + 1;
        $year_periode = $year1 . "-" . $year2;

        $get_rab = Rabs::where('periode_tahun',CommonHelpers::periode())->get();
        foreach($get_rab as $rab){
            Rabs::create([
                'periode_tahun' => $year_periode,
                'fkDivisi_id' => $rab->fkDivisi_id,
                'keperluan' => $rab->keperluan,
                'periode' => $rab->periode,
                'biaya' => $rab->biaya,
                'bulan_1' => $rab->bulan_1,
                'bulan_2' => $rab->bulan_2,
                'bulan_3' => $rab->bulan_3,
                'bulan_4' => $rab->bulan_4,
                'bulan_5' => $rab->bulan_5,
                'bulan_6' => $rab->bulan_6,
                'bulan_7' => $rab->bulan_7,
                'bulan_8' => $rab->bulan_8,
                'bulan_9' => $rab->bulan_9,
                'bulan_10' => $rab->bulan_10,
                'bulan_11' => $rab->bulan_11,
                'bulan_12' => $rab->bulan_12,
            ]);
        }
        return json_encode(array("status" => true, "message" => 'Berhasil menduplikasi RAB Tahunan'));
    }

    public function rab_management_building($id=null){
        $manag_buildings = RabManagBuildings::get();
        $detail_manag_buildings = null;
        if($id!=null){
            $detail_manag_buildings = RabManagBuildingDetails::where('fkRabManagBuilding_id',$id)->get();
        }
        return view('keuangan.management_building', [
            'detail_of' => RabManagBuildings::find($id),
            'manag_buildings' => $manag_buildings,
            'detail_manag_buildings' => $detail_manag_buildings,
        ]);
    }

    public function store_management_building(Request $request){
        if($request->input('parent_id')==""){
            $create = RabManagBuildings::create([
                'nama' => $request->input('name'),
                'periode_bulan' => $request->input('date'),
                'deskripsi' => $request->input('deskripsi')
            ]);
            if($create){
                return redirect()->route('rab management building')->with('success', 'Berhasil menambah pengajuan');
            }else{
                return redirect()->route('rab management building')->withErrors(['failed' => 'Gagal menambah pengajuan']);
            }
        }else{
            $create = RabManagBuildings::find($request->input('parent_id'));
            if($request->input('status')!=null){
                if($request->input('status')=='posted'){
                    // posting jurnal
                    $check_posted_jurnal = Jurnals::where('fkRabManagBuilding_id',$create->id)->first();
                    if($check_posted_jurnal==null){
                        $data = Jurnals::create([
                            'fkBank_id' => 2,
                            'fkPos_id' => 1,
                            'tanggal' => date('Y-m-d H:i:s'),
                            'jenis' => 'out',
                            'uraian' => $create->nama,
                            'qty' => 1,
                            'nominal' => $create->total_biaya(),
                            'created_by' => auth()->user()->fullname,
                            'tipe_pengeluaran' => 'Non Rutin',
                            'fkRabManagBuilding_id' => $create->id
                        ]);
                    }else{
                        $check_posted_jurnal->tanggal = $create->periode_bulan;
                        $check_posted_jurnal->uraian = $create->nama;
                        $check_posted_jurnal->nominal = $create->total_biaya();
                        $check_posted_jurnal->created_by = auth()->user()->fullname;
                        $check_posted_jurnal->save();
                    }
                }elseif($request->input('status')=='draft' && $create->status=="posted"){
                    Jurnals::where('fkRabManagBuilding_id',$create->id)->first()->delete();
                }

                $create->status = $request->input('status');
                $create->save();
                if($create){
                    return json_encode(array("status" => true, "message" => 'Berhasil mengubah status pengajuan'));
                }else{
                    return json_encode(array("status" => false, "message" => 'Gagal mengubah status pengajuan'));
                }
            }else{
                $create->nama = $request->input('name');
                $create->periode_bulan = $request->input('date');
                $create->deskripsi = $request->input('deskripsi');
                $create->save();
                if($create){
                    return redirect()->route('rab management building')->with('success', 'Berhasil update pengajuan');
                }else{
                    return redirect()->route('rab management building')->withErrors(['failed' => 'Gagal update pengajuan']);
                }
            }
        }
    }

    public function store_detail_management_building(Request $request){
        if($request->input('id')==""){
            $create = RabManagBuildingDetails::create([
                'fkRabManagBuilding_id' => $request->input('parent_id_detail'),
                'uraian' => $request->input('uraian'),
                'qty' => $request->input('qty'),
                'satuan' => $request->input('satuan'),
                'biaya' => $request->input('biaya'),
                'realisasi' => $request->input('realisasi'),
            ]);
            if($create){
                return redirect()->route('rab management building id',$request->input('parent_id_detail'))->with('success', 'Berhasil menambah detail pengajuan');
            }else{
                return redirect()->route('rab management building id',$request->input('parent_id_detail'))->withErrors(['failed' => 'Gagal menambah detail pengajuan']);
            }
        }else{
            $create = RabManagBuildingDetails::find($request->input('id'));
            $create->uraian = $request->input('uraian');
            $create->qty = $request->input('qty');
            $create->satuan = $request->input('satuan');
            $create->biaya = $request->input('biaya');
            $create->qty_realisasi = $request->input('qty_realisasi');
            $create->satuan_realisasi = $request->input('satuan_realisasi');
            $create->biaya_realisasi = $request->input('biaya_realisasi');
            $create->save();
            if($create){
                return redirect()->route('rab management building id',$request->input('parent_id_detail'))->with('success', 'Berhasil update detail pengajuan');
            }else{
                return redirect()->route('rab management building id',$request->input('parent_id_detail'))->withErrors(['failed' => 'Gagal update detail pengajuan']);
            }
        }
    }

    public function delete_management_building($id){
        $data = RabManagBuildings::find($id);
        if ($data) {
            if ($data->delete()) {
                RabManagBuildingDetails::where('fkRabManagBuilding_id',$id)->delete();
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus pengajuan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus pengajuan'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Pengajuan tidak ditemukan'));
        }
    }

    public function delete_detail_management_building($id){
        $data = RabManagBuildingDetails::find($id);
        if ($data) {
            if ($data->delete()) {
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus detail pengajuan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus detail pengajuan'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Detail pengajuan tidak ditemukan'));
        }
    }

    public function laporan_pusat($select_bulan = null, $print = false)
    {
        if ($select_bulan == null) {
            $select_bulan = date('Y-m');
        }

        return (new PublicController)->laporan_pusat($select_bulan, $print);

        // $bulans = DB::table('jurnals')
        //         ->select(DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as ym'))
        //         ->groupBy('ym')
        //         ->orderBy('ym', 'DESC')
        //         ->get();

        // if ($select_bulan == null) {
        //     $select_bulan = date('Y-m');
        // }

        // $nextmonth = strtotime('+1 month', strtotime($select_bulan));
        // $nextmonth = date('Y-m', $nextmonth);
        // $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->where('biaya','!=',0)->orderBy('fkDivisi_id','ASC')->get();

        // if($select_bulan=="all"){
        //     $jurnals = Jurnals::orderBy('tanggal','ASC')->get();
        // }else{
        //     $jurnals = Jurnals::where('tanggal', 'like', $select_bulan . '%')->orderBy('tanggal','ASC')->get();
        // }

        // $total_in = 0;
        // foreach($jurnals->where('jenis','in') as $in){
        //     $total_in = $total_in + ($in->qty*$in->nominal);
        // }

        // $total_out_rutin = 0;
        // foreach($jurnals->where('jenis','out')->where('tipe_pengeluaran','Rutin') as $outr){
        //     $total_out_rutin = $total_out_rutin + ($outr->qty*$outr->nominal);
        // }
        // $total_out_nonrutin = 0;
        // foreach($jurnals->where('jenis','out')->where('tipe_pengeluaran','Non Rutin') as $outnr){
        //     $total_out_nonrutin = $total_out_nonrutin + ($outnr->qty*$outnr->nominal);
        // }

        // $manag_building = $jurnals->whereNotNull('fkRabManagBuilding_id')->where('fkRabManagBuilding_id','!=',0);
        // $rab_kegiatan = $jurnals->whereNotNull('fkRabKegiatan_id')->where('fkRabKegiatan_id','!=',0);

        // $pengajuan_manag_buildings = RabManagBuildings::where('status','submit')->get();
        
        // $saldo = 0;
        // if($select_bulan!='all'){
        //     $saldo_jurnal = Jurnals::where('tanggal', '<', $select_bulan.'-1')->orderBy('tanggal','ASC')->get();
        //     if($saldo_jurnal!=null){
        //         foreach($saldo_jurnal as $j){
        //             if($j->jenis=="in"){
        //                 $saldo = $saldo + $j->nominal;
        //             }else if($j->jenis=="out"){
        //                 $saldo = $saldo - $j->nominal;
        //             }
        //         }
        //     }
        // }
        
        // return view('keuangan.laporan_pusat', [
        //     'print' => $print,
        //     'saldo' => $saldo,
        //     'jurnals' => $jurnals,
        //     'bulans' => $bulans,
        //     'select_bulan' => $select_bulan,
        //     'manag_building' => $manag_building,
        //     'rab_kegiatan' => $rab_kegiatan,
        //     'pengajuan_manag_buildings' => $pengajuan_manag_buildings,
        //     'nextmonth' => $nextmonth,
        //     'rabs' => $rabs,
        //     'total_in' => $total_in,
        //     'total_out_rutin' => $total_out_rutin,
        //     'total_out_nonrutin' => $total_out_nonrutin,
        // ]);
    }

    public function rab_kegiatan($id=null){
        $ketuapanitia = false;
        if(!CommonHelpers::isKetuaBendahara()){
            return redirect()->route('dashboard');
        }else{
            $ketuapanitia = true;
        }
        $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->where('create_rab',1)->get();
        $santris = DB::table('v_user_santri')->orderBy('fullname','ASC')->where('gender','male')->get();
        $kegiatans = RabKegiatans::get();
        if($ketuapanitia && !(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku'))){
            $santri_id = auth()->user()->santri->id;
            $kegiatans = RabKegiatans::where('fkSantri_id_ketua', $santri_id)->orWhere('fkSantri_id_bendahara', $santri_id)->get();
        }
        $detail_kegiatans = null;
        if($id!=null){
            $detail_kegiatans = RabKegiatanDetails::where('fkRabKegiatan_id',$id)->get();
        }
        return view('keuangan.rab_kegiatan', [
            'detail_of' => RabKegiatans::find($id),
            'kegiatans' => $kegiatans,
            'detail_kegiatans' => $detail_kegiatans,
            'rabs' => $rabs,
            'santris' => $santris,
        ]);
    }

    public function store_rab_kegiatan(Request $request){
        if(!CommonHelpers::isKetuaBendahara()){
            return redirect()->route('dashboard');
        }
        if($request->input('parent_id')==""){
            $create = RabKegiatans::create([
                'fkRab_id' => $request->input('fkRab_id'),
                'nama' => $request->input('name'),
                'periode_bulan' => $request->input('date'),
                'deskripsi' => $request->input('deskripsi'),
                'fkSantri_id_ketua' => $request->input('fkSantri_id_ketua'),
                'fkSantri_id_bendahara' => $request->input('fkSantri_id_bendahara'),
                'ids' => uniqid()
            ]);
            if($create){
                return redirect()->route('rab kegiatan')->with('success', 'Berhasil menambah pengajuan');
            }else{
                return redirect()->route('rab kegiatan')->withErrors(['failed' => 'Gagal menambah pengajuan']);
            }
        }else{
            $create = RabKegiatans::find($request->input('parent_id'));
            if($request->input('status')!=null){
                if($request->input('status')=='posted'){
                    // posting jurnal
                    $check_posted_jurnal = Jurnals::where('fkRabKegiatan_id',$create->id)->first();
                    if($check_posted_jurnal==null){
                        $data = Jurnals::create([
                            'fkBank_id' => 1,
                            'fkPos_id' => 1,
                            'fkDivisi_id' => $create->rab->divisi->id,
                            'fkRab_id' => $create->fkRab_id,
                            'tanggal' => date('Y-m-d H:i:s'),
                            'jenis' => 'out',
                            'uraian' => $create->nama,
                            'qty' => 1,
                            'nominal' => $create->total_biaya(),
                            'created_by' => auth()->user()->fullname,
                            'tipe_pengeluaran' => 'Rutin',
                            'fkRabKegiatan_id' => $create->id
                        ]);
                    }else{
                        $check_posted_jurnal->tanggal = $create->periode_bulan;
                        $check_posted_jurnal->uraian = $create->nama;
                        $check_posted_jurnal->nominal = $create->total_biaya();
                        $check_posted_jurnal->created_by = auth()->user()->fullname;
                        $check_posted_jurnal->save();
                    }
                }elseif($request->input('status')=='draft' && $create->status=="posted"){
                    Jurnals::where('fkRabKegiatan_id',$create->id)->first()->delete();
                }
                $create->status = $request->input('status');
                $create->justifikasi_rab = $request->input('justifikasi_rab');
                $create->justifikasi_realisasi = $request->input('justifikasi_realisasi');
                $create->save();
                if($create){
                    return json_encode(array("status" => true, "message" => 'Berhasil mengubah status pengajuan'));
                }else{
                    return json_encode(array("status" => false, "message" => 'Gagal mengubah status pengajuan'));
                }
            }else{
                $create->fkRab_id = $request->input('fkRab_id');
                $create->nama = $request->input('name');
                $create->periode_bulan = $request->input('date');
                $create->deskripsi = $request->input('deskripsi');
                $create->fkSantri_id_ketua = $request->input('fkSantri_id_ketua');
                $create->fkSantri_id_bendahara = $request->input('fkSantri_id_bendahara');
                if($create->ids==""){
                    $create->ids = uniqid();
                }
                $create->save();
                if($create){
                    return redirect()->route('rab kegiatan')->with('success', 'Berhasil update pengajuan');
                }else{
                    return redirect()->route('rab kegiatan')->withErrors(['failed' => 'Gagal update pengajuan']);
                }
            }
        }
    }

    public function store_detail_rab_kegiatan(Request $request){
        if(!CommonHelpers::isKetuaBendahara()){
            return redirect()->route('dashboard');
        }
        if($request->input('id')==""){
            if($request->input('status')!="approved"){
                $create = RabKegiatanDetails::create([
                    'fkRabKegiatan_id' => $request->input('parent_id_detail'),
                    'uraian' => $request->input('uraian'),
                    'qty' => $request->input('qty'),
                    'satuan' => $request->input('satuan'),
                    'biaya' => $request->input('biaya'),
                    'realisasi' => $request->input('realisasi'),
                    'divisi' => $request->input('divisi'),
                ]);
                if($create){
                    return redirect()->route('rab kegiatan id',$request->input('parent_id_detail'))->with('success', 'Berhasil menambah detail pengajuan');
                }else{
                    return redirect()->route('rab kegiatan id',$request->input('parent_id_detail'))->withErrors(['failed' => 'Gagal menambah detail pengajuan']);
                }
            }else{
                return redirect()->route('rab kegiatan id',$request->input('parent_id_detail'))->withErrors(['failed' => 'Status Approved tidak dapat menambah item baru']);
            }
        }else{
            $create = RabKegiatanDetails::find($request->input('id'));
            $create->uraian = $request->input('uraian');
            $create->qty = $request->input('qty');
            $create->satuan = $request->input('satuan');
            $create->biaya = $request->input('biaya');
            $create->qty_realisasi = $request->input('qty_realisasi');
            $create->satuan_realisasi = $request->input('satuan_realisasi');
            $create->biaya_realisasi = $request->input('biaya_realisasi');
            $create->divisi = $request->input('divisi');
            $create->save();
            if($create){
                return redirect()->route('rab kegiatan id',$request->input('parent_id_detail'))->with('success', 'Berhasil update detail pengajuan');
            }else{
                return redirect()->route('rab kegiatan id',$request->input('parent_id_detail'))->withErrors(['failed' => 'Gagal update detail pengajuan']);
            }
        }
    }

    public function delete_rab_kegiatan($id){
        if(!CommonHelpers::isKetuaBendahara()){
            return redirect()->route('dashboard');
        }
        $data = RabKegiatans::find($id);
        if ($data) {
            if ($data->delete()) {
                RabKegiatanDetails::where('fkRabKegiatan_id',$id)->delete();
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus pengajuan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus pengajuan'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Pengajuan tidak ditemukan'));
        }
    }

    public function delete_detail_rab_kegiatan($id){
        if(!CommonHelpers::isKetuaBendahara()){
            return redirect()->route('dashboard');
        }
        $data = RabKegiatanDetails::find($id);
        if ($data) {
            if ($data->delete()) {
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus detail pengajuan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus detail pengajuan'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Detail pengajuan tidak ditemukan'));
        }
    }
}

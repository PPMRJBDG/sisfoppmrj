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
use App\Models\RabInouts;
use App\Models\Banks;
use App\Models\Poses;
use App\Models\Santri;
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
        if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku')){
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
                if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku')) {
                    $status = 'approved';
                }
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
                    return json_encode(array("status" => true, "message" => 'Pembayaran berhasil dikonfirmasi dengan status "Approved'));
                }else{
                    return json_encode(array("status" => false, "message" => 'Pembayaran berhasil dikonfirmasi dengan status "Rejected'));
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
        $bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
        if ($check) {
            $terbayar = 0;
            $history_payment = '
*Riwayat Pembayaran:*';
            foreach ($bulan as $b) {
                $terbayar = $terbayar + $check->$b;
                if ($check->$b != '') {
                    $history_payment = $history_payment . '
- Rp ' . number_format($check->$b, 0);
                }
            }
            $nominal_kekurangan = $check->nominal - $terbayar;
            $text_kekurangan = '';
            $status_lunas = '*[LUNAS]*';
            if ($nominal_kekurangan > 0) {
                $text_kekurangan = '
Masih memiliki kekurangannya senilai: *Rp ' . number_format($nominal_kekurangan, 0) . ',-*';
                $status_lunas = '*[BELUM LUNAS]*';
            } else {
                $check->status_lunas = 1;
                $check->save();
            }
            // kirim wa
            $nohp = $check->santri->nohp_ortu;
            if ($nohp != '') {
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $setting = Settings::find(1);
                $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                    $query->where('name', 'NOT LIKE', '%Bulk%');
                })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                if ($wa_phone != null) {
                    $caption = $status_lunas . ' Mengingatkan Kewajiban Pembayaran Sodaqoh Tahunan ' . $setting->org_name . ' Periode ' . $check->periode . ' an. *' . $check->santri->user->fullname . '*.
' . $history_payment . '
' . $text_kekurangan;
                    WaSchedules::save('Sodaqoh: [' . $check->santri->angkatan . '] ' . $check->santri->user->fullname . ' - ' . $check->periode, $caption, $wa_phone->pid);
                }
            }
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
        $rabs = Rabs::where('periode_tahun', $select_periode)->get();
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

    public function jurnal($select_periode = null, $select_bulan = null)
    {
        $bulans = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        if ($select_periode == null) {
            $select_periode = CommonHelpers::periode();
        }

        if ($select_bulan == 'all') {
            $rabinout = RabInouts::where('periode_tahun', $select_periode)->get();
        } else {
            if ($select_bulan == null) {
                $select_bulan = date('m');
            }
            $rabinout = RabInouts::where('periode_tahun', $select_periode)->where('tanggal', 'like', date('Y') . '-' . $select_bulan . '%')->get();
        }
        $rabs = Rabs::where('periode_tahun', $select_periode)->get();
        $sodaqohs = Sodaqoh::where('periode', $select_periode)->get();
        $divisis = Divisies::where('active', 1)->get();
        $banks = Banks::get();
        $poses = Poses::get();
        $periodes = Periode::get();

        return view('keuangan.jurnal', [
            'rabs' => $rabs,
            'rabinouts' => $rabinout,
            'sodaqohs' => $sodaqohs,
            'divisis' => $divisis,
            'banks' => $banks,
            'poses' => $poses,
            'periodes' => $periodes,
            'bulans' => $bulans,
            'select_periode' => $select_periode,
            'select_bulan' => $select_bulan,
        ]);
    }

    public function jurnal_store(Request $request)
    {
        if ($request->input('inout_id') == '') {
            if ($request->input('jenis') == 'out') {
                $data = RabInouts::create([
                    'fkBank_id' => $request->input('fkBank_id'),
                    'fkPos_id' => $request->input('fkPos_id'),
                    'periode_tahun' => $request->input('periode_tahun'),
                    'fkDivisi_id' => $request->input('fkDivisi_id'),
                    'fkRab_id' => $request->input('fkRab_id'),
                    'tanggal' => $request->input('tanggal'),
                    'jenis' => $request->input('jenis'),
                    'uraian' => $request->input('keterangan'),
                    'qty' => $request->input('qty'),
                    'nominal' => $request->input('nominal'),
                    'tipe_pengeluaran' => $request->input('tipe_pengeluaran')
                ]);
            } elseif ($request->input('jenis') == 'in') {
                $data = RabInouts::create([
                    'fkBank_id' => $request->input('fkBank_id'),
                    'fkPos_id' => $request->input('fkPos_id'),
                    'periode_tahun' => $request->input('periode_tahun'),
                    'fkDivisi_id' => $request->input('fkDivisi_id'),
                    'fkSodaqoh_id' => $request->input('fkSodaqoh_id'),
                    'tanggal' => $request->input('tanggal'),
                    'jenis' => $request->input('jenis'),
                    'uraian' => $request->input('keterangan'),
                    'nominal' => $request->input('nominal'),
                    'tipe_penerimaan' => $request->input('tipe_penerimaan')
                ]);
            } elseif ($request->input('jenis') == 'kuop') {
                $data = RabInouts::create([
                    'fkBank_id' => $request->input('fkBank_id'),
                    'fkPos_id' => $request->input('fkPos_id'),
                    'periode_tahun' => $request->input('periode_tahun'),
                    'tanggal' => $request->input('tanggal'),
                    'jenis' => $request->input('status'),
                    'uraian' => $request->input('keterangan'),
                    'nominal' => $request->input('nominal')
                ]);
            }
        } else {
            // $data = Rabs::find($request->input('rab_id'));
            // $data->periode_tahun = $request->input('periode_tahun');
            // $data->fkDivisi_id = $request->input('fkDivisi_id');
            // $data->keperluan = $request->input('keperluan');
            // $data->periode = $request->input('periode');
            // $data->biaya = $request->input('biaya');
            // $data->bulan_1 = $request->input('bulan_1');
            // $data->bulan_2 = $request->input('bulan_2');
            // $data->bulan_3 = $request->input('bulan_3');
            // $data->bulan_4 = $request->input('bulan_4');
            // $data->bulan_5 = $request->input('bulan_5');
            // $data->bulan_6 = $request->input('bulan_6');
            // $data->bulan_7 = $request->input('bulan_7');
            // $data->bulan_8 = $request->input('bulan_8');
            // $data->bulan_9 = $request->input('bulan_9');
            // $data->bulan_10 = $request->input('bulan_10');
            // $data->bulan_11 = $request->input('bulan_11');
            // $data->bulan_12 = $request->input('bulan_12');
            // $data->save();
        }

        if ($data) {
            $divisi = ($data->fkDivisi_id == '') ? '' : $data->divisi->divisi;
            $rab = ($data->fkRab_id == '') ? '' : $data->rab->keperluan;
            $masuk = ($data->jenis == 'in') ? number_format($data->nominal, 0) : '';
            $keluar = ($data->jenis == 'out') ? number_format($data->nominal, 0) : '';
            $content = '<tr id="inout-' . $data->id . '" style="background: #f3d4cd;">' .
                '<td class="new-td text-uppercase">' . $data->bank->name . '</td>' .
                '<td class="new-td text-uppercase">' . $data->pos->name . '</td>' .
                '<td class="new-td text-uppercase">' . $divisi . '</td>' .
                '<td class="new-td">' . $rab . '</td>' .
                '<td class="new-td">' . date_format(date_create($data->tanggal), "Y-m-d") . '</td>' .
                '<!-- <td class="text-uppercase text-center">' . $data->jenis . '</td> -->' .
                '<td class="new-td">' . $data->uraian . '</td>' .
                '<td class="new-td text-center">' . $data->qty . '</td>' .
                '<td class="new-td text-end">' . $masuk . '</td>' .
                '<td class="new-td text-end">' . $keluar . '</td>' .
                '<!-- <td></td> -->' .
                '<td class="p-0 text-center">' .
                '<a class="btn btn-success btn-sm mb-0" style="padding:5px 15px;border-radius:0px;" type="submit" value="Edit" onclick="ubahInout(' . $data . ')">' .
                '<i class="fas fa-edit" aria-hidden="true"></i>' .
                '</a>' .
                '<a class="btn btn-danger btn-sm mb-0" style="padding:5px 15px;border-radius:0px;" type="submit" value="Hapus" onclick="hapusInout(' . $data->id . ')">' .
                '<i class="fas fa-trash" aria-hidden="true"></i>' .
                '</a>' .
                '</td>' .
                '</tr>';
            return json_encode(array("status" => true, "message" => 'Berhasil menyimpan jurnal', "content" => $content));
        } else {
            return json_encode(array("status" => false, "message" => 'Gagal menyimpan jurnal'));
        }
    }

    public function jurnal_delete($id)
    {
        $data = RabInouts::find($id);
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
}

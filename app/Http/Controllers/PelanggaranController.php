<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Pelanggaran;
use App\Models\JenisPelanggaran;
use App\Helpers\WaSchedules;
use App\Helpers\CommonHelpers;

class PelanggaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($is_archive = 0, $value = null, $id = null)
    {
        $count_pelanggaran = array(); //Pelanggaran::select(DB::raw('fkJenis_pelanggaran_id, COUNT(fkJenis_pelanggaran_id) as kategori'))->where('is_archive', $is_archive)->groupBy('fkJenis_pelanggaran_id')->get();
        if ($value == null) {
            $value = 'all';
        }

        if ($value == 'all') {
            if ($id == null) {
                $list_pelanggaran = Pelanggaran::where('is_archive', $is_archive)->get();
            } else {
                $list_pelanggaran = Pelanggaran::where('fkJenis_pelanggaran_id', $id)->where('is_archive', $is_archive)->get();
            }
        } else {
            if ($value == 'sp') {
                if ($id == null) {
                    $list_pelanggaran = Pelanggaran::whereNotNull('kategori_sp_real')->where('is_archive', $is_archive)->get();
                } else {
                    $list_pelanggaran = Pelanggaran::where('fkJenis_pelanggaran_id', $id)->whereNotNull('kategori_sp_real')->where('is_archive', $is_archive)->get();
                }
            } else if ($value == 'pantau') {
                if ($id == null) {
                    $list_pelanggaran = Pelanggaran::whereNull('kategori_sp_real')->where('is_archive', $is_archive)->get();
                } else {
                    $list_pelanggaran = Pelanggaran::where('fkJenis_pelanggaran_id', $id)->whereNull('kategori_sp_real')->where('is_archive', $is_archive)->get();
                }
            } else {
                $list_pelanggaran = Pelanggaran::where('fkJenis_pelanggaran_id', $id)->where('is_archive', $is_archive)->get();
            }
        }

        foreach ($list_pelanggaran as $lp) {
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['kategori'] = $lp->jenis->kategori_pelanggaran;
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pelanggaran'] = $lp->jenis->jenis_pelanggaran;
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'] = 0;
            }
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'] = 0;
            }
            if ($lp->keringanan_sp == '') {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan']++;
            } else {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix']++;
            }
        }

        $column = [
            'Nama',
            // 'Angkatan',
            'Pelanggaran',
            // 'Pemanggilan',
            'SP',
            // 'Peringatan Keras',
            'Keterangan'
        ];

        return view('pelanggaran.list', [
            'id' => $id,
            'value' => $value,
            'column' => $column,
            'is_archive' => $is_archive,
            'list_pelanggaran' => $list_pelanggaran,
            'jenis_pelanggaran' => JenisPelanggaran::orderBy('kategori_pelanggaran','ASC')->get(),
            'count_pelanggaran' => $count_pelanggaran
        ]);
    }

    public function create()
    {
        $list_santri = DB::table('v_user_santri')->orderBy('fullname')->get();
        $list_jenis_pelanggaran = JenisPelanggaran::get();
        $column = Pelanggaran::attr(); //DB::getSchemaBuilder()->getColumnListing('pelanggarans');

        return view('pelanggaran.create', [
            'column' => $column,
            'list_santri' => $list_santri,
            'list_jenis_pelanggaran' => $list_jenis_pelanggaran
        ]);
    }

    public function edit($id)
    {
        $list_santri = DB::table('v_user_santri')->orderBy('fullname')->get();
        $list_jenis_pelanggaran = JenisPelanggaran::get();
        $datax = Pelanggaran::find($id);
        $column = Pelanggaran::attr();; //DB::getSchemaBuilder()->getColumnListing('pelanggarans');

        return view('pelanggaran.create', [
            'id' => $id,
            'column' => $column,
            'datax' => $datax,
            'list_santri' => $list_santri,
            'list_jenis_pelanggaran' => $list_jenis_pelanggaran
        ]);
    }

    public function archive($id)
    {
        $datax = Pelanggaran::find($id);
        $datax->is_archive = 1;
        $message = 'Data gagal diarsipkan';
        if ($datax->save()) {
            $message = 'Data berhasil diarsipkan';
        }

        return redirect()->route('pelanggaran tm1')->with('success', $message);
    }

    public function delete($id)
    {
        $datax = Pelanggaran::find($id);
        $message = 'Data gagal dihapus';
        if ($datax->delete()) {
            $message = 'Data berhasil dihapus';
        }

        return redirect()->route('pelanggaran tm1')->with('success', $message);
    }

    public function store(Request $request)
    {
        $message = '';
        $update = false;
        $column = DB::getSchemaBuilder()->getColumnListing('pelanggarans');
        if ($request->input('id') != null) {
            $update = true;
            $request->validate([
                'fkSantri_id' => 'required'
            ]);
            $data = Pelanggaran::find($request->input('id'));
            foreach ($column as $c) {
                if ($c != 'is_archive' && $c != 'created_at' && $c != 'updated_at') {
                    $data->$c = $request->input($c);
                }
            }
            if ($data->save()) {
                $message = 'Berhasil mengubah data';
            } else {
                $message = 'Data gagal disimpan';
            }
        } else {
            $store = [];
            foreach ($column as $c) {
                $store[$c] = $request->input($c);
            }
            $store['is_archive'] = 0;
            $store['periode_tahun'] = CommonHelpers::periode();
            $data = Pelanggaran::create($store);
            if ($data) {
                $message = 'Berhasil menambahkan data';
            } else {
                $message = 'Gagal menambahkan data';
            }
        }

        if ($request->input('is_wa') == 1) {
            $jenis_pelanggaran = JenisPelanggaran::find($request->input('fkJenis_pelanggaran_id'));
            $caption = 'Penambahan data pelanggaran dari Mahasiswa:
- Nama: *[' . $data->santri->angkatan . '] ' . $data->santri->user->fullname . '*
- Jenis Pelanggaran: *[' . $jenis_pelanggaran->kategori_pelanggaran . '] ' . $jenis_pelanggaran->jenis_pelanggaran . '*
- Waktu: *' . $data->tanggal_melanggar . '*
- Keterangan: *' . $data->keterangan . '*';
            WaSchedules::save('Data Pelanggaran ' . $data->santri->user->fullname, $caption, CommonHelpers::settings()->wa_dewan_guru_group_id);
        }

        if ($update) {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit pelanggaran', $request->input('id')))->with('success', $message);
        } else {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('pelanggaran tm1'))->with('success', $message);
        }
    }

    public function by_mahasiswa(){
        // Hubungkan dengan Dashboard KBM < 80%
        $santris = Pelanggaran::select('fkSantri_id')->groupBy('fkSantri_id')->where('is_archive',0)->get();
        $santri_l = Pelanggaran::select('fkSantri_id')->groupBy('fkSantri_id')
                    ->join('santris','pelanggarans.fkSantri_id', '=', 'santris.id')
                    ->join('users','users.id', '=', 'santris.fkUser_id')
                    ->where('pelanggarans.is_archive',0)
                    ->where('users.gender', 'Male')
                    ->get();
        $santri_p = Pelanggaran::select('fkSantri_id')->groupBy('fkSantri_id')
                    ->join('santris','pelanggarans.fkSantri_id', '=', 'santris.id')
                    ->join('users','users.id', '=', 'santris.fkUser_id')
                    ->where('pelanggarans.is_archive',0)
                    ->where('users.gender', 'Female')
                    ->get();
        $column_pelanggarans = Pelanggaran::select('fkJenis_pelanggaran_id')->groupBy('fkJenis_pelanggaran_id')->where('is_archive',0)->get();

        return view('pelanggaran.by_mahasiswa', [
            'santris' => $santris,
            'santri_l' => $santri_l,
            'santri_p' => $santri_p,
            'column_pelanggarans' => $column_pelanggarans,
        ]);
    }

    public function wa(Request $request){
        if($request->input('santri_id')==0){
            $santris = DB::table('v_user_santri')->get();
        }else{
            $santris = DB::table('v_user_santri')->where('santri_id',$request->input('santri_id'))->get();
        }

        if($santris){
            foreach($santris as $s){
                $pelanggarans = Pelanggaran::where('fkSantri_id',$s->santri_id)->where('is_archive',0)->get();
                
                if(count($pelanggarans)>0){
                    $time = 1;
                    $jenis_pelanggaran = "*Jenis Pelanggaran:*
";
                    foreach($pelanggarans as $p){
                        $jenis_pelanggaran .= "- [".strtoupper($p->jenis->kategori_pelanggaran)."] ".strtoupper($p->jenis->jenis_pelanggaran)."
";
                    }
                    $caption = "*[INFORMASI PEMANGGILAN]*
Berdasarkan penyaksian dan hasil evaluasi dari Tim Ketertiban PPMRJ, *an. ".$s->fullname."* harap memenuhi panggilan dari Pengurus, yang akan dilaksanakan:
📆 Waktu: ".date_format(date_create($request->input('datetime')),'d M Y | H:i:s')."
🕌 Tempat: ".$request->input('tempat')."

".$jenis_pelanggaran."
NB:
- Wajib mendatangi pemanggilan
- Jika tidak memenuhi panggilan, akan dipanggil Orangtua untuk datang ke PPMRJ";
                    WaSchedules::save('Pemanggilan an. ' . $s->fullname, $caption, WaSchedules::getContactId($s->nohp), $time);
                    $time++;
                    WaSchedules::save('Pemanggilan an. ' . $s->fullname, $caption, WaSchedules::getContactId($s->nohp_ortu), $time);
                    $time++;
                }
            }
        }

        return json_encode(array("status" => true, "message" => 'Berhasil melakukan pemanggilan'));
    }

    public function selesai_kafaroh(Request $request)
    {
        $pelanggarans = Pelanggaran::where('fkSantri_id',$request->input('santri_id'))->where('is_archive',0)->get();
        if(count($pelanggarans)>0){
            foreach($pelanggarans as $p){
                $done = Pelanggaran::find($p->id);
                $done->is_archive = 1;
                $done->save();
            }
        }
        return json_encode(array("status" => true, "message" => 'Berhasil update pelanggaran'));
    }

    public function update_pelanggaran(Request $request)
    {
        $pelanggarans = Pelanggaran::find($request->input('id'));
        if($pelanggarans!=null){
            $field = $request->input('field');
            $pelanggarans->$field = $request->input('value');
            if($pelanggarans->save()){
                return json_encode(array("status" => true, "message" => 'Berhasil update pelanggaran'));
            }else{
                return json_encode(array("status" => false, "message" => 'Gagal update pelanggaran'));
            }
        }else{
            return json_encode(array("status" => false, "message" => 'Pelanggaran tidak ditemukan'));
        }
    }
}

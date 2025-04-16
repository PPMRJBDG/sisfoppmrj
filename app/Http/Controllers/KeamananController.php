<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JagaMalams;
use App\Models\TelatPulangMalams;
use App\Models\LaporanKeamanans;
use App\Models\Santri;
use App\Models\Presence;
use App\Models\Present;
use App\Helpers\WaSchedules;
use App\Models\Settings;

class KeamananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $datax = JagaMalams::orderBy('putaran_ke','ASC')->get();
        $santris = DB::table('v_user_santri')->where('gender','male')->orderBy('fullname','ASC')->get();

        return view('keamanan.jagamalam', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }

    public function store_jagamalam(Request $request)
    {
        $check = JagaMalams::where('ppm',$request->input('ppm'))->get();

        if($request->input('id')!= ""){
            $insert = JagaMalams::find($request->input('id'));
            $insert->ppm = $request->input('ppm');
            $insert->putaran_ke = $request->input('putaran_ke');
            $insert->anggota = $request->input('anggota');
            $insert->save();
        }else{
            // $putaran = 0;
            // if($check==null){
            //     $putaran = 1;
            // }else{
            //     $putaran = count($check)+1;
            // }
            $insert = JagaMalams::create([
                'ppm' => $request->input('ppm'),
                'putaran_ke' => $request->input('putaran_ke'),
                'anggota' => $request->input('anggota'),
            ]);
        }

        if($insert){
            return json_encode(array("status" => true));
        }else{
            return json_encode(array("status" => false));
        }
    }

    public function store_pulangmalam(Request $request)
    {
        $insert = TelatPulangMalams::create([
            'fkJaga_malam_id' => auth()->user()->santri->id,
            'fkSantri_id' => $request->input('santri_id'),
            'jam_pulang' => $request->input('jam_pulang'),
            'alasan' => $request->input('alasan'),
        ]);

        if($insert){
            $setting = Settings::find(1);
            $santri = Santri::find($request->input('santri_id'));
            $caption = '*[JAGA MALAM]* Laporan dari: *'.auth()->user()->fullname.'*,
- *'.$santri->user->fullname.'* baru pulang pukul '.date_format(date_create($request->input('jam_pulang')),'H:i:s').' dengan alasan: *'.$request->input('alasan').'*';
            WaSchedules::save('Terlambat Pulang Malam: '.$santri->user->fullname, $caption, $setting->wa_info_presensi_group_id, null, true);
            return json_encode(array("status" => true));
        }else{
            return json_encode(array("status" => false));
        }
    }

    public function delete_jagamalam($id)
    {
        $data = JagaMalams::find($id);
        if (!$data)
            return redirect()->route('index keamanan', $id)->withErrors(['periode_not_found' => 'Periode tidak ditemukan.']);
        $data->delete();
        return redirect()->route('index keamanan', $id)->with('success', 'Berhasil menghapus periode');
    }

    public function pulang_malam()
    {
        $data_telatpulang = TelatPulangMalams::orderBy('id','DESC')->get();

        return view('keamanan.pulangmalam', [
            'data_telatpulang' => $data_telatpulang,
        ]);
    }

    public function store_jobdesk(Request $request)
    {
        $check = LaporanKeamanans::find(auth()->user()->santri->fkLaporan_keamanan_id);
        if($check!=null){
            $check->jd_kunci_gerbang = ($request->input('jd_kunci_gerbang')) ? 1 : 0;
            $check->jd_cek_air = ($request->input('jd_cek_air')) ? 1 : 0;
            $check->jd_cek_listrik = ($request->input('jd_cek_listrik')) ? 1 : 0;
            $check->jd_cek_lingkungan = ($request->input('jd_cek_lingkungan')) ? 1 : 0;
            $check->jd_cek_lahan = ($request->input('jd_cek_lahan')) ? 1 : 0;
            $check->jd_adzan_malam = ($request->input('jd_adzan_malam')) ? 1 : 0;
            $check->jd_nerobos_muadzin = ($request->input('jd_nerobos_muadzin')) ? 1 : 0;
            $check->jd_kondisi_umum = ($request->input('jd_kondisi_umum')) ? $request->input('jd_kondisi_umum') : null;
            if($check->save()){
                // laporan WA
            $setting = Settings::find(1);
            $jd_kunci_gerbang = ($request->input('jd_kunci_gerbang')) ? '✅' : '';
            $jd_cek_air = ($request->input('jd_cek_air')) ? '✅' : '';
            $jd_cek_listrik = ($request->input('jd_cek_listrik')) ? '✅' : '';
            $jd_cek_lingkungan = ($request->input('jd_cek_lingkungan')) ? '✅' : '';
            $jd_cek_lahan = ($request->input('jd_cek_lahan')) ? '✅' : '';
            $jd_adzan_malam = ($request->input('jd_adzan_malam')) ? '✅' : '';
            $jd_nerobos_muadzin = ($request->input('jd_nerobos_muadzin')) ? '✅' : '';
            $caption = '*UPDATE INFO JAM MALAM PPM* | _'.date('H:i:s').'_

👮🏼‍♂️ *'.auth()->user()->fullname.'*:
- Mengunci Gerbang '.$jd_kunci_gerbang.'
- Mengecek Air '.$jd_cek_air.'
- Mengecek Listrik '.$jd_cek_listrik.'
- Mengecek Lingkungan '.$jd_cek_lingkungan.'
- Mengecek Lahan '.$jd_cek_lahan.'
- Sudah Adzan 1/3 Malam '.$jd_adzan_malam.'
- Salah Satu Sudah Adzan Shubuh '.$jd_nerobos_muadzin.'
- Kondisi Umum: '.$request->input('jd_kondisi_umum');
            WaSchedules::save('Update Info Jam Malam: '.auth()->user()->fullname, $caption, $setting->wa_info_presensi_group_id, null, true);

                // create presence
                $event_date = date('Y-m-d', strtotime("+1 day", strtotime($check->event_date)));
                $presence = Presence::where('event_date', $event_date)->where('fkPresence_group_id',1)->first();
                if($presence!=null){
                    $present = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', auth()->user()->santri->id)->first();
                    if($present==null){
                        Present::create([
                            'fkSantri_id' => auth()->user()->santri->id,
                            'fkPresence_id' => $presence->id,
                            'is_late' => 0,
                            'updated_by' => 'Jaga Malam',
                            'metadata' => $_SERVER['HTTP_USER_AGENT']
                        ]);
                    }
                }
            }
        }
        return redirect()->route('dashboard');
    }
}

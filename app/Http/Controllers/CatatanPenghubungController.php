<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CatatanPenghubungs;
use App\Models\Settings;
use App\Models\SpWhatsappPhoneNumbers;
use App\Helpers\WaSchedules;

class CatatanPenghubungController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cat_penghubung = CatatanPenghubungs::get();
        $cat_penghubung = DB::select(
            "SELECT a.angkatan, a.fullname, a.santri_id, b.id, b.cat_kepribadian, b.cat_sholat, b.cat_kbm, b.cat_asmara, b.cat_akhlaq, b.cat_umum
            FROM v_user_santri a LEFT JOIN catatan_penghubungs b ON a.santri_id=b.fkSantri_id AND b.status=1
            ORDER BY a.angkatan"
        );

        return view('catatanPenghubung.index', [
            'cat_penghubung' => $cat_penghubung
        ]);
    }

    public function store(Request $request)
    {
        $data = CatatanPenghubungs::find($request->input('id'));
        if (!$data) {
            $data = CatatanPenghubungs::create([
                'fkSantri_id' => $request->input('santri_id'),
                'cat_kepribadian' => $request->input('cat_kepribadian'),
                'cat_sholat' => $request->input('cat_sholat'),
                'cat_kbm' => $request->input('cat_kbm'),
                'cat_asmara' => $request->input('cat_asmara'),
                'cat_akhlaq' => $request->input('cat_akhlaq'),
                'cat_umum' => $request->input('cat_umum'),
                'status' => 1,
                'created_by' => auth()->user()->fullname
            ]);
            if ($data) {
                return json_encode(array("status" => true, "message" => 'Catatan berhasil disimpan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Catatan gagal disimpan'));
            }
        } else {
            $data->cat_kepribadian = $request->input('cat_kepribadian');
            $data->cat_sholat = $request->input('cat_sholat');
            $data->cat_kbm = $request->input('cat_kbm');
            $data->cat_asmara = $request->input('cat_asmara');
            $data->cat_akhlaq = $request->input('cat_akhlaq');
            $data->cat_umum = $request->input('cat_umum');
            $data->status = 1;
            $data->created_by = auth()->user()->fullname;
            if ($data->save()) {
                if ($request->input('info_wa') == "true") {
                    $nohp = $data->santri->nohp_ortu;
                    if ($nohp != '') {
                        if ($nohp[0] == '0') {
                            $nohp = '62' . substr($nohp, 1);
                        }
                        $setting = Settings::find(1);
                        $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                            $query->where('name', 'NOT LIKE', '%Bulk%');
                        })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                        if ($wa_phone != null) {
                            $caption = 'Bapak/Ibu yang kami hormati, berikut kami sampaikan *Catatan Penghubung* antara Mahasiswa - Pengurus - Orangtua:

*Kepribadian*: ' . $data->cat_kepribadian . '
*Sholat*: ' . $data->cat_sholat . '
*KBM*: ' . $data->cat_kbm . '
*Asmara*: ' . $data->cat_asmara . '
*Akhlaq*: ' . $data->cat_akhlaq . '
*Umum*: ' . $data->cat_umum;
                            WaSchedules::save('Catatan Penghubung: [' . $data->santri->angkatan . '] ' . $data->santri->user->fullname, $caption, $wa_phone->pid);
                        }
                    }
                }
                return json_encode(array("status" => true, "message" => 'Catatan berhasil diubah'));
            } else {
                return json_encode(array("status" => false, "message" => 'Catatan gagal diubah'));
            }
        }
    }

    public function sendWaOrtu()
    {
    }
}

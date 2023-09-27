<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sodaqoh;
use App\Models\Settings;
use App\Models\SpWhatsappPhoneNumbers;
use App\Helpers\WaSchedules;
use Illuminate\Support\Facades\DB;

class SodaqohController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list($periode = null, $angkatan = null)
    {
        $datax = [];
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'ASC')
            ->get();
        $list_periode = Sodaqoh::select('periode')->groupBy('periode')->get();

        if (($periode == '-' && $angkatan == '-') || ($periode == null && $angkatan == null)) {
            $datax = Sodaqoh::get();
            $periode = '-';
            $angkatan = '-';
        } elseif ($periode != '-' && $angkatan != '-') {
            $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                $query->where('angkatan', $angkatan);
            })->where('periode', $periode)->get();
        } elseif ($angkatan != null && ($periode == null || $periode == '-')) {
            $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                $query->where('angkatan', $angkatan);
            })->get();
        } elseif ($periode != null && ($angkatan == null || $angkatan == '-')) {
            $datax = Sodaqoh::where('periode', $periode)->get();
        }

        return view('sodaqoh.list', [
            'datax' => $datax,
            'select_angkatan' => $angkatan,
            'list_periode' => $list_periode,
            'list_angkatan' => $list_angkatan,
            'periode' => $periode
        ]);
    }

    public function store(Request $request)
    {
        $check = Sodaqoh::find($request->input('id'));
        $bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
        if ($check) {
            // crosscheck
            if ($check->fkSantri_id == $request->input('fkSantri_id')) {
                $check->nominal = $request->input('nominal');
                $check->keterangan = $request->input('keterangan');
                foreach ($bulan as $b) {
                    $c = $b . '_date';
                    if ($request->input($b) != null) {
                        $check->$b = $request->input($b);
                    }
                    if ($request->input($c) != null) {
                        $check->$c = $request->input($c);
                    }
                }
                if ($check->save()) {
                    $terbayar = 0;
                    foreach ($bulan as $b) {
                        $terbayar = $terbayar + $check->$b;
                    }
                    $nominal_kekurangan = $check->nominal - $terbayar;
                    $text_kekurangan = '';
                    $status_lunas = '*[LUNAS]*';
                    if ($nominal_kekurangan > 0) {
                        $text_kekurangan = 'Adapun kekurangannya masih senilai: *Rp ' . number_format($nominal_kekurangan, 0) . ',-*';
                        $status_lunas = '*[BELUM LUNAS]*';
                    } else {
                        $check->status_lunas = 1;
                        $check->save();
                    }
                    // kirim wa
                    if ($request->input('info-wa') == "true") {
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
                                $caption = $status_lunas . ' Pembayaran Sodaqoh Tahunan PPM RJ Periode ' . $check->periode . ' an. ' . $check->santri->user->fullname . ' sudah dikonfirmasi. ' . $text_kekurangan;
                                WaSchedules::save('Sodaqoh: [' . $check->santri->angkatan . '] ' . $check->santri->user->fullname . ' - ' . $check->periode, $caption, $wa_phone->pid);
                            }
                        }
                    }
                    // end kirim wa
                    return json_encode(array("status" => true, "message" => 'Berhasil diinput'));
                } else {
                    return json_encode(array("status" => false, "message" => 'Gagal diinput'));
                }
            } else {
                return json_encode(array("status" => false, "message" => 'ID Mahasiswa tidak valid'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Data tidak ditemukan'));
        }
    }

    public function delete($id, $periode, $angkatan)
    {
        $data = Sodaqoh::find($id);
        if (!$data)
            return redirect()->route('list periode sodaqoh', [$periode, $angkatan])->withErrors(['periode_not_found' => 'Sodaqoh tidak ditemukan.']);
        $data->delete();
        return redirect()->route('list periode sodaqoh', [$periode, $angkatan])->with('success', 'Berhasil menghapus sodaqoh');
    }
}

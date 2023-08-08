<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sodaqoh;
use Illuminate\Support\Facades\DB;

class SodaqohController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list($periode = null, $angkatan = null)
    {
        $datax = null;
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->groupBy('angkatan')
            ->get();
        $list_periode = Sodaqoh::select('periode')->groupBy('periode')->get();

        if ($periode != '-' && $angkatan != '-') {
            $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                $query->where('angkatan', $angkatan);
            })->where('periode', $periode)->get();
        } elseif ($periode == '-' && $angkatan != '-') {
            $datax = Sodaqoh::whereHas('santri', function ($query) use ($angkatan) {
                $query->where('angkatan', $angkatan);
            })->get();
        } elseif ($periode != '-' && $angkatan == '-') {
            $datax = Sodaqoh::where('periode', $periode)->get();
        } elseif ($periode == '-' && $angkatan == '-') {
            $datax = null;
            $periode = null;
            $angkatan = null;
        }

        return view('sodaqoh.list', [
            'datax' => $datax,
            'select_angkatan' => $angkatan,
            'list_periode' => $list_periode,
            'list_angkatan' => $list_angkatan,
            'periode' => $periode,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required',
            'bulan' => 'required',
            'santri_id' => 'required',
            'jumlah' => 'required',
        ]);

        $check = Sodaqoh::find($request->input('id'));
        if ($check) {
            // crosscheck
            if ($check->fkSantri_id == $request->input('santri_id')) {
                $bulan = $request->input('bulan');
                if ($bulan == 'ket') {
                    $check->keterangan = $request->input('jumlah');
                } else {
                    if ($check->$bulan == null || $check->$bulan == "") {
                        $check->$bulan = 0;
                    }
                    $check->$bulan = intval($check->$bulan) + intval($request->input('jumlah'));
                }
                if ($check->save()) {
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
}

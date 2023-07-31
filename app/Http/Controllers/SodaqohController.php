<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Sodaqoh;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SodaqohController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list($periode = null)
    {
        $datax = null;
        $list_periode = Sodaqoh::select('periode')->groupBy('periode')->get();
        if (count($list_periode) == 1) {
            $periode = $list_periode[0]->periode;
        }
        if (isset($periode)) {
            $datax = Sodaqoh::where('periode', $periode)->get();
        }
        return view('sodaqoh.list', [
            'datax' => $datax,
            'list_periode' => $list_periode,
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
                if ($check->$bulan == null || $check->$bulan == "") {
                    $check->$bulan = 0;
                }
                $check->$bulan = intval($check->$bulan) + intval($request->input('jumlah'));
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

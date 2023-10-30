<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rabs;
use App\Models\RabInouts;
use App\Models\RabPeriodes;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function receipt($periode = null)
    {
        if ($periode == null) {
            $rabs = [];
        } else {
            $rabs = Rabs::where('periode_tahunan', $periode)->get();
        }

        return view('keuangan.receipt', [
            'rabs' => $rabs,
            'periode' => $periode,
        ]);
    }

    public function rab($periode = null)
    {
        if ($periode == null) {
            $rabs = [];
        } else {
            $rabs = Rabs::where('periode_tahunan', $periode)->get();
        }

        return view('keuangan.rab', [
            'rabs' => $rabs,
            'periode' => $periode,
        ]);
    }

    public function rab_create_update($periode = null)
    {
        if ($periode == null) {
            $rabs = [];
        } else {
            $rabs = Rabs::where('periode_tahunan', $periode)->get();
        }

        return view('keuangan.rab', [
            'rabs' => $rabs,
            'periode' => $periode,
        ]);
    }

    public function rab_store($periode = null)
    {
        if ($periode == null) {
            $rabs = [];
        } else {
            $rabs = Rabs::where('periode_tahunan', $periode)->get();
        }

        return view('keuangan.rab', [
            'rabs' => $rabs,
            'periode' => $periode,
        ]);
    }

    public function inout($periode = null)
    {

        return view('keuangan.inout', []);
    }
}

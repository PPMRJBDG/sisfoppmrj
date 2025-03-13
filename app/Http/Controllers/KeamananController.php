<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JagaMalams;

class KeamananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $datax = JagaMalams::get();
        $santris = DB::table('v_user_santri')->where('gender','male')->orderBy('fullname','ASC')->get();

        return view('keamanan.jagamalam', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }

    public function store_jagamalam()
    {
        $datax = JagaMalams::get();
        $santris = DB::table('v_user_santri')->where('gender','male')->orderBy('fullname','ASC')->get();

        return view('keamanan.jagamalam', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }
}

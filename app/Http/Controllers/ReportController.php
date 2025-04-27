<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReportScheduler;
use App\Helpers\CommonHelpers;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function link_ortu()
    {
        $datax = ReportScheduler::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->get();
        $status = ReportScheduler::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
            $query->where('status',1);
        })->get();

        return view('report.report', [
            'datax' => $datax,
            'status' => $status,
        ]);
    }

    public function penilaian()
    {
        $mahasiswa = DB::table('v_user_santri')->orderBy('fullname','ASC')->get();

        return view('report.penilaian', [
            'mahasiswa' => $mahasiswa,
        ]);
    }
}

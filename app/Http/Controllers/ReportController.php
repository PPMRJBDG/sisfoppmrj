<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReportScheduler;
use App\Models\Evaluations;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;

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
        $mahasiswa = DB::table('v_evaluasi_mahasiswa')->orderBy('angkatan')->get();

        return view('report.penilaian', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function store_evaluation(Request $request)
    {
        if($request->input('value')=="-"){
            return json_encode(array("status" => false));
        }
        $datax = Evaluations::where('fkSantri_id',$request->input('santri_id'))->first();
        
        if($datax==null){
            $store['fkSantri_id'] = $request->input('santri_id');
            $store[$request->input('field')] = $request->input('value');
            Evaluations::create($store);
        }else{
            $field = $request->input('field');
            $datax->$field = $request->input('value');
            $datax->save();
        }

        $score = array();
        $mahasiswa = DB::table('v_evaluasi_mahasiswa')->where('santri_id', $request->input('santri_id'))->first();
        if($mahasiswa!=null){
            $score = CountDashboard::score($mahasiswa);
        }

        return json_encode(array("status" => true, "score" => $score));
    }
}

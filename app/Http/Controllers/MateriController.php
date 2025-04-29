<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\DewanPengajars;
use App\Models\KalenderPpmTemplates;
use App\Models\KalenderPpms;
use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\HourKbms;
use App\Models\DayKbms;
use App\Models\JadwalHariJamKbms;
use Illuminate\Support\Facades\DB;
use App\Helpers\PresenceGroupsChecker;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;

class MateriController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the list and manage table of materis.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage()
    {
        $materis = Materi::all();

        return view('materi.list_and_manage', ['materis' => $materis]);
    }

    /**
     * Show the create form of materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        return view('materi.create');
    }

    /**
     * Insert new materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'pageNumbers' => 'required|integer',
            'for' => 'string'
        ]);

        $inserted = Materi::create($request->all());

        return redirect()->route('materi tm')->with($inserted ? 'success' : 'failed', $inserted ? 'Materi baru berhasil ditambahkan.' : 'Gagal menambah materi baru.');
    }

    /**
     * Show the create form of materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $materi = Materi::find($id);

        return view('materi.edit', ['materi' => $materi]);
    }

    /**
     * Update materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $materiIdToUpdate = $request->route('id');

        $request->validate([
            'name' => 'required',
            'pageNumbers' => 'required|integer',
        ]);

        // validate availability of materi existence
        $materi = Materi::find($materiIdToUpdate);

        if (!$materi)
            return redirect()->route('edit materi', $materiIdToUpdate)->withErrors(['materi_not_found' => 'Can\'t update unexisting Materi.']);

        $materi->name = $request->input('name');
        $materi->pageNumbers = $request->input('pageNumbers');

        $updated = $materi->save();

        if (!$updated)
            return redirect()->route('edit materi', $materiIdToUpdate)->withErrors(['failed_updating_materi' => 'Gagal mengubah materi.']);

        return redirect()->route('edit materi', $materiIdToUpdate)->with('success', 'Berhasil mengubah materi.');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete($id)
    {
        $materi = Materi::find($id);

        if ($materi) {
            $deleted = $materi->delete();

            if (!$deleted)
                return redirect()->route('materi tm')->withErrors(['failed_deleting_materi', 'Gagal menghapus Materi.']);
        }

        return redirect()->route('materi tm')->with('success', 'Berhasil menghapus Materi');
    }

    public function jadwal_kbm()
    {
        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru')) {
            $santri_id = 0;
        } else {
            $santri_id = auth()->user()->santri->id;
        }
        $day_kbm = DayKbms::whereNull('is_holiday')->get();
        $hour_kbm = HourKbms::all();

        return view('materi.jadwal_kbm', ['day_kbm' => $day_kbm, 'hour_kbm' => $hour_kbm, 'santri_id' => $santri_id]);
    }

    public function jadwal_kbm_store(Request $request)
    {
        $santri_id = auth()->user()->santri->id;

        if ($request->input('action') == 'insert') {
            $jadwal = JadwalHariJamKbms::where('fkSantri_id', $santri_id)
                ->where('fkHari_kbm_id', $request->input('day'))
                ->where('fkJam_kbm_id', $request->input('hour'))->first();

            if ($jadwal == null) {
                JadwalHariJamKbms::create([
                    'fkSantri_id' => $santri_id,
                    'fkHari_kbm_id' => $request->input('day'),
                    'fkJam_kbm_id' => $request->input('hour')
                ]);
            }
        } elseif ($request->input('action') == 'delete') {
            $jadwal = JadwalHariJamKbms::where('fkSantri_id', $santri_id)
                ->where('fkHari_kbm_id', $request->input('day'))
                ->where('fkJam_kbm_id', $request->input('hour'))->first();
            $jadwal->delete();
        }
    }

    public function list_pengajar()
    {
        $pengajar = DewanPengajars::orderBy('name','DESC')->get();
        $presence = PresenceGroup::whereIn('id', [1, 2])->get();

        return view('materi.list_pengajar', ['pengajar' => $pengajar, 'presence' => $presence]);
    }

    public function store_pengajar(Request $request)
    {
        $inserted = DewanPengajars::create($request->all());

        return json_encode(['status' => true, 'message' => 'Berhasil menambahkan pengajar', 'id' => $inserted->id]);
    }

    public function update_pengajar(Request $request)
    {
        $id = $request->route('id');
        $pengajar = DewanPengajars::find($id);

        if ($pengajar) {
            $pengajar->name = $request->input('name');
            $updated = $pengajar->save();

            if (!$updated)
                return json_encode(['status' => false, 'message' => 'Gagal mengubah pengajar']);
        }

        return json_encode(['status' => true, 'message' => 'Berhasil mengubah pengajar']);
    }

    public function delete_pengajar($id)
    {
        $pengajar = DewanPengajars::find($id);

        if ($pengajar) {
            $deleted = $pengajar->delete();

            if (!$deleted)
                return json_encode(['status' => false, 'message' => 'Gagal menghapus pengajar']);
        }

        return json_encode(['status' => true, 'message' => 'Berhasil menghapus pengajar']);
    }

    public function template_kalender_ppm(){
        $today = date('Y-m-d', strtotime(today()));
        $pengajars = DewanPengajars::whereNotNull('is_degur')->orderBy('is_degur','ASC')->get();
        $template = KalenderPpmTemplates::all();
        $counts = DB::table('kalender_ppm_templates as a')
                    ->select('a.fkDewanPengajar_id', 'b.name', DB::raw('count(a.id) as total'))
                    ->leftJoin('dewan_pengajars as b', function ($join) {
                        $join->on('a.fkDewanPengajar_id','=','b.id');
                    })
                    ->whereNotNull('a.fkDewanPengajar_id')
                    ->groupBy('a.fkDewanPengajar_id', 'b.name')
                    ->orderBy('b.name','ASC')
                    ->get();
        return view('materi.template_kalender_ppm', ['today' => $today, 'pengajars' => $pengajars, 'template' => $template, 'counts' => $counts]);
    }

    public function store_template_kalender_ppm(Request $request){
        $get = KalenderPpmTemplates::find($request->input('id'));
        if(!$get){
            $get = KalenderPpmTemplates::where('sequence',$request->input('sequence'))->where('waktu',$request->input('waktu'))->where('kelas',$request->input('kelas'))->first();
        }

        if(!$get){
            $insert = KalenderPpmTemplates::create([
                        'waktu' => $request->input('waktu'),
                        'kelas' => $request->input('kelas'),
                        'sequence' => $request->input('sequence'),
                        'fkDewanPengajar_id' => $request->input('fkDewanPengajar_id'),
                        'is_agenda_khusus' => $request->input('is_agenda_khusus'),
                        'nama_agenda_khusus' => $request->input('nama_agenda_khusus'),
                        'day' => $request->input('day'),
                    ]);
        }else{
            $get->is_agenda_khusus = $request->input('is_agenda_khusus');
            if($request->input('is_agenda_khusus')==1){
                $get->nama_agenda_khusus = $request->input('nama_agenda_khusus');
                $get->waktu = $request->input('waktu');
                $get->kelas = null;
                $get->fkDewanPengajar_id = null;
            }else{
                $get->nama_agenda_khusus = null;
                $get->waktu = $request->input('waktu');
                $get->kelas = $request->input('kelas');
                $get->fkDewanPengajar_id = $request->input('fkDewanPengajar_id');
            }
            $get->save();
            if($request->input('is_agenda_khusus')==1){
                $delete = KalenderPpmTemplates::where('is_agenda_khusus',0)->where('waktu',$request->input('waktu'))->where('sequence',$request->input('sequence'))->delete();
            }else{
                $delete = KalenderPpmTemplates::where('is_agenda_khusus',1)->where('waktu',$request->input('waktu'))->where('sequence',$request->input('sequence'))->delete();
            }
        }
    }

    public function store_kalender_ppm(Request $request){
        if($request->input('ak')=="false"){
            // perubahan start sequence pada tanggal sekian
            $get = KalenderPpms::where('id',$request->input('id'))->where('x',$request->input('x'))->first();

            if(!$get){
                $insert = KalenderPpms::create([
                            'x' => $request->input('x'),
                            'bulan' => $request->input('bulan'),
                            'start' => $request->input('start'),
                        ]);
            }else{
                $get->bulan = $request->input('bulan');
                $get->start = $request->input('start');
                $get->save();

                if($request->input('x')==2){
                    $seq = 28;
                    for($a=$request->input('start'); $a>1; $a--){
                        $datax = KalenderPpms::where('bulan',$request->input('bulan'))->where('x',1)->first();
                        if($datax){
                            $datax->start = $seq;
                            $datax->save();
                        }
                        $seq--;
                    }

                    // aksi saat mengubah start tanggal dan sequence
                    if($request->input('bulan')==intval(date('m'))){
                        PresenceGroupsChecker::createPresence(true);
                    }
                }
            }
        }else{
            // perubahan tanggal tertentu (single)
            $get = KalenderPpms::where('is_certain_conditions',1)->where('waktu_certain_conditions',$request->input('waktu'))->where('bulan',$request->input('bulan'))->where('start',$request->input('start'))->first();
            if($get){
                if($request->input('nama')=="reset"){
                    $get->delete();
                }else{
                    $get->nama_certain_conditions = $request->input('nama');
                    $get->save();
                }
            }else{
                if($request->input('nama')!="reset"){
                    $insert = KalenderPpms::create([
                        'bulan' => $request->input('bulan'),
                        'start' => $request->input('start'),
                        'is_certain_conditions' => 1,
                        'waktu_certain_conditions' => $request->input('waktu'),
                        'nama_certain_conditions' => $request->input('nama'),
                    ]);
                    
                }
            }

            if($request->input('bulan')==intval(date('m'))){
                $degur_mt = null;
                $degur_reg = null;
                $degur_pemb = null;
                $is_hasda = 0;
                $is_put_together = 0;

                $currentDate = date_format(date_create(date('Y')."-".$request->input('bulan')."-".$request->input('start')),'Y-m-d');

                $presenceGroup_id = 1;
                if($request->input('waktu')=='malam'){
                    $presenceGroup_id = 2;
                }elseif(strtolower(date_format(date_create($currentDate), 'l'))=='sunday'){
                    $presenceGroup_id = 8;
                }

                $presenceGroup = PresenceGroup::find($presenceGroup_id);
                $presenceInThisDate = Presence::where('fkPresence_group_id', $presenceGroup_id)
                        ->where('event_date', $currentDate)->first();

                $presenceName = $presenceGroup->name . ' ' . date_format(date_create($currentDate),'d/m');

                if($request->input('nama')=="reset"){
                    $data_calendar = PresenceGroupsChecker::getCalendar(date('Y'),$request->input('bulan'),$request->input('start'));
                    if($presenceGroup->id==1){ // shubuh
                        if($data_calendar['shubuh']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['shubuh']['nama'];
                            if (str_contains($data_calendar['shubuh']['nama'], 'HASDA-')) {
                                $is_hasda = 1;
                            }
                        }else{
                            $degur_mt = $data_calendar['shubuh']['mt']['id_degur'];
                            $degur_reg = $data_calendar['shubuh']['reguler']['id_degur'];
                            $degur_pemb = $data_calendar['shubuh']['pemb']['id_degur'];
                        }
                    }elseif($presenceGroup->id==2){ // malam
                        if($data_calendar['malam']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['malam']['nama'];
                        }else{
                            $degur_mt = $data_calendar['malam']['mt']['id_degur'];
                            $degur_reg = $data_calendar['malam']['reguler']['id_degur'];
                            $degur_pemb = $data_calendar['malam']['pemb']['id_degur'];
                        }
                    }elseif($presenceGroup->id==8){ // bulanan
                        $is_put_together = 1;
                        if($data_calendar['shubuh']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['shubuh']['nama'];
                        }elseif($data_calendar['malam']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['malam']['nama'];
                        }
                    }
                }else{
                    $presenceName .= " " . $request->input('nama');
                }
                
                if (str_contains($presenceName, 'HASDA-')) {
                    $is_hasda = 1;
                }
                if (str_contains($presenceName, 'HASDA-TEKS') || 
                    str_contains($presenceName, 'PENGARAHAN KHUSUS') || 
                    str_contains($presenceName, 'HASDA-ORGANISASI') || 
                    str_contains($presenceName, 'ASAD') ||
                    str_contains($presenceName, 'PRA-PPM') ||
                    str_contains($presenceName, 'SARASEHAN') ||
                    str_contains($presenceName, 'MANAJEMEN') ||
                    str_contains($presenceName, 'NASEHAT PENGURUS') ||
                    str_contains($presenceName, 'PAT')) {
                    $is_put_together = 1;
                }

                if (isset($presenceInThisDate)) {
                    if(str_contains($presenceName, 'LIBUR')){
                        $presenceInThisDate->delete();
                    }else{
                        $newPresenceInThisDate = Presence::find($presenceInThisDate->id)->update([
                            'name' => strtoupper($presenceName),
                            'total_mhs' => CountDashboard::total_mhs('all'),
                            'start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->start_hour)),
                            'end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->end_hour)),
                            'presence_start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_start_hour)),
                            'presence_end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_end_hour)),
                            'pre_fkDewan_pengajar_mt' => $degur_mt,
                            'pre_fkDewan_pengajar_reg' => $degur_reg,
                            'pre_fkDewan_pengajar_pemb' => $degur_pemb,
                            'is_deleted' => 2,
                            'is_hasda' => $is_hasda,
                            'is_put_together' => $is_put_together,
                        ]);
                    }
                }else{
                    $newPresenceInThisDate = Presence::create([
                        'fkPresence_group_id' => $presenceGroup->id,
                        'name' => strtoupper($presenceName),
                        'event_date' => $currentDate,
                        'total_mhs' => CountDashboard::total_mhs('all'),
                        'start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->start_hour)),
                        'end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->end_hour)),
                        'presence_start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_start_hour)),
                        'presence_end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_end_hour)),
                        'pre_fkDewan_pengajar_mt' => $degur_mt,
                        'pre_fkDewan_pengajar_reg' => $degur_reg,
                        'pre_fkDewan_pengajar_pemb' => $degur_pemb,
                        'is_deleted' => 2,
                        'is_hasda' => $is_hasda,
                        'is_put_together' => $is_put_together,
                    ]);
                }
            }
        }
    }

    public function reset_kalender_ppm(){
        $periode = CommonHelpers::periode();
        $periode = explode("-",$periode);
        $month = ['09','10','11','12','01','02','03','04','05','06','07','08'];
        $year = [$periode[0],$periode[0],$periode[0],$periode[0],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1]];

        $deletes = KalenderPpms::whereNull('x')->where('is_certain_conditions',1)->delete();

        for($i=0; $i<12; $i++){
            $jumlah_tanggal = cal_days_in_month(CAL_GREGORIAN, $month[$i], $year[$i]);
            $is_first_saturday = 0;
            for($x=1; $x<=$jumlah_tanggal; $x++){
                $currentDay = strtolower(date_format(date_create($x.'-'.$month[$i].'-'.$year[$i]), 'l'));
                if($currentDay=='saturday' && $is_first_saturday==0){
                    $is_first_saturday = 1;
                    $datas = KalenderPpms::where('bulan',intval($month[$i]))->where('x',2)->first();
                    if($datas){
                        $datas->start = $x;
                        $datas->save();
                    }

                    $seq = 28;
                    for($a=$x; $a>1; $a--){
                        $datax = KalenderPpms::where('bulan',intval($month[$i]))->where('x',1)->first();
                        if($datax){
                            $datax->start = $seq;
                            $datax->save();
                        }
                        $seq--;
                    }
                }
            }
        }
    }
}

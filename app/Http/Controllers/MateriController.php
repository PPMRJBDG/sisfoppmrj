<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\DewanPengajars;
use App\Models\KalenderPpmTemplates;
use App\Models\KalenderPpms;
use App\Models\PresenceGroup;
use App\Models\HourKbms;
use App\Models\DayKbms;
use App\Models\JadwalHariJamKbms;
use Illuminate\Support\Facades\DB;

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
        if (auth()->user()->hasRole('superadmin')) {
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
        $pengajar = DewanPengajars::all();
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

    // public function jadwal()
    // {
    //     $pengajar = DewanPengajars::all();
    //     $template_kalender = KalenderPpmTemplates::all();
    //     $presence_group = PresenceGroup::whereIn('id', [1, 2])->get();

    //     return view('materi.list_jadwal', ['template_kalender' => $template_kalender, 'presence_group' => $presence_group, 'pengajar' => $pengajar]);
    // }

    // public function jadwal_store(Request $request)
    // {
    //     $pengajar = KalenderPpmTemplates::where('fkPresence_group_id', $request->input('presence'))->where('day', $request->input('day'))->where('ppm', $request->input('ppm'))->first();

    //     if ($pengajar) {
    //         $pengajar->fkDewan_pengajar_id = $request->input('pengajar');
    //         $updated = $pengajar->save();
    //     } else {
    //         $updated = KalenderPpmTemplates::create([
    //             'fkPresence_group_id' => $request->input('presence'),
    //             'fkDewan_pengajar_id' => $request->input('pengajar'),
    //             'day' => $request->input('day'),
    //             'ppm' => $request->input('ppm')
    //         ]);
    //     }

    //     if ($updated) {
    //         return json_encode(['status' => true, 'message' => 'Berhasil mengubah pengajar']);
    //     } else {
    //         return json_encode(['status' => false, 'message' => 'Gagal mengubah pengajar']);
    //     }
    // }

    public function template_kalender_ppm(){
        $today = date('Y-m-d', strtotime(today()));
        $pengajars = DewanPengajars::all();
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
        if($request->input('ak')==false){
            $get = KalenderPpms::where('id',$request->input('id'))->where('x',$request->input('x'))->first();

            if(!$get){
                $insert = KalenderPpms::create([
                            'x' => $request->input('x'),
                            'bulan' => $request->input('bulan'),
                            'start' => $request->input('start'),
                        ]);
            }else{
                $get->x = $request->input('x');
                $get->bulan = $request->input('bulan');
                $get->start = $request->input('start');
                $get->save();
            }
        }else{
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
        }
    }
}

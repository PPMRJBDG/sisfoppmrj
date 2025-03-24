<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\Present;
use App\Helpers\CountDashboard;
use App\Models\Periode;
use App\Models\TelatPulangMalams;
use App\Models\LaporanKeamanans;
use App\Models\Lorong;
use App\Helpers\PresenceGroupsChecker;

class HomeController extends Controller
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

    public function index()
    {

        return view('base.app', ['count_dashboard' => CountDashboard::index()]);
    }

    public function dashboard($tb = null, $select_angkatan = null, $select_periode = null, $json = false)
    {
        if ($tb == null && $select_angkatan == null && $select_periode == null) {
            $periode_tahun = Periode::latest('periode_tahun')->first();
            if(auth()->user()->hasRole('superadmin')){
                $tb = null;
            }else{
                $tb = date("Y-m");   
            }
            $select_angkatan = null;
            $select_periode = $periode_tahun->periode_tahun;
        }
        $tahun_bulan = [];
        $count_dashboard = '';
        $periode_tahun = Periode::get();
        $presence_group = PresenceGroup::get();
        $get_presence_today = Presence::where('event_date', date("Y-m-d"))->where('is_deleted', 0)->get();
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->orderBy('angkatan', 'ASC')
            ->groupBy('angkatan')
            ->get();

        if ($select_angkatan == '-') {
            $select_angkatan = null;
        }
        if ($select_periode == '-') {
            $select_periode = null;
        }
        if ($tb == '-') {
            $tb = null;
        }

        $bfjkah = false;
        if ($json) {
            $bfjkah = true;
        } else {
            if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong')) {
                $bfjkah = true;
            }
        }

        if ($bfjkah) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->groupBy('ym')
                ->orderBy('ym', 'DESC')
                ->get();
        } elseif (auth()->user()->hasRole('santri')) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->where('event_date', '>=', auth()->user()->santri->join_at)
                ->groupBy('ym')
                ->orderBy('ym', 'DESC')
                ->get();
            if ($tb == null) {
                $tb = date('Y-m');
            }
        }

        if (count($tahun_bulan) == 0) {
            $tb = null;
        }

        $get_lorong = null;
        if(isset(auth()->user()->id)){
            if(auth()->user()->hasRole('koor lorong')){
                $get_lorong = Lorong::where('fkSantri_leaderId',auth()->user()->santri->id)->first();
            }
        }
        $view_usantri = DB::table('v_user_santri')->orderBy('fullname','ASC')->get();
        
        $datapg = null;
        $all_presences = null;
        $presences = null;
        $permit = null;
        $all_permit = array();

        if ($bfjkah) {
            if ($tb != null) {
                if ($select_angkatan == null) {
                    $xtb = explode("-", $tb);
                    if (intval($xtb[1]) < 9) {
                        if($get_lorong==null){
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', '<', $xtb[0])
                                ->orderBy('fullname')->get();
                        }else{
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', '<', $xtb[0])
                                ->where('fkLorong_id', $get_lorong->id)
                                ->orderBy('fullname')->get();
                        }
                    } else {
                        if($get_lorong==null){
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', '<=', $xtb[0])
                                ->orderBy('fullname')->get();
                        }else{
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', '<=', $xtb[0])
                                ->where('fkLorong_id', $get_lorong->id)
                                ->orderBy('fullname')->get();
                        }
                    }
                } else {
                    if ($select_angkatan == intval(explode("-", $tb)[0]) && intval(explode("-", $tb)[1] < 9)) {
                        $view_usantri = null;
                    } else {
                        if($get_lorong==null){
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', $select_angkatan)
                                ->orderBy('fullname')->get();
                        }else{
                            $view_usantri = DB::table('v_user_santri')
                                ->where('angkatan', $select_angkatan)
                                ->where('fkLorong_id', $get_lorong->id)
                                ->orderBy('fullname')->get();
                        }
                    }
                }
            } elseif ($select_periode != null) {
                $split_periode = explode("-", $select_periode);
                if ($select_angkatan == null) {
                    if($get_lorong==null){
                        $view_usantri = DB::table('v_user_santri')
                            ->where('angkatan', '<=', $split_periode[0])
                            ->orderBy('fullname')->get();
                    }else{
                        $view_usantri = DB::table('v_user_santri')
                            ->where('angkatan', '<=', $split_periode[0])
                            ->where('fkLorong_id', $get_lorong->id)
                            ->orderBy('fullname')->get();
                    }
                } else {
                    if($get_lorong==null){
                        $view_usantri = DB::table('v_user_santri')
                            ->where('angkatan', '<=', $split_periode[0])
                            ->where('angkatan', $select_angkatan)
                            ->orderBy('fullname')->get();
                    }else{
                        $view_usantri = DB::table('v_user_santri')
                            ->where('angkatan', '<=', $split_periode[0])
                            ->where('angkatan', $select_angkatan)
                            ->where('fkLorong_id', $get_lorong->id)
                            ->orderBy('fullname')->get();
                    }
                }
            }

            if ($view_usantri != null) {
                foreach ($view_usantri as $vu) {
                    foreach ($presence_group as $pg) {
                        $like_tb_a = " AND event_date LIKE '%$tb%'";
                        $like_tb_b = " AND b.event_date LIKE '%$tb%'";
                        $like_tb_c = " AND b.event_date LIKE '%$tb%'";
                        $q_angkatan = $select_angkatan;
                        if ($select_angkatan == null) {
                            $q_angkatan = $vu->angkatan;
                        }
                        if ($tb == null) {
                            if ($select_periode != null) {
                                $split_periode = explode("-", $select_periode);
                                $like_tb_a = " AND event_date >= '$split_periode[0]-09-01' AND event_date <= '$split_periode[1]-08-31'";
                                $like_tb_b = " AND b.event_date >= '$split_periode[0]-09-01' AND b.event_date <= '$split_periode[1]-08-31'";
                                $like_tb_c = $like_tb_b;
                            } else {
                                if ($select_angkatan == null) {
                                    $like_tb_a = " AND event_date >= '$vu->angkatan-09-01'";
                                    $like_tb_b = " AND b.event_date >= '$vu->angkatan-09-01'";
                                    $like_tb_c = $like_tb_b;
                                } else {
                                    $like_tb_a = " AND event_date >= '$select_angkatan-09-01'";
                                    $like_tb_b = " AND b.event_date >= '$select_angkatan-09-01'";
                                    $like_tb_c = $like_tb_b;
                                }
                            }
                        }

                        $all_presences[$vu->santri_id][$pg->id] = DB::select(
                            "SELECT COUNT(*) as c_all 
                            FROM presences
                            WHERE is_deleted = 0 AND fkPresence_group_id=" . $pg->id . $like_tb_a
                        );

                        $presences[$vu->santri_id][$pg->id] = DB::select(
                            "SELECT a.santri_id, a.fullname, COUNT(b.fkSantri_id) as cp 
                            FROM v_user_santri a 
                            LEFT JOIN v_presensi b ON a.santri_id=b.fkSantri_id " . $like_tb_b . " 
                            AND b.fkPresence_group_id=" . $pg->id . " 
                            WHERE a.angkatan = " . $q_angkatan . "
                            AND a.santri_id = " . $vu->santri_id . " 
                            GROUP BY a.santri_id 
                            ORDER BY a.fullname"
                        );

                        $permit = DB::select(
                            "SELECT a.fkSantri_id, count(a.fkSantri_id) as approved 
                            FROM `permits` a 
                            JOIN `presences` b ON a.fkPresence_id=b.id AND b.is_deleted = 0
                            WHERE a.status='approved' " . $like_tb_c . " 
                            AND b.fkPresence_group_id = " . $pg->id . " 
                            AND a.fkSantri_id = " . $vu->santri_id . " 
                            GROUP BY a.fkSantri_id"
                        );

                        if ($permit != null) {
                            foreach ($permit as $p) {
                                $all_permit[$pg->id][$p->fkSantri_id] = $p->approved;
                            }
                        }
                    }
                }
            }
        }

        if (auth()->user()->hasRole('santri')) {
            $presences_santri = DB::table('presences as a')
                ->leftJoin('presents as b', function ($join) {
                    $join->on('a.id', '=', 'b.fkPresence_id');
                    $join->where('b.fkSantri_id', auth()->user()->santri->id);
                })
                ->where('is_deleted', 0)
                ->where('a.event_date', 'like', '%' . $tb . '%')
                ->orderBy('a.event_date', 'ASC')
                ->get();
            if ($presences_santri != null) {
                foreach ($presence_group as $pg) {
                    $datapg[$pg->id]['loopr'] = 0;
                    $datapg[$pg->id]['kehadiran'] = 0;
                    $loopr = 0;
                    $kehadiran = 0;
                    foreach ($presences_santri as $ps) {
                        if ($pg->id == $ps->fkPresence_group_id) {
                            $loopr++;
                            if ($ps->fkSantri_id != "") {
                                $kehadiran++;
                            }
                            $datapg[$pg->id]['loopr'] = $loopr;
                        }
                        $datapg[$pg->id]['kehadiran'] = $kehadiran;
                    }
                }
            }
        } else {
            $presences_santri = null;
        }

        $data_presensi = array();
        if (!$json) {
            $total_presensi = array();
            $tanggal_presensi = array();
            $detil_presensi = array();
            $q_angkatan = $select_angkatan;
            if ($select_angkatan == null) {
                $q_angkatan = 'all';
            }
            $data_presensi = ['total_presensi' => $total_presensi, 'tanggal_presensi' => $tanggal_presensi, 'detil_presensi' => $detil_presensi];
        }

        $datetime = date("Y-m-d H:i:s");
        $sign_in_out = Presence::where('is_deleted', 0)->where('start_date_time', '<=', $datetime)
            ->where('end_date_time', '>=', $datetime)->first();
        $my_sign = null;
        if ($sign_in_out != null) {
            $santriIdToInsert = auth()->user()->santri;
            $my_sign = Present::where('fkPresence_id', $sign_in_out->id)
                ->where('fkSantri_id', $santriIdToInsert)->first();
        }

        $yesterday = strtotime('-1 day', strtotime(date("Y-m-d")));
        $yesterday = date('Y-m-d', $yesterday);
        $data_telatpulang = TelatPulangMalams::where('jam_pulang','like',date('Y-m-d').'%')->orWhere('jam_pulang','like',$yesterday.'%')->orderBy('id','DESC')->get();
        $data_jobdesk_jaga = null;
        if(isset(auth()->user()->id)){
            if(auth()->user()->santri){
                $data_jobdesk_jaga = LaporanKeamanans::where('id',auth()->user()->santri->fkLaporan_keamanan_id)->first();
            }
        }

        if ($json) {
            return [
                'presences' => $presences,
                'presences_santri' => $presences_santri,
                'presence_group' => $presence_group,
                'datapg' => $datapg,
                'tahun_bulan' => $tahun_bulan,
                'tb' => $tb,
                'view_usantri' => $view_usantri,
                'all_presences' => $all_presences,
                'list_angkatan' => $list_angkatan,
                'select_angkatan' => $select_angkatan,
                'data_telatpulang' => $data_telatpulang,
                'all_permit' => $all_permit,
                'data_jobdesk_jaga' => $data_jobdesk_jaga,
            ];
        } else {
            return view('dashboard', [
                'sign_in_out' => $sign_in_out,
                'my_sign' => $my_sign,
                'periode_tahun' => $periode_tahun,
                'presences_santri' => $presences_santri,
                'presences' => $presences,
                'presence_group' => $presence_group,
                'datapg' => $datapg,
                'tahun_bulan' => $tahun_bulan,
                'tb' => $tb,
                'view_usantri' => $view_usantri,
                'all_presences' => $all_presences,
                'list_angkatan' => $list_angkatan,
                'select_angkatan' => $select_angkatan,
                'select_periode' => $select_periode,
                'all_permit' => $all_permit,
                'data_presensi' => $data_presensi,
                'data_presensi' => $data_presensi,
                'data_telatpulang' => $data_telatpulang,
                'get_presence_today' => $get_presence_today,
                'data_jobdesk_jaga' => $data_jobdesk_jaga,
            ]);
        }
    }

    public function tabgraf($tb = null, $select_angkatan = null, $select_periode = null, $json = false)
    {
        if ($tb == null && $select_angkatan == null && $select_periode == null) {
            $periode_tahun = Periode::latest('periode_tahun')->first();
            $tb = null;
            $select_angkatan = null;
            $select_periode = $periode_tahun->periode_tahun;
        }
        if ($select_angkatan == '-') {
            $select_angkatan = null;
        }
        if ($tb == '-') {
            $tb = null;
        }
        $presence_group = PresenceGroup::get();
        $tahun_bulan = DB::table('presences')
            ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
            ->groupBy('ym')
            ->orderBy('ym', 'DESC')
            ->get();
        $data_presensi = array();
        $total_presensi = array();
        $tanggal_presensi = array();
        $detil_presensi = array();
        $q_angkatan = $select_angkatan;
        if ($select_angkatan == null) {
            $q_angkatan = 'all';
        }
        foreach ($presence_group as $pg) {
            if ($select_periode != null && $select_periode != '-') {
                $split_periode = explode("-", $select_periode);
                $get_presence = Presence::where('event_date', '>=', $split_periode[0] . '-09-01')
                    ->where('event_date', '<=', $split_periode[1] . '-08-31')
                    ->where('fkPresence_group_id', $pg->id)
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'DESC')
                    ->get();
            } elseif ($tb == null) {
                $get_presence = Presence::where('event_date', '>=', $tahun_bulan[count($tahun_bulan) - 1]->ym . '-01')
                    ->where('fkPresence_group_id', $pg->id)
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                $get_presence = Presence::where('event_date', '>=', $tb . '-01')->where('event_date', '<=', $tb . '-31')
                    ->where('fkPresence_group_id', $pg->id)
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'DESC')
                    ->get();
            }
            if (count($get_presence) > 0) {
                foreach ($get_presence as $presence) {
                    $tanggal_presensi[$pg->id][] = $presence->event_date;
                    $hadir = CountDashboard::mhs_hadir($presence->id, $q_angkatan);
                    $ijin = CountDashboard::mhs_ijin($presence->id, $q_angkatan);
                    $alpha = CountDashboard::mhs_alpha($presence->id, $q_angkatan, $presence->event_date);

                    $total_presensi[$pg->id]['hadir'][] = count($hadir);
                    $total_presensi[$pg->id]['ijin'][] = count($ijin);
                    $total_presensi[$pg->id]['alpha'][] = count($alpha);

                    $detil_presensi[$presence->event_date][$pg->id]['id'] = $presence->id;
                    $detil_presensi[$presence->event_date][$pg->id]['hadir'] = count($hadir);
                    $detil_presensi[$presence->event_date][$pg->id]['ijin'] = count($ijin);
                    $detil_presensi[$presence->event_date][$pg->id]['alpha'] = count($alpha);
                }
            }
        }
        $data_presensi = ['total_presensi' => $total_presensi, 'tanggal_presensi' => $tanggal_presensi, 'detil_presensi' => $detil_presensi];

        return [
            'data_presensi' => $data_presensi
        ];
    }
}

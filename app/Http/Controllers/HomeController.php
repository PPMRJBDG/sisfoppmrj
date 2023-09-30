<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Helpers\CountDashboard;
use App\Models\Periode;

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

    /**
     * Show the list table of latest presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard($tb = null, $select_angkatan = null, $select_periode = null, $json = false)
    {
        if ($tb == null && $select_angkatan == null && $select_periode == null) {
            $periode_tahun = Periode::latest('periode_tahun')->first();
            $tb = null;
            $select_angkatan = null;
            $select_periode = $periode_tahun->periode_tahun;
        }
        $tahun_bulan = [];
        $count_dashboard = '';
        $periode_tahun = Periode::get();
        $presence_group = PresenceGroup::get();
        $get_presence_today = Presence::where('event_date', date("Y-m-d"))->get();
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->orderBy('angkatan', 'ASC')
            ->groupBy('angkatan')
            ->get();

        if ($select_angkatan == '-') {
            $select_angkatan = null;
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
            $count_dashboard = CountDashboard::index();
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->where('event_date', '>=', $select_angkatan . '-09-01')
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

        $view_usantri = null;
        $datapg = null;
        $all_presences = null;
        $presences = null;
        $permit = null;
        $all_permit = array();

        if ($bfjkah) {
            if ($select_angkatan == null) {
                $view_usantri = DB::table('v_user_santri')->orderBy('fullname')->get();
            } else {
                $view_usantri = DB::table('v_user_santri')->where('angkatan', $select_angkatan)->orderBy('fullname')->get();
            }
            foreach ($view_usantri as $vu) {
                foreach ($presence_group as $pg) {
                    $like_tb_a = " AND event_date LIKE '%$tb%'";
                    $like_tb_b = " AND b.event_date LIKE '%$tb%'";
                    $like_tb_c = " AND b.event_date LIKE '%$tb%'";
                    $q_angkatan = $select_angkatan;
                    if ($select_angkatan == null) {
                        $q_angkatan = $vu->angkatan;
                    }
                    if ($tb == null || $tb == '-') {
                        if ($select_periode != null || $select_periode != '-') {
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
                            WHERE fkPresence_group_id=" . $pg->id . $like_tb_a
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
                            JOIN `presences` b ON a.fkPresence_id=b.id 
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
        } elseif (auth()->user()->hasRole('santri')) {
            $presences = DB::table('presences as a')
                ->leftJoin('presents as b', function ($join) {
                    $join->on('a.id', '=', 'b.fkPresence_id');
                    $join->where('b.fkSantri_id', auth()->user()->santri->id);
                })
                ->where('a.event_date', 'like', '%' . $tb . '%')
                ->orderBy('a.event_date', 'ASC')
                ->get();
            if ($presences != null) {
                foreach ($presence_group as $pg) {
                    $datapg[$pg->id]['loopr'] = 0;
                    $datapg[$pg->id]['kehadiran'] = 0;
                    $loopr = 0;
                    $kehadiran = 0;
                    foreach ($presences as $ps) {
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
            $presences = null;
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
            foreach ($presence_group as $pg) {
                if ($select_periode != null && $select_periode != '-') {
                    $split_periode = explode("-", $select_periode);
                    $get_presence = Presence::where('event_date', '>=', $split_periode[0] . '-09-01')
                        ->where('event_date', '<=', $split_periode[1] . '-08-31')
                        ->where('fkPresence_group_id', $pg->id)
                        ->orderBy('event_date', 'ASC')
                        ->get();
                } elseif ($tb == null || $tb == '-') {
                    $get_presence = Presence::where('event_date', '>=', $tahun_bulan[count($tahun_bulan) - 1]->ym . '-01')
                        ->where('fkPresence_group_id', $pg->id)
                        ->orderBy('event_date', 'ASC')
                        ->get();
                } else {
                    $get_presence = Presence::where('event_date', '>=', $tb . '-01')
                        ->where('fkPresence_group_id', $pg->id)
                        ->orderBy('event_date', 'ASC')
                        ->get();
                }
                if (count($get_presence) > 0) {
                    foreach ($get_presence as $presence) {
                        $tanggal_presensi[$pg->id][] = $presence->event_date;
                        $hadir = CountDashboard::mhs_hadir($presence->id, $q_angkatan);
                        $ijin = CountDashboard::mhs_ijin($presence->id, $q_angkatan);
                        $alpha = CountDashboard::mhs_alpha($presence->id, $q_angkatan);

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
        }
        // echo var_export($data_presensi, true);
        // exit;
        if ($json) {
            return [
                'presences' => $presences,
                'presence_group' => $presence_group,
                'datapg' => $datapg,
                'tahun_bulan' => $tahun_bulan,
                'tb' => $tb,
                'view_usantri' => $view_usantri,
                'all_presences' => $all_presences,
                'list_angkatan' => $list_angkatan,
                'select_angkatan' => $select_angkatan,
                'all_permit' => $all_permit
            ];
        } else {
            return view('dashboard', [
                'periode_tahun' => $periode_tahun,
                'count_dashboard' => $count_dashboard,
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
                'get_presence_today' => $get_presence_today
            ]);
        }
    }
}

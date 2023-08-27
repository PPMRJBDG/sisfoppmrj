<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PresenceGroup;
use App\Helpers\CountDashboard;

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
    public function dashboard($tb = null, $select_angkatan = null, $json = false)
    {
        $tahun_bulan = [];
        $count_dashboard = CountDashboard::index();
        $presence_group = PresenceGroup::get();
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->orderBy('angkatan', 'ASC')
            ->groupBy('angkatan')
            ->get();
        if ($select_angkatan == null) {
            $select_angkatan = $list_angkatan[0]->angkatan;
        }
        $bfjkah = false;
        if ($json) {
            $bfjkah = true;
        } else {
            if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk')) {
                $bfjkah = true;
            }
        }

        if ($bfjkah) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->where('event_date', '>=', $select_angkatan . '-09-01')
                ->groupBy('ym')
                ->get();
        } elseif (auth()->user()->hasRole('santri')) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->where('event_date', '>=', auth()->user()->santri->join_at)
                ->groupBy('ym')
                ->get();
            if ($tb == null) {
                $tb = date('Y-m');
            }
        }

        if (count($tahun_bulan) == 0) {
            $tb = null;
        }

        $like_tb_a = " AND event_date LIKE '%$tb%'";
        $like_tb_b = " AND b.event_date LIKE '%$tb%'";
        $like_tb_c = " AND b.event_date LIKE '%$tb%'";
        if ($tb == null || $tb == '-') {
            $like_tb_a = " AND event_date >= '$select_angkatan-09-01'";
            $like_tb_b = " AND b.event_date >= '$select_angkatan-09-01'";
            $like_tb_c = " AND b.event_date >= '$select_angkatan-09-01'";
        }

        $view_usantri = null;
        $datapg = null;
        $all_presences = null;
        $permit = null;
        $all_permit = array();

        if ($bfjkah) {
            $view_usantri = DB::table('v_user_santri')->where('angkatan', $select_angkatan)->orderBy('fullname')->get();
            foreach ($presence_group as $pg) {
                $all_presences[$pg->id] = DB::select("SELECT COUNT(*) as c_all FROM presences WHERE fkPresence_group_id=" . $pg->id . $like_tb_a);
                $presences[$pg->id] = DB::select("SELECT a.santri_id, a.fullname, COUNT(b.fkSantri_id) as cp FROM v_user_santri a LEFT JOIN v_presensi b ON a.santri_id=b.fkSantri_id " . $like_tb_b . " AND b.fkPresence_group_id=" . $pg->id . " WHERE a.angkatan = " . $select_angkatan . " GROUP BY a.santri_id ORDER BY a.fullname");

                $permit = DB::select("SELECT a.fkSantri_id, count(a.fkSantri_id) as approved FROM `permits` a JOIN `presences` b ON a.fkPresence_id=b.id WHERE a.status='approved' " . $like_tb_c . " AND b.fkPresence_group_id = " . $pg->id . " GROUP BY a.fkSantri_id");
                if ($permit != null) {
                    foreach ($permit as $p) {
                        $all_permit[$pg->id][$p->fkSantri_id] = $p->approved;
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
                'all_permit' => $all_permit
            ]);
        }
    }
}

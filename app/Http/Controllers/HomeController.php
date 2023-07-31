<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\PresenceGroupsChecker;
use Illuminate\Support\Facades\DB;
use App\Models\Presence;
use App\Models\SystemMetaData;
use App\Models\Lorong;
use App\Models\Santri;
use App\Models\PresenceGroup;

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
    public function dashboard($tb = null)
    {
        // $lastScheduleCheck = SystemMetaData::where('key', 'lastScheduleCheck')->first();
        // if (!$lastScheduleCheck || $lastScheduleCheck->value != date('Y-m-d')) {
        //     PresenceGroupsChecker::checkPresenceGroups();
        //     PresenceGroupsChecker::checkPermitGenerators();
        //     SystemMetaData::updateOrCreate(['key' => 'lastScheduleCheck'], ['value' => date('Y-m-d')]);
        // }
        // $today = date('Y-m-d', strtotime(today()));
        // $presences = Presence::orderBy('event_date', 'DESC')->whereMonth('event_date', '=', Carbon::now()->month)->whereYear('event_date', '=', Carbon::now()->year)->get();

        // SELECT DATE_FORMAT(event_date,"%Y-%m") as ym FROM presences WHERE event_date > '2022-05-05' GROUP BY ym;

        $presence_group = PresenceGroup::get();
        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru')) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->groupBy('ym')
                ->get();
        } elseif (auth()->user()->hasRole('santri')) {
            $tahun_bulan = DB::table('presences')
                ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
                ->where('event_date', '>=', auth()->user()->santri->join_at)
                ->groupBy('ym')
                ->get();
        } else {
            $tahun_bulan = null;
        }

        if ($tb == null) {
            $tb = date("Y-m");
        }
        $view_usantri = null;
        $datapg = null;
        $all_presences = null;
        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru')) {
            $view_usantri = DB::table('v_user_santri')->orderBy('fullname')->get();
            foreach ($presence_group as $pg) {
                $all_presences[$pg->id] = DB::select("SELECT COUNT(*) as c_all FROM presences WHERE fkPresence_group_id=" . $pg->id . " AND event_date LIKE '%" . $tb . "%'");
                $presences[$pg->id] = DB::select("SELECT a.santri_id, a.fullname, COUNT(b.fkSantri_id) as cp FROM v_user_santri a LEFT JOIN v_presensi b ON a.santri_id=b.fkSantri_id AND b.event_date LIKE '%" . $tb . "%' AND b.fkPresence_group_id=" . $pg->id . " GROUP BY a.santri_id ORDER BY a.fullname");
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
        return view('dashboard', [
            'presences' => $presences,
            'presence_group' => $presence_group,
            'datapg' => $datapg,
            'tahun_bulan' => $tahun_bulan,
            'tb' => $tb,
            'view_usantri' => $view_usantri,
            'all_presences' => $all_presences
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\SystemMetaData;
use App\Helpers\PresenceGroupsChecker;
use Illuminate\Support\Carbon;
use App\Models\Lorong;

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
    public function dashboard(Request $request)
    {
        $lastScheduleCheck = SystemMetaData::where('key', 'lastScheduleCheck')->first();        

        if(!$lastScheduleCheck || $lastScheduleCheck->value != date('Y-m-d'))
        {
            PresenceGroupsChecker::checkPresenceGroups();
            PresenceGroupsChecker::checkPermitGenerators();

            SystemMetaData::updateOrCreate(['key' => 'lastScheduleCheck'], ['value' => date('Y-m-d')]);
        }

        $presences = Presence::orderBy('event_date', 'DESC')->whereMonth('event_date', '=', Carbon::now()->month)->whereYear('event_date', '=', Carbon::now()->year)->get();

        return view('dashboard', ['presences' => $presences]);
    }
}

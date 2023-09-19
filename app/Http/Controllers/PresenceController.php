<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\Santri;
use App\Models\User;
use App\Models\Presence;
use App\Models\PresenceGroup;
use App\Models\Present;
use App\Models\Permit;
use App\Models\RangedPermitGenerator;
use App\Models\Lorong;
use App\Helpers\PresenceGroupsChecker;
use App\Helpers\WaSchedules;
use App\Models\SystemMetaData;
use App\Helpers\CommonHelpers;

use Carbon\Carbon;

class PresenceController extends Controller
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


    // ============ PRESENCE REPORT ============

    /**
     * Show the list and manage table of presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function report()
    {
        // return 'Halaman ini ditutup sementara karena sepertinya menggunakan resource yang besar.';

        $listedPresenceGroups = PresenceGroup::where('show_summary_at_home', 1)->get();

        return view('presence.report', ['listedPresenceGroups' => $listedPresenceGroups]);
    }


    // ============ PRESENCE ============

    /**
     * Show the list and manage table of presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage()
    {
        PresenceGroupsChecker::checkPresenceGroups();

        $presences = Presence::where('fkPresence_group_id', null)->get();
        $presenceGroups = PresenceGroup::all();

        return view('presence.list_and_manage', ['presences' => $presences, 'presenceGroups' => $presenceGroups]);
    }

    /**
     * Show the list table of latest presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function latest_list(Request $request)
    {
        $page = $request->get('page') ? $request->get('page') : 1;
        $monthDecrement = $page - 1;
        $decreasedDate = Carbon::now()->startOfMonth()->subMonths($monthDecrement);
        $presences = Presence::orderBy('event_date', 'DESC')->whereMonth('event_date', '=', $decreasedDate->month)->whereYear('event_date', '=', $decreasedDate->year)->get();
        $today = date('Y-m-d', strtotime(today()));
        // exit;
        return view('presence.latest_list', ['presences' => $presences, 'page' => $page, 'date' => $decreasedDate, 'today' => $today]);
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $presenceGroups = PresenceGroup::all();

        return view('presence.create', ['presenceGroups' => $presenceGroups]);
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'event_date' => 'required|date'
        ]);

        if ($request->input('is_date_time_limited'))
            $request->validate([
                'start_date_time' => 'required|date',
                'end_date_time' => 'required|date',
            ]);

        if ($request->input('fkPresence_group_id'))
            $request->validate([
                'fkPresence_group_id' => 'integer|exists:presence_groups,id',
            ]);

        $inserted = Presence::create([
            'name' => $request->input('name'),
            'start_date_time' => $request->input('start_date_time') ? $request->input('start_date_time') : null,
            'end_date_time' => $request->input('end_date_time') ? $request->input('end_date_time') : null,
            'fkPresence_group_id' => $request->input('fkPresence_group_id') ? $request->input('fkPresence_group_id') : null,
            'event_date' => $request->input('event_date')
        ]);

        if (!$inserted)
            return redirect()->route('create presence')->withErrors(['failed_creating_presence']);

        return redirect()->route('presence tm')->with('success', 'Berhasil membuat presensi');
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'event_date' => 'date',
        ]);

        if ($request->input('is_date_time_limited'))
            $request->validate([
                'start_date_time' => 'required|date',
                'end_date_time' => 'required|date',
            ]);

        if ($request->input('fkPresence_group_id'))
            $request->validate([
                'fkPresence_group_id' => 'integer',
            ]);

        $presence = Presence::find($request->route('id'));

        $presence->name = $request->input('name');
        $presence->event_date = $request->input('event_date');
        $presence->fkPresence_group_id = $request->input('fkPresence_group_id') ? $request->input('fkPresence_group_id') : null;

        if ($request->input('is_date_time_limited')) {
            $presence->start_date_time = $request->input('start_date_time');
            $presence->end_date_time = $request->input('end_date_time');
        } else {
            $presence->start_date_time = null;
            $presence->end_date_time = null;
        }

        $updated = $presence->save();

        if (!$updated)
            return redirect()->back()->withErrors(['failed_updating_presence']);

        return redirect()->back()->with('success', 'Berhasil mengubah presensi');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete($id)
    {
        $presence = Presence::find($id);

        if ($presence) {
            $deleted = $presence->delete();

            if (!$deleted)
                return redirect()->route('presence tm')->withErrors(['failed_deleting_presence', 'Gagal menghapus presensi.']);
        }

        return redirect()->back()->with('success', 'Berhasil menghapus presensi');
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $presence = Presence::find($id);
        $presenceGroups = PresenceGroup::all();

        return view('presence.edit', ['presence' => $presence, 'presenceGroups' => $presenceGroups]);
    }

    /**
     * Show the presence info and its presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function view($id)
    {
        $presence = Presence::find($id);

        $presents = $presence->presents()
            ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
            ->join('users', 'users.id', '=', 'santris.fkUser_id')
            ->orderBy('users.fullname')
            ->get();

        $permits = Permit::where('fkPresence_id', $id)->where('status', 'approved')->get();

        return view('presence.view', ['presence' => $presence, 'permits' => $permits, 'presents' => $presents]);
    }



    // ============ PRESENCE GROUP ============

    /**
     * Show the create form of presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_group()
    {
        return view('presence.create_group');
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_group(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($request->input('is_date_time_limited'))
            $request->validate([
                'start_hour' => 'required|date_format:H:i',
                'end_hour' => 'required|date_format:H:i',
            ]);

        $inserted = PresenceGroup::create([
            'name' => $request->input('name'),
            'days' => implode(', ', $request->input('days')),
            'start_hour' => $request->input('start_hour') ? $request->input('start_hour') : null,
            'end_hour' => $request->input('end_hour') ? $request->input('end_hour') : null,
            'show_summary_at_home' => $request->input('show_summary_at_home') ? 1 : 0,
            'status' => $request->input('status') ? 'active' : 'inactive'
        ]);

        if (!$inserted)
            return redirect()->route('create presence group')->withErrors(['failed_creating_presence_group']);

        return redirect()->route('presence tm')->with('success', 'Berhasil membuat grup presensi');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_group($id)
    {
        $presenceGroup = PresenceGroup::find($id);

        if ($presenceGroup) {
            $deleted = $presenceGroup->delete();

            if (!$deleted)
                return redirect()->route('presence tm')->withErrors(['failed_deleting_presence_group', 'Gagal menghapus grup presensi.']);
        }

        return redirect()->route('presence tm')->with('success', 'Berhasil menghapus grup presensi.');
    }

    /**
     * Show the edit form of a presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit_group($id)
    {
        $presenceGroup = PresenceGroup::find($id);

        return view('presence.edit_group', ['presenceGroup' => $presenceGroup]);
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update_group(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($request->input('is_date_time_limited'))
            $request->validate([
                'start_hour' => 'required|date_format:H:i',
                'end_hour' => 'required|date_format:H:i',
            ]);

        $presenceGroup = PresenceGroup::find($request->route('id'));

        $presenceGroup->name = $request->input('name');
        $presenceGroup->days = implode(', ', $request->input('days'));
        $presenceGroup->show_summary_at_home = $request->input('show_summary_at_home') ? 1 : 0;
        $presenceGroup->status = $request->input('status');

        if ($request->input('is_date_time_limited')) {
            $presenceGroup->start_hour = $request->input('start_hour');
            $presenceGroup->end_hour = $request->input('end_hour');
        } else {
            $presenceGroup->start_hour = null;
            $presenceGroup->end_hour = null;
        }

        $updated = $presenceGroup->save();

        if (!$updated)
            return redirect()->route('edit presence group', $request->route('id'))->withErrors(['failed_updating_presence_group']);

        return redirect()->route('edit presence group', $request->route('id'))->with('success', 'Berhasil mengubah grup presensi.');
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function view_group($id, Request $request)
    {
        $page = $request->get('page') ? $request->get('page') : 1;

        $presenceGroup = PresenceGroup::find($id);

        return view('presence.view_group', ['presenceGroup' => $presenceGroup, 'page' => $page]);
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_in_group($id)
    {
        $presenceGroup = PresenceGroup::find($id);

        return view('presence.create_in_group', ['presenceGroup' => $presenceGroup]);
    }

    /**
     * Show the create form of presence.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_in_group(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'event_date' => 'required|date'
        ]);

        if ($request->input('is_date_time_limited'))
            $request->validate([
                'start_date_time' => 'required|date',
                'end_date_time' => 'required|date',
            ]);

        $inserted = Presence::create([
            'name' => $request->input('name'),
            'start_date_time' => $request->input('start_date_time') ? $request->input('start_date_time') : null,
            'end_date_time' => $request->input('end_date_time') ? $request->input('end_date_time') : null,
            'fkPresence_group_id' => $request->route('id'),
            'event_date' => $request->input('event_date')
        ]);

        if (!$inserted)
            return redirect()->route('create presence in group', $request->route('id'))->withErrors(['failed_creating_presence_in_group', 'Gagal menambah presensi pada grup.']);

        return redirect()->route('view presence group', $request->route('id'))->with('success', 'Berhasil membuat presensi pada grup.');
    }

    /**
     * Show the specified presence group's recaps list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function select_presence_group_recap($id)
    {
        $presenceGroup = PresenceGroup::find($id);
        $lorongs = Lorong::all();

        return view('presence.view_presence_group_recap', ['lorongs' => $lorongs, 'presenceGroup' => $presenceGroup]);
    }

    /**
     * Show the specified presence group's recaps list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function view_presence_group_recap($id, $fromDate, $toDate, $lorongId)
    {
        $presenceGroup = PresenceGroup::find($id);
        $lorongs = Lorong::all();

        $santris = [];

        if (isset($fromDate, $toDate)) {
            if ($lorongId != 'all') {
                // get leader first
                $santriLeaderId = Lorong::find($lorongId)->fkSantri_leaderId;

                $santris = Santri::where(function ($query) use ($toDate) {
                    $query->where('exit_at', '>=', $toDate);
                    $query->orWhere('exit_at', null);
                })
                    ->where('fkLorong_id', $lorongId)->orWhere('id', $santriLeaderId)->get();
            } else
                $santris = Santri::where(function ($query) use ($toDate) {
                    $query->where('exit_at', '>=', $toDate);
                    $query->orWhere('exit_at', null);
                })->get();
        }

        return view('presence.view_presence_group_recap', ['lorongs' => $lorongs, 'presenceGroup' => $presenceGroup, 'fromDate' => $fromDate, 'toDate' => $toDate, 'lorongId' => $lorongId, 'santris' => $santris]);
    }

    public function view_presence_group_daily_public_recap($year, $month, $date, $id)
    {
        $presence = Presence::find($id);

        $presents = $presence->presents()
            ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
            ->join('users', 'users.id', '=', 'santris.fkUser_id')
            ->orderBy('users.fullname')
            ->get();

        $permits = Permit::where('fkPresence_id', $id)->where('status', 'approved')->get();

        $presencesInDate = Presence::whereDate('event_date', '=', "$year-$month-$date")->get();

        return view('presence.view', ['presence' => $presence, 'permits' => $permits, 'presents' => $presents, 'presencesInDate' => $presencesInDate]);
    }

    /**
     * Check and execute presence group schedules.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function check_schedules()
    {
        PresenceGroupsChecker::checkPresenceGroups();
        PresenceGroupsChecker::checkPermitGenerators();

        return redirect()->back();
    }


    // ============ PRESENT ============

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_my_present($id)
    {
        $santri = auth()->user()->santri;
        $existingPresent = null;

        // manually validate composite keys
        if ($santri)
            $existingPresent = Present::where('fkPresence_id', '=', $id, 'and')->where('fkSantri_id', '=', $santri->id)->first();

        if ($existingPresent)
            $status = 'sudah presensi';

        $presence = Presence::find($id);

        if ($presence) {
            if (!$existingPresent)
                if ($presence->start_date_time && $presence->end_date_time)
                    $status = date('Y-m-d H:i') > $presence->start_date_time && date('Y-m-d H:i') < $presence->end_date_time ? 'terbuka' : 'tutup';
                else
                    $status = 'terbuka';
        } else
            $status = 'tidak ditemukan';

        return view('presence.create_my_present', ['santri' => $santri, 'presence' => $presence, 'status' => $status]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_present(Request $request)
    {
        $usersWithSantri = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->orderBy('fullname')->get();

        $presence = Presence::find($request->route('id'));
        $presents = Present::where('fkPresence_id', $request->route('id'))->get();

        return view('presence.create_present', ['usersWithSantri' => $usersWithSantri, 'presence' => $presence, 'presents' => $presents, 'errors' => $request->session()->get('errors')]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_present(Request $request)
    {
        $presenceIdToInsert = $request->route('id');

        $presence = Presence::find($presenceIdToInsert);

        $errors = [];
        $successes = [];

        if (!$presence)
            return redirect()->route('create present', $presenceIdToInsert)->withErrors(['Presensi tidak ditemukan.']);

        if ($request->input('santri-ids')) {
            $santriIdsToInsert = explode(',', $request->input('santri-ids'));

            foreach ($santriIdsToInsert as $santriIdToInsert) {
                // manually validate composite keys
                $existingPresent = Present::where('fkPresence_id', '=', $presenceIdToInsert, 'and')->where('fkSantri_id', '=', $santriIdToInsert)->first();

                if ($existingPresent) {
                    array_push($successes, $existingPresent->santri->user->fullname . ' sudah presensi.');
                    continue;
                }

                $santri = Santri::find($santriIdToInsert);

                if (!$santri) {
                    array_push($errors, "Santri dengan id $santriIdToInsert tidak ditemukan.");
                    continue;
                }

                // check whether the santri has permit at that presence
                $permit = Permit::where('fkPresence_id', $presenceIdToInsert)->where('fkSantri_id', $santriIdToInsert)->where('status', 'approved')->first();

                if ($permit) {
                    array_push($errors, 'Santri dengan nama ' . $santri->user->fullname . ' memiliki izin yang disetujui di presensi ini.');
                    continue;
                }

                $inserted = Present::create([
                    'fkSantri_id' => $santri->id,
                    'fkPresence_id' => $presenceIdToInsert,
                    'is_late' => 0
                ]);

                if (!$inserted) {
                    array_push($errors, 'Santri dengan nama ' . $santri->user->fullname . ' memiliki gagal dipresensikan.');
                    continue;
                } else
                    array_push($successes, 'Berhasil presensikan ' . $santri->user->fullname);
            }
        }

        if ($request->input('late-santri-ids')) {
            $lateSantriIdsToInsert = explode(',', $request->input('late-santri-ids'));;

            foreach ($lateSantriIdsToInsert as $santriIdToInsert) {
                // manually validate composite keys
                $existingPresent = Present::where('fkPresence_id', '=', $presenceIdToInsert, 'and')->where('fkSantri_id', '=', $santriIdToInsert)->first();

                if ($existingPresent) {
                    array_push($successes, $existingPresent->santri->user->fullname . ' sudah presensi.');
                    continue;
                }

                $santri = Santri::find($santriIdToInsert);

                if (!$santri) {
                    array_push($errors, "Santri dengan id $santriIdToInsert tidak ditemukan.");
                    continue;
                }

                // check whether the santri has permit at that presence
                $permit = Permit::where('fkPresence_id', $presenceIdToInsert)->where('fkSantri_id', $santriIdToInsert)->where('status', 'approved')->first();

                if ($permit) {
                    array_push($errors, 'Santri dengan nama ' . $santri->user->fullname . ' memiliki izin yang disetujui di presensi ini.');
                    continue;
                }

                $inserted = Present::create([
                    'fkSantri_id' => $santri->id,
                    'fkPresence_id' => $presenceIdToInsert,
                    'is_late' => 1
                ]);

                if (!$inserted) {
                    array_push($errors, 'Santri dengan nama ' . $santri->user->fullname . ' memiliki gagal dipresensikan.');
                    continue;
                } else
                    array_push($successes, 'Berhasil presensikan ' . $santri->user->fullname);
            }
        }

        $successText = '<ul>';

        if (sizeof($successes) > 0)
            foreach ($successes as $success)
                $successText .= "<li>$success</li>";

        $successText .= '</ul>';

        $errorText = '<ul>';

        if (sizeof($errors) > 0)
            foreach ($errors as $error)
                $errorText .= "<li>$error</li>";

        $errorText .= '</ul>';

        $flashes = [];

        if (sizeof($successes) > 0)
            $request->session()->flash('successes', $successText);

        if (sizeof($errors) > 0)
            $request->session()->flash('errors', $errorText);

        return redirect()->route('view presence', $presenceIdToInsert);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_my_present($id)
    {
        $presenceIdToInsert = $id;

        $santri = auth()->user()->santri;

        if (!$santri)
            return redirect()->back()->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        // manually validate composite keys
        $existingPresent = Present::where('fkPresence_id', '=', $presenceIdToInsert, 'and')->where('fkSantri_id', '=', $santri->id)->first();

        if ($existingPresent)
            return redirect()->back()->with('success', 'Sudah presensi.');

        $presence = Presence::find($presenceIdToInsert);

        if (!$presence)
            return redirect()->back()->withErrors(['presence_not_found' => 'Presensi tidak ditemukan.']);

        // check whether the santri has permit at that presence
        $permit = Permit::where('fkPresence_id', $presenceIdToInsert)->where('fkSantri_id', $santri->id)->where('status', 'approved')->first();

        if ($permit)
            return redirect()->back()->withErrors(['permit_exists' => 'Santri memiliki izin yang disetujui di presensi ini.']);

        $inserted = Present::create([
            'fkSantri_id' => $santri->id,
            'fkPresence_id' => $presenceIdToInsert
        ]);

        if (!$inserted)
            return redirect()->back()->withErrors(['failed_adding_present' => 'Gagal presensi.']);

        return redirect()->back();
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_present($id, $santriId)
    {
        $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId);

        if ($present) {
            $deleted = $present->delete();

            if (!$deleted)
                return redirect()->route('view presence', $id)->withErrors(['failed_deleting_present', 'Gagal menghapus presensi.']);
        }

        return redirect()->route('view presence', $id)->with('success', 'Berhasil menghapus presensi');
    }


    // ============ PERMIT ============

    /**
     * Show and control requested permits from current user's Lorong members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function permit_approval($tb = null)
    {
        $lastScheduleCheck = SystemMetaData::where('key', 'lastScheduleCheck')->first();

        if (!$lastScheduleCheck || $lastScheduleCheck->value != date('Y-m-d')) {
            PresenceGroupsChecker::checkPresenceGroups();
            PresenceGroupsChecker::checkPermitGenerators();
            SystemMetaData::updateOrCreate(['key' => 'lastScheduleCheck'], ['value' => date('Y-m-d')]);
        }

        // get current santri
        $santri = auth()->user()->santri;
        $lorong = null;
        $permits = Permit::query();
        $tahun_bulan = DB::table('presences')
            ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
            ->groupBy('ym')
            ->orderBy('ym', 'DESC')
            ->get();
        if ($tb == null || $tb == '-') {
            $tb = date('Y-m');
        }

        if ($santri) {
            // get current user's lorong id
            if (auth()->user()->hasRole('koor lorong'))
                $lorong = Lorong::where('fkSantri_leaderId', $santri->id)->first();
        }

        // get current santri
        $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
            ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
            ->orderBy('permits.created_at', 'DESC')
            ->get();
        if ($lorong) {
            if (auth()->user()->hasRole('koor lorong'))
                $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
                    ->join('santris', 'santris.id', '=', 'permits.fkSantri_id')
                    ->where('santris.fkLorong_id', $lorong->id)
                    ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
                    ->orderBy('permits.created_at', 'DESC')
                    ->get();
        }

        return view('presence.permit_approval', ['permits' => $permits, 'lorong' => $lorong, 'santri' => $santri, 'tahun_bulan' => $tahun_bulan, 'tb' => $tb]);
    }

    /**
     * Show and control requested permits from current user's Lorong members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function approve_permit(Request $request)
    {
        // get current santri
        $presenceId = $request->get('presenceId');
        $santriId = $request->get('santriId');
        $page = $request->get('page');

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();

        if (!$permit)
            return redirect()->route('presence permit approval')->withErrors(['permit_not_found', 'Izin tidak ditemukan.']);

        // check whether santri is present in the presence
        $present = Present::where('fkSantri_id', $santriId)->where('fkPresence_id', $presenceId)->first();

        if ($present)
            return redirect()->route('presence permit approval')->withErrors(['santri_is_present' => 'Santri telah hadir di presensi ini.']);

        $permit->status = 'approved';

        $updated = $permit->save();

        if (!$updated)
            return redirect()->route('presence permit approval')->withErrors(['failed_updating_permit', 'Izin gagal disetujui.']);

        return redirect()->route('presence permit approval', ['page' => $page])->with('success', 'Izin berhasil disetujui.');
    }

    /**
     * Show and control requested permits from current user's Lorong members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reject_permit(Request $request)
    {
        // get current santri
        $presenceId = $request->get('presenceId');
        $santriId = $request->get('santriId');

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();

        if (!$permit)
            return redirect()->route('presence permit approval')->withErrors(['permit_not_found', 'Izin tidak ditemukan.']);

        $permit->status = 'rejected';

        $updated = $permit->save();

        if (!$updated)
            return redirect()->route('presence permit approval')->withErrors(['failed_updating_permit', 'Izin gagal disetujui.']);

        return redirect()->route('presence permit approval')->with('success', 'Izin berhasil ditolak.');
    }

    /**
     * Show the current user's presence permits.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function my_permits()
    {
        $lastScheduleCheck = SystemMetaData::where('key', 'lastScheduleCheck')->first();

        if (!$lastScheduleCheck || $lastScheduleCheck->value != date('Y-m-d')) {
            PresenceGroupsChecker::checkPresenceGroups();
            PresenceGroupsChecker::checkPermitGenerators();
            SystemMetaData::updateOrCreate(['key' => 'lastScheduleCheck'], ['value' => date('Y-m-d')]);
        }

        // get current santri
        $santri = auth()->user()->santri;
        if ($santri) {
            $from = date_format(date_create(date('Y-m') . "-01 00:00:00"), "Y-m-d H:i:s");
            $to = date_format(date_create(date('Y-m') . "-31 23:59:59"), "Y-m-d H:i:s");
            $myPermits = Permit::where('fkSantri_id', $santri->id)->whereBetween('created_at', [$from, $to])->get();
            $myRangedPermits = RangedPermitGenerator::where('fkSantri_id', $santri->id)->orderBy('created_at', 'DESC')->get();
            return view('presence.my_permits', ['myPermits' => $myPermits, 'myRangedPermits' => $myRangedPermits]);
        } elseif (auth()->user()->hasRole('superadmin')) {
            $time = strtotime(date('Y-m') . "-01 00:00:00");
            $from = date("Y-m-d H:i:s", strtotime("-1 month", $time));
            $to = date_format(date_create(date('Y-m') . "-31 23:59:59"), "Y-m-d H:i:s");
            $myPermits = Permit::whereBetween('created_at', [$from, $to])->get();
            $myRangedPermits = RangedPermitGenerator::orderBy('created_at', 'DESC')->get();
            return view('presence.my_permits', ['myPermits' => $myPermits, 'myRangedPermits' => $myRangedPermits]);
        }

        return view('presence.my_permits', ['myPermits' => []])->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);
    }

    /**
     * Show the current user's presence permits.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function permits_list($tb = null)
    {
        $tahun_bulan = DB::table('presences')
            ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
            ->groupBy('ym')
            ->get();
        if ($tb == null || $tb == '-') {
            $tb = date('Y-m');
        }
        // get current santri
        $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
            ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
            ->orderBy('permits.created_at', 'DESC')
            ->get();

        return view('presence.permits_list', ['permits' => $permits, 'tahun_bulan' => $tahun_bulan, 'tb' => $tb]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_my_permit(Request $request)
    {
        $presenceId = $request->get('presenceId');
        $dayname = date('D', strtotime(today()));
        if ($dayname == "Fri") {
            $openPresences = Presence::orderBy('event_date', 'DESC')->limit(3)->get();
        } else {
            $openPresences = Presence::orderBy('event_date', 'DESC')->limit(2)->get();
        }

        return view('presence.create_my_permit', ['openPresences' => $openPresences, 'presenceId' => $presenceId]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_my_ranged_permit(Request $request)
    {
        $presenceGroups = PresenceGroup::all();

        return view('presence.create_my_ranged_permit', ['presenceGroups' => $presenceGroups]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_my_permit(Request $request)
    {
        $presenceIdToInsert = $request->input('fkPresence_id');

        // get current santri
        $santri = Santri::find(auth()->user()->santri->id);

        if (!$santri)
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $request->validate([
            'reason' => 'required|string',
            'reason_category' => 'required|string',
            'fkPresence_id' => 'required|exists:presences,id|integer',
        ]);

        // check whether permit already exists
        $existingPermit = Permit::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santri->id)->first();

        if (isset($existingPermit))
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['permit_already_exists' => 'Izin pada presensi tersebut sudah ada.']);

        // check whether the person is present at that presence
        $existingPresent = Present::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santri->id)->first();

        if (isset($existingPresent))
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['presence_already_exists' => 'Santri sudah hadir pada presensi tersebut. Tidak bisa izin.']);

        $ids = uniqid();
        $inserted = false;
        $inserted = Permit::create([
            'fkSantri_id' => $santri->id,
            'fkPresence_id' => $request->input('fkPresence_id'),
            'reason' => $request->input('reason'),
            'reason_category' => $request->input('reason_category'),
            'status' => 'approved', // default (apabila ijin tidak sesuai baru di reject)
            'ids' => $ids
        ]);

        if ($inserted) {
            $presence = Presence::find($request->input('fkPresence_id'));
            if ($santri->fkLorong_id == '') {
                $lorong = '*Koor Lorong*';
            } else {
                $lorong = '*' . $santri->lorong->name . '*';
            }
            $caption = '*[Perijinan Dari ' . $santri->user->fullname . ']*

' . $lorong . '
Presensi: ' . $presence->name . '
Kategori: ' . $request->input('reason_category') . '
Alasan: ' . $request->input('reason') . '
            
Link reject: ' . CommonHelpers::settings()->host_url . '/permit/' . $ids;

            WaSchedules::insertToKetertiban($santri, $caption);

            return redirect()->route('my presence permits')->with('success', 'Berhasil membuat izin. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
        } else {
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Gagal membuat izin.']);
        }
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_my_ranged_permit(Request $request)
    {
        $presenceIdToInsert = $request->input('fkPresence_id');

        // get current santri
        $santri = Santri::find(auth()->user()->santri->id);

        if (!$santri)
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $request->validate([
            'reason' => 'required|string',
            'reason_category' => 'required|string',
            'from_date' => 'required|date|before:to_date',
            'to_date' => 'required|date|after:from_date',
            'fkPresenceGroup_id' => 'required|exists:presence_groups,id|integer',
        ]);

        // check whether another ranged permit already exists within similar range
        $existingRangedPermit = RangedPermitGenerator::orWhere(function ($query) use ($request, $santri) {
            $query->where('fkSantri_id', $santri->id);
            $query->where('fkPresenceGroup_id', $request->input('fkPresenceGroup_id'));
            $query->whereDate('from_date', '>=', $request->input('from_date'));
            $query->whereDate('from_date', '<=', $request->input('to_date'));
        })
            ->orWhere(function ($query) use ($request, $santri) {
                $query->where('fkSantri_id', $santri->id);
                $query->where('fkPresenceGroup_id', $request->input('fkPresenceGroup_id'));
                $query->whereDate('to_date', '>=', $request->input('from_date'));
                $query->whereDate('to_date', '<=', $request->input('to_date'));
            })
            ->orWhere(function ($query) use ($request, $santri) {
                $query->where('fkSantri_id', $santri->id);
                $query->where('fkPresenceGroup_id', $request->input('fkPresenceGroup_id'));
                $query->whereDate('from_date', '<=', $request->input('from_date'));
                $query->whereDate('to_date', '>=', $request->input('from_date'));
            })
            ->orWhere(function ($query) use ($request, $santri) {
                $query->where('fkSantri_id', $santri->id);
                $query->where('fkPresenceGroup_id', $request->input('fkPresenceGroup_id'));
                $query->whereDate('from_date', '<=', $request->input('to_date'));
                $query->whereDate('to_date', '>=', $request->input('to_date'));
            })
            ->first();

        if (isset($existingRangedPermit))
            return redirect()->route('ranged presence permit submission')->withErrors(['ranged_permit_already_exists' => 'Izin berjangka pada presensi tersebut dengan waktu yang sama sudah sudah ada.']);

        // we don't have to check for existing present as when the software tries to add permit to 
        // presence where the user is present, it can't

        // now let's insert permits to existing presences.
        $createdPresences = Presence::whereDate('event_date', '>=', $request->input('from_date'))
            ->whereDate('event_date', '<=', $request->input('to_date'))
            ->get();

        foreach ($createdPresences as $presence) {
            // check whether permit already exists
            $existingPermit = Permit::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri->id)->first();

            if (isset($existingPermit))
                continue;

            // check whether the person is present at that presence
            $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri->id)->first();

            if (isset($existingPresent))
                continue;

            $ids = uniqid();
            $inserted = Permit::create([
                'fkSantri_id' => $santri->id,
                'fkPresence_id' => $presence->id,
                'reason' => $request->input('reason'),
                'reason_category' => $request->input('reason_category'),
                'status' => 'approved',
                'ids' => $ids
            ]);
        }

        // create generator
        $inserted = RangedPermitGenerator::create([
            'fkSantri_id' => $santri->id,
            'fkPresenceGroup_id' => $request->input('fkPresenceGroup_id'),
            'reason' => $request->input('reason'),
            'reason_category' => $request->input('reason_category'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ]);

        if ($inserted) {
            $presencex = PresenceGroup::find($request->input('fkPresenceGroup_id'));
            if ($santri->fkLorong_id == '') {
                $lorong = '*Koor Lorong*';
            } else {
                $lorong = '*' . $santri->lorong->name . '*';
            }
            $caption = '*[Perijinan Dari ' . $santri->user->fullname . ']*

' . $lorong . '
Presensi: ' . $presencex->name . '
Kategori: ' . $request->input('reason_category') . '
Alasan: ' . $request->input('reason') . '
Dari: ' . $request->input('from_date') . '
Sampai: ' . $request->input('to_date') . '
            
NB: dikarenakan ijin berjangka, silahkan mengecek di sisfo';

            WaSchedules::insertToKetertiban($santri, $caption);

            return redirect()->route('my presence permits')->with('success', 'Berhasil membuat izin berjangka, silakan cek daftar izin kamu. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
        } else {
            return redirect()->route('presence permit submission')->withErrors(['failed_adding_permit' => 'Gagal membuat izin berjangka.']);
        }
    }

    public function delete_my_ranged_permit($id)
    {
        // get current santri
        $santri = auth()->user()->santri;

        if (!$santri)
            return redirect()->route('my presence permits')->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $rangedPermit = RangedPermitGenerator::find($id);

        if (!$rangedPermit)
            return redirect()->route('my presence permits')->withErrors(['ranged_permit_not_found' => 'Izin berjangka tidak ditemukan.']);

        $deleted = $rangedPermit->delete();

        if (!$deleted)
            return redirect()->route('my presence permits')->withErrors(['failed_deleting_ranged_permit', 'Gagal menghapus izin berjangka.']);

        return redirect()->route('my presence permits')->with('success', 'Berhasil menghapus izin berjangka');
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_permit(Request $request)
    {
        $dayname = date('D', strtotime(today()));
        if ($dayname == "Fri") {
            $openPresences = Presence::orderBy('event_date', 'DESC')->limit(15)->get();
        } else {
            $openPresences = Presence::orderBy('event_date', 'DESC')->limit(14)->get();
        }

        if (auth()->user()->hasRole('superadmin')) {
            $usersWithSantri = User::whereHas('santri', function ($query) {
                $query->whereNull('exit_at');
            })->orderBy('fullname')->get();
        } elseif (auth()->user()->hasRole('koor lorong')) {
            $lorong = Lorong::where('fkSantri_leaderId', auth()->user()->santri->id)->first();
            if ($lorong) {
                $usersWithSantri = User::whereHas('santri', function ($query) use ($lorong) {
                    $query->whereNull('exit_at');
                    $query->where('fkLorong_id', '=', $lorong->id);
                })->orderBy('fullname')->get();
            }
        } else {
            $usersWithSantri = User::whereHas('santri', function ($query) {
                $query->where('id', '!=', auth()->user()->santri->id);
                $query->whereNull('exit_at');
            })->orderBy('fullname')->get();
        }

        // $usersWithSantri = User::whereHas('santri', function($query) {
        //     $query->whereNull('exit_at');
        // })->orderBy('fullname')->get();

        return view('presence.create_permit', ['openPresences' => $openPresences, 'usersWithSantri' => $usersWithSantri]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_permit(Request $request)
    {
        $presenceIdToInsert = $request->input('fkPresence_id');
        $santriId = $request->input('fkSantri_id');

        $request->validate([
            'reason' => 'required|string',
            'fkPresence_id' => 'required|exists:presences,id|integer',
            'fkSantri_id' => 'required|exists:santris,id|integer',
            'reason_category' => 'required|string'
        ]);

        // check whether permit already exists
        $existingPermit = Permit::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santriId)->first();

        if (isset($existingPermit))
            return redirect()->route('create presence permit')->withErrors(['permit_already_exists' => 'Izin pada presensi tersebut sudah ada.']);

        // check whether the person is present at that presence
        $existingPresent = Present::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santriId)->first();

        if (isset($existingPresent))
            return redirect()->route('create presence permit')->withErrors(['presence_already_exists' => 'Santri sudah hadir pada presensi tersebut. Tidak bisa izin.']);

        $ids = uniqid();
        $inserted = Permit::create([
            'fkSantri_id' => $santriId,
            'fkPresence_id' => $request->input('fkPresence_id'),
            'reason' => $request->input('reason'),
            'reason_category' => $request->input('reason_category'),
            'status' => $request->input('status'),
            'ids' => $ids
        ]);

        if ($inserted) {
            $santri = Santri::find($santriId);
            $presence = Presence::find($request->input('fkPresence_id'));
            if ($santri->fkLorong_id == '') {
                $lorong = '*Koor Lorong*';
            } else {
                $lorong = '*' . $santri->lorong->name . '*';
            }
            $caption = '*[Perijinan Dari ' . $santri->user->fullname . ']*

' . $lorong . '
Presensi: ' . $presence->name . '
Kategori: ' . $request->input('reason_category') . '
Alasan: ' . $request->input('reason') . '
            
Link reject: ' . CommonHelpers::settings()->host_url . '/permit/' . $ids;

            WaSchedules::insertToKetertiban($santri, $caption);

            return redirect()->route('presence permit approval')->with('success', 'Berhasil membuat izin. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
        } else {
            return redirect()->route('create presence permit')->withErrors(['failed_adding_permit' => 'Gagal membuat izin.']);
        }
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit_permit(Request $request)
    {
        $presenceId = $request->get('presenceId');

        // get current santri
        $santri = auth()->user()->santri;

        if (!$santri)
            return redirect()->route('edit presence permit', ['presenceId' => $presenceId])->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santri->id)->first();

        $openPresences = Presence::whereDate('start_date_time', date('Y-m-d'))->get();

        return view('presence.edit_permit', ['permit' => $permit, 'openPresences' => $openPresences]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update_permit(Request $request)
    {
        $presenceId = $request->get('presenceId');

        $request->validate([
            'reason' => 'required|string',
            'fkPresence_id' => 'required|exists:presences,id|integer',
        ]);

        // get current santri
        $santri = auth()->user()->santri;

        if (!isset($santri))
            return redirect()->route('edit presence permit', ['presenceId' => $presenceId])->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $updated = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santri->id)->update([
            'reason' => $request->input('reason'),
        ]);

        if ($updated <= 0)
            return redirect()->route('edit presence permit', ['presenceId' => $presenceId])->withErrors(['failed_updating_permit' => 'Gagal memperbarui izin.']);

        return redirect()->route('edit presence permit', ['presenceId' => $presenceId])->with('success', 'Berhasil memperbarui izin.');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_my_permit(Request $request)
    {
        $presenceId = $request->get('presenceId');

        // get current santri
        $santri = auth()->user()->santri;

        if (!$santri)
            return redirect()->route('my presence permits')->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santri->id);

        if (!$permit)
            return redirect()->route('my presence permits')->withErrors(['permit_not_found' => 'Izin tidak ditemukan.']);

        $deleted = $permit->delete();

        if (!$deleted)
            return redirect()->route('my presence permits')->withErrors(['failed_deleting_permit', 'Gagal menghapus izin.']);

        return redirect()->route('my presence permits')->with('success', 'Berhasil menghapus izin');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_permit(Request $request)
    {
        $presenceId = $request->get('presenceId');
        $santriId = $request->get('santriId');
        $tb = $request->get('tb');

        // get current santri
        $santri = Santri::find($santriId);

        if (!$santri)
            return redirect()->route('presence permit approval', ['tb' => $tb])->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santri->id);

        if (!$permit)
            return redirect()->route('presence permit approval', ['tb' => $tb])->withErrors(['permit_not_found' => 'Izin tidak ditemukan.']);

        $deleted = $permit->delete();

        if (!$deleted)
            return redirect()->route('presence permit approval', ['tb' => $tb])->withErrors(['failed_deleting_permit', 'Gagal menghapus izin.']);

        return redirect()->route('presence permit approval', ['tb' => $tb])->with('success', 'Berhasil menghapus izin');
    }
}

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
use App\Models\Settings;
use App\Models\DewanPengajars;
use App\Models\SpWhatsappPhoneNumbers;
use App\Helpers\PresenceGroupsChecker;
use App\Helpers\WaSchedules;
use App\Models\SystemMetaData;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;

use Carbon\Carbon;
use Error;

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

    public function barcode()
    {
        return view('presence.barcode');
    }

    public function check_barcode(Request $request)
    {
        $existingBarcode1 = Present::where('barcode_in', $request->input('barcode1'))
            ->whereOr('barcode_out', $request->input('barcode1'))->first();

        $existingBarcode2 = Present::where('barcode_in', $request->input('barcode2'))
            ->whereOr('barcode_out', $request->input('barcode2'))->first();

        $datetime = date("Y-m-d H:i:s");
        $presence = Presence::where('is_deleted', 0)->where('presence_start_date_time', '<=', $datetime)
            ->where('presence_end_date_time', '>=', $datetime)->first();

        $barcode1 = false;
        $barcode2 = false;
        if ($existingBarcode1 != null) {
            $barcode1 = true;
        }
        if ($existingBarcode2 != null) {
            $barcode2 = true;
        }

        return json_encode(['barcode1' => $barcode1, 'barcode2' => $barcode2, 'presence_name' => ($presence != null) ? $presence->name : '', 'presence' => ($presence == null) ? false : true]);
    }

    public function generate_barcode()
    {
        return view('presence.barcode_generate');
    }

    public function store_present_barcode(Request $request)
    {
        try {
            $santriIdToInsert = auth()->user()->santri->id;

            $datetime = date("Y-m-d H:i:s");
            $presence = Presence::where('is_deleted', 0)->where('presence_start_date_time', '<=', $datetime)
                ->where('presence_end_date_time', '>=', $datetime)->first();
            if ($presence == null) {
                return json_encode(['status' => false, 'message' => 'Presensi KBM tidak ditemukan']);
            } else {
                $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santriIdToInsert)->first();
                $barcode = $request->input('barcode');
                if ($existingPresent == null) {
                    // sign in
                    $sign_in = date("Y-m-d H:i:s");
                    $is_late = 0;
                    if ($sign_in > $presence->start_date_time) {
                        $is_late = 1;
                    }
                    $inserted = Present::create([
                        'fkSantri_id' => $santriIdToInsert,
                        'fkPresence_id' => $presence->id,
                        'barcode_in' => $barcode,
                        'sign_in' => $sign_in,
                        'updated_by' => auth()->user()->fullname,
                        'metadata' => $_SERVER['HTTP_USER_AGENT'],
                        'is_late' => $is_late
                    ]);

                    if ($inserted) {
                        return json_encode(['status' => true, 'sign' => 'in', 'message' => 'Sign in berhasil']);
                    } else {
                        return json_encode(['status' => false, 'sign' => 'in', 'message' => 'Sign in gagal']);
                    }
                } else {
                    // sign out
                    if ($existingPresent->sign_out != '') {
                        return json_encode(['status' => true, 'sign' => 'out', 'message' => 'Anda sudah sign out']);
                    } elseif (date("Y-m-d H:i:s") < $presence->end_date_time && $request->input('reason') == '') {
                        return json_encode(['status' => true, 'sign' => 'confirm_out', 'message' => 'Anda sign out sebelum jam pulang KBM, silahkan masukkan alasannya']);
                    }
                    $existingPresent->barcode_out = $barcode;
                    $existingPresent->reason_togo_home_early = $request->input('reason');
                    $existingPresent->sign_out = date("Y-m-d H:i:s");
                    // $existingPresent->reason_togo_home_early = $request->input('reason_togo_home_early');

                    if ($existingPresent->save()) {
                        return json_encode(['status' => true, 'sign' => 'out', 'message' => 'Sign out berhasil']);
                    } else {
                        return json_encode(['status' => false, 'sign' => 'out', 'message' => 'Sign out gagal']);
                    }
                }
            }
        } catch (Exception $err) {
            return json_encode(['status' => false, 'message' => $err]);
        }
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

        $presences = Presence::where('is_deleted', 0)->where('fkPresence_group_id', null)->get();
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
        $presences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->whereMonth('event_date', '=', $decreasedDate->month)->whereYear('event_date', '=', $decreasedDate->year)->get();
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
                'presence_start_date_time' => 'required|date',
                'presence_end_date_time' => 'required|date',
            ]);

        if ($request->input('fkPresence_group_id'))
            $request->validate([
                'fkPresence_group_id' => 'integer|exists:presence_groups,id',
            ]);

        $inserted = Presence::create([
            'name' => $request->input('name'),
            'start_date_time' => $request->input('start_date_time') ? $request->input('start_date_time') : null,
            'end_date_time' => $request->input('end_date_time') ? $request->input('end_date_time') : null,
            'presence_start_date_time' => $request->input('presence_start_date_time') ? $request->input('presence_start_date_time') : null,
            'presence_end_date_time' => $request->input('presence_end_date_time') ? $request->input('presence_end_date_time') : null,
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
        $json = $request->input('json');
        if (isset($json)) {
            if ($json) {
                $presence = Presence::find($request->route('id'));
                $presence->name = $request->input('name');
                $presence->fkDewan_pengajar_1 = $request->input('dp1');
                $presence->fkDewan_pengajar_2 = $request->input('dp2');
                $presence->is_hasda = $request->input('is_hasda');
                $presence->is_put_together = $request->input('is_put_together');
                $updated = $presence->save();
                if ($updated) {
                    return json_encode(['status' => true, 'message' => 'Berhasil update nama presensi dan dewan pengajar']);
                } else {
                    return json_encode(['status' => false, 'message' => 'Gagal update nama presensi dan dewan pengajar']);
                }
            }
        } else {
            $request->validate([
                'name' => 'required|max:100',
                'event_date' => 'date',
            ]);

            if ($request->input('is_date_time_limited'))
                $request->validate([
                    'start_date_time' => 'required|date',
                    'end_date_time' => 'required|date',
                    'presence_start_date_time' => 'required|date',
                    'presence_end_date_time' => 'required|date',
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
                $presence->presence_start_date_time = $request->input('presence_start_date_time');
                $presence->presence_end_date_time = $request->input('presence_end_date_time');
            } else {
                $presence->start_date_time = null;
                $presence->end_date_time = null;
                $presence->presence_start_date_time = null;
                $presence->presence_end_date_time = null;
            }

            $updated = $presence->save();

            if (!$updated)
                return redirect()->back()->withErrors(['failed_updating_presence']);

            return redirect()->route('view presence', $request->route('id'))->with('success', 'Berhasil mengubah presensi');
        }
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
            $presence->is_deleted = 1;
            $presence->deleted_by = auth()->user()->fullname;
            $presence->save();
            // $deleted = $presence->delete();

            if (!$presence)
                return redirect()->route('presence tm')->withErrors(['failed_deleting_presence', 'Gagal menghapus presensi.']);
        }

        return redirect()->route('presence tm')->with('success', 'Berhasil menghapus presensi');
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
    public function view($id, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $presence = Presence::find($id);
        if ($presence == null) {
            return redirect()->route('index');
        } elseif ($presence->is_deleted) {
            return redirect()->route('presence tm')->with('success', 'Presensi ' . $presence->name . ' telah dihapus oleh ' . $presence->deleted_by);
        }

        $for = '';
        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk')) {
            $for = 'all';
        } elseif (auth()->user()->santri != '') {
            $for = 'lorong';
        }

        // jumlah mhs / anggota lorong
        $jumlah_mhs = CountDashboard::total_mhs($for, $lorong);

        // hadir
        $presents = CountDashboard::mhs_hadir($id, $for, $lorong);

        // ijin berdasarkan lorong masing2
        $permits = CountDashboard::mhs_ijin($id, $for, $lorong);

        // need approval
        if($for == 'lorong'){
            $arr_santri = [];
            if (auth()->user()->santri->lorongUnderLead) {
                foreach (auth()->user()->santri->lorongUnderLead->members as $santri) {
                    $arr_santri[] = $santri->id;
                }
            }
            $need_approval = Permit::where('fkPresence_id', $id)->whereNotIn('status', ['approved'])->whereIn('fkSantri_id', $arr_santri)->get();
        }else{
            $need_approval = Permit::where('fkPresence_id', $id)->whereNotIn('status', ['approved'])->get();
        }

        // alpha
        $mhs_alpha = CountDashboard::mhs_alpha($id, $for, $presence->event_date, $lorong);

        $update = true;
        if ($presence != null) {
            $selisih = strtotime(date("Y-m-d")) - strtotime($presence->event_date);
            $selisih = $selisih / 60 / 60 / 24;
            if ($selisih > 1 && $for != 'all') {
                $update = false;
            }
        }

        return view('presence.view', [
            'id' => $id,
            'presence' => $presence,
            'jumlah_mhs' => $jumlah_mhs,
            'mhs_alpha' => $mhs_alpha,
            'permits' => $permits,
            'need_approval' => $need_approval,
            'presents' => $presents == null ? [] : $presents,
            'data_lorong' => Lorong::all(),
            'dewan_pengajar' => DewanPengajars::all(),
            'lorong' => $lorong,
            'update' => $update
        ]);
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
            $presenceGroup->presence_start_hour = $request->input('presence_start_hour');
            $presenceGroup->presence_end_hour = $request->input('presence_end_hour');
        } else {
            $presenceGroup->start_hour = null;
            $presenceGroup->end_hour = null;
            $presenceGroup->presence_start_hour = null;
            $presenceGroup->presence_end_hour = null;
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
                'presence_start_date_time' => 'required|date',
                'presence_end_date_time' => 'required|date',
            ]);

        $inserted = Presence::create([
            'name' => $request->input('name'),
            'start_date_time' => $request->input('start_date_time') ? $request->input('start_date_time') : null,
            'end_date_time' => $request->input('end_date_time') ? $request->input('end_date_time') : null,
            'presence_start_date_time' => $request->input('presence_start_date_time') ? $request->input('presence_start_date_time') : null,
            'presence_end_date_time' => $request->input('presence_end_date_time') ? $request->input('presence_end_date_time') : null,
            'fkPresence_group_id' => $request->route('id'),
            'total_mhs' => CountDashboard::total_mhs('all'),
            'event_date' => $request->input('event_date')
        ]);
        
        PresenceGroupsChecker::checkPermitGenerators();

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
    public function delete_present($id, $santriId, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId);

        if ($present) {
            $deleted = $present->delete();

            if (!$deleted)
                return redirect()->route('view presence', [$id, 'lorong' => $lorong])->withErrors(['failed_deleting_present', 'Gagal menghapus presensi.']);
        }
        if ($request->get('json') == 'true') {
            return json_encode(array("status" => true));
        } else {
            return redirect()->route('view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil menghapus presensi');
        }
    }

    public function is_present($id, $santriId, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId)->first();

        if ($present == null) {
            Present::create([
                'fkSantri_id' => $santriId,
                'fkPresence_id' => $id,
                'is_late' => 0,
                'updated_by' => auth()->user()->fullname,
                'metadata' => $_SERVER['HTTP_USER_AGENT']
            ]);
        }

        if ($request->get('json') == 'true') {
            return json_encode(array("status" => true));
        } else {
            return redirect()->route('view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil menginput presensi');
        }
    }

    // public function is_late($id, $santriId, Request $request)
    // {
    //     $lorong = $request->get('lorong');
    //     if ($lorong == null) {
    //         $lorong = '-';
    //     }
    //     $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId);

    //     if ($present) {
    //         $present->delete();
    //         Present::create([
    //             'fkSantri_id' => $santriId,
    //             'fkPresence_id' => $id,
    //             'is_late' => 1
    //         ]);
    //     }

    //     return redirect()->route('view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil mengubah telat');
    // }

    // public function is_not_late($id, $santriId, Request $request)
    // {
    //     $lorong = $request->get('lorong');
    //     if ($lorong == null) {
    //         $lorong = '-';
    //     }
    //     $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId);

    //     if ($present) {
    //         $present->delete();
    //         Present::create([
    //             'fkSantri_id' => $santriId,
    //             'fkPresence_id' => $id,
    //             'is_late' => 0
    //         ]);
    //     }

    //     return redirect()->route('view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil mengubah telat');
    // }


    // ============ PERMIT ============

    /**
     * Show and control requested permits from current user's Lorong members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function permit_approval($tb = null, $status = 'pending')
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
        // $permits = Permit::query();
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

        $rangedPermitGenerator = null;
        // get current santri
        if ($status == 'all') {
            $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
                ->join('santris', 'santris.id', '=', 'permits.fkSantri_id')
                ->whereNull('santris.exit_at')
                ->select('permits.*', 'permits.updated_at')
                ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
                ->orderBy('permits.created_at', 'DESC')
                ->get();
            $rangedPermitGenerator = RangedPermitGenerator::leftJoin('santris', 'santris.id', '=', 'ranged_permit_generators.fkSantri_id')
                ->whereNull('santris.exit_at')
                ->select('ranged_permit_generators.*')
                ->orderBy('ranged_permit_generators.created_at', 'DESC')
                ->get();
        } else {
            $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
                ->join('santris', 'santris.id', '=', 'permits.fkSantri_id')
                ->whereNull('santris.exit_at')
                ->select('permits.*', 'permits.updated_at')
                ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
                ->where('permits.status', $status)
                ->orderBy('permits.created_at', 'DESC')
                ->get();
            $rangedPermitGenerator = RangedPermitGenerator::leftJoin('santris', 'santris.id', '=', 'ranged_permit_generators.fkSantri_id')
                ->whereNull('santris.exit_at')
                ->select('ranged_permit_generators.*')
                ->where('ranged_permit_generators.status', $status)
                ->orderBy('ranged_permit_generators.created_at', 'DESC')
                ->get();
        }
        if ($lorong) {
            if (auth()->user()->hasRole('koor lorong'))
                if ($status == 'all') {
                    $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
                        ->join('santris', 'santris.id', '=', 'permits.fkSantri_id')
                        ->select('permits.*', 'permits.updated_at')
                        ->where('santris.fkLorong_id', $lorong->id)
                        ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
                        ->orderBy('permits.status', 'DESC')
                        ->orderBy('permits.created_at', 'DESC')
                        ->get();
                } else {
                    $permits = Permit::join('presences', 'presences.id', '=', 'permits.fkPresence_id')
                        ->join('santris', 'santris.id', '=', 'permits.fkSantri_id')
                        ->select('permits.*', 'permits.updated_at')
                        ->where('santris.fkLorong_id', $lorong->id)
                        ->where('presences.event_date', 'LIKE', '%' . $tb . '%')
                        ->where('permits.status', $status)
                        ->orderBy('permits.status', 'DESC')
                        ->orderBy('permits.created_at', 'DESC')
                        ->get();
                }
        }

        return view(
            'presence.permit_approval',
            [
                'permits' => $permits,
                'rangedPermitGenerator' => $rangedPermitGenerator,
                'lorong' => $lorong,
                'santri' => $santri,
                'tahun_bulan' => $tahun_bulan,
                'tb' => $tb,
                'status' => $status
            ]
        );
    }

    /**
     * Show and control requested permits from current user's Lorong members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function approve_permit(Request $request)
    {
        // get current santri
        $json = $request->get('json');
        $setting = Settings::find(1);
        if (isset($json)) {
            if ($json) {
                $caption = '*' . auth()->user()->fullname . '* Menyetujui perizinan dari:
';
                $message = '';
                $updated = false;
                $data_json = json_decode($request->get('data_json'));
                $is_present = '';
                foreach ($data_json as $dt) {
                    $presenceId = $dt[0];
                    $santriId = $dt[1];

                    $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();
                    if ($permit) {
                        $present = Present::where('fkSantri_id', $santriId)->where('fkPresence_id', $presenceId)->first();
                        if ($present) {
                            if ($is_present == '') {
                                $is_present = $permit->santri->user->fullname;
                            } else {
                                $is_present = $is_present . ', ' . $permit->santri->user->fullname;
                            }
                            $message = ' *(Ternyata gagal: Karena mahasiswa telah hadir di presensi ini, silahkan dihapus perizinan ini)*';
                        } else {
                            $permit->status = 'approved';
                            $permit->approved_by = auth()->user()->fullname;
                            $permit->alasan_rejected = '';
                            $permit->metadata = $_SERVER['HTTP_USER_AGENT'];
                            $updated = $permit->save();

                            $notif_info = '*' . auth()->user()->fullname . '* Menyetujui perizinan dari: *'.$permit->santri->user->fullname.'* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason;
                            WaSchedules::save('Permit Approval - Mahasiswa', $notif_info, WaSchedules::getContactId($permit->santri->user->nohp), null, true);
                            WaSchedules::save('Permit Approval - Ortu', $notif_info, WaSchedules::getContactId($permit->santri->nohp_ortu), null, true);
                        }
                        $caption = $caption . '- *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason . $message . '
';
                    }
                }

                WaSchedules::save('Permit Approval - Info Perizinan', $caption, $setting->wa_info_presensi_group_id, null, true);
                return json_encode(['status' => true, 'message' => 'Ijin berhasil disetujui', 'is_present' => $is_present]);
            }
        } else {
            $presenceId = $request->get('presenceId');
            $santriId = $request->get('santriId');

            $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();

            if (!$permit)
                return redirect()->back()->withErrors('permit_not_found', 'Izin tidak ditemukan.');

            // check whether santri is present in the presence
            $present = Present::where('fkSantri_id', $santriId)->where('fkPresence_id', $presenceId)->first();

            if ($present)
                return redirect()->back()->with('santri_is_present', 'Santri telah hadir di presensi ini.');

            $permit->status = 'approved';
            $permit->approved_by = auth()->user()->fullname;
            $permit->alasan_rejected = '';
            $permit->metadata = $_SERVER['HTTP_USER_AGENT'];

            $updated = $permit->save();

            if ($updated) {
                $caption = '*' . auth()->user()->fullname . '* Menyetujui perijinan dari *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason;
                WaSchedules::save('Permit Approval', $caption, $setting->wa_info_presensi_group_id, null, true);
            } else {
                return redirect()->back()->withErrors('failed_updating_permit', 'Izin gagal disetujui.');
            }
            return redirect()->back()->with('success', 'Izin berhasil disetujui.');
        }
    }

    public function approve_range_permit(Request $request)
    {
        try {
            $data_json = json_decode($request->get('data_json_berjangka'));
            foreach ($data_json as $dt) {
                $rpgId = $dt[0];
                $santriId = $dt[1];

                $permit = RangedPermitGenerator::where('id', $rpgId)->where('fkSantri_id', $santriId)->whereNot('status', 'approved')->first();
                if ($permit) {
                    PresenceGroupsChecker::checkPermitGenerators();
                    $permit->status = 'approved';
                    $permit->approved_by = auth()->user()->fullname;
                    $permit->save();

                    $notif_info = '*' . auth()->user()->fullname . '* Menyetujui perizinan berjangka dari: *'.$permit->santri->user->fullname.'* pada:
- ' . $permit->presenceGroup->name . '
- Kategori Alasan: ' . $permit->reason_category . '
- Alasan: ' . $permit->reason.'
- Tanggal: '.date_format(date_create($permit->from_date),"d-m-Y").' s.d. '.date_format(date_create($permit->to_date),"d-m-Y");
                    WaSchedules::save('Ranged Permit Approval - Mahasiswa', $notif_info, WaSchedules::getContactId($permit->santri->user->nohp));
                    WaSchedules::save('Ranged Permit Approval - Ortu', $notif_info, WaSchedules::getContactId($permit->santri->nohp_ortu));
                }
            }

            return json_encode(['status' => true, 'message' => 'Ijin berhasil disetujui']);
        } catch (Exception $err) {
            return json_encode(['status' => false, 'message' => 'Gagal, sedang terjadi kesalahan sistem']);
        }
    }

    public function reject_range_permit(Request $request)
    {
        try {
            $data_json = json_decode($request->get('data_json_berjangka'));
            foreach ($data_json as $dt) {
                $rpgId = $dt[0];
                $santriId = $dt[1];

                $permit = RangedPermitGenerator::where('id', $rpgId)->where('fkSantri_id', $santriId)->whereNot('status', 'rejected')->first();
                if ($permit) {
                    PresenceGroupsChecker::checkPermitGenerators();
                    $permit->status = 'rejected';
                    $permit->approved_by = auth()->user()->fullname;
                    $permit->save();

                    $notif_info = '*' . auth()->user()->fullname . '* Menolak perizinan berjangka dari: *'.$permit->santri->user->fullname.'* pada:
- ' . $permit->presenceGroup->name . '
- Kategori Alasan: ' . $permit->reason_category . '
- Alasan: ' . $permit->reason.'
- Tanggal: '.date_format(date_create($permit->from_date),"d-m-Y").' s.d. '.date_format(date_create($permit->to_date),"d-m-Y");
                    WaSchedules::save('Ranged Permit Rejected - Mahasiswa', $notif_info, WaSchedules::getContactId($permit->santri->user->nohp));
                    WaSchedules::save('Ranged Permit Rejected - Ortu', $notif_info, WaSchedules::getContactId($permit->santri->nohp_ortu));
                }
            }

            return json_encode(['status' => true, 'message' => 'Ijin berhasil ditolak']);
        } catch (Exception $err) {
            return json_encode(['status' => false, 'message' => 'Gagal, sedang terjadi kesalahan sistem']);
        }
    }

    public function reject_permit(Request $request)
    {
        // get current santri
        $json = $request->get('json');
        $setting = Settings::find(1);
        if (isset($json)) {
            if ($json) {
                $time_post = 1;
                $caption = '*' . auth()->user()->fullname . '* Menolak perijinan dari:
';
                $message = '';
                $data_json = json_decode($request->get('data_json'));

                foreach ($data_json as $dt) {
                    $presenceId = $dt[0];
                    $santriId = $dt[1];
                    $alasan_rejected = $dt[2];

                    $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();
                    if ($permit) {
                        $permit->status = 'rejected';
                        $permit->rejected_by = auth()->user()->fullname;
                        if (isset($alasan_rejected)) {
                            $permit->alasan_rejected = $alasan_rejected;
                        }
                        $permit->ijin_kuota = "";
                        $permit->metadata = $_SERVER['HTTP_USER_AGENT'];
                        $updated = $permit->save();
                        if ($updated) {
                            $caption = $caption . '- *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason . $message  . '
*Alasan Ditolak:* Karena ' . $permit->alasan_rejected . '
';

                            $name = '[Rejected] Perijinan Dari ' . $permit->santri->user->fullname;
                            // kirim ke yg ijin
                            $nohp = $permit->santri->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                                $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                    $query->where('name', 'NOT LIKE', '%Bulk%');
                                })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                                if ($wa_phone != null) {
                                    $caption_a = 'Mohon maaf, Perijinan pada ' . $permit->presence->name . ' Anda di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                                    WaSchedules::save($name, $caption_a, $wa_phone->pid, $time_post);
                                }
                            }

                            $name = '[Rejected-Ortu] Perijinan Dari ' . $permit->santri->user->fullname;
                            // kirim ke orangtua
                            $nohp_ortu = $permit->santri->nohp_ortu;
                            if ($nohp_ortu != '') {
                                if ($nohp_ortu[0] == '0') {
                                    $nohp_ortu = '62' . substr($nohp_ortu, 1);
                                }
                                $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                    $query->where('name', 'NOT LIKE', '%Bulk%');
                                })->where('team_id', $setting->wa_team_id)->where('phone', $nohp_ortu)->first();
                                if ($wa_phone != null) {
                                    $caption_b = 'Mohon maaf, Perijinan *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ' di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                                    WaSchedules::save($name, $caption_b, $wa_phone->pid, $time_post);
                                }
                            }
                            $time_post++;
                        }
                    }
                }

                WaSchedules::save('Permit Rejected', $caption, $setting->wa_info_presensi_group_id, $time_post, true);

                return json_encode(['status' => true, 'message' => 'Izin berhasil ditolak']);
            }
        } else {
            $presenceId = $request->get('presenceId');
            $santriId = $request->get('santriId');

            $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();

            if (!$permit)
                return redirect()->route('presence permit approval')->withErrors(['permit_not_found', 'Izin tidak ditemukan.']);

            $permit->status = 'rejected';
            $permit->alasan_rejected = $request->get('alasan');
            $permit->rejected_by = auth()->user()->fullname;
            $permit->metadata = $_SERVER['HTTP_USER_AGENT'];
            $permit->ijin_kuota = "";

            $updated = $permit->save();

            if ($updated) {
                $caption = '*' . auth()->user()->fullname . '* Menolak perijinan dari *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason;
                WaSchedules::save('Permit Rejected', $caption, $setting->wa_info_presensi_group_id, 1, true);

                $name = '[Rejected] Perijinan Dari ' . $permit->santri->user->fullname;
                // kirim ke yg ijin
                $nohp = $permit->santri->user->nohp;
                if ($nohp != '') {
                    if ($nohp[0] == '0') {
                        $nohp = '62' . substr($nohp, 1);
                    }
                    $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                        $query->where('name', 'NOT LIKE', '%Bulk%');
                    })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                    if ($wa_phone != null) {
                        $caption = 'Perijinan pada ' . $permit->presence->name . ' Anda di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                        WaSchedules::save($name, $caption, $wa_phone->pid, 2);
                    }
                }

                $name = '[Rejected-Ortu] Perijinan Dari ' . $permit->santri->user->fullname;
                // kirim ke orangtua
                $nohp_ortu = $permit->santri->nohp_ortu;
                if ($nohp_ortu != '') {
                    if ($nohp_ortu[0] == '0') {
                        $nohp_ortu = '62' . substr($nohp_ortu, 1);
                    }
                    $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                        $query->where('name', 'NOT LIKE', '%Bulk%');
                    })->where('team_id', $setting->wa_team_id)->where('phone', $nohp_ortu)->first();
                    if ($wa_phone != null) {
                        $caption = 'Perijinan *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ' di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                        WaSchedules::save($name, $caption, $wa_phone->pid, 3);
                    }
                }
                // return redirect()->route('presence permit approval')->with('success', 'Izin berhasil ditolak.');
                return redirect()->back()->with('success', 'Izin berhasil ditolak.');
            } else {
                return redirect()->route('presence permit approval')->withErrors(['failed_updating_permit', 'Izin gagal disetujui.']);
            }
        }
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
            $myPermits = Permit::where('fkSantri_id', $santri->id)->whereBetween('created_at', [$from, $to])->orderBy('created_at', 'DESC')->get();
            $myRangedPermits = RangedPermitGenerator::where('fkSantri_id', $santri->id)->orderBy('created_at', 'DESC')->get();
            return view('presence.my_permits', ['myPermits' => $myPermits, 'myRangedPermits' => $myRangedPermits]);
        } elseif (auth()->user()->hasRole('superadmin')) {
            $time = strtotime(date('Y-m') . "-01 00:00:00");
            $from = date("Y-m-d H:i:s", strtotime("-1 month", $time));
            $to = date_format(date_create(date('Y-m') . "-31 23:59:59"), "Y-m-d H:i:s");
            $myPermits = Permit::whereBetween('created_at', [$from, $to])->orderBy('created_at', 'DESC')->get();
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
            ->orderBy('ym', 'DESC')
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
        $data_kbm_ijin = CommonHelpers::statusPerijinan();
        $presenceId = $request->get('presenceId');
        $dayname = date('D', strtotime(today()));
        if ($dayname == "Fri") {
            $openPresences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->limit(3)->get();
        } elseif ($dayname == "Sun") {
            $openPresences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->limit(1)->get();
        } else {
            $openPresences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->limit(2)->get();
        }

        return view('presence.create_my_permit', ['openPresences' => $openPresences, 'presenceId' => $presenceId, 'data_kbm_ijin' => $data_kbm_ijin]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_my_ranged_permit(Request $request)
    {
        $data_kbm_ijin = CommonHelpers::statusPerijinan();
        $presenceGroups = PresenceGroup::all();

        return view('presence.create_my_ranged_permit', ['presenceGroups' => $presenceGroups, 'data_kbm_ijin' => $data_kbm_ijin]);
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_my_permit(Request $request)
    {
        $data_kbm_ijin = CommonHelpers::statusPerijinan();
        $presenceIdToInsert = $request->input('fkPresence_id');
        if (strlen($request->input('reason')) < 10) {
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Masukkan alasan minimal 10 karakter']);
        }
        if ($data_kbm_ijin['status']) {
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

            if (isset($existingPresent)) {
                $del = Present::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santri->id);
                $del->delete();
                // return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['presence_already_exists' => 'Santri sudah hadir pada presensi tersebut. Tidak bisa izin.']);
            }

            $add_ss = $request->input('status_ss');
            $add_ss_k = '';
            if (isset($add_ss)) {
                $add_ss = '- Status SS: ' . $add_ss;
                $add_ss_k = $add_ss;
                if (str_contains($request->input('status_ss'), 'Belum')) {
                    $add_ss_k = $add_ss_k . '

*NB: Silahkan Dewan Guru mempersiapkan SS kepada yang bersangkutan dan dikirim melalui WA*';
                }
            } else {
                if (str_contains($request->input('reason_category'), 'Pulang -')) {
                    return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Status SS harus dipilih.']);
                }
            }

            $ids = uniqid();
            $inserted = false;
            $inserted = Permit::create([
                'fkSantri_id' => $santri->id,
                'fkPresence_id' => $request->input('fkPresence_id'),
                'reason' => $request->input('reason'),
                'reason_category' => $request->input('reason_category'),
                'status' => 'approved', // default (apabila ijin tidak sesuai baru di reject)
                'ids' => $ids,
                'status_ss' => $request->input('status_ss'),
                'ijin_kuota' => ($data_kbm_ijin['ijin'] + 1).'/'.$data_kbm_ijin['kuota']
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
- Presensi: ' . $presence->name . '
- Alasan: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Perijinan ke: *' . ($data_kbm_ijin['ijin'] + 1) . ' (dari Kuota ' . $data_kbm_ijin['kuota'] . ')*
' . $add_ss_k;

                $caption_ortu = '*[Perijinan Dari ' . $santri->user->fullname . ']*
' . $lorong . '
- Presensi: ' . $presence->name . '
- Alasan:  [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Perijinan ke: *' . ($data_kbm_ijin['ijin'] + 1) . ' (dari Kuota ' . $data_kbm_ijin['kuota'] . ')*
' . $add_ss;

                WaSchedules::insertToKetertiban($santri, $caption, $caption_ortu);
                return redirect()->route('my presence permits')->with('success', 'Berhasil membuat izin. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
            } else {
                return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Gagal membuat izin.']);
            }
        } else {
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Mohon maaf, Kuota ijin Anda sudah habis :(']);
        }
    }

    /**
     * Show the specified presence group.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_my_ranged_permit(Request $request)
    {
        $data_kbm_ijin = CommonHelpers::statusPerijinan();
        $presenceIdToInsert = $request->input('fkPresence_id');
        if ($data_kbm_ijin['status']) {
            // get current santri
            $santri = Santri::find(auth()->user()->santri->id);

            if (!$santri)
                return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

            $request->validate([
                'reason' => 'required|string',
                'reason_category' => 'required|string',
                'from_date' => 'required|date|before:to_date',
                'to_date' => 'required|date|after:from_date',
                'fkPresenceGroup_id' => 'required',
                // 'fkPresenceGroup_id' => 'required|exists:presence_groups,id|integer',
            ]);

            // check whether another ranged permit already exists within similar range
            if ($request->input('fkPresenceGroup_id') == 'all-kbm') {
                $all_presence = PresenceGroup::get();
                $pres_name = 'KBM Shubuh, KBM Malam, Apel Malam, MM Drh';
            } else {
                $all_presence = PresenceGroup::where('id', $request->input('fkPresenceGroup_id'))->get();
                $pres_name = $all_presence[0]->name;
            }
            foreach ($all_presence as $pres) {
                $existingRangedPermit = RangedPermitGenerator::orWhere(function ($query) use ($request, $santri, $pres) {
                    $query->where('fkSantri_id', $santri->id);
                    $query->where('fkPresenceGroup_id', $pres->id);
                    $query->whereDate('from_date', '>=', $request->input('from_date'));
                    $query->whereDate('from_date', '<=', $request->input('to_date'));
                })
                    ->orWhere(function ($query) use ($request, $santri, $pres) {
                        $query->where('fkSantri_id', $santri->id);
                        $query->where('fkPresenceGroup_id', $pres->id);
                        $query->whereDate('to_date', '>=', $request->input('from_date'));
                        $query->whereDate('to_date', '<=', $request->input('to_date'));
                    })
                    ->orWhere(function ($query) use ($request, $santri, $pres) {
                        $query->where('fkSantri_id', $santri->id);
                        $query->where('fkPresenceGroup_id', $pres->id);
                        $query->whereDate('from_date', '<=', $request->input('from_date'));
                        $query->whereDate('to_date', '>=', $request->input('from_date'));
                    })
                    ->orWhere(function ($query) use ($request, $santri, $pres) {
                        $query->where('fkSantri_id', $santri->id);
                        $query->where('fkPresenceGroup_id', $pres->id);
                        $query->whereDate('from_date', '<=', $request->input('to_date'));
                        $query->whereDate('to_date', '>=', $request->input('to_date'));
                    })
                    ->first();

                if (isset($existingRangedPermit))
                    return redirect()->route('ranged presence permit submission')->withErrors(['ranged_permit_already_exists' => 'Izin berjangka pada presensi tersebut dengan waktu yang sama sudah sudah ada.']);


                $add_ss = $request->input('status_ss');
                $add_ss_k = '';
                if (isset($add_ss)) {
                    $add_ss = '- Status SS: ' . $add_ss;
                    $add_ss_k = $add_ss;
                    if (str_contains($request->input('status_ss'), 'Belum')) {
                        $add_ss_k = $add_ss_k . '

*NB: Silahkan Dewan Guru mempersiapkan SS kepada yang bersangkutan dan dikirim melalui WA*';
                    }
                } else {
                    if (str_contains($request->input('reason_category'), 'Pulang -') || $request->input('reason_category')=='Magang') {
                        return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Status SS harus dipilih.']);
                    }
                }

                // now let's insert permits to existing presences.
                $createdPresences = Presence::whereDate('event_date', '>=', $request->input('from_date'))
                    ->whereDate('event_date', '<=', $request->input('to_date'))
                    ->where('fkPresence_group_id', $pres->id)
                    ->get();

                $loop_ijin = 1;
                foreach ($createdPresences as $presence) {
                    // check whether permit already exists
                    $existingPermit = Permit::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri->id)->first();

                    if (isset($existingPermit))
                        continue;

                    // check whether the person is present at that presence
                    $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri->id)->first();

                    if (isset($existingPresent))
                        continue;

                    $statusx = 'pending';
                    $updated_by = '';
                    // if ($presence->event_date == date('Y-m-d')) {
                    //     $statusx = 'approved';
                    //     $updated_by = 'System';
                    // }
                    $ids = uniqid();
                    $inserted = Permit::create([
                        'fkSantri_id' => $santri->id,
                        'fkPresence_id' => $presence->id,
                        'reason' => $request->input('reason'),
                        'reason_category' => $request->input('reason_category'),
                        'status' => $statusx,
                        'approved_by' => $updated_by,
                        'ids' => $ids,
                        'status_ss' => $request->input('status_ss'),
                        'ijin_kuota' => ($data_kbm_ijin['ijin'] + $loop_ijin).'/'.$data_kbm_ijin['kuota']
                    ]);
                    $loop_ijin++;
                }

                // create generator
                $inserted = RangedPermitGenerator::create([
                    'fkSantri_id' => $santri->id,
                    'fkPresenceGroup_id' => $pres->id,
                    'reason' => $request->input('reason'),
                    'reason_category' => $request->input('reason_category'),
                    'from_date' => $request->input('from_date'),
                    'to_date' => $request->input('to_date'),
                ]);
            }

            if ($inserted) {
                if ($santri->fkLorong_id == '') {
                    $lorong = '*Koor Lorong*';
                } else {
                    $lorong = '*' . $santri->lorong->name . '*';
                }

                $caption = '*[Perijinan Dari ' . $santri->user->fullname . ']*
' . $lorong . '
- Presensi: ' . $pres_name . '
- Kategori: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Tanggal: ' . $request->input('from_date') . ' s.d. ' . $request->input('to_date') . '
' . $add_ss_k.'
*PERLU PERSETUJUAN PENGURUS*';

                $caption_ortu = '*[Perijinan Dari ' . $santri->user->fullname . ']*
' . $lorong . '
- Presensi: ' . $pres_name . '
- Kategori: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Tanggal: ' . $request->input('from_date') . ' s.d. ' . $request->input('to_date') . '
' . $add_ss.'
*PERLU PERSETUJUAN PENGURUS*';

                WaSchedules::insertToKetertiban($santri, $caption, $caption_ortu);

                return redirect()->route('my presence permits')->with('success', 'Berhasil membuat izin berjangka, silakan cek daftar izin kamu. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
            } else {
                return redirect()->route('presence permit submission')->withErrors(['failed_adding_permit' => 'Gagal membuat izin berjangka.']);
            }
        } else {
            return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Mohon maaf, Kuota ijin Anda sudah habis :(']);
        }
    }

    public function delete_my_ranged_permit($id)
    {
        // get current santri
        // $santri = auth()->user()->santri;

        // if (!$santri)
        //     return redirect()->route('my presence permits')->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

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
            $openPresences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->limit(5)->get();
        } else {
            $openPresences = Presence::where('is_deleted', 0)->orderBy('event_date', 'DESC')->limit(4)->get();
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

        $presenceGroups = PresenceGroup::all();

        // $usersWithSantri = User::whereHas('santri', function($query) {
        //     $query->whereNull('exit_at');
        // })->orderBy('fullname')->get();

        return view('presence.create_permit', ['openPresences' => $openPresences, 'usersWithSantri' => $usersWithSantri, 'presenceGroups' => $presenceGroups]);
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

        if (isset($existingPresent)) {
            $del = Present::where('fkPresence_id', $request->input('fkPresence_id'))->where('fkSantri_id', $santriId);
            $del->delete();
            // return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['presence_already_exists' => 'Santri sudah hadir pada presensi tersebut. Tidak bisa izin.']);
        }

        $add_ss = $request->input('status_ss');
        $add_ss_k = '';
        if (isset($add_ss)) {
            $add_ss = '- Status SS: ' . $add_ss;
            $add_ss_k = $add_ss;
            if (str_contains($request->input('status_ss'), 'Belum')) {
                $add_ss_k = $add_ss_k . '

*NB: Silahkan Dewan Guru mempersiapkan SS kepada yang bersangkutan dan dikirim melalui WA*';
            }
        } else {
            if (str_contains($request->input('reason_category'), 'Pulang -')) {
                return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Status SS harus dipilih.']);
            }
        }

        $data_kbm_ijin = CommonHelpers::statusPerijinan($santriId);

        $ids = uniqid();
        $inserted = Permit::create([
            'fkSantri_id' => $santriId,
            'fkPresence_id' => $request->input('fkPresence_id'),
            'reason' => $request->input('reason'),
            'reason_category' => $request->input('reason_category'),
            'status' => $request->input('status'),
            'ids' => $ids,
            'status_ss' => $request->input('status_ss'),
            'ijin_kuota' => ($data_kbm_ijin['ijin'] + 1).'/'.$data_kbm_ijin['kuota']
        ]);

        if ($inserted) {
            $santri = Santri::find($santriId);
            $presence = Presence::find($request->input('fkPresence_id'));
            if ($santri->fkLorong_id == '') {
                $lorong = '*Koor Lorong*';
            } else {
                $lorong = '*' . $santri->lorong->name . '*';
            }

            $need_approval = '';
            if($request->input('status')=='pending'){
                $need_approval = '*PERLU PERSETUJUAN PENGURUS*';
            }

            $caption = '*[Perijinan Dari ' . $santri->user->fullname . '] -> Diinput oleh ' . auth()->user()->fullname . '*
' . $lorong . '
- Presensi: ' . $presence->name . '
- Alasan: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Perijinan ke: *' . ($data_kbm_ijin['ijin'] + 1) . ' (dari Kuota ' . $data_kbm_ijin['kuota'] . ')*
' . $add_ss_k.$need_approval;

            $caption_ortu = '*[Perijinan Dari ' . $santri->user->fullname . '] -> Diinput oleh ' . auth()->user()->fullname . '*
' . $lorong . '
- Presensi: ' . $presence->name . '
- Alasan: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Perijinan ke: *' . ($data_kbm_ijin['ijin'] + 1) . ' (dari Kuota ' . $data_kbm_ijin['kuota'] . ')*
' . $add_ss.$need_approval;

            WaSchedules::insertToKetertiban($santri, $caption, $caption_ortu);

            return redirect()->route('presence permit approval')->with('success', 'Berhasil membuat izin. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
        } else {
            return redirect()->route('create presence permit')->withErrors(['failed_adding_permit' => 'Gagal membuat izin.']);
        }
    }

    public function store_permit_ranged(Request $request)
    {
        // get current santri
        $santri = Santri::find($request->input('fkSantri_id'));
        $request->validate([
            'reason' => 'required|string',
            'reason_category' => 'required|string',
            'from_date' => 'required|date|before:to_date',
            'to_date' => 'required|date|after:from_date',
            'fkPresenceGroup_id' => 'required',
            // 'fkPresenceGroup_id' => 'required|exists:presence_groups,id|integer',
        ]);

        // check whether another ranged permit already exists within similar range
        if ($request->input('fkPresenceGroup_id') == 'all-kbm') {
            $all_presence = PresenceGroup::get();
            $pres_name = 'KBM Shubuh, KBM Malam, Apel Malam, MM Drh';
        } else {
            $all_presence = PresenceGroup::where('id', $request->input('fkPresenceGroup_id'))->get();
            $pres_name = $all_presence[0]->name;
        }

        $data_kbm_ijin = CommonHelpers::statusPerijinan($request->input('fkSantri_id'));
        foreach ($all_presence as $pres) {
            $existingRangedPermit = RangedPermitGenerator::orWhere(function ($query) use ($request, $santri, $pres) {
                $query->where('fkSantri_id', $santri->id);
                $query->where('fkPresenceGroup_id', $pres->id);
                $query->whereDate('from_date', '>=', $request->input('from_date'));
                $query->whereDate('from_date', '<=', $request->input('to_date'));
            })
                ->orWhere(function ($query) use ($request, $santri, $pres) {
                    $query->where('fkSantri_id', $santri->id);
                    $query->where('fkPresenceGroup_id', $pres->id);
                    $query->whereDate('to_date', '>=', $request->input('from_date'));
                    $query->whereDate('to_date', '<=', $request->input('to_date'));
                })
                ->orWhere(function ($query) use ($request, $santri, $pres) {
                    $query->where('fkSantri_id', $santri->id);
                    $query->where('fkPresenceGroup_id', $pres->id);
                    $query->whereDate('from_date', '<=', $request->input('from_date'));
                    $query->whereDate('to_date', '>=', $request->input('from_date'));
                })
                ->orWhere(function ($query) use ($request, $santri, $pres) {
                    $query->where('fkSantri_id', $santri->id);
                    $query->where('fkPresenceGroup_id', $pres->id);
                    $query->whereDate('from_date', '<=', $request->input('to_date'));
                    $query->whereDate('to_date', '>=', $request->input('to_date'));
                })
                ->first();

            if (isset($existingRangedPermit))
                return redirect()->route('ranged presence permit submission')->withErrors(['ranged_permit_already_exists' => 'Izin berjangka pada presensi tersebut dengan waktu yang sama sudah sudah ada.']);

            $add_ss = $request->input('status_ss');
            $add_ss_k = '';
            if (isset($add_ss)) {
                $add_ss = '- Status SS: ' . $add_ss;
                $add_ss_k = $add_ss;
                if (str_contains($request->input('status_ss'), 'Belum')) {
                    $add_ss_k = $add_ss_k . '

*NB: Silahkan Dewan Guru mempersiapkan SS kepada yang bersangkutan dan dikirim melalui WA*';
                }
            } else {
                if (str_contains($request->input('reason_category'), 'Pulang -') || $request->input('reason_category')=='Magang') {
                    return redirect()->route('presence permit submission', $presenceIdToInsert)->withErrors(['failed_adding_permit' => 'Status SS harus dipilih.']);
                }
            }

            // now let's insert permits to existing presences.
            $createdPresences = Presence::whereDate('event_date', '>=', $request->input('from_date'))
                ->whereDate('event_date', '<=', $request->input('to_date'))
                ->where('fkPresence_group_id', $pres->id)
                ->get();

            $loop_ijin = 1;
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
                    'status' => $request->input('status'),
                    'approved_by' => 'system',
                    'ids' => $ids,
                    'status_ss' => $request->input('status_ss'),
                    'ijin_kuota' => ($data_kbm_ijin['ijin'] + $loop_ijin).'/'.$data_kbm_ijin['kuota']
                ]);
                $loop_ijin++;
            }

            // create generator
            $inserted = RangedPermitGenerator::create([
                'fkSantri_id' => $santri->id,
                'fkPresenceGroup_id' => $pres->id,
                'reason' => $request->input('reason'),
                'reason_category' => $request->input('reason_category'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ]);
        }

        if ($inserted) {
            if ($santri->fkLorong_id == '') {
                $lorong = '*Koor Lorong*';
            } else {
                $lorong = '*' . $santri->lorong->name . '*';
            }

            $sttijn = '';
            if ($request->input('status') == 'pending') {
                $sttijn = 'Status: *Pending (Perlu persetujuan, amshol cek di Sisfo)*';
            }

            $caption = '*[Perijinan Dari ' . $santri->user->fullname . '] -> Diinput oleh ' . auth()->user()->fullname . '*
' . $lorong . '
- Presensi: ' . $pres_name . '
- Alasan: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Tanggal: ' . $request->input('from_date') . ' s.d. ' . $request->input('to_date') . '
' . $sttijn . '
' . $add_ss_k;

            if ($request->input('status') == 'pending') {
                $sttijn = 'Status: *Pending*';
            }
            $caption_ortu = '*[Perijinan Dari ' . $santri->user->fullname . '] -> Diinput oleh ' . auth()->user()->fullname . '*
' . $lorong . '
- Presensi: ' . $pres_name . '
- Alasan: [' . $request->input('reason_category') . '] ' . $request->input('reason') . '
- Tanggal: ' . $request->input('from_date') . ' s.d. ' . $request->input('to_date') . '
' . $sttijn . '
' . $add_ss;

            WaSchedules::insertToKetertiban($santri, $caption, $caption_ortu);

            return redirect()->route('presence permit approval')->with('success', 'Berhasil membuat izin berjangka, silakan cek daftar izin. Semoga Allah paring pengampunan, aman selamat lancar barokah. Alhamdulillah jazakumullahu khoiro.');
        } else {
            return redirect()->route('create presence permit')->withErrors(['failed_adding_permit' => 'Gagal membuat izin berjangka.']);
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

        $openPresences = Presence::where('is_deleted', 0)->whereDate('start_date_time', date('Y-m-d'))->get();

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


    public function delete_and_present(Request $request)
    {
        $presenceId = $request->get('presence_id');
        $santriId = $request->get('santri_id');

        $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId);
        if ($permit->delete()) {
            $check_present = Present::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();

            if ($check_present == null) {
                $present = Present::create([
                    'fkSantri_id' => $santriId,
                    'fkPresence_id' => $presenceId,
                    'is_late' => 0,
                    'updated_by' => auth()->user()->fullname,
                    'metadata' => $_SERVER['HTTP_USER_AGENT']
                ]);
                if ($present) {
                    return json_encode(array("status" => true));
                } else {
                    return json_encode(array("status" => false, 'message' => $check_present->santri->user->fullname . ' gagal dihadirkan pada presensi ini'));
                }
            } else {
                return json_encode(array("status" => false, 'message' => $check_present->santri->user->fullname . ' sudah hadir pada presensi ini'));
            }
        }
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_permit(Request $request)
    {
        $setting = Settings::find(1);
        $json = $request->get('json');
        if (isset($json)) {
            if ($json) {
                $caption = '*' . auth()->user()->fullname . '* Menghapus perijinan dari:
';
                $data_json = json_decode($request->get('data_json'));

                foreach ($data_json as $dt) {
                    $presenceId = $dt[0];
                    $santriId = $dt[1];

                    $permit = Permit::where('fkPresence_id', $presenceId)->where('fkSantri_id', $santriId)->first();
                    if ($permit) {
                        $permit->delete();

                        $caption = $caption . '- *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason . '
';
                    }
                }

                WaSchedules::save('Permit Delete', $caption, $setting->wa_info_presensi_group_id, null, true);
                return json_encode(['status' => true, 'message' => 'Izin berhasil dihapus']);
            }
        } else {
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
}

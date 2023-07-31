<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\SystemMetaData;

use Carbon\Carbon;

class PublicController extends Controller
{
    public function view_daily_public_presences_recaps($year, $month, $date, $id = null)
    {
        $presencesInDate = Presence::whereDate('event_date', '=', "$year-$month-$date")->get();

        $presence = $id ? Presence::find($id) : null;

        $presents = $presence ?
            $presence->presents()
            ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
            ->join('users', 'users.id', '=', 'santris.fkUser_id')
            ->orderBy('users.fullname')
            ->get()
            :
            [];

        $permits = $id ? Permit::where('fkPresence_id', $id)->where('status', 'approved')->get() : [];

        return view('presence.view_daily_public_presences_recaps', ['presence' => $presence, 'permits' => $permits, 'presents' => $presents, 'presencesInDate' => $presencesInDate, 'year' => $year, 'month' => $month, 'date' => $date]);
    }

    public function view_permit($ids)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        if ($permit != null) {
            if ($permit->status == 'approved') {
            } else {
                $message = 'Permintaan ijin sudah ditolak.';
            }
        } else {
            $message = 'Perijinan tidak ditemukan.';
        }
        return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }

    public function reject_permit($ids)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        if ($permit != null) {
            if ($permit->status == 'approved') {
                $permit->status = 'rejected';
                if ($permit->save()) {
                    $message = 'Permintaan ijin berhasil ditolak.';
                }
            } else {
                $message = 'Permintaan ijin sudah ditolak.';
            }
        } else {
            $message = 'Perijinan tidak ditemukan.';
        }

        return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }
}

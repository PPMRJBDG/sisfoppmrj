<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Permit;
use App\Models\User;

class PublicController extends Controller
{
    public function view_daily_public_presences_recaps($year, $month, $date, $angkatan)
    {
        $presencesInDate = Presence::whereDate('event_date', '=', "$year-$month-$date")->get();
        $mahasiswa = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->whereHas('santri', function ($query) use ($angkatan) {
            $query->where('angkatan', $angkatan);
        })->orderBy('fullname', 'asc')->get();
        $presents = array();
        foreach ($presencesInDate as $pid) {
            $presence = Presence::find($pid->id);
            $presents[$pid->id] = $presence ?
                $presence->presents()
                ->select('presents.*')
                ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                ->join('users', 'users.id', '=', 'santris.fkUser_id')
                ->where('angkatan', $angkatan)
                ->orderBy('users.fullname')
                ->get()
                :
                null;

            $permits[$pid->id] = $presence ?
                Permit::where('fkPresence_id', [$pid->id])->where('status', 'approved')->get()
                :
                null;
        }
        // echo var_dump(($presents[549]));
        // exit;

        return view('presence.view_daily_public_presences_recaps', [
            'mahasiswa' => $mahasiswa,
            'presence' => $presence,
            'permits' => $permits,
            'presents' => $presents,
            'presencesInDate' => $presencesInDate,
            'year' => $year,
            'month' => $month,
            'date' => $date,
            'angkatan' => $angkatan
        ]);
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

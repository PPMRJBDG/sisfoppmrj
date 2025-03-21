<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Present;
use App\Models\Permit;
use App\Models\PmbPanitias;

class Santri extends Model
{
    protected $fillable = [
        'nis',
        'fkUser_id',
        'angkatan',
        'nama_ortu',
        'nohp_ortu',
        'fkLorong_id',
        'join_at',
        'exit_at',
        'alasan_keluar',
        'ids',
        'template_fs1',
        'jaga_malam',
        'fkLaporan_keamanan_id'
    ];

    public function panitiaPmb()
    {
        return $this->hasOne(PmbPanitias::class, 'fkSantri_id');
    }

    public function monitoringMateris()
    {
        return $this->hasMany(MonitoringMateri::class, 'fkSantri_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'fkUser_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function lorong()
    {
        return $this->belongsTo(Lorong::class, 'fkLorong_id');
    }

    public function lorongUnderLead()
    {
        return $this->hasOne(Lorong::class, 'fkSantri_leaderId');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function presents()
    {
        return $this->hasMany(Present::class, 'fkPresence_id');
    }

    public function recapPresentsByMonthYear($month, $year, $presenceGroupId)
    {
        /**
         * percentage = (total user's present in month/year AND in presence which in presence group X) / (total presences of presence group in month/year) * 100
         */

        // TODO: PERMIT

        $totalPresentsInDate = Present::where('fkSantri_id', $this->id)->whereHas('presence', function ($query) use ($month, $year, $presenceGroupId) {
            $query->whereMonth('event_date', $month)->whereYear('event_date', $year);

            $query->whereHas('presenceGroup', function ($query) use ($presenceGroupId) {
                $query->where('id', $presenceGroupId);
            });
        })->count();

        $totalPresencesInDate = Presence::where('is_deleted', 0)->where('fkPresence_group_id', $presenceGroupId)->whereMonth('event_date', $month)->whereYear('event_date', $year)->count();

        $totalPermitsInDate = Permit::whereHas('presence', function ($query) use ($presenceGroupId, $month, $year) {
            $query->where('fkPresence_group_id', $presenceGroupId)->whereMonth('event_date', $month)->whereYear('event_date', $year);
        })->where('fkSantri_id', $this->id)->where('status', 'approved')->count();

        if ($totalPresencesInDate > 0)
            // permits are considered as present
            $percentage = ($totalPresentsInDate + $totalPermitsInDate) / $totalPresencesInDate * 100;
        else
            $percentage = 0;

        return [
            'percentage' => $percentage,
            'totalPermits' => $totalPermitsInDate
        ];
    }

    public function recapPresentsByRange($fromDate, $toDate, $presenceGroupId)
    {
        /**
         * percentage = (total user's present in month/year AND in presence which in presence group X) / (total presences of presence group in month/year) * 100
         */

        // TODO: PERMIT

        $totalPresentsInDate = Present::where('fkSantri_id', $this->id)->whereHas('presence', function ($query) use ($fromDate, $toDate, $presenceGroupId) {
            $query->whereDate('event_date', '>=', $fromDate)->whereDate('event_date', '<=', $toDate);

            $query->whereHas('presenceGroup', function ($query) use ($presenceGroupId) {
                $query->where('id', $presenceGroupId);
            });
        })->count();

        $totalPresencesInDate = Presence::where('is_deleted', 0)->where('fkPresence_group_id', $presenceGroupId)->whereDate('event_date', '>=', $fromDate)->whereDate('event_date', '<=', $toDate)->count();

        $totalPermitsInDate = Permit::whereHas('presence', function ($query) use ($presenceGroupId, $fromDate, $toDate) {
            $query->where('fkPresence_group_id', $presenceGroupId)->whereDate('event_date', '>=', $fromDate)->whereDate('event_date', '<=', $toDate);
        })->where('fkSantri_id', $this->id)->where('status', 'approved')->count();

        if ($totalPresencesInDate > 0)
            // permits are considered as present
            $percentage = ($totalPresentsInDate + $totalPermitsInDate) / $totalPresencesInDate * 100;
        else
            $percentage = 0;

        return [
            'percentage' => $percentage,
            'totalPermits' => $totalPermitsInDate
        ];
    }
}

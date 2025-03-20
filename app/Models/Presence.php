<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PresenceGroup;
use App\Models\Santri;
use App\Models\Present;
use App\Models\DewanPengajars;

class Presence extends Model
{
    protected $fillable = [
        'name',
        'start_date_time',
        'end_date_time',
        'presence_start_date_time',
        'presence_end_date_time',
        'fkPresence_group_id',
        'event_date',
        'total_mhs',
        'fkDewan_pengajar_1',
        'fkDewan_pengajar_2',
        'is_hasda',
        'is_put_together',
        'is_deleted',
        'deleted_by',
        'sign_in_degur1',
        'sign_in_degur2',
        'status_terlambat_degur1',
        'status_terlambat_degur2',
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function dewanPengajar1()
    {
        return $this->belongsTo(DewanPengajars::class, 'fkDewan_pengajar_1');
    }
    public function dewanPengajar2()
    {
        return $this->belongsTo(DewanPengajars::class, 'fkDewan_pengajar_2');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function presenceGroup()
    {
        return $this->belongsTo(PresenceGroup::class, 'fkPresence_group_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function presents()
    {
        return $this->hasMany(Present::class, 'fkPresence_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function myPresent()
    {
        $santri = auth()->user()->santri;

        if (!$santri)
            return null;

        return Present::where('fkPresence_id', $this->id)->where('fkSantri_id', $santri->id)->first();
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function myPermit()
    {
        $santri = auth()->user()->santri;

        if (!$santri)
            return null;

        return Permit::where('fkPresence_id', $this->id)->where('fkSantri_id', $santri->id)->first();
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function summary()
    {
        $malePresents = $this->presents()->get()->filter(function ($value, $key) {
            return $value->santri->user->gender == 'male'
                && $value->santri->join_at <= $this->event_date
                && ($value->santri->exit_at >= $this->event_date
                    || $value->santri->exit_at == null);
        });

        $malePermitsCount = Permit::whereHas('santri.user', function ($query) {
            $query->where('gender', 'male');
        })->where('fkPresence_id', $this->id)->where('status', 'approved')->count();

        $femalePresents = $this->presents()->get()->filter(function ($value, $key) {
            return $value->santri->user->gender == 'female'
                && $value->santri->join_at <= $this->event_date
                && ($value->santri->exit_at >= $this->event_date
                    || $value->santri->exit_at == null);;
        });

        $femalePermitsCount = Permit::whereHas('santri.user', function ($query) {
            $query->where('gender', 'female');
        })->where('fkPresence_id', $this->id)->where('status', 'approved')->count();

        $totalSantris = Santri::where('join_at', '<=', $this->event_date)->where(function ($query) {
            $query->where('exit_at', '>=', $this->event_date);
            $query->orWhere('exit_at', null);
        })->count();

        $totalMaleSantris = Santri::where('join_at', '<=', $this->event_date)->where(function ($query) {
            $query->where('exit_at', '>=', $this->event_date);
            $query->orWhere('exit_at', null);
        })
            ->whereHas('user', function ($query) {
                $query->where('gender', '=', 'male');
            })->count();

        $totalFemaleSantris = Santri::where('join_at', '<=', $this->event_date)->where(function ($query) {
            $query->where('exit_at', '>=', $this->event_date);
            $query->orWhere('exit_at', null);
        })
            ->whereHas('user', function ($query) {
                $query->where('gender', '=', 'female');
            })->count();

        if ($totalSantris > 0)
            $totalPercentage = (sizeof($malePresents) + sizeof($femalePresents) + $malePermitsCount + $femalePermitsCount) / $totalSantris * 100;
        else
            $totalPercentage = 0;

        if ($totalMaleSantris > 0)
            $totalMalePercentage = (sizeof($malePresents) + $malePermitsCount) / $totalMaleSantris * 100;
        else
            $totalMalePercentage = 0;

        if ($totalFemaleSantris > 0)
            $totalFemalePercentage = (sizeof($femalePresents) + $femalePermitsCount) / $totalFemaleSantris * 100;
        else
            $totalFemalePercentage = 0;

        return [
            'totalMales' => sizeof($malePresents),
            'totalFemales' => sizeof($femalePresents),
            'total' => (sizeof($malePresents) + sizeof($femalePresents)),
            'totalPercentage' => $totalPercentage,
            'totalMalePercentage' => $totalMalePercentage,
            'totalFemalePercentage' => $totalFemalePercentage
        ];
    }
}

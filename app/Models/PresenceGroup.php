<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Presence;
use App\Models\Santri;
use Illuminate\Support\Str;

class PresenceGroup extends Model
{
    protected $fillable = [
        'name',
        'start_hour',
        'end_hour',
        'days',
        'show_summary_at_home',
        'status'
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function presences()
    {
        return $this->hasMany(Presence::class, 'fkPresence_group_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function days_in_bahasa()
    {
        $jadwal = strtolower($this->days);

        $jadwal = Str::replace('monday', 'senin', $jadwal);
        $jadwal = Str::replace('tuesday', 'selasa', $jadwal);
        $jadwal = Str::replace('wednesday', 'rabu', $jadwal);
        $jadwal = Str::replace('thursday', 'kamis', $jadwal);
        $jadwal = Str::replace('friday', 'jumat', $jadwal);
        $jadwal = Str::replace('saturday', 'sabtu', $jadwal);
        $jadwal = Str::replace('sunday', 'minggu', $jadwal);

        return $jadwal;
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function array_days()
    {
        $days = Str::replace(' ', '', $this->days);
        $days = explode(',', $days);

        return $days;
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function summary_in_range($fromDate, $toDate)
    {
        $santris = Santri::where('exit_at', '>=', date("Y-m-t", strtotime($toDate)))
            ->orWhereNull('exit_at')->get();

        $summary = [
            'avg_present_percentage' => 0,
            'total_presences' => 0,
            'presences' => [],
        ];

        $presencesInRange = Presence::where('fkPresence_group_id', $this->id)->whereDate('event_date', '>=', $fromDate)->whereDate('event_date', '<=', $toDate)->orderBy('event_date', 'ASC')->get();

        if(sizeof($presencesInRange) > 0)
        {
            // getting avg present percentage
            foreach($presencesInRange as $presence)
            {
                $summary['avg_present_percentage'] += $presence->summary()['totalPercentage'];
            }

            // add permits to percentage
    
            $summary['avg_present_percentage'] /= sizeof($presencesInRange);
            // $summary['avg_present_percentage'] = $summary['avg_present_percentage'] / sizeof($santris) * 100;
        }
        else
        {
            $summary['avg_present_percentage'] = 0;
        }

        // getting total presences
        $summary['total_presences'] = sizeof($presencesInRange);

        // getting presences
        $summary['presences'] = $presencesInRange;

        $presenceGroupId = $this->id;

        $summary['total_permits'] = Permit::whereHas('santri', function($query) use($presenceGroupId, $toDate)
        {
            $query->where('exit_at', '>=', date("Y-m-t", strtotime($toDate)))
            ->orWhereNull('exit_at');

            // ($value->santri->exit_at >= $this->event_date
            //     || $value->santri->exit_at == null);
        })
        ->whereHas('presence', function($query) use($presenceGroupId, $fromDate, $toDate)
        {
            $query->where('fkPresence_group_id', $presenceGroupId)->whereDate('event_date', '>=', $fromDate)->whereDate('event_date', '<=', $toDate);

        })->where('status', 'approved')->count();

        return $summary;
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function summary_in_month($month, $year, $withDifference = true)
    {
        $santris = Santri::where('exit_at', '>=', date("Y-m-t", strtotime("$year-$month-01")))
            ->orWhereNull('exit_at')->get();

        $summary = [
            'avg_present_percentage' => 0,
            'total_presences' => 0,
            'presences' => [],
        ];

        $presencesInRange = Presence::where('fkPresence_group_id', $this->id)->whereMonth('event_date', $month)->whereYear('event_date', $year)->orderBy('event_date', 'ASC')->get();

        if(sizeof($presencesInRange) > 0)
        {
            // getting avg present percentage
            foreach($presencesInRange as $presence)
            {
                $summary['avg_present_percentage'] += $presence->summary()['totalPercentage'];
            }

            // add permits to percentage
    
            $summary['avg_present_percentage'] /= sizeof($presencesInRange);
            // $summary['avg_present_percentage'] = $summary['avg_present_percentage'] / sizeof($santris) * 100;
        }
        else
        {
            $summary['avg_present_percentage'] = 0;
        }

        // getting comparison with previous
        $previousMonth = $month - 1 > 0 ? $month - 1 : 12;
        $previousMonthsYear = $month - 1 > 0 ? $year : $year - 1;

        if($withDifference)
            $summary['difference_with_previous_month'] = $summary['avg_present_percentage'] - $this->summary_in_month($previousMonth, $previousMonthsYear, false)['avg_present_percentage'];

        // getting total presences
        $summary['total_presences'] = sizeof($presencesInRange);

        // getting presences
        $summary['presences'] = $presencesInRange;

        $presenceGroupId = $this->id;

        $summary['total_permits'] = Permit::whereHas('santri', function($query) use($presenceGroupId, $month, $year)
        {
            $query->where('exit_at', '>=', date("Y-m-t", strtotime("$year-$month-01")))
            ->orWhereNull('exit_at');

            // ($value->santri->exit_at >= $this->event_date
            //     || $value->santri->exit_at == null);
        })
        ->whereHas('presence', function($query) use($presenceGroupId, $month, $year)
        {
            $query->where('fkPresence_group_id', $presenceGroupId)->whereMonth('event_date', $month)->whereYear('event_date', $year);

        })->where('status', 'approved')->count();

        return $summary;
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function summary_in_year($year, $withDifference = true)
    {
        $summary = [
            'avg_present_percentage' => 0,
            'avg_present_percentage_monthly' => [],
            'difference_with_previous_year' => 0
        ];

        // loop through months (1-12)
        for($i = 1; $i < 13; $i++)
        {
            $summaryInMonth = $this->summary_in_month($i, $year);

            $summary['avg_present_percentage'] += $summaryInMonth['avg_present_percentage'];
            $summary['avg_present_percentage_monthly'][$i-1] = $summaryInMonth['avg_present_percentage'];
        }

        $summary['avg_present_percentage'] /= 12;

        // getting comparison with previous year
        $previousYear = $year - 1;

        if($withDifference)
        {
            $summary['difference_with_previous_year'] = $summary['avg_present_percentage'] - $this->summary_in_year($previousYear, false)['avg_present_percentage'];
        }

        return $summary;
    }
}


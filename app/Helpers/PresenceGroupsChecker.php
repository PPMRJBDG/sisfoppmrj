<?php

namespace App\Helpers;

use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\RangedPermitGenerator;
use App\Models\Permit;
use App\Models\Present;
use App\Models\Liburan;
use App\Models\Santri;
use App\Models\JadwalPengajars;
use App\Helpers\CountDashboard;

class PresenceGroupsChecker
{
    public static function checkPresenceGroups()
    {
        $results = [
            'created_presences' => [],
            'already_created_presences' => [],
            'presences_failed_to_create' => [],
            'presence_groups_have_schedules' => [],
            'day' => ''
        ];

        // get current day
        $currentDate = date('Y-m-d');
        $currentDateNumber = date('d');
        $currentMonth = date('m');
        $currentDay = strtolower(date('l'));
        $results['day'] = $currentDay;

        $check_liburan = Liburan::where('liburan_from', '<=', $currentDate)->where('liburan_to', '>=', $currentDate)->get();

        if (count($check_liburan) == 0 && $currentDay != 'sunday') {
            // load all presence groups that the 'days' attribute contains current day AND are ACTIVE
            $presenceGroups = PresenceGroup::where('days', 'LIKE', '%' . $currentDay . '%')->where('status', 'active')->get();

            $results['presence_groups_have_schedules'] = $presenceGroups;

            // start to check and create all of them
            foreach ($presenceGroups as $presenceGroup) {
                $presenceName = $presenceGroup->name . ' ' . $currentDateNumber . '/' . $currentMonth;

                // check if the presenceGroup already has a presence in this day
                $presenceInThisDate = Presence::where('fkPresence_group_id', $presenceGroup->id)
                    ->where('event_date', $currentDate)->first();

                if (isset($presenceInThisDate)) {
                    array_push($results['already_created_presences'], $presenceInThisDate);
                    continue;
                }

                $getPengajar1 = JadwalPengajars::where('fkPresence_group_id', $presenceGroup->id)
                    ->where('day', $currentDay)->where('ppm', 1)->first();
                $getPengajar2 = JadwalPengajars::where('fkPresence_group_id', $presenceGroup->id)
                    ->where('day', $currentDay)->where('ppm', 2)->first();

                $newPresenceInThisDate = Presence::create([
                    'fkPresence_group_id' => $presenceGroup->id,
                    'name' => $presenceName,
                    'event_date' => $currentDate,
                    'total_mhs' => CountDashboard::total_mhs('all'),
                    'start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->start_hour)),
                    'end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->end_hour)),
                    'presence_start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_start_hour)),
                    'presence_end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_end_hour)),
                    'fkDewan_pengajar_1' => ($getPengajar1 != null) ? $getPengajar1->fkDewan_pengajar_id : null,
                    'fkDewan_pengajar_2' => ($getPengajar2 != null) ? $getPengajar2->fkDewan_pengajar_id : null,
                    'is_deleted' => 0
                ]);

                if (!$newPresenceInThisDate) {
                    Log::error('Unable to a create Presence in PresenceGroup scheduling in process of inserting to DB.
                    Presence name: ' . $presenceName);

                    array_push($results['presences_failed_to_create'], $presenceName);

                    continue;
                }

                array_push($results['created_presences'], $presenceName);
            }
        }

        return $results;
    }

    public static function checkPermitGenerators()
    {
        $currentDate = date('Y-m-d');

        $rangedPermitGenerators = RangedPermitGenerator::whereDate('from_date', '<=', $currentDate)
            ->whereDate('to_date', '>=', $currentDate)
            ->where('status', 'approved')
            ->get();

        foreach ($rangedPermitGenerators as $rangedPermitGenerator) {

            // now let's insert permits to existing presences.
            $createdPresences = Presence::whereDate('event_date', '>=', $rangedPermitGenerator->from_date)
                ->whereDate('event_date', '<=', $rangedPermitGenerator->to_date)
                ->where('fkPresence_group_id', $rangedPermitGenerator->fkPresenceGroup_id)
                ->where('is_deleted', 0)
                ->get();

            foreach ($createdPresences as $presence) {
                $check_santri = Santri::where('id',$rangedPermitGenerator->fkSantri_id)->where('exit_at', null)->first();

                if($check_santri!=null){
                    // check whether permit already exists
                    $existingPermit = Permit::where('fkPresence_id', $presence->id)->where('fkSantri_id', $rangedPermitGenerator->fkSantri_id)->first();

                    if (isset($existingPermit))
                        continue;

                    // check whether the person is present at that presence
                    $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $rangedPermitGenerator->fkSantri_id);
                    if (isset($existingPresent)) {
                        $existingPresent->delete();
                    }

                    $inserted = Permit::create([
                        'fkSantri_id' => $rangedPermitGenerator->fkSantri_id,
                        'fkPresence_id' => $presence->id,
                        'reason' => $rangedPermitGenerator->reason,
                        'reason_category' => $rangedPermitGenerator->reason_category,
                        'status' => 'approved',
                        'approved_by' => 'system',
                        'ids' => uniqid()
                    ]);
                }
            }
        }
    }
}

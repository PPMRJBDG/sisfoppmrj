<?php

namespace App\Helpers;

use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\RangedPermitGenerator;
use App\Models\Permit;
use App\Models\Present;
use App\Models\Liburan;
use App\Models\Santri;
use App\Models\KalenderPpmTemplates;
use App\Models\KalenderPpms;
use App\Helpers\CountDashboard;

class PresenceGroupsChecker
{
    public static function checkPresenceGroups()
    {
        $currentDate = date('Y-m-d');
        $currentDay = strtolower(date('l'));

        $presenceGroups = PresenceGroup::where('days', 'LIKE', '%' . $currentDay . '%')->where('status', 'active')->get();

        foreach ($presenceGroups as $presenceGroup) {
            $presenceInThisDate = Presence::where('fkPresence_group_id', $presenceGroup->id)
                ->where('event_date', $currentDate)->first();

            if (isset($presenceInThisDate)) {
                if($presenceInThisDate->is_deleted==2){
                    $presenceInThisDate->is_deleted = 0;
                    $presenceInThisDate->save();
                }
            }
        }
    }

    public static function createPresence($update=false)
    {
        $results = [
            'created_presences' => [],
            'already_created_presences' => [],
            'presences_failed_to_create' => [],
            'presence_groups_have_schedules' => [],
            'day' => ''
        ];

        $tahun = date('Y');
        $bulan = date('m');
        $jumlah_tanggal = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        
        for($i=0; $i<$jumlah_tanggal; $i++){
            if(intval(date('d')) >= $i && $update){
                continue;
            }
            // get the first day of the month
            $currentDate = date('Y-m-d', strtotime('+'.$i.' day', strtotime(date('Y-m-00'))));
            $currentDateNumber = date_format(date_create($currentDate), 'd');
            $currentMonth = date_format(date_create($currentDate), 'm');
            $currentDay = strtolower(date_format(date_create($currentDate), 'l'));
            $results['day'] = $currentDay;

            $check_liburan = Liburan::where('liburan_from', '<=', $currentDate)->where('liburan_to', '>=', $currentDate)->get();

            if (count($check_liburan) == 0) { // && $currentDay != 'sunday'
                // load all presence groups that the 'days' attribute contains current day AND are ACTIVE
                $presenceGroups = PresenceGroup::where('days', 'LIKE', '%' . $currentDay . '%')->where('status', 'active')->get();

                $results['presence_groups_have_schedules'] = $presenceGroups;

                $data_calendar = PresenceGroupsChecker::getCalendar($tahun,intval($currentMonth),$currentDateNumber);
                // start to check and create all of them
                foreach ($presenceGroups as $presenceGroup) {
                    $presenceName = $presenceGroup->name . ' ' . $currentDateNumber . '/' . $currentMonth;

                    $degur_mt = null;
                    $degur_reg = null;
                    $degur_pemb = null;
                    $is_hasda = 0;
                    $is_put_together = 0;
                    
                    if($presenceGroup->id==1){ // shubuh
                        if($data_calendar['shubuh']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['shubuh']['nama'];
                            if (str_contains($data_calendar['shubuh']['nama'], 'HASDA-')) {
                                $is_hasda = 1;
                            }
                        }else{
                            $degur_mt = $data_calendar['shubuh']['mt']['id_degur'];
                            $degur_reg = $data_calendar['shubuh']['reguler']['id_degur'];
                            $degur_pemb = $data_calendar['shubuh']['pemb']['id_degur'];
                        }
                    }elseif($presenceGroup->id==2){ // malam
                        if($data_calendar['malam']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['malam']['nama'];
                            if (str_contains($data_calendar['malam']['nama'], 'HASDA-')) {
                                $is_hasda = 1;
                            }
                            if (str_contains($data_calendar['malam']['nama'], 'HASDA-TEKS') || 
                                str_contains($data_calendar['malam']['nama'], 'PENGARAHAN KHUSUS') || 
                                str_contains($data_calendar['malam']['nama'], 'HASDA-ORGANISASI') || 
                                str_contains($data_calendar['malam']['nama'], 'ASAD') ||
                                str_contains($data_calendar['malam']['nama'], 'PRA-PPM') ||
                                str_contains($data_calendar['malam']['nama'], 'SARASEHAN') ||
                                str_contains($data_calendar['malam']['nama'], 'MANAJEMEN') ||
                                str_contains($data_calendar['malam']['nama'], 'NASEHAT PENGURUS') ||
                                str_contains($data_calendar['malam']['nama'], 'PAT')) {
                                $is_put_together = 1;
                            }
                        }else{
                            $degur_mt = $data_calendar['malam']['mt']['id_degur'];
                            $degur_reg = $data_calendar['malam']['reguler']['id_degur'];
                            $degur_pemb = $data_calendar['malam']['pemb']['id_degur'];
                        }
                    }elseif($presenceGroup->id==8){ // bulanan
                        $is_put_together = 1;
                        if($data_calendar['shubuh']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['shubuh']['nama'];
                        }elseif($data_calendar['malam']['is_agenda_khusus']==1){
                            $presenceName .= " ".$data_calendar['malam']['nama'];
                        }else{
                            continue;
                        }
                    }

                    if(str_contains($presenceName, 'LIBUR')){
                        $delete = Presence::where('fkPresence_group_id', $presenceGroup->id)
                                    ->where('event_date', $currentDate)->first();
                        if($delete){
                            $delete->delete();
                        }
                        continue;
                    }

                    // sabtu malam tidak ada KBM kecuali xxx
                    if($currentDay=='saturday' && $presenceGroup->id==2 && !str_contains($presenceName, 'PRA-PPM') && !str_contains($presenceName, 'SARASEHAN') && !str_contains($presenceName, 'MANAJEMEN')){
                        continue;
                    }

                    // check if the presenceGroup already has a presence in this day
                    $presenceInThisDate = Presence::where('fkPresence_group_id', $presenceGroup->id)
                        ->where('event_date', $currentDate)->first();

                    if (isset($presenceInThisDate)) {
                        // array_push($results['already_created_presences'], $presenceInThisDate);
                        // continue;
                        
                        $newPresenceInThisDate = Presence::find($presenceInThisDate->id)->update([
                            'name' => strtoupper($presenceName),
                            'total_mhs' => CountDashboard::total_mhs('all'),
                            'start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->start_hour)),
                            'end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->end_hour)),
                            'presence_start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_start_hour)),
                            'presence_end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_end_hour)),
                            'pre_fkDewan_pengajar_mt' => $degur_mt,
                            'pre_fkDewan_pengajar_reg' => $degur_reg,
                            'pre_fkDewan_pengajar_pemb' => $degur_pemb,
                            'is_deleted' => ($i==0) ? 0 : 2,
                            'is_hasda' => $is_hasda,
                            'is_put_together' => $is_put_together,
                        ]);
                    }else{
                        $newPresenceInThisDate = Presence::create([
                            'fkPresence_group_id' => $presenceGroup->id,
                            'name' => strtoupper($presenceName),
                            'event_date' => $currentDate,
                            'total_mhs' => CountDashboard::total_mhs('all'),
                            'start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->start_hour)),
                            'end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->end_hour)),
                            'presence_start_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_start_hour)),
                            'presence_end_date_time' => date('Y-m-d H:i', strtotime($currentDate . ' ' . $presenceGroup->presence_end_hour)),
                            'pre_fkDewan_pengajar_mt' => $degur_mt,
                            'pre_fkDewan_pengajar_reg' => $degur_reg,
                            'pre_fkDewan_pengajar_pemb' => $degur_pemb,
                            'is_deleted' => ($i==0) ? 0 : 2,
                            'is_hasda' => $is_hasda,
                            'is_put_together' => $is_put_together,
                        ]);

                        if (!$newPresenceInThisDate) {
                            Log::error('Unable to a create Presence in PresenceGroup scheduling in process of inserting to DB.
                            Presence name: ' . $presenceName);

                            array_push($results['presences_failed_to_create'], $presenceName);

                            continue;
                        }
                    }

                    array_push($results['created_presences'], $presenceName);
                }
            }
        }

        return $results;
    }

    public static function getCalendar($year,$month,$today){ // 04 - 20
        $kalenders = KalenderPpms::get();
        $templates = KalenderPpmTemplates::orderBy('waktu', 'ASC')->get();

        $data_return = [];
        $start_seq = 0;
        $start_tgl = 0;
        if($kalenders){
            $kalenders1 = $kalenders->where('x',1)->where('bulan',$month)->first();
            if($kalenders1){
              $start_seq = $kalenders1->start; // 18
            }
            $kalenders2 = $kalenders->where('x',2)->where('bulan',$month)->first();
            if($kalenders2){
              $start_tgl = $kalenders2->start; // 12
            }
            $kalender_conditions = $kalenders->where('is_certain_conditions',1)->where('bulan',$month);


            $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
            $numberDays = date('t', $firstDayOfMonth);
            $currentDay = 1;

            while ($currentDay <= $numberDays) {
                if($start_tgl == $currentDay){
                    $start_seq = 1;
                }

                if($today==$currentDay){
                    $get_data = $templates->where('sequence',$start_seq); // 18
                    $certain_condition_waktu = "";
                    foreach($get_data as $dt){
                        $certain_condition = $kalender_conditions->where('waktu_certain_conditions',$dt->waktu)->where('start',$currentDay)->first();
                        if($certain_condition){
                            if($certain_condition_waktu != $dt->waktu){
                                // nama agenda khusus
                                $data_return[$dt->waktu]['is_agenda_khusus'] = 1;
                                $data_return[$dt->waktu]['nama'] = strtoupper($certain_condition->nama_certain_conditions);
                                $certain_condition_waktu = $dt->waktu;
                            }
                        }else{
                            if($dt->is_agenda_khusus){
                                $data_return[$dt->waktu]['is_agenda_khusus'] = 1;
                                $data_return[$dt->waktu]['nama'] = strtoupper($dt->nama_agenda_khusus);
                            }elseif($dt->pengajar){
                                $data_return[$dt->waktu]['is_agenda_khusus'] = 0;
                                $data_return[$dt->waktu][$dt->kelas]['nama'] = $dt->pengajar->name;
                                $data_return[$dt->waktu][$dt->kelas]['id_degur'] = $dt->pengajar->id;
                            }
                        }
                    }
                }

                $currentDay++;
                $start_seq++;
            }
        }
        return $data_return;
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

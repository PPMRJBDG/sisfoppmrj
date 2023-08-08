<?php

namespace App\Helpers;

use App\Models\SpWhatsappSchedules;
use App\Models\Settings;
use App\Models\Lorong;
use App\Models\SpWhatsappPhoneNumbers;

class WaSchedules
{
    public static function report_schedule($contact_id, $name, $caption)
    {
        $setting = Settings::find(1);
        $insert = SpWhatsappSchedules::create([
            'ids' => uniqid(),
            'team_id' => $setting->wa_team_id, // superadmin
            'type' => $setting->wa_type,
            'template' => $setting->wa_template,
            'accounts' => '["' . $setting->wa_sender_account_id . '"]', // akun WA pengirim
            'contact_id' => $contact_id, // nomor or group tujuan
            'time_post' => strtotime('+1 minutes'),
            'min_delay' => $setting->wa_min_delay,
            'max_delay' => $setting->wa_max_delay,
            'schedule_time' => '',
            'timezone' => 'Asia/Jakarta',
            'name' => $name,
            'caption' => $caption,
            'media' => '',
            'run' => 0,
            'status' => 1,
            'changed' => time(),
            'created' => time()
        ]);
        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    public static function save($santri, $caption, $contact_id)
    {
        $caption = $caption . '
        
*[SISFO PPMRJ]*';

        $setting = Settings::find(1);
        if ($contact_id == null) {
            $contact_id = $setting->wa_ketertiban_group_id;
        }

        if ($setting != null) {
            SpWhatsappSchedules::create([
                'ids' => uniqid(),
                'team_id' => $setting->wa_team_id, // superadmin
                'type' => $setting->wa_type,
                'template' => $setting->wa_template,
                'accounts' => '["' . $setting->wa_sender_account_id . '"]', // akun WA pengirim
                'contact_id' => $contact_id, // nomor or group tujuan
                'time_post' => strtotime('+1 minutes'),
                'min_delay' => $setting->wa_min_delay,
                'max_delay' => $setting->wa_max_delay,
                'schedule_time' => '',
                'timezone' => 'Asia/Jakarta',
                'name' => 'Perijinan Dari ' . $santri->user->fullname,
                'caption' => $caption,
                'media' => '',
                'run' => 0,
                'status' => 1,
                'changed' => time(),
                'created' => time()
            ]);
        }
    }

    public static function insertToKetertiban($santri, $caption)
    {
        // kirim ke group koor lorong
        WaSchedules::save($santri, $caption, null);
        // kirim ke koor lorong
        $koor_lorong = Lorong::find($santri->fkLorong_id);
        $setting = Settings::first();
        if ($koor_lorong != null && $setting != null) {
            $nohp = $koor_lorong->leader->user->nohp;
            if ($nohp != '') {
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $wa_phone = SpWhatsappPhoneNumbers::where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                if ($wa_phone != null) {
                    WaSchedules::save($santri, $caption, $wa_phone->pid);
                }
            }
        }
    }
}

<?php

namespace App\Helpers;

use App\Models\SpWhatsappSchedules;
use App\Models\Settings;
use App\Models\Lorong;
use App\Models\SpWhatsappPhoneNumbers;

class WaSchedules
{
    public static function save($name, $caption, $contact_id, $time_post = null, $without_header_footer = false)
    {
        $setting = Settings::find(1);
        if ($contact_id == 'wa_dewanguru_group_id') {
            $contact_id = $setting->wa_dewanguru_group_id;
        } elseif ($contact_id == 'wa_ketertiban_group_id') {
            $contact_id = $setting->wa_ketertiban_group_id;
        } elseif ($contact_id == 'Bulk Koor Lorong') {
            $contact = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) use ($contact_id) {
                $query->where('name', $contact_id);
            })->where('team_id', $setting->wa_team_id)->first();

            if ($contact != null) {
                $contact_id = $contact->pid;
            } else {
                $contact_id = 0;
            }
        }

        if ($without_header_footer) {
            $caption_body = $caption;
        } else {
            $caption_body = $setting->wa_header . '

' . $caption . '
        
' . $setting->wa_footer;
        }

        if ($time_post == null) {
            $time_post = strtotime('+1 minutes');
        } else {
            $time_post = strtotime('+' . $time_post . ' minutes');
        }
        if ($setting != null) {
            $xsend = SpWhatsappSchedules::create([
                'ids' => uniqid(),
                'team_id' => $setting->wa_team_id, // superadmin
                'type' => $setting->wa_type,
                'template' => $setting->wa_template,
                'accounts' => '["' . $setting->wa_sender_account_id . '"]', // akun WA pengirim
                'contact_id' => $contact_id, // nomor or group tujuan
                'time_post' => $time_post,
                'min_delay' => $setting->wa_min_delay,
                'max_delay' => $setting->wa_max_delay,
                'schedule_time' => '',
                'timezone' => 'Asia/Jakarta',
                'name' => $name,
                'caption' => $caption_body,
                'media' => '',
                'run' => 0,
                'status' => 1,
                'changed' => time(),
                'created' => time()
            ]);

            if ($xsend) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function getContactId($nohp)
    {
        $setting = Settings::first();
        if ($nohp != '') {
            if ($nohp[0] == '0') {
                $nohp = '62' . substr($nohp, 1);
            }
            $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                $query->where('name', 'NOT LIKE', '%Bulk%');
            })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
            if ($wa_phone != null) {
                return $wa_phone->pid;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function insertToKetertiban($santri, $caption, $caption_ortu)
    {
        $setting = Settings::first();

        // kirim ke group info ketertiban
        $nohp = $santri->user->nohp;
        if ($nohp != '') {
            if ($nohp[0] == '0') {
                $nohp = '62' . substr($nohp, 1);
            }
        }
        $caption = $caption.'
Hubungi: wa.me/'.$nohp;
        WaSchedules::save('Perijinan Dari ' . $santri->user->fullname, $caption, $setting->wa_info_presensi_group_id, null, true);

        // kirim ke ortu
        if ($caption_ortu != null) {
            $nohp_ortu = $santri->nohp_ortu;
            if ($nohp_ortu != '') {
                if ($nohp_ortu[0] == '0') {
                    $nohp_ortu = '62' . substr($nohp_ortu, 1);
                }
                $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                    $query->where('name', 'NOT LIKE', '%Bulk%');
                })->where('team_id', $setting->wa_team_id)->where('phone', $nohp_ortu)->first();
                if ($wa_phone != null) {
                    WaSchedules::save('Perijinan Dari ' . $santri->user->fullname.' ke Ortu', $caption_ortu, $wa_phone->pid, 5);
                }
            }
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'apps_name',
        'profileImgUrl',
        'bgImage',
        'wa_username',
        'wa_sender_account_id',
        'wa_team_id',
        'wa_type',
        'wa_template',
        'wa_min_delay',
        'wa_max_delay',
        'wa_ketertiban_group_id',
        'wa_ortu_group_id',
        'wa_maurus_group_id',
        'wa_dewanguru_group_id',
        'wa_info_presensi_group_id',
        'wa_keuangan_group_id',
        'wa_jam_malam_group_id',
        'host_url',
        'wa_header',
        'wa_footer',
        'wa_info_alpha_ortu',
        'wa_info_lulus',
        'wa_link_presensi_koor',
        // scheduler
        'wa_info_jaga_malam',
        'wa_info_tatatertib',
        'cloud_fs',
        'token_fs',
        'cron_daily',
        'cron_preview_daily',
        'cron_weekly',
        'cron_monthly',
        'cron_presence',
        'cron_jam_malam',
        'cron_nerobos',
        'cron_tatib',
        'cron_minutes',
        'reminder_kbm',
        'reminder_nerobos',
        'reminder_alpha_ortu',
        'status_perijinan',
        'status_scan_degur',
        'lock_calendar',
        'reminder_layanan_domain',
        'reminder_layanan_server',
        'reminder_layanan_fingerprint',
        'account_info'
    ];
}

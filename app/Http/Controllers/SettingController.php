<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sodaqoh;
use App\Models\Periode;
use App\Models\Liburan;
use App\Models\JenisPelanggaran;
use App\Models\SpAccounts;
use App\Models\Settings;
use App\Models\SpTeam;
use App\Models\SpUsers;
use App\Models\SpWhatsappContacts;
use App\Models\DewanPengajars;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\CommonHelpers;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $list_periode = Periode::get();
        $list_liburan = Liburan::orderBy('id', 'DESC')->limit(10)->get();
        $list_jenis_pelanggaran = JenisPelanggaran::get();
        $list_setting = Settings::first();
        $list_wa_user = SpUsers::where('username', $list_setting->wa_username)->get();
        // $total_santri = DB::table('v_user_santri')->get();
        $total_santri_tfs1 = DB::table('v_user_santri')->where('template_fs1', null)->get();
        $total_santri_tfs2 = DB::table('v_user_santri')->where('template_fs2', null)->get();
        $total_santri_tfs3 = DB::table('v_user_santri')->where('template_fs3', null)->get();
        $total_degur_tfs1 = DewanPengajars::where('cloud_fs1', null)->whereNotNull('pin')->get();
        $total_degur_tfs2 = DewanPengajars::where('cloud_fs2', null)->whereNotNull('pin')->get();
        $total_degur_tfs3 = DewanPengajars::where('cloud_fs3', null)->whereNotNull('pin')->get();

        $list_wa_team = null;
        $list_wa_account = null;
        $list_wa_group = null;
        if ($list_wa_user != null) {
            $id_user = array();
            foreach ($list_wa_user as $user) {
                $id_user[] = $user->id;
            }
            $list_wa_team = SpTeam::whereIn('owner', $id_user)->get();
        }
        if ($list_wa_team != null) {
            $id_team = array();
            foreach ($list_wa_team as $user) {
                $id_team[] = $user->id;
            }
            $list_wa_account = SpAccounts::where('social_network', 'whatsapp')->whereIn('team_id', $id_team)->get();
            $list_wa_group = SpWhatsappContacts::whereIn('team_id', $id_team)->where('name', 'LIKE', '%Group%')->get();
        }

        return view('setting', [
            'list_periode' => $list_periode,
            'list_liburan' => $list_liburan,
            'list_setting' => $list_setting,
            'list_wa_account' => $list_wa_account,
            'list_wa_team' => $list_wa_team,
            'list_wa_group' => $list_wa_group,
            'list_jenis_pelanggaran' => $list_jenis_pelanggaran,
            'total_santri_tfs1' => $total_santri_tfs1,
            'total_santri_tfs2' => $total_santri_tfs2,
            'total_santri_tfs3' => $total_santri_tfs3,
            'total_degur_tfs1' => $total_degur_tfs1,
            'total_degur_tfs2' => $total_degur_tfs2,
            'total_degur_tfs3' => $total_degur_tfs3,
            'cloud_fs' => CommonHelpers::settings()->cloud_fs,
        ]);
    }

    public function store_apps(Request $request)
    {
        $setting = Settings::find(1);
        if ($setting != null) {
            $setting->org_name = $request->org;
            $setting->apps_name = $request->apps;

            if ($request->hasFile('logoImg')) {
                $request->validate([
                    'logoImg' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
                ]);
                // Save the file locally in the storage/public/ folder under a new folder named /destinations
                $request->logoImg->store('logo-apps', 'public');
                $setting->logoImgUrl = $request->logoImg->hashName();
            }
            if ($request->hasFile('bgImg')) {
                $request->validate([
                    'bgImg' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
                ]);
                // Save the file locally in the storage/public/ folder under a new folder named /destinations
                $request->bgImg->store('logo-apps', 'public');
                $setting->bgImage = $request->bgImg->hashName();
            }

            $setting->save();
        }
        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil update aplikasi.');
    }

    public function store_settings(Request $request)
    {
        $setting = Settings::find(1);
        if ($setting->wa_username == '') {
            $request->validate([
                'host_url' => 'required',
                'wa_username' => 'required',
            ]);
        } else {
            $request->validate([
                'host_url' => 'required',
                'wa_username' => 'required',
                'wa_team_id' => 'required',
                'wa_sender_account_id' => 'required',
                'wa_type' => 'required',
                'wa_template' => 'required',
                'wa_min_delay' => 'required',
                'wa_max_delay' => 'required',
                'wa_header' => 'required',
                'wa_footer' => 'required',
                'wa_info_alpha_ortu' => 'required',
                'wa_info_jaga_malam' => 'required',
                'wa_info_lulus' => 'required',
                'status_perijinan' => 'required',
                'wa_link_presensi_koor' => 'required',
                'auto_generate_hadir' => 'required',
                'reminder_kbm' => 'required',
                'reminder_nerobos' => 'required',
                'reminder_alpha_ortu' => 'required',
                'status_scan_degur' => 'required',
            ]);
        }
        if ($setting == null) {
            Settings::create([
                'host_url' => $request->input('host_url'),
                'cloud_fs' => $request->input('cloud_fs'),
                'token_fs' => $request->input('token_fs'),
                'wa_username' => $request->input('wa_username'),
                'wa_team_id' => $request->input('wa_team_id'),
                'wa_sender_account_id' => $request->input('wa_sender_account_id'),
                'wa_type' => $request->input('wa_type'),
                'wa_template' => $request->input('wa_template'),
                'wa_min_delay' => $request->input('wa_min_delay'),
                'wa_max_delay' => $request->input('wa_max_delay'),
                'wa_ketertiban_group_id' => $request->input('wa_ketertiban_group_id'),
                'wa_info_presensi_group_id' => $request->input('wa_info_presensi_group_id'),
                'wa_ortu_group_id' => $request->input('wa_ortu_group_id'),
                'wa_maurus_group_id' => $request->input('wa_maurus_group_id'),
                'wa_dewanguru_group_id' => $request->input('wa_dewanguru_group_id'),
                'wa_keuangan_group_id' => $request->input('wa_keuangan_group_id'),
                'wa_jam_malam_group_id' => $request->input('wa_jam_malam_group_id'),
                'wa_om_group_id' => $request->input('wa_om_group_id'),
                'wa_header' => $request->input('wa_header'),
                'wa_footer' => $request->input('wa_footer'),
                'wa_info_alpha_ortu' => $request->input('wa_info_alpha_ortu'),
                'wa_info_jaga_malam' => $request->input('wa_info_jaga_malam'),
                'wa_info_lulus' => $request->input('wa_info_lulus'),
                'status_perijinan' => $request->input('status_perijinan'),
                'wa_link_presensi_koor' => $request->input('wa_link_presensi_koor'),
                'auto_generate_hadir' => $request->input('auto_generate_hadir'),
                'cron_daily' => $request->input('cron_daily'),
                'cron_preview_daily' => $request->input('cron_preview_daily'),
                'cron_weekly' => $request->input('cron_weekly'),
                'cron_monthly' => $request->input('cron_monthly'),
                'cron_presence' => $request->input('cron_presence'),
                'cron_jam_malam' => $request->input('cron_jam_malam'),
                'cron_nerobos' => $request->input('cron_nerobos'),
                'cron_tatib' => $request->input('cron_tatib'),
                'cron_minutes' => $request->input('cron_minutes'),
                'reminder_kbm' => $request->input('reminder_kbm'),
                'reminder_nerobos' => $request->input('reminder_nerobos'),
                'reminder_alpha_ortu' => $request->input('reminder_alpha_ortu'),
                'status_scan_degur' => $request->input('status_scan_degur'),
                'lock_calendar' => $request->input('lock_calendar'),
                'reminder_layanan_domain' => $request->input('reminder_layanan_domain'),
                'reminder_layanan_server' => $request->input('reminder_layanan_server'),
                'reminder_layanan_fingerprint' => $request->input('reminder_layanan_fingerprint'),
                'account_info' => $request->input('account_info'),
            ]);
        } else {
            $setting->host_url = $request->input('host_url');
            $setting->cloud_fs = $request->input('cloud_fs');
            $setting->token_fs = $request->input('token_fs');
            $setting->wa_username = $request->input('wa_username');
            $setting->wa_team_id = $request->input('wa_team_id');
            $setting->wa_sender_account_id = $request->input('wa_sender_account_id');
            $setting->wa_type = $request->input('wa_type');
            $setting->wa_template = $request->input('wa_template');
            $setting->wa_min_delay = $request->input('wa_min_delay');
            $setting->wa_max_delay = $request->input('wa_max_delay');
            $setting->wa_ketertiban_group_id = $request->input('wa_ketertiban_group_id');
            $setting->wa_info_presensi_group_id = $request->input('wa_info_presensi_group_id');
            $setting->wa_ortu_group_id = $request->input('wa_ortu_group_id');
            $setting->wa_maurus_group_id = $request->input('wa_maurus_group_id');
            $setting->wa_dewanguru_group_id = $request->input('wa_dewanguru_group_id');
            $setting->wa_keuangan_group_id = $request->input('wa_keuangan_group_id');
            $setting->wa_jam_malam_group_id = $request->input('wa_jam_malam_group_id');
            $setting->wa_om_group_id = $request->input('wa_om_group_id');
            $setting->wa_header = $request->input('wa_header');
            $setting->wa_footer = $request->input('wa_footer');
            $setting->wa_info_alpha_ortu = $request->input('wa_info_alpha_ortu');
            $setting->wa_info_jaga_malam = $request->input('wa_info_jaga_malam');
            $setting->wa_info_lulus = $request->input('wa_info_lulus');
            $setting->status_perijinan = $request->input('status_perijinan');
            $setting->wa_link_presensi_koor = $request->input('wa_link_presensi_koor');
            $setting->auto_generate_hadir = $request->input('auto_generate_hadir');
            $setting->cron_daily = $request->input('cron_daily');
            $setting->cron_preview_daily = $request->input('cron_preview_daily');
            $setting->cron_weekly = $request->input('cron_weekly');
            $setting->cron_monthly = $request->input('cron_monthly');
            $setting->cron_presence = $request->input('cron_presence');
            $setting->cron_jam_malam = $request->input('cron_jam_malam');
            $setting->cron_nerobos = $request->input('cron_nerobos');
            $setting->cron_tatib = $request->input('cron_tatib');
            $setting->cron_minutes = $request->input('cron_minutes');
            $setting->reminder_kbm = $request->input('reminder_kbm');
            $setting->reminder_nerobos = $request->input('reminder_nerobos');
            $setting->reminder_alpha_ortu = $request->input('reminder_alpha_ortu');
            $setting->status_scan_degur = $request->input('status_scan_degur');
            $setting->lock_calendar = $request->input('lock_calendar');
            $setting->reminder_layanan_domain = $request->input('reminder_layanan_domain');
            $setting->reminder_layanan_server = $request->input('reminder_layanan_server');
            $setting->reminder_layanan_fingerprint = $request->input('reminder_layanan_fingerprint');
            $setting->account_info = $request->input('account_info');
            $setting->save();
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil update setting.');
    }

    public function store_periode(Request $request)
    {
        $request->validate([
            'periode' => 'required'
        ]);
        Periode::create([
            'periode_tahun' => $request->input('periode')
        ]);

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil menambah periode.');
    }

    public function store_liburan(Request $request)
    {
        $request->validate([
            'liburan_from' => 'required',
            'liburan_to' => 'required',
            'keterangan' => 'required',
        ]);
        if($request->input('liburan_id')==""){
            Liburan::create([
                'liburan_from' => $request->input('liburan_from'),
                'liburan_to' => $request->input('liburan_to'),
                'keterangan' => $request->input('keterangan')
            ]);
        }else{
            $data = Liburan::find($request->input('liburan_id'));
            $data->liburan_from = $request->input('liburan_from');
            $data->liburan_to = $request->input('liburan_to');
            $data->keterangan = $request->input('keterangan');
            $data->save();
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil menambah liburan.');
    }

    public function store_jenis_pelanggaran(Request $request)
    {
        $request->validate([
            'jenis_pelanggaran' => 'required',
            'kategori_pelanggaran' => 'required',
        ]);
        if($request->input('pelanggaran_id')==""){
            JenisPelanggaran::create([
                'jenis_pelanggaran' => $request->input('jenis_pelanggaran'),
                'kategori_pelanggaran' => $request->input('kategori_pelanggaran')
            ]);
        }else{
            $data = JenisPelanggaran::find($request->input('pelanggaran_id'));
            $data->jenis_pelanggaran = $request->input('jenis_pelanggaran');
            $data->kategori_pelanggaran = $request->input('kategori_pelanggaran');
            $data->save();
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil menambah jenis pelanggaran.');
    }

    public function delete_periode($id)
    {
        $data = Periode::find($id);
        if (!$data)
            return redirect()->route('list setting', $id)->withErrors(['periode_not_found' => 'Periode tidak ditemukan.']);
        $data->delete();
        return redirect()->route('list setting', $id)->with('success', 'Berhasil menghapus periode');
    }

    public function delete_liburan($id)
    {
        $data = Liburan::find($id);
        if (!$data)
            return redirect()->route('list setting', $id)->withErrors(['liburan_not_found' => 'Liburan tidak ditemukan.']);
        $data->delete();
        return redirect()->route('list setting', $id)->with('success', 'Berhasil menghapus liburan');
    }

    public function delete_jenis_pelanggaran($id)
    {
        $data = JenisPelanggaran::find($id);
        if (!$data)
            return redirect()->route('list setting', $id)->withErrors(['liburan_not_found' => 'Jenis pelanggaran tidak ditemukan.']);
        $data->delete();
        return redirect()->route('list setting', $id)->with('success', 'Berhasil menghapus jenis pelanggaran');
    }

    public function store_generate_sodaqoh(Request $request)
    {
        $request->validate([
            'periode' => 'required',
            'nominal' => 'required',
        ]);
        $loop = 0;
        $view_usantri = DB::table('v_user_santri')->orderBy('fullname')->get();
        foreach ($view_usantri as $vusr) {
            $check = Sodaqoh::where('fkSantri_id', $vusr->santri_id)
                ->where('periode', $request->input('periode'))
                ->get();

            if (count($check) == null) {
                Sodaqoh::create([
                    'fkSantri_id' => $vusr->santri_id,
                    'periode' => $request->input('periode'),
                    'nominal' => $request->input('nominal')
                ]);
                $loop++;
            }
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list periode sodaqoh', [$request->input('periode'), '-']))->with('success', 'Berhasil generate sodaqoh tahunan ' . $loop . ' mahasiswa.');
    }
}

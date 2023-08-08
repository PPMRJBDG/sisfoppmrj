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
use Illuminate\Support\Facades\DB;

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
        $list_wa_user = SpUsers::where('username', 'ppmhs.roudhotuljannah')->get();
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
        ]);
    }

    public function store_settings(Request $request)
    {
        $request->validate([
            'host_url' => 'required',
            'wa_team_id' => 'required',
            'wa_sender_account_id' => 'required',
            'wa_type' => 'required',
            'wa_template' => 'required',
            'wa_min_delay' => 'required',
            'wa_max_delay' => 'required',
            'wa_ketertiban_group_id' => 'required',
            'wa_ortu_group_id' => 'required',
        ]);
        $setting = Settings::find(1);
        if ($setting == null) {
            Settings::create([
                'host_url' => $request->input('host_url'),
                'wa_team_id' => $request->input('wa_team_id'),
                'wa_sender_account_id' => $request->input('wa_sender_account_id'),
                'wa_type' => $request->input('wa_type'),
                'wa_template' => $request->input('wa_template'),
                'wa_min_delay' => $request->input('wa_min_delay'),
                'wa_max_delay' => $request->input('wa_max_delay'),
                'wa_ketertiban_group_id' => $request->input('wa_ketertiban_group_id'),
                'wa_ortu_group_id' => $request->input('wa_ortu_group_id'),
            ]);
        } else {
            $setting->host_url = $request->input('host_url');
            $setting->wa_team_id = $request->input('wa_team_id');
            $setting->wa_sender_account_id = $request->input('wa_sender_account_id');
            $setting->wa_type = $request->input('wa_type');
            $setting->wa_template = $request->input('wa_template');
            $setting->wa_min_delay = $request->input('wa_min_delay');
            $setting->wa_max_delay = $request->input('wa_max_delay');
            $setting->wa_ketertiban_group_id = $request->input('wa_ketertiban_group_id');
            $setting->wa_ortu_group_id = $request->input('wa_ortu_group_id');
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

        Liburan::create([
            'liburan_from' => $request->input('liburan_from'),
            'liburan_to' => $request->input('liburan_to'),
            'keterangan' => $request->input('keterangan')
        ]);

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil menambah liburan.');
    }

    public function store_jenis_pelanggaran(Request $request)
    {
        $request->validate([
            'jenis_pelanggaran' => 'required',
            'kategori_pelanggaran' => 'required',
        ]);

        JenisPelanggaran::create([
            'jenis_pelanggaran' => $request->input('jenis_pelanggaran'),
            'kategori_pelanggaran' => $request->input('kategori_pelanggaran')
        ]);

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

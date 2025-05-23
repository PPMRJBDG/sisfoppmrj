<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PmbCamabas;
use App\Models\PmbKonfigurasis;
use App\Models\Settings;
use App\Helpers\WaSchedules;

class PmbPublicController extends Controller
{

    public function index()
    {
        $konfigurasi_pmb = PmbKonfigurasis::where('tahun_pmb',date('Y'))->first();
        $status_pmb = false;
        if($konfigurasi_pmb!=null){
            if(date('Y-m-d') >= $konfigurasi_pmb->gelombang1){
                $status_pmb = true;
            }
        }
        return view('pmb.index', [
            'konfigurasi_pmb' => $konfigurasi_pmb,
            'status_pmb' => $status_pmb,
        ]);
    }

    public function registration_successful()
    {
        return view('pmb.registration_successful', [
            
        ]);
    }

    public function store_maba(Request $request)
    {
        if ($request->hasFile('foto_pas')) {
            $request->validate([
                'foto_pas' => 'mimes:jpeg,png' // Only allow .jpg and .png file types.
            ]);
            $request->foto_pas->store('users', 'public');
        }

        $cek_gelombang = PmbKonfigurasis::where('tahun_pmb',date('Y'))->first();
        $gelombang = 0;
        if($cek_gelombang!=null){
            if($cek_gelombang->gelombang1 <= date('Y') && $cek_gelombang->gelombang2 > date('Y')){
                $gelombang = 1;
            }else{
                $gelombang = 2;
            }
        }

        $insert = PmbCamabas::create([
            'fullname' => strtoupper($request->input('fullname')),
            'gender' => strtoupper($request->input('gender')),
            'place_of_birth' => strtoupper($request->input('place_of_birth')),
            'birthday' => strtoupper($request->input('birthday')),
            'blood_group' => strtoupper($request->input('blood_group')),
            'riwayat_penyakit' => strtoupper($request->input('riwayat_penyakit')),
            'nomor_wa' => strtoupper($request->input('nomor_wa')),
            'email' => strtoupper($request->input('email')),
            'id_line' => strtoupper($request->input('id_line')),
            'alamat' => strtoupper($request->input('alamat')),
            'kota_kab' => strtoupper($request->input('kota_kab')),
            'daerah' => strtoupper($request->input('daerah')),
            'desa' => strtoupper($request->input('desa')),
            'kelompok' => strtoupper($request->input('kelompok')),
            'wa_pengurus' => strtoupper($request->input('wa_pengurus')),
            'universitas' => strtoupper($request->input('universitas')),
            'jurusan' => strtoupper($request->input('jurusan')),
            'motivasi' => strtoupper($request->input('motivasi')),
            'mondok_asal' => strtoupper($request->input('mondok_asal')),
            'muballigh' => strtoupper($request->input('muballigh')),
            'foto_pas' => $request->hasFile('foto_pas') ? $request->foto_pas->hashName() : null,
            'status_ortu_wali' => strtoupper($request->input('status_ortu_wali')),
            'nama_ayah' => strtoupper($request->input('nama_ayah')),
            'profesi_ayah' => strtoupper($request->input('profesi_ayah')),
            'nama_ibu' => strtoupper($request->input('nama_ibu')),
            'profesi_ibu' => strtoupper($request->input('profesi_ibu')),
            'alamat_ortu_wali' => strtoupper($request->input('alamat_ortu_wali')),
            'nomor_wa_ortu_wali' => strtoupper($request->input('nomor_wa_ortu_wali')),
            'seleksi_luring' => strtoupper($request->input('seleksi_luring')),
            'alasan_seleksi_daring' => strtoupper($request->input('alasan_seleksi_daring')),
            'tanggal_seleksi' => strtoupper($request->input('tanggal_seleksi')),
            'pendamping_seleksi' => strtoupper($request->input('pendamping_seleksi')),
            'angkatan' => date('Y'),
            'gelombang' => $gelombang,
            'status' => 'pending',
        ]);

        $setting = Settings::find(1);
        $caption = '*[PMB '.date('Y').']* Pendaftar Mahasiswa Baru an. *'.strtoupper($request->input('fullname')).'* Asal *'.strtoupper($request->input('daerah')).'*.

*Profil:*
- Kelamin: '.strtoupper($request->input('gender')).'
- Kampus: '.strtoupper($request->input('universitas')).' | '.strtoupper($request->input('jurusan')).'
- Status Mondok: '.strtoupper($request->input('mondok_asal')).'
- Status Muballigh: '.strtoupper($request->input('muballigh')).'

*Seleksi:*
- Luring: '.strtoupper($request->input('seleksi_luring')).'
- Alasan Daring: '.strtoupper($request->input('alasan_seleksi_daring')).'
- Tanggal: '.date_format(date_create($request->input('tanggal_seleksi')), 'd M Y').'

*Kontak:*
- No HP Calon Maba: '.$request->input('nomor_wa').'
- No HP Ortu / Wali: '.$request->input('nomor_wa_ortu_wali').'
- No HP Pengurus Klp: '.$request->input('wa_pengurus').'

*Lampiran Foto:*
https://sisfo.ppmrjbandung.com/storage/users/'.$insert->foto_pas;

        WaSchedules::save('[PMB] Pendaftar Mahasiswa Baru '.date('Y'), $caption, $setting->wa_om_group_id, null, true);
        
        return redirect()->route('registration successful');
    }
    
}

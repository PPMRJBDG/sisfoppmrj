<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Camabas;

class PmbPublicController extends Controller
{

    public function index()
    {
        return view('pmb.index', [
            
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

        $insert = Camabas::create([
            'fullname' => touppercase($request->input('fullname')),
            'gender' => touppercase($request->input('gender')),
            'place_of_birth' => touppercase($request->input('place_of_birth')),
            'birthday' => touppercase($request->input('birthday')),
            'blood_group' => touppercase($request->input('blood_group')),
            'riwayat_penyakit' => touppercase($request->input('riwayat_penyakit')),
            'nomor_wa' => touppercase($request->input('nomor_wa')),
            'email' => touppercase($request->input('email')),
            'id_line' => touppercase($request->input('id_line')),
            'alamat' => touppercase($request->input('alamat')),
            'kota_kab' => touppercase($request->input('kota_kab')),
            'daerah' => touppercase($request->input('daerah')),
            'desa' => touppercase($request->input('desa')),
            'kelompok' => touppercase($request->input('kelompok')),
            'wa_pengurus' => touppercase($request->input('wa_pengurus')),
            'universitas' => touppercase($request->input('universitas')),
            'jurusan' => touppercase($request->input('jurusan')),
            'motivasi' => touppercase($request->input('motivasi')),
            'mondok_asal' => touppercase($request->input('mondok_asal')),
            'muballigh' => touppercase($request->input('muballigh')),
            'foto_pas' => $request->hasFile('foto_pas') ? $request->foto_pas->hashName() : null,
            'nama_ayah' => touppercase($request->input('nama_ayah')),
            'profesi_ayah' => touppercase($request->input('profesi_ayah')),
            'nama_ibu' => touppercase($request->input('nama_ibu')),
            'profesi_ibu' => touppercase($request->input('profesi_ibu')),
            'nama_wali' => touppercase($request->input('nama_wali')),
            'profesi_wali' => touppercase($request->input('profesi_wali')),
            'alamat_ortu_wali' => touppercase($request->input('alamat_ortu_wali')),
            'nomor_wa_ortu_wali' => touppercase($request->input('nomor_wa_ortu_wali')),
            'seleksi_luring' => touppercase($request->input('seleksi_luring')),
            'alasan_seleksi_daring' => touppercase($request->input('alasan_seleksi_daring')),
            'tanggal_seleksi' => touppercase($request->input('tanggal_seleksi')),
            'pendamping_seleksi' => touppercase($request->input('pendamping_seleksi')),
            'angkatan' => date('Y'),
            'status' => 'in-review',
        ]);
        
        return redirect()->route('registration successful');
    }
    
}

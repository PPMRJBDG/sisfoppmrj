<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camabas extends Model
{
    protected $fillable = [
        'fullname',
        'gender',
        'place_of_birth',
        'birthday',
        'blood_group',
        'riwayat_penyakit',
        'nomor_wa',
        'email',
        'id_line',
        'alamat',
        'kota_kab',
        'daerah',
        'desa',
        'kelompok',
        'wa_pengurus',
        'universitas',
        'jurusan',
        'motivasi',
        'mondok_asal',
        'muballigh',
        'foto_pas',
        'nama_ayah',
        'profesi_ayah',
        'nama_ibu',
        'profesi_ibu',
        'nama_wali',
        'profesi_wali',
        'alamat_ortu_wali',
        'nomor_wa_ortu_wali',
        'seleksi_luring',
        'alasan_seleksi_daring',
        'tanggal_seleksi',
        'pendamping_seleksi',
        'angkatan',
        'status',
        'catatan_khusus',
    ];
}

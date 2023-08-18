<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pelanggaran extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkJenis_pelanggaran_id',
        'saksi',
        'tanggal_melanggar',
        'lokasi_melanggar',
        'alasan_melanggar',
        'kategori_sp_real',
        'keringanan_sp',
        'is_peringatan_keras',
        'is_st',
        'is_surat_peringatan',
        'is_surat_perubahan',
        'penasehat_1',
        'penasehat_1_tgl',
        'info_ortu_1',
        'penasehat_2',
        'penasehat_2_tgl',
        'info_ortu_2',
        'penasehat_3',
        'penasehat_3_tgl',
        'info_ortu_3',
        'penasehat_4',
        'penasehat_4_tgl',
        'info_ortu_4',
        'penasehat_5',
        'penasehat_5_tgl',
        'info_ortu_5',
        'penasehat_atas',
        'penasehat_atas_tgl',
        'info_ortu_atas',
        'is_st_dikembalikan',
        'keterangan',
        'created_at',
        'updated_at',
        'is_archive'
    ];

    public static function attr()
    {
        return [
            'fkSantri_id',
            'fkJenis_pelanggaran_id',
            'saksi',
            'tanggal_melanggar',
            'lokasi_melanggar',
            'alasan_melanggar',
            'kategori_sp_real',
            'keringanan_sp',
            'is_peringatan_keras',
            'is_st',
            'is_surat_peringatan',
            'is_surat_perubahan',
            'penasehat_1',
            'penasehat_1_tgl',
            'info_ortu_1',
            'penasehat_2',
            'penasehat_2_tgl',
            'info_ortu_2',
            'penasehat_3',
            'penasehat_3_tgl',
            'info_ortu_3',
            'penasehat_4',
            'penasehat_4_tgl',
            'info_ortu_4',
            'penasehat_5',
            'penasehat_5_tgl',
            'info_ortu_5',
            'penasehat_atas',
            'penasehat_atas_tgl',
            'info_ortu_atas',
            'is_st_dikembalikan',
            'keterangan',
            'created_at',
            'updated_at',
            'is_archive'
        ];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'fkJenis_pelanggaran_id');
    }
}

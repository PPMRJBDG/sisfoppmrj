@include('base.start_without_bars', ['path' => 'pmb', 'containerClass' => '', 'title' => "Penerimaan Mahasiswa Baru"])

<div class="row">
    <div class="col-md-3">
    </div>
    <div class="col-md-6">
        <form action="{{ route('store maba') }}" id="upload-file" method="POST" enctype="multipart/form-data">
            <div class="card border shadow-sm p-2" style="background: #049759;">
                <img src="https://i.ibb.co.com/nNsP2s8t/BANNER-FORM-OM-PPMRJ-2025.png" class="card-img-top" alt="PMB 2025-2026"/>
            </div>
            
            <div class="card border shadow-sm mt-2">
                <div class="card-body">
                    <h5 class="card-title mb-2">INFORMASI PENTING</h5>
                    @if($status_pmb)
                        <div  style="line-height: 1.4;">
                            <p class="card-text">
                                Calon Santri Baru PPM RJ Tahun Ajaran 2025/2026 wajib mengisi formulir pendaftaran ini dengan sebenar-benarnya.
                            </p>
                            <p>
                                Jika belum diterima sebagai Mahasiswa di salah satu Universitas, maka data kampus, jurusan, dan angkatan dapat dikosongkan terlebih dahulu.
                            </p>
                            <p>
                                Untuk informasi lebih lanjut, silahkan menghubungi:
                                <br>- Sdr. Hifdzi Khomisa P (<a href="http://wa.me/6282112063590" target="_blank">0821-1206-3590</a>)
                                <br>- Sdr. Muhammad Rizki Akbar (<a href="http://wa.me/681285328431" target="_blank">0812-8532-8431</a>)  
                                <br>- Sdr. Naufal Faza Dzulkenzi (<a href="http://wa.me/6895339557313" target="_blank">0895-3395-57313</a>)  
                            </p>
                        </div>
                    @else
                        @if($konfigurasi_pmb!=null)
                            <div  style="line-height: 0.4;">
                                <p class="card-text">Penerimaan Mahasiswa Baru Dibuka Pada Tanggal:</p>
                                <p class="card-text">- Gelombang 1: {{date_format(date_create($konfigurasi_pmb->gelombang1),'d M Y')}}</p>
                                <p class="card-text">- Gelombang 2: {{date_format(date_create($konfigurasi_pmb->gelombang2),'d M Y')}}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($status_pmb)
                <h6 class="card-title mt-4 font-weight-bolder">DATA DIRI</h6>
                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nama Lengkap</label>
                            <input class="form-control" type="text" id="fullname" name="fullname" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Jenis Kelamin</label>
                            <select class="form-control" value="" id="gender" name="gender" required>
                                <option value="">--Pilih--</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Tempat Lahir</label>
                            <input class="form-control" type="text" name="place_of_birth" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Tanggal Lahir <small>(ex: 18-07-1991)</small></label>
                            <input class="form-control" type="date" name="birthday" value="{{date('Y-m-d')}}" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Golongan Darah</label>
                            <select class="form-control" value="" id="blood_group" name="blood_group" required>
                                <option value="">--Pilih--</option>
                                <option value="A">A</option>
                                <option value="AB">AB</option>
                                <option value="B">B</option>
                                <option value="O">O</option>
                                <option value="Other">Belum Tahu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Riwayat Penyakit</label>
                            <input class="form-control" type="text" name="riwayat_penyakit">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nomor WhatsApp</label>
                            <input class="form-control" type="text" name="nomor_wa" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Email</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">ID Line</label>
                            <input class="form-control" type="text" name="id_line">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Alamat Rumah Lengkap</label>
                            <input class="form-control" type="text" name="alamat" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Kota / Kabupaten Asal</label>
                            <input class="form-control" type="text" name="kota_kab" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Daerah Asal Sambung</label>
                            <input class="form-control" type="text" name="daerah" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Desa Asal Sambung</label>
                            <input class="form-control" type="text" name="desa" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Kelompok Asal Sambung</label>
                            <input class="form-control" type="text" name="kelompok" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nomor WhatsApp Pengurus Kelompok</label>
                            <input class="form-control" type="text" name="wa_pengurus" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Universitas</label>
                            <input class="form-control" type="text" name="universitas" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Jurusan</label>
                            <input class="form-control" type="text" name="jurusan" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Apa motivasi Anda menjadi Santri di PPM RJ ?</label>
                            <input class="form-control" type="text" name="motivasi" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Apakah sebelumnya sudah pernah mondok ? Dimana ?</label>
                            <input class="form-control" type="text" name="mondok_asal">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Apakah Anda sudah Muballigh/Muballighot ?</label>
                            <select class="form-control" value="" id="muballigh" name="muballigh" required>
                                <option value="">--Pilih--</option>
                                <option value="sudah">Sudah</option>
                                <option value="belum">Belum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Foto Pas</label>
                            <input class="form-control" type="file" name="foto_pas" id="foto_pas">
                        </div>
                    </div>
                </div>

                <h6 class="card-title mt-4 font-weight-bolder">DATA ORANG TUA / WALI</h6>
                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nama Ayah</label>
                            <input class="form-control" type="text" name="nama_ayah" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Profesi Ayah</label>
                            <input class="form-control" type="text" name="profesi_ayah" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nama Ibu</label>
                            <input class="form-control" type="text" name="nama_ibu" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Profesi Ibu</label>
                            <input class="form-control" type="text" name="profesi_ibu" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nama Wali <small>(kosongkan jika tidak diwalikan)</small></label>
                            <input class="form-control" type="text" name="nama_wali">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Profesi Wali</label>
                            <input class="form-control" type="text" name="profesi_wali">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Alamat Orang Tua / Wali</label>
                            <input class="form-control" type="text" name="alamat_ortu_wali" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Nomor WhatsApp Orangtua / Wali Aktif</label>
                            <input class="form-control" type="text" name="nomor_wa_ortu_wali" required>
                        </div>
                    </div>
                </div>

                <h6 class="card-title mt-4 font-weight-bolder">RENCANA PELAKSANAAN TAHAP SELEKSI (TAHAP 1)</h6>
                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <p>Tahap Seleksi Gelombang I akan dilaksanakan pada April 2025. Calon Santri Baru dipersilahkan memilih hari Sabtu atau Minggu selama periode waktu tersebut.</p>

                        <p>Calon Santri Baru yang berdomisili di wilayah Jawa Barat wajib melaksanakan Tahap Seleksi di PPM RJ Bandung.</p>

                        <p>Bagi Calon Santri Baru yang berada di luar wilayah Jawa Barat, tetap dianjurkan untuk dapat mengikuti Tahap Seleksi di PPM RJ Bandung, namun jika tidak bisa dapat dilakukan dengan cara online melalui media video conference.</p>

                        <p>Setelah memilih jadwal seleksi melalui form registrasi ini, setiap calon santri harus segera mengonfirmasi kesediaan pelaksanaan seleksi kepada contact person yang telah disediakan. Calon santri baru harus didampingi oleh Orang Tua / Wali selama berlangsungnya Tahap Seleksi. Tahap Seleksi akan dilaksanakan dengan rangkaian sebagai berikut:</p>
                        <p class="mb-0">- Pembukaan dan Pembacaan Peraturan & Tata Tertib PPM RJ Bandung</p>
                        <p class="mb-0">- Wawancara Orang Tua/Wali dan Calon Santri (dilaksanakan secara terpisah)</p>
                        <p class="mb-0">- Pengetesan Bacaan</p>
                        <p class="mb-0">- Pengetesan Adzan dan Pemanqulan (Bagi calon santri baru yang telah berstatus MT)</p>
                        <p class="mb-0">- Pengetesan Pegon</p>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Dapat melaksanakan seleksi secara luring ?</label>
                            <select class="form-control" value="" id="seleksi_luring" name="seleksi_luring" required>
                                <option value="">--Pilih--</option>
                                <option value="bisa">Bisa</option>
                                <option value="tidak">Tidak</option>
                            </select>
                            <input class="form-control mt-2" type="text" name="alasan_seleksi_daring" placeholder="Jika Tidak, berikan alasan kenapa">
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Pilih Tanggal Seleksi <small>(ex: 18-07-2025)</small></label>
                            <input class="form-control" type="date" name="tanggal_seleksi" value="{{date('Y-m-d')}}" required>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">Pendamping Saat Seleksi</label>
                            <select class="form-control" value="" id="pendamping_seleksi" name="pendamping_seleksi" required>
                                <option value="">--Pilih--</option>
                                <option value="ortu">Orang Tua</option>
                                <option value="wali">Wali</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm mt-2">
                    <div class="card-body">
                        <input class="btn btn-primary btn-block mb-0" type="submit" value="SIMPAN">
                    </div>
                </div>
            @endif
        </form>
    </div>
    <div class="col-md-3">
    </div>
</div>

<!-- New Material Design -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.umd.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>

<script>
    $(document).ready(() => {
        new WOW().init();
    });
</script>
<?php
$bulan = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags', 'sep', 'okt', 'nov', 'des'];
?>
@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if (session('success'))
<div class="alert alert-success text-white">
    {{ session('success') }}
</div>
@endif
<div class="row">
    <div class="col-md-7">
        <div class="col-md-12 mb-2">
            <h5 class="mb-0"><b>Profile</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <form action="{{ route('store apps') }}" id="upload-file" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Nama Organisasi
                                    </label>
                                    <input class="form-control" id="org" type="text" name="org" value="{{$list_setting->org_name}}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Nama Aplikasi
                                    </label>
                                    <input class="form-control" id="apps" type="text" name="apps" value="{{$list_setting->apps_name}}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Logo Aplikasi</label>
                                    @if($list_setting->logoImgUrl!='')
                                    <div class="mb-2">
                                        <center>
                                            <img style="width: 60px" src="{{ url('storage/logo-apps/' . $list_setting->logoImgUrl) }}" alt="">
                                        </center>
                                    </div>
                                    @endif
                                    <input class="form-control" type="file" name="logoImg" id="logoImg">
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Backgroun Aplikasi</label>
                                    @if(isset($list_setting->logoImgUrl))
                                    <div class="">
                                        <center>
                                            <?php
                                            // {{ url('storage/logo-apps/' . $list_setting->bgImage) }} 
                                            ?>
                                            <img style="width: 100%" src="" alt="">
                                        </center>
                                    </div>
                                    @endif
                                    <input class="form-control" type="file" name="bgImg">
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="Simpan Aplikasi">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- sync fingerspot -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0 mt-3"><b>Sinkronisasi FP</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <form action="{{ route('sync set fs') }}" id="sync-fs-set" method="POST" enctype="multipart/form-data">
                        <?php
                        $split_cloud_fs = explode(",", $cloud_fs);
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        Sinkroniasi dengan Fingerprint
                                        <?php
                                        $i=1;
                                        foreach($split_cloud_fs as $fs){
                                            if($i==1){
                                                echo '<br>';
                                                echo '<i><small><b>'.$fs.'</b>:<br>- '.count($total_santri_tfs1).' mahasiswa dan '.count($total_degur_tfs1).' pengajar belum sinkron</small></i>';
                                            }elseif($i==2){
                                                echo '<br>';
                                                echo '<i><small><b>'.$fs.'</b>:<br>- '.count($total_santri_tfs2).' mahasiswa dan '.count($total_degur_tfs2).' pengajar belum sinkron</small></i>';
                                            }elseif($i==3){
                                                echo '<br>';
                                                echo '<i><small><b>'.$fs.'</b>:<br>- '.count($total_santri_tfs3).' mahasiswa dan '.count($total_degur_tfs3).' pengajar belum sinkron</small></i>';
                                            }
                                            
                                            $i++;
                                        }
                                        ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="Sync - Set User Info">
                                </div>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('sync get fs') }}" id="sync-fs-get" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="Sync - Get User Info">
                                </div>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('sync delete fs') }}" id="sync-fs-get" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-danger btn-sm btn-block mb-0" type="submit" value="Sync - Delete User Info">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Periode Tahun -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0 mt-3"><b>Periode</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store periode') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                <span class="text-warning text-xs mb-0 pb-0">Jangan menambahkan sebelum pergantian periode perkuliahan!</span>
                                                <br>
                                                Periode Tahunan [ex: 2019-2020]
                                            </label>
                                            <input class="form-control" type="text" name="periode" placeholder="2019-2020" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary btn-sm btn-block mb-2" type="submit" value="Simpan Periode">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="datatable datatable-sm">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Angkatan</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $x = 1; ?>
                                        @if(isset($list_periode))
                                            @foreach($list_periode as $data)
                                                <tr class="text-sm">
                                                    <td>
                                                        {{ $data->periode_tahun }}
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        @if(count($list_periode)==$x)
                                                        <a href="{{ route('delete periode tahun', [$data->id])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <?php $x++; ?>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Generate Sodaqoh -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0 mt-3"><b>Generate Sodaqoh</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <form action="{{ route('store generate sodaqoh') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Pilih Periode Tahunan</label>
                                    <select data-mdb-filter="true" class="select periode form-control" name="periode" id="periode">
                                        <option value="">Periode</option>
                                        @if(count($list_periode)>0)
                                        @foreach($list_periode as $per)
                                        <option value="{{$per->periode_tahun}}">{{$per->periode_tahun}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Nominal per Tahun</label>
                                    <input class="form-control" type="number" name="nominal" placeholder="5000000" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="Generate Sodaqoh Tahunan">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Calendar Liburan -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0 mt-3"><b>Liburan</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store liburan') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" id="liburan_id" name="liburan_id" value="">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                <span class="text-warning text-xs mb-0 pb-0">Yang periode tahun lalu dihapus saja!</span>
                                                <br>
                                                Awal Liburan
                                            </label>
                                            <input class="form-control" type="date" name="liburan_from" id="liburan_from" placeholder="Contoh: 27/03/2000" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Akhir Liburan
                                            </label>
                                            <input class="form-control" type="date" name="liburan_to" id="liburan_to" placeholder="Contoh: 27/03/2000" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Keterangan
                                            </label>
                                            <input class="form-control" type="text" name="keterangan" id="keterangan" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary btn-sm btn-block mb-2" type="submit" value="Simpan Liburan">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="datatable datatable-sm">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Dari</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Sampai</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Ket</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($list_liburan))
                                        @foreach($list_liburan as $data)
                                        <tr class="text-sm">
                                            <td>
                                                {{ $data->liburan_from }}
                                            </td>
                                            <td>
                                                {{ $data->liburan_to }}
                                            </td>
                                            <td>
                                                {{ $data->keterangan }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <a href="#" class="btn btn-warning btn-sm mb-0" onclick="setChangeLiburan({{$data}})">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="{{ route('delete liburan', [$data->id])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jenis Pelanggaran -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0 mt-3"><b>Daftar Pelanggaran</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store jenis pelanggaran') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" id="pelanggaran_id" name="pelanggaran_id" value="">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Nama Pelanggaran
                                            </label>
                                            <input class="form-control" type="text" name="jenis_pelanggaran" id="jenis_pelanggaran" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Kategori Pelanggaran
                                            </label>
                                            <select class="kategori_pelanggaran form-control" name="kategori_pelanggaran" id="kategori_pelanggaran">
                                                <option value="">Kategori Pelanggaran</option>
                                                <option value="ringan">Ringan</option>
                                                <option value="sedang">Sedang</option>
                                                <option value="berat">Berat</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary btn-sm btn-block mb-2" type="submit" value="Simpan Pelanggaran">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="datatable datatable-sm">
                                <table id="table-pelanggaran" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Jenis</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($list_jenis_pelanggaran))
                                            @foreach($list_jenis_pelanggaran as $data)
                                                <tr class="text-sm">
                                                    <td>
                                                        <b>[{{ $data->kategori_pelanggaran }}]</b> {{ $data->jenis_pelanggaran }}
                                                    </td>
                                                    <td class="align-middle text-center text-xs">
                                                        <a href="#" class="btn btn-warning btn-sm mb-0" onclick="setChangePelanggaran({{$data}})">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('delete jenis pelanggaran', [$data->id])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <!-- WA Settings -->
        <div class="col-md-12 mb-2">
            <h5 class="mb-0"><b>Pengaturan</b></h5>
            
            <div class="card border p-2 shadow-lg">
                <div class="">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('store settings') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">

                                        <h5><b>Umum</b></h5>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Host URL
                                            </label>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->host_url : '' }}" name="host_url" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Akun Studio
                                            </label>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->wa_username : '' }}" name="wa_username" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cloud FS
                                            </label>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->cloud_fs : '' }}" name="cloud_fs">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Token FS
                                            </label>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->token_fs : '' }}" name="token_fs">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Kunci Kalender PPM
                                            </label>
                                            <select class="select form-control" name="lock_calendar" id="lock_calendar">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->lock_calendar) ? 'selected' : '';
                                                        } ?> value="0">Buka</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->lock_calendar) ? 'selected' : '';
                                                        } ?> value="1">Kunci</option>
                                            </select>
                                        </div>

                                        <h5><b>WhatsApp</b></h5>
                                        
                                        <div class="form-group">
                                            @if($list_setting->wa_username!='')
                                            <label class="form-control-label">
                                                WA - Team Account
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_team_id form-control" name="wa_team_id" id="wa_team_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_team)>0)
                                                @foreach($list_wa_team as $wt)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wt->id == $list_setting->wa_team_id) ? 'selected' : '';
                                                        } ?> value="{{$wt->id}}">{{$wt->user->fullname}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Sender Account
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_sender_account_id form-control" name="wa_sender_account_id" id="wa_sender_account_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_account)>0)
                                                @foreach($list_wa_account as $waac)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($waac->id == $list_setting->wa_sender_account_id) ? 'selected' : '';
                                                        } ?> value="{{$waac->id}}">{{$waac->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Maurus
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_maurus_group_id form-control" name="wa_maurus_group_id" id="wa_maurus_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_maurus_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Keuangan
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_keuangan_group_id form-control" name="wa_keuangan_group_id" id="wa_keuangan_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_keuangan_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Dewan Guru
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_dewanguru_group_id form-control" name="wa_dewanguru_group_id" id="wa_dewanguru_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_dewanguru_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Diskusi Ketertiban
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_ketertiban_group_id form-control" name="wa_ketertiban_group_id" id="wa_ketertiban_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_ketertiban_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Info Presensi & Perijinan
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_info_presensi_group_id form-control" name="wa_info_presensi_group_id" id="wa_info_presensi_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_info_presensi_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Grup Ortu
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_ortu_group_id form-control" name="wa_ortu_group_id" id="wa_ortu_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_ortu_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;">
                                            @if($list_setting->wa_username!='')
                                            <label class="form-control-label">
                                                WA - Type
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_type : '' }}" name="wa_type" required readonly>
                                        </div>
                                        <div class="form-group" style="display:none;">
                                            <label class="form-control-label">
                                                WA - Template
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_template : '' }}" name="wa_template" required readonly>
                                        </div>
                                        <div class="form-group" style="display:none;">
                                            <label class="form-control-label">
                                                WA - Min Delay
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_min_delay : '' }}" name="wa_min_delay" required readonly>
                                        </div>
                                        <div class="form-group" style="display:none;">
                                            <label class="form-control-label">
                                                WA - Max Delay
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_max_delay : '' }}" name="wa_max_delay" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Header
                                            </label>
                                            <textarea rows="3" class="form-control" name="wa_header" required>{{ ($list_setting) ? $list_setting->wa_header : '' }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                WA - Footer
                                            </label>
                                            <textarea rows="3" class="form-control" name="wa_footer" required>{{ ($list_setting) ? $list_setting->wa_footer : '' }}</textarea>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><b>Presensi</b></h5>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Info Alpha ke Ortu
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_info_alpha_ortu form-control" name="wa_info_alpha_ortu" id="wa_info_alpha_ortu">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->wa_info_alpha_ortu) ? 'selected' : '';
                                                        } ?> value="0">Tidak</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->wa_info_alpha_ortu) ? 'selected' : '';
                                                        } ?> value="1">Ya</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Link Presensi ke Koor Lorong
                                            </label>
                                            <select data-mdb-filter="true" class="select wa_link_presensi_koor form-control" name="wa_link_presensi_koor" id="wa_link_presensi_koor">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->wa_link_presensi_koor) ? 'selected' : '';
                                                        } ?> value="0">Tidak</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->wa_link_presensi_koor) ? 'selected' : '';
                                                        } ?> value="1">Ya</option>
                                            </select>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Auto Generate Hadir
                                            </label>
                                            <select data-mdb-filter="true" class="select auto_generate_hadir form-control" name="auto_generate_hadir" id="auto_generate_hadir">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->auto_generate_hadir) ? 'selected' : '';
                                                        } ?> value="0">Tidak</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->auto_generate_hadir) ? 'selected' : '';
                                                        } ?> value="1">Ya</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Status Perijinan 30%
                                            </label>
                                            <select data-mdb-filter="true" class="select status_perijinan form-control" name="status_perijinan" id="status_perijinan">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->status_perijinan) ? 'selected' : '';
                                                        } ?> value="0">Tidak Diaktifkan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->status_perijinan) ? 'selected' : '';
                                                        } ?> value="1">Diaktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Mekanisme Dewan Guru Scan Awal
                                            </label>
                                            <select data-mdb-filter="true" class="select status_scan_degur form-control" name="status_scan_degur" id="status_scan_degur">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->status_scan_degur) ? 'selected' : '';
                                                        } ?> value="0">Tidak Diaktifkan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->status_scan_degur) ? 'selected' : '';
                                                        } ?> value="1">Diaktifkan</option>
                                            </select>
                                        </div>

                                        <h5><b>Scheduler</b></h5>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Daily
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_daily form-control" name="cron_daily" id="cron_daily">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_daily) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_daily) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Preview Daily
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_preview_daily form-control" name="cron_preview_daily" id="cron_preview_daily">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_preview_daily) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_preview_daily) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Weekly
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_weekly form-control" name="cron_weekly" id="cron_weekly">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_weekly) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_weekly) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Monthly
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_monthly form-control" name="cron_monthly" id="cron_monthly">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_monthly) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_monthly) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Presence
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_presence form-control" name="cron_presence" id="cron_presence">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_presence) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_presence) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Jam Malam
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_jam_malam form-control" name="cron_jam_malam" id="cron_jam_malam">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_jam_malam) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_jam_malam) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Nerobos
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_nerobos form-control" name="cron_nerobos" id="cron_nerobos">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_nerobos) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_nerobos) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Tata Tertib
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_tatib form-control" name="cron_tatib" id="cron_tatib">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_tatib) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_tatib) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Cron - Per Menit
                                            </label>
                                            <select data-mdb-filter="true" class="select cron_minutes form-control" name="cron_minutes" id="cron_minutes">
                                                <option <?php if ($list_setting != null) {
                                                            echo (0 == $list_setting->cron_minutes) ? 'selected' : '';
                                                        } ?> value="0">Matikan</option>
                                                <option <?php if ($list_setting != null) {
                                                            echo (1 == $list_setting->cron_minutes) ? 'selected' : '';
                                                        } ?> value="1">Aktifkan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Reminder KBM (menit)
                                            </label>
                                            <br>
                                            <small>
                                                [pengingat dijalankan sebelum {{$list_setting->reminder_kbm}} menit KBM dimulai]
                                            </small>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->reminder_kbm : '' }}" name="reminder_kbm">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Reminder Kehadiran (menit)
                                            </label>
                                            <br>
                                            <small>
                                                [pengingat dijalankan setelah {{$list_setting->reminder_nerobos}} menit KBM dimulai]
                                            </small>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->reminder_nerobos : '' }}" name="reminder_nerobos">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Info Alpha Ortu (menit)
                                            </label>
                                            <br>
                                            <small>
                                                [pengingat dijalankan setelah {{$list_setting->reminder_alpha_ortu}} menit KBM selesai]
                                            </small>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->reminder_alpha_ortu : '' }}" name="reminder_alpha_ortu">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h5><b>Template WA</b></h5>
                                        
                                        <div class="form-group">
                                            @if($list_setting->wa_username!='')
                                            <label class="form-control-label">
                                                Info Jaga Malam
                                            </label>
                                            <textarea rows="6" class="form-control mb-2" name="wa_info_jaga_malam" required>{{ ($list_setting) ? $list_setting->wa_info_jaga_malam : '' }}</textarea>

                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Info Untuk Mahasiswa yang Sudah Lulus
                                            </label>
                                            <textarea rows="6" class="form-control mb-2" name="wa_info_lulus" required>{{ ($list_setting) ? $list_setting->wa_info_lulus : '' }}</textarea>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h5><b>Layanan IT</b></h5>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Expired Layanan Domain
                                            </label>
                                            <input class="form-control" type="date" value="{{ ($list_setting) ? $list_setting->reminder_layanan_domain : '' }}" name="reminder_layanan_domain">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Expired Layanan Server
                                            </label>
                                            <input class="form-control" type="date" value="{{ ($list_setting) ? $list_setting->reminder_layanan_server : '' }}" name="reminder_layanan_server">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Expired Layanan Fingerprint
                                            </label>
                                            <input class="form-control" type="date" value="{{ ($list_setting) ? $list_setting->reminder_layanan_fingerprint : '' }}" name="reminder_layanan_fingerprint">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Informasi Akun
                                            </label>
                                            <textarea rows="6" class="form-control mb-2" name="account_info" required>{{ ($list_setting) ? $list_setting->account_info : '' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="Update Setting">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
</script>
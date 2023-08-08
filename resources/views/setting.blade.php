@include('base.start', ['path' => 'setting', 'title' => 'Setting', 'breadcrumbs' => ['Setting']])

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
    <div class="col-md-6">
        <!-- Periode Tahun -->
        <div class="col-md-12 mb-2">
            <div class="card shadow-lg">
                <div class="card-body">
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
                                            <input class="btn btn-primary form-control mb-0" type="submit" value="Tambah Periode">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Angkatan</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($list_periode))
                                        @foreach($list_periode as $data)
                                        <tr class="text-sm">
                                            <td>
                                                {{ $data->periode_tahun }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <a href="{{ route('delete periode tahun', [$data->id])}}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
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
        <!-- Generate Sodaqoh -->
        <div class="col-md-12 mb-2">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form action="{{ route('store generate sodaqoh') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Pilih Periode Tahunan</label>
                                    <select class="periode form-control" name="periode" id="periode">
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
                                    <input class="form-control" type="number" name="nominal" placeholder="3000000" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="btn btn-primary form-control mb-0" type="submit" value="Generate Sodaqoh Tahunan">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Calendar Liburan -->
        <div class="col-md-12 mb-2">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store liburan') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                <span class="text-warning text-xs mb-0 pb-0">Yang sudah lewat dihapus saja!</span>
                                                <br>
                                                Awal Liburan
                                            </label>
                                            <input class="form-control" type="date" name="liburan_from" placeholder="Contoh: 27/03/2000" required>
                                            <label class="form-control-label">
                                                Akhir Liburan
                                            </label>
                                            <input class="form-control" type="date" name="liburan_to" placeholder="Contoh: 27/03/2000" required>
                                            <label class="form-control-label">
                                                Keterangan
                                            </label>
                                            <input class="form-control" type="text" name="keterangan" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary form-control mb-0" type="submit" value="Tambah Liburan">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
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
                                                <a href="{{ route('delete liburan', [$data->id])}}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
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
        <!-- WA Settings -->
        <div class="col-md-12 mb-2">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store settings') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Host URL
                                            </label>
                                            <input class="form-control" type="text" value="{{ ($list_setting) ? $list_setting->host_url : '' }}" name="host_url" required>
                                            <label class="form-control-label">
                                                WA - Team Account
                                            </label>
                                            <select class="wa_team_id form-control" name="wa_team_id" id="wa_team_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_team)>0)
                                                @foreach($list_wa_team as $wt)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wt->id == $list_setting->wa_team_id) ? 'selected' : '';
                                                        } ?> value="{{$wt->id}}">{{$wt->user->fullname}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <label class="form-control-label">
                                                WA - Sender Account
                                            </label>
                                            <select class="wa_sender_account_id form-control" name="wa_sender_account_id" id="wa_sender_account_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_account)>0)
                                                @foreach($list_wa_account as $waac)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($waac->id == $list_setting->wa_sender_account_id) ? 'selected' : '';
                                                        } ?> value="{{$waac->id}}">{{$waac->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <label class="form-control-label">
                                                WA - Grup Ketertiban
                                            </label>
                                            <select class="wa_ketertiban_group_id form-control" name="wa_ketertiban_group_id" id="wa_ketertiban_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_ketertiban_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <label class="form-control-label">
                                                WA - Grup Ortu
                                            </label>
                                            <select class="wa_ortu_group_id form-control" name="wa_ortu_group_id" id="wa_ortu_group_id">
                                                <option value="">--pilih--</option>
                                                @if(count($list_wa_group)>0)
                                                @foreach($list_wa_group as $wg)
                                                <option <?php if ($list_setting != null) {
                                                            echo ($wg->id == $list_setting->wa_ortu_group_id) ? 'selected' : '';
                                                        } ?> value="{{$wg->id}}">{{$wg->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <label class="form-control-label">
                                                WA - Type
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_type : '' }}" name="wa_type" required>
                                            <label class="form-control-label">
                                                WA - Template
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_template : '' }}" name="wa_template" required>
                                            <label class="form-control-label">
                                                WA - Min Delay
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_min_delay : '' }}" name="wa_min_delay" required>
                                            <label class="form-control-label">
                                                WA - Max Delay
                                            </label>
                                            <input class="form-control" type="number" value="{{ ($list_setting) ? $list_setting->wa_max_delay : '' }}" name="wa_max_delay" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-primary form-control mb-0" type="submit" value="Update Setting">
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
    <div class="col-md-6">
        <!-- Jenis Pelanggaran -->
        <div class="col-md-12 mb-2">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form action="{{ route('store jenis pelanggaran') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Jenis Pelanggaran
                                            </label>
                                            <input class="form-control" type="text" name="jenis_pelanggaran" required>
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
                                            <input class="btn btn-primary form-control mb-0" type="submit" value="Tambah Jenis Pelanggaran">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table id="table-pelanggaran" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Jenis</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Kategori</th>
                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($list_jenis_pelanggaran))
                                        @foreach($list_jenis_pelanggaran as $data)
                                        <tr class="text-sm">
                                            <td>
                                                {{ $data->jenis_pelanggaran }}
                                            </td>
                                            <td>
                                                {{ $data->kategori_pelanggaran }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <a href="{{ route('delete jenis pelanggaran', [$data->id])}}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
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
</div>
<script>
    $('#table-pelanggaran').DataTable({
        order: [
            [1, 'desc']
        ],
    });
</script>
@include('base.end')
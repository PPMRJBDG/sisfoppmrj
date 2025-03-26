<?php
$santri_jaga = false;
if(isset(auth()->user()->santri)){
    if(auth()->user()->santri->jaga_malam==1){
        $santri_jaga = true;
    }
}
?>

@if($santri_jaga || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi keamanan'))
<div class="card border mb-2" style="background-color:#f6f9fc;">
    <div class="card-body p-2">
        <script>
            function togglePrsc() {
                $("#toggle-prsc").toggle();
            }
            function simpanPulangMalam() {
                var datax = {};
                datax['santri_id'] = $("#santri_id").val();
                datax['jam_pulang'] = $("#jam_pulang").val();
                datax['alasan'] = $("#alasan").val();

                if(datax['alasan']==""){
                    alert("Silahkan masukkan alasan");
                }else{
                    $("#loadingSubmit").show();
                    $.post("{{ route('store pulangmalam') }}", datax,
                        function(data, status) {
                            window.location.reload();
                        }
                    )
                }
            }
        </script>
        <center><button class="btn btn-secondary btn-sm btn-block mb-0" onclick="togglePrsc()">JAGA MALAM</button></center>
        <div id="toggle-prsc" class="pt-2" style="display:none;">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a data-mdb-ripple-init onclick="return false;" class="nav-link active font-weight-bolder" id="nav-jobdesk-tab" data-bs-toggle="tab" href="#nav-jobdesk" role="tab" aria-controls="nav-jobdesk" aria-selected="true">Jobdesk</a>
                    <a data-mdb-ripple-init onclick="return false;" class="nav-link font-weight-bolder" id="nav-formpulang-tab" data-bs-toggle="tab" href="#nav-formpulang" role="tab" aria-controls="nav-formpulang">Pulang Malam</a>
                </div>

                <div class="tab-content p-0 pt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-jobdesk" role="tabpanel" aria-labelledby="nav-jobdesk-tab">
                        <div class="card p-2" style="background-color:#f6f9fc;">
                            <form action="{{ route('store jobdesk') }}" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mt-2">
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_1 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_kunci_gerbang){
                                                        $c_1 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_1 }} id="jd_kunci_gerbang" name="jd_kunci_gerbang">
                                            <label class="form-check-label" for="jd_kunci_gerbang">Mengunci Gerbang</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_2 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_cek_air){
                                                        $c_2 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_2 }} id="jd_cek_air" name="jd_cek_air">
                                            <label class="form-check-label" for="jd_cek_air">Keliling Mengecek Air</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_3 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_cek_listrik){
                                                        $c_3 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_3 }} id="jd_cek_listrik" name="jd_cek_listrik">
                                            <label class="form-check-label" for="jd_cek_listrik">Keliling Mengecek Listrik</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_4 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_cek_lingkungan){
                                                        $c_4 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_4 }} id="jd_cek_lingkungan" name="jd_cek_lingkungan">
                                            <label class="form-check-label" for="jd_cek_lingkungan">Keliling Mengecek Lingkungan</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_5 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_cek_lahan){
                                                        $c_5 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_5 }} id="jd_cek_lahan" name="jd_cek_lahan">
                                            <label class="form-check-label" for="jd_cek_lahan">Keliling Mengecek Lahan</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_6 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_adzan_malam){
                                                        $c_6 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_6 }} id="jd_adzan_malam" name="jd_adzan_malam">
                                            <label class="form-check-label" for="jd_adzan_malam">Sudah Adzan 1/3 Malam</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <?php 
                                                $c_7 = '';
                                                if($data_jobdesk_jaga){
                                                    if($data_jobdesk_jaga->jd_nerobos_muadzin){
                                                        $c_7 = 'checked';
                                                    }
                                                }
                                            ?>
                                            <input class="form-check-input" type="checkbox" {{ $c_7 }} id="jd_nerobos_muadzin" name="jd_nerobos_muadzin">
                                            <label class="form-check-label" for="jd_nerobos_muadzin">Salah Satu Sudah Adzan Shubuh</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <label class="form-control-label">
                                                Kondisi Lingkungan PPM pada Malam ini:
                                            </label>
                                            <textarea rows="5" class="form-control" name="jd_kondisi_umum" required placeholder="Ex: kondisi tenang, mahasiswa sudah tidur, cuaca dingin">{{ ($data_jobdesk_jaga) ? $data_jobdesk_jaga->jd_kondisi_umum : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mt-2 p-2 bg-white border">
                                            <p>Pelaksanaan ini di monitoring dan di kontrol juga oleh Koor Ketertiban (Ust. Latif & Ust. Yopi), jika ditemukan laporan yang tidak sesuai dengan yang sebenarnya, maka mohon ridhonya untuk diberikan kafaroh oleh Koor Ketertiban.</p>
                                            <p>Bagi yang sudah jaga dan melaporkan ini dinyatakan (auto) "Hadir" pada KBM Shubuh, dan bagi yang tidak jaga (berhalangan) masih memiliki kewajiban untuk mengikuti KBM Shubuh.</p>
                                            <p><b>Ttd,<br>Pegurus PPM</b></p>
                                        </div>
                                        <hr>
                                        <input class="btn btn-primary btn-sm btn-block mb-0" type="submit" value="SIMPAN">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="nav-formpulang" role="tabpanel" aria-labelledby="nav-formpulang-tab">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <select data-mdb-filter="true" class="select form-control" value="" id="santri_id" name="santri_id" required>
                                    <option value="">--Pilih santri--</option>
                                    @foreach($view_usantri as $s)
                                    <option value="{{$s->santri_id}}">{{$s->fullname}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input class="form-control" type="datetime-local" value="{{date('Y-m-d H:i:s')}}" id="jam_pulang" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input class="form-control" type="text" value="" id="alasan" placeholder="Tuliskan alasan kenapa terlambat" required>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-primary mb-2 btn-block" onclick="simpanPulangMalam()">
                                    <i class="fas fa-save" aria-hidden="true"></i>
                                    SIMPAN
                                </a>
                            </div>
                        </div>

                        <div class="p-2" style="background: #f6f6f6;">
                            <div class="datatable datatable-sm">
                                <table id="table-report" class="table align-items-center mb-0">
                                    <thead style="background-color:#f6f9fc;">
                                        <tr>
                                            <th class="text-uppercase text-sm text-secondary">JAGA</th>
                                            <th class="text-uppercase text-sm text-secondary">SANTRI</th>
                                            <th class="text-uppercase text-sm text-secondary">JAM PULANG</th>
                                            <th class="text-uppercase text-sm text-secondary">ALASAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data_telatpulang as $d)
                                            <tr class="text-sm">
                                                <td>
                                                    {{ $d->jaga->user->fullname }}
                                                </td>
                                                <td>
                                                    {{ $d->santri->user->fullname }}
                                                </td>
                                                <td>
                                                    {{ date_format(date_create($d->jam_pulang), 'd-m-Y H:i:s') }}
                                                </td>
                                                <td>
                                                    {{ $d->alasan }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
<div class="col-12 p-0 mb-2">
    <div class="card border border p-2">
        <p class="m-0 mb-2 text-sm font-weight-bolder">Shortcut Presensi Hari Ini</p>
        <a href="{{ url('presensi/izin/persetujuan') }}" class="btn btn-primary btn-sm btn-rounded m-0 mb-2">
            Terima / Tolak Ijin
        </a>
        <section id="section-1"></section>
        <div class="row">
            @foreach($get_presence_today as $gpt)
            <div class="col-md-{{(12/count($get_presence_today))}}">
                <a href="/presensi/list/{{ $gpt->id }}" class="btn btn-outline-primary btn-sm btn-block btn-rounded mb-2">
                    Presensi {{ $gpt->name }}
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('ku'))
<div class="col-12 p-0 mb-2">
    <div class="card border p-2">
        <button type="button" onclick="getReport('<?php echo base64_encode(auth()->user()->santri->id); ?>')" data-mdb-ripple-init class="btn font-weight-bolder btn-sm btn-warning btn-rounded mb-0">Lihat Laporan Saya</a>
    </div>
</div>
@endif

@if(auth()->user()->hasRole('ku'))
<div class="card border p-2">
    <h6 class="mt-1">Selamat datang, {{auth()->user()->fullname}}</h6>
</div>
@else
<div class="">
    <p class="mb-2 text-sm font-weight-bolder">Laporan Presensi</p>
    <div class="card border p-2 mb-2">
        <div class="row">
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                <div class="col-md-4 mb-2">
                    <select data-mdb-filter="true" class="select select_angkatan form-control" name="select_angkatan" id="select_angkatan">
                        <option value="-">Semua Angkatan</option>
                        @foreach($list_angkatan as $la)
                        <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-4 mb-2">
                <select data-mdb-filter="true" class="select select_tb form-control" name="select_tb" id="select_tb">
                    <option value="-">Keseluruhan Bulan Tanggal</option>
                    @foreach($tahun_bulan as $tbx)
                    <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                <div class="col-md-4">
                    <select data-mdb-filter="true" class="select select_periode form-control" name="select_periode" id="select_periode">
                        <option value="-">Keseluruhan Periode</option>
                        @foreach($periode_tahun as $prt)
                        <option {{ ($select_periode == $prt->periode_tahun) ? 'selected' : '' }} value="{{$prt->periode_tahun}}">{{$prt->periode_tahun}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <div class="card border">
        <nav>
            <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
                @if(auth()->user()->hasRole('santri'))
                <a data-mdb-ripple-init class="nav-link active font-weight-bolder" id="nav-mahasiswa-tab" data-bs-toggle="tab" href="#nav-mahasiswa" role="tab" aria-controls="nav-mahasiswa" aria-selected="true">
                    Mahasiswa
                </a>
                @endif
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                <a data-mdb-ripple-init class="nav-link {{(auth()->user()->hasRole('superadmin')) ? 'active' : ''}} font-weight-bolder" id="nav-dashboard-tab" data-bs-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">
                    Dashboard
                </a>
                <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-table-tab" onclick="openTab('{{$presence_group}}')" data-bs-toggle="tab" href="#nav-table" role="tab" aria-controls="nav-table" aria-selected="false">
                    Table
                </a>
                <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-grafik-tab" onclick="openGraf('{{$presence_group}}')" data-bs-toggle="tab" href="#nav-grafik" role="tab" aria-controls="nav-grafik" aria-selected="false">
                    Grafik
                </a>
                @endif
            </div>

            <div class="tab-content p-0 mt-2" id="nav-tabContent">
                @if(auth()->user()->hasRole('santri'))
                    <div class="tab-pane fade show active" id="nav-mahasiswa" role="tabpanel" aria-labelledby="nav-mahasiswa-tab">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($presence_group as $pg)
                                        <div class="col-12 mb-2">
                                            <div class="">
                                                <div class="card-body p-2 text-center">
                                                    <h6 class="text-sm font-weight-bolder mb-0 bg-primary p-2 text-white">
                                                        {{ $pg->name }}
                                                        (<?php
                                                            if ($datapg[$pg->id]['loopr'] != 0) {
                                                                echo number_format(($datapg[$pg->id]['kehadiran'] / $datapg[$pg->id]['loopr']) *  100, 2) . "%";
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>)
                                                    </h6>
                                                    <div class="datatable datatable-sm" data-mdb-pagination="false">
                                                        <table class="table align-items-center mb-0">
                                                            <thead>
                                                                <tr class="text-xs">
                                                                    <th class="text-uppercase font-weight-bolder bg-grey">TANGGAL</th>
                                                                    <th class="text-uppercase font-weight-bolder">STATUS</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($presences_santri))
                                                                    @foreach($presences_santri as $prs)
                                                                        @if($pg->id==$prs->fkPresence_group_id)
                                                                            <tr class="text-sm">
                                                                                <td>
                                                                                    {{ date_format(date_create($prs->event_date), 'd M') }}
                                                                                </td>
                                                                                <td>
                                                                                    @if($prs->fkSantri_id!="")
                                                                                        <span class="badge badge-primary">Hadir</span>
                                                                                    @else
                                                                                        <?php
                                                                                        $check_permit = null;
                                                                                        if(isset(auth()->user()->santri)){
                                                                                            $check_permit = App\Models\Permit::where('fkPresence_id', $prs->id)->where('status', 'approved')->where('fkSantri_id', auth()->user()->santri->id)->first();
                                                                                        }
                                                                                        ?>
                                                                                        @if($check_permit!=null)
                                                                                            <span class="badge badge-secondary">Ijin</span>
                                                                                        @else
                                                                                            <span class="badge badge-warning">Alpha</span>
                                                                                        @endif
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                <div class="tab-pane fade show {{(auth()->user()->hasRole('superadmin')) ? 'active' : ''}}" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">
                    <div class="datatable datatable-sm border" data-mdb-pagination="false" data-mdb-fixed-header="true">
                        <table id="table-hadir" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xs font-weight-bolder ps-2" data-mdb-width="300" data-mdb-fixed="true">ANGKATAN<br>NAMA</th>
                                    @foreach($presence_group as $pg)
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">
                                        {{strtoupper($pg->name)}}
                                        <br>
                                        H | I | A | T
                                    </th>
                                    @endforeach
                                    <th class="text-uppercase text-xs font-weight-bolder ps-2"></th>
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>HADIR</th>
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>IJIN</th>
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>ALPHA</th>
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>KBM</th>
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>PERSENTASE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($view_usantri!=null)
                                @foreach($view_usantri as $vu)
                                <tr>
                                    <td>
                                        <a href="#" block-id="return-false" onclick="getReport('<?php echo base64_encode($vu->santri_id); ?>')">
                                            [{{ $vu->angkatan }}] {{ $vu->fullname }}
                                        </a>
                                    </td>
                                    <?php
                                    $all_persentase = 0;
                                    $all_kbm = 0;
                                    $all_hadir = 0;
                                    $all_alpha = 0;
                                    $all_ijin = 0;
                                    ?>
                                    @foreach($presence_group as $pg)
                                    <td class="text-center">
                                        @foreach($presences[$vu->santri_id][$pg->id] as $listcp)
                                        <?php
                                        $ijin = 0;
                                        if (isset($all_permit[$pg->id][$vu->santri_id])) {
                                            $ijin = $all_permit[$pg->id][$vu->santri_id];
                                        }
                                        ?>
                                        {{ $listcp->cp }} | {{ $ijin }} | {{ $all_presences[$vu->santri_id][$pg->id][0]->c_all - ($listcp->cp + $ijin) }} | {{$all_presences[$vu->santri_id][$pg->id][0]->c_all}}
                                        <?php
                                        if ($all_presences[$vu->santri_id][$pg->id][0]->c_all == 0) {
                                            $persentase = 0;
                                        } else {
                                            $persentase = number_format(($listcp->cp + $ijin) / $all_presences[$vu->santri_id][$pg->id][0]->c_all * 100, 2);
                                        }
                                        $all_kbm = $all_kbm + $all_presences[$vu->santri_id][$pg->id][0]->c_all;
                                        $all_hadir = $all_hadir + $listcp->cp;
                                        $all_ijin = $all_ijin + $ijin;
                                        $all_alpha = $all_kbm - ($all_hadir + $all_ijin);
                                        ?>
                                        <span class="font-weight-bolder {{($persentase<80) ? 'text-danger' : '' }}">({{ $persentase }}%)</span>
                                        @endforeach
                                    </td>
                                    @endforeach
                                    <td class="text-center"><i class="ni ni-atom text-white text-sm opacity-10"></i></td>
                                    <td class="text-center">{{ $all_hadir  }}</td>
                                    <td class="text-center">{{ $all_ijin  }}</td>
                                    <td class="text-center">{{ $all_alpha }}</td>
                                    <td class="text-center">{{ $all_kbm }}</td>
                                    <td class="text-center">
                                        <?php
                                        if ($all_kbm > 0) {
                                            $all_persentase = ($all_hadir + $all_ijin) / $all_kbm * 100;
                                        }
                                        ?>
                                        <span class="font-weight-bolder {{ ($all_persentase<80) ? 'text-danger' : ''}}">
                                            {{ number_format($all_persentase,2) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="nav-table" role="tabpanel" aria-labelledby="nav-table-tab">
                    <div class="card-body" id="loading-table" style="border-radius:4px;">
                        <div class="text-center">
                            <center>
                                <div class="spinner-grow text-primary" role="status"></div>
                                <div class="spinner-grow text-warning" role="status"></div>
                                <div class="spinner-grow text-danger" role="status"></div>
                            </center>
                        </div>
                    </div>
                    <div class="datatablex table-responsive datatable-sm" data-mdb-pagination="false">
                        <div class="card-table" id="card-table" style="display:none;">
                            <table id="tab-table" class="table align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr style="background-color:#f6f9fc;">
                                        <th colspan="2" class="text-uppercase text-center text-xs font-weight-bolder"></th>
                                        @foreach($presence_group as $pg)
                                            <th colspan="3" class="text-uppercase text-center text-xs font-weight-bolder">
                                                {{$pg->name}}
                                            </th>
                                        @endforeach
                                        <th class="text-uppercase text-center text-xs font-weight-bolder"></th>
                                    </tr>
                                    <tr style="background-color:#f6f9fc;">
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">NO</th>
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">TANGGAL</th>
                                        @foreach($presence_group as $pg)
                                            <th class="text-uppercase text-center text-xs font-weight-bolder">H</th>
                                            <th class="text-uppercase text-center text-xs font-weight-bolder">I</th>
                                            <th class="text-uppercase text-center text-xs font-weight-bolder">A</th>
                                        @endforeach
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">TOTAL<br>PERSENTASE</th>
                                    </tr>
                                </thead>
                                <tbody id="data-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="nav-grafik" role="tabpanel" aria-labelledby="nav-grafik-tab">
                    <div class="card-body" id="loading-grafik" style="border-radius:4px;">
                        <div class="text-center">
                            <center>
                                <div class="spinner-grow text-primary" role="status"></div>
                                <div class="spinner-grow text-warning" role="status"></div>
                                <div class="spinner-grow text-danger" role="status"></div>
                            </center>
                        </div>
                    </div>
                    <div class="card-grafik p-2" id="card-grafik" style="display:none;">
                        @foreach($presence_group as $pg)
                        <p class="mb-0 text-sm font-weight-bolder">Grafik Kehadiran {{ $pg->name }}</p>
                        <label style="color:#3A416F;"><i class="fa fa-air-baloon"></i></label> hadir
                        <label style="color:#5e72e4;"><i class="fa fa-air-baloon"></i></label> ijin
                        <label style="color:#f56565;"><i class="fa fa-air-baloon"></i></label> alpha
                        <div class="card border mb-3">
                            <div class="card-body p-2">
                                <div class="chart">
                                    <canvas id="mixed-chart-{{ $pg->id }}" class="chart-canvas" height="300px"></canvas>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </nav>
    </div>
</div>
@endif

<script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>

<script>
    $('.select_tb').change((e) => {
        var angkatan = $('#select_angkatan').val();
        getPage(base_url + `/home/${$(e.currentTarget).val()}/` + angkatan + `/-`);
    })

    $('.select_angkatan').change((e) => {
        var tb = $('#select_tb').val();
        var periode = $('#select_periode').val();
        getPage(base_url + `/home/` + tb + `/${$(e.currentTarget).val()}/` + periode);
    })

    $('.select_periode').change((e) => {
        var angkatan = $('#select_angkatan').val();
        getPage(base_url + `/home/-/` + angkatan + `/${$(e.currentTarget).val()}`);
    })
</script>
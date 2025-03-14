@if(isset(auth()->user()->santri->jaga_malam) || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi keamanan'))
<div class="card shadow border mb-2">
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
        <center><button class="btn btn-secondary btn-block mb-0" onclick="togglePrsc()">Input Keterlambatan Pulang</button></center>
        <div id="toggle-prsc" class="pt-2" style="display:none;">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select data-mdb-filter="true" class="select form-control" value="" id="santri_id" name="santri_id" required>
                        <option value="">--pilih santri--</option>
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
                                <th class="text-uppercase text-sm text-secondary">SANTRI</th>
                                <th class="text-uppercase text-sm text-secondary">JAM PULANG</th>
                                <th class="text-uppercase text-sm text-secondary">ALASAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_telatpulang as $d)
                                <tr class="text-sm">
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
</div>
@endif

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
<div class="col-12 p-0 mb-2">
    <div class="card shadow border border p-2">
        <p class="m-0 mb-2 text-sm font-weight-bolder">Shortcut Presensi Hari Ini</p>
        <a href="{{ url('presensi/izin/persetujuan') }}" class="btn btn-primary btn-sm btn-rounded m-0 mb-2">
            Terima / Tolak Ijin
        </a>
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

@if(!auth()->user()->hasRole('superadmin'))
<div class="col-12 p-0 mb-2">
    <div class="card shadow border p-2">
        <button type="button" onclick="getReport('<?php echo base64_encode(auth()->user()->santri->id); ?>')" data-mdb-ripple-init class="btn font-weight-bolder btn-sm btn-warning btn-rounded mb-0">Lihat Laporan Saya</a>
    </div>
</div>
@endif

<div class="">
    <p class="mb-2 text-sm font-weight-bolder">Laporan Presensi</p>
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
    <div class="card shadow border p-2 mb-2">
        <div class="row">
            <div class="col-md-4 mb-2">
                <select data-mdb-filter="true" class="select select_angkatan form-control" name="select_angkatan" id="select_angkatan">
                    <option value="-">Angkatan</option>
                    @foreach($list_angkatan as $la)
                    <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select data-mdb-filter="true" class="select select_tb form-control" name="select_tb" id="select_tb">
                    <option value="-">Keseluruhan</option>
                    @foreach($tahun_bulan as $tbx)
                    <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select data-mdb-filter="true" class="select select_periode form-control" name="select_periode" id="select_periode">
                    <option value="-">Keseluruhan</option>
                    @foreach($periode_tahun as $prt)
                    <option {{ ($select_periode == $prt->periode_tahun) ? 'selected' : '' }} value="{{$prt->periode_tahun}}">{{$prt->periode_tahun}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow border">
        <nav>
            <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
                <a data-mdb-ripple-init class="nav-link active font-weight-bolder" id="nav-mahasiswa-tab" data-bs-toggle="tab" href="#nav-mahasiswa" role="tab" aria-controls="nav-mahasiswa" aria-selected="true">
                    Mahasiswa
                </a>
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                <?php $presence_group = App\Models\PresenceGroup::get(); ?>
                <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-table-tab" onclick="openTabGraf('tabtable','{{$presence_group}}')" data-bs-toggle="tab" href="#nav-table" role="tab" aria-controls="nav-table" aria-selected="false">
                    Table
                </a>
                <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-grafik-tab" onclick="openTabGraf('tabgrafik','{{$presence_group}}')" data-bs-toggle="tab" href="#nav-grafik" role="tab" aria-controls="nav-grafik" aria-selected="false">
                    Grafik
                </a>
                @endif
            </div>

            <div class="tab-content p-0 mt-2" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-mahasiswa" role="tabpanel" aria-labelledby="nav-mahasiswa-tab">
                    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
                    <div class="datatable datatable-sm border" data-mdb-pagination="false" data-mdb-fixed-header="true">
                        <table id="table-hadir" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xs font-weight-bolder ps-2">ANGKATAN<br>NAMA</th>
                                    @foreach($presence_group as $pg)
                                    <th class="text-uppercase text-center text-xs font-weight-bolder">
                                        {{strtoupper($pg->name)}}
                                        <br>
                                        H / I / A / T
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
                                        {{ $listcp->cp }} / {{ $ijin }} / {{ $all_presences[$vu->santri_id][$pg->id][0]->c_all - ($listcp->cp + $ijin) }} / {{$all_presences[$vu->santri_id][$pg->id][0]->c_all}}
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
                    @elseif(auth()->user()->hasRole('santri'))
                    <div class="row">
                        <div class="col-12">
                            <div class="p-2">
                                <label class="m-0 text-sm">Filter</label>
                                <div class="p-0">
                                    <select data-mdb-filter="true" class="select select_tb form-control" name="select_tb" id="select_tb">
                                        <option value="-">Keseluruhan</option>
                                        @foreach($tahun_bulan as $tbx)
                                        <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                @foreach($presence_group as $pg)
                                @if($pg->id!=7)
                                <div class="col-12 mb-2">
                                    <div class="">
                                        <div class="card-body p-2 text-center">
                                            <h6 class="text-sm font-weight-bolder bg-primary p-2 text-white">
                                                {{$pg->name}}
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
                                                        @if(isset($presences))
                                                        @foreach($presences as $prs)
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
                                                                $check_permit = App\Models\Permit::where('fkPresence_id', $prs->id)->where('status', 'approved')->where('fkSantri_id', auth()->user()->santri->id)->first();
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
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
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
                    <div class="datatable datatable-sm" data-mdb-pagination="false" style="display:none;">
                        <div class="card-table" id="card-table" style="display:none;">
                            <table id="tab-table" class="table align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr style="background-color:#f6f9fc;">
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">NO</th>
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">TANGGAL</th>
                                        @foreach($presence_group as $pg)
                                        <th class="text-uppercase text-center text-xs font-weight-bolder">
                                            {{$pg->name}}
                                            <br>
                                            H | I | A
                                        </th>
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
                    <div class="card-grafik" id="card-grafik" style="display:none;">
                        @foreach($presence_group as $pg)
                        <p class="mb-0 text-sm font-weight-bolder">Grafik Kehadiran {{ $pg->name }}</p>
                        <label style="color:#3A416F;"><i class="ni ni-air-baloon"></i></label> hadir
                        <label style="color:#5e72e4;"><i class="ni ni-air-baloon"></i></label> ijin
                        <label style="color:#f56565;"><i class="ni ni-air-baloon"></i></label> alpha
                        <div class="card shadow border mb-3">
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
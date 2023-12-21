@include('base.start', ['path' => '', 'title' => 'Dashboard', 'breadcrumbs' => ['Dashboard']])

<p class="mb-2 text-sm font-weight-bolder text-white">Selamat datang, {{ auth()->user()->fullname }}!</p>

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
<!-- @if(count($get_presence_today)>0) -->
<div class="col-12 mb-2">
    <div class="card shadow-lg p-3">
        <p class="m-0 mb-2 text-sm font-weight-bolder">Shortcut Presensi Hari Ini</p>
        <a href="{{ url('presensi/izin/persetujuan') }}" class="btn btn-warning btn-sm">
            Terima / Tolak Ijin
        </a>
        @foreach($get_presence_today as $gpt)
        <a href="/presensi/list/{{ $gpt->id }}" class="btn btn-primary btn-sm">
            Presensi {{ $gpt->name }}
        </a>
        @endforeach
    </div>
</div>
<!-- @endif -->
@endif

@if(!auth()->user()->hasRole('superadmin'))
<div class="col-12 mb-2">
    <div class="card shadow-lg p-3">
        <a href="#" onclick="getReport('<?php echo base64_encode(auth()->user()->santri->id); ?>')" class="btn btn-warning form-control mb-0">Lihat Laporan Saya</a>
    </div>
</div>
@endif

@if($count_dashboard!='')
<div class="card shadow-lg mb-2">
    <div class="card-body p-3">
        <p class="mb-2 text-sm font-weight-bolder">Cacah Jiwa</p>
        <?php echo $count_dashboard; ?>
    </div>
</div>
@endif

<div class="card shadow-lg mb-2 p-3">
    <p class="mb-2 text-sm font-weight-bolder">Laporan Presensi</p>
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
    <div class="p-0 d-flex mb-2">
        <select class="select_angkatan form-control" name="select_angkatan" id="select_angkatan">
            <option value="-">Pilih Angkatan</option>
            @foreach($list_angkatan as $la)
            <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
            @endforeach
        </select>
        <select class="select_tb form-control" name="select_tb" id="select_tb">
            <option value="-">Keseluruhan</option>
            @foreach($tahun_bulan as $tbx)
            <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
            @endforeach
        </select>
        <select class="select_periode form-control" name="select_periode" id="select_periode">
            <option value="-">Keseluruhan</option>
            @foreach($periode_tahun as $prt)
            <option {{ ($select_periode == $prt->periode_tahun) ? 'selected' : '' }} value="{{$prt->periode_tahun}}">{{$prt->periode_tahun}}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="tab">
        <button class="tablinks active" onclick="openTab(event, 'tabmahasiswa')">Mahasiswa</button>
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
        <button class="tablinks" onclick="openTab(event, 'tabtable')">Tabel</button>
        <button class="tablinks" onclick="openTab(event, 'tabgrafik')">Grafik</button>
        @endif
    </div>

    <div class="card-body p-0 pt-2 tabcontent" id="tabmahasiswa" style="display:block;">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
        <div class="table-responsive mt-2">
            <table id="table-hadir" class="table align-items-center mb-0">
                <thead class="thead-light">
                    <tr style="background-color:#f6f9fc;">
                        <th class="text-uppercase text-xs font-weight-bolder ps-2">NAMA</th>
                        @foreach($presence_group as $pg)
                        <th class="text-uppercase text-center text-xs font-weight-bolder">
                            {{$pg->name}}
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
                    <tr data-toggle="tooltip" data-placement="top" title="Klik unutk melihat report" class="text-sm" onclick="getReport('<?php echo base64_encode($vu->santri_id); ?>')" style="cursor:pointer;">
                        <td>
                            [{{ $vu->angkatan }}] {{ $vu->fullname }}
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
                        <td class="text-center"><i class="ni ni-atom text-info text-sm opacity-10"></i></td>
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
        <div class="row mt-2 mb-2">
            <div class="col-12">
                <div class="card shadow-lg p-3">
                    <label class="m-0 text-sm">Filter</label>
                    <div class="p-0 d-flex">
                        <select class="select_tb form-control" name="select_tb" id="select_tb">
                            <option value="-">Keseluruhan</option>
                            @foreach($tahun_bulan as $tbx)
                            <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    @foreach($presence_group as $pg)
                    <div class="col-12 mb-2">
                        <div class="card shadow-lg">
                            <div class="card-body p-2 text-center">
                                <h6 class="text-sm font-weight-bolder">
                                    {{$pg->name}}
                                    (<?php
                                        if ($datapg[$pg->id]['loopr'] != 0) {
                                            echo number_format(($datapg[$pg->id]['kehadiran'] / $datapg[$pg->id]['loopr']) *  100, 2) . "%";
                                        } else {
                                            echo "-";
                                        }
                                        ?>)
                                </h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr class="text-xs">
                                                <th class="text-uppercase font-weight-bolder">TGL</th>
                                                <th class="text-uppercase font-weight-bolder">STATUS</th>
                                                <th class="text-uppercase font-weight-bolder">TELAT</th>
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
                                                    <i class="ni ni-check-bold text-info text-sm opacity-10"></i>
                                                    @else
                                                    <span style="color:red;">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($prs->fkSantri_id!="")
                                                    @if($prs->is_late)
                                                    <i class="ni ni-check-bold text-warning text-xs opacity-10"></i>
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
        @endif
    </div>
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('koor lorong'))
    <div class="card-body p-0 pt-2 tabcontent" id="tabtable">
        <div class="table-responsive mt-2">
            <table id="table-grafik" class="table align-items-center mb-0">
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
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data_presensi['detil_presensi'] as $key=>$detpres)
                    <tr class="text-sm">
                        <td class="text-center">
                            <?php
                            echo $no;
                            $no++;
                            ?>
                        </td>
                        <td class="text-center font-weight-bolder">
                            {{ App\Helpers\CommonHelpers::hari_ini(date_format(date_create($key), "D")) }}, {{ date_format(date_create($key),"d M Y") }}
                        </td>
                        <?php
                        $persentase = 0;
                        $hadir = 0;
                        $alpha = 0;
                        ?>
                        @foreach($presence_group as $pg)
                        <td class="text-center">
                            @if(isset($detpres[$pg->id]))
                            {{ $detpres[$pg->id]['hadir'] }} |
                            {{ $detpres[$pg->id]['ijin'] }} |
                            {{ $detpres[$pg->id]['alpha'] }}
                            <?php
                            $hadir = $hadir + ($detpres[$pg->id]['hadir'] + $detpres[$pg->id]['ijin']);
                            $alpha = $alpha + $detpres[$pg->id]['alpha'];
                            ?>
                            @endif
                        </td>
                        @endforeach
                        <?php
                        $persentase = $hadir / ($hadir + $alpha) * 100;
                        ?>
                        <td class="text-center font-weight-bolder">{{ number_format($persentase,2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body p-0 pt-2 tabcontent" id="tabgrafik">
        @foreach($presence_group as $pg)
        <p class="mb-0 text-sm font-weight-bolder">Grafik Kehadiran {{ $pg->name }}</p>
        <label style="color:#3A416F;"><i class="ni ni-air-baloon"></i></label> hadir
        <label style="color:#5e72e4;"><i class="ni ni-air-baloon"></i></label> ijin
        <label style="color:#f56565;"><i class="ni ni-air-baloon"></i></label> alpha
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="mixed-chart-{{ $pg->id }}" class="chart-canvas" height="300px"></canvas>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
<script src="{{ asset('js/plugins/Chart.extension.js') }}"></script>

<script>
    var presence_group = <?php echo json_encode($presence_group); ?>;
    var data_presensi = <?php echo json_encode($data_presensi); ?>;

    presence_group.forEach(function(item, index) {
        var ctx7 = document.getElementById("mixed-chart-" + item.id).getContext("2d");
        var gradientStroke1 = ctx7.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(94,114,228,0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(94,114,228,0)'); //purple colors
        new Chart(ctx7, {
            data: {
                labels: data_presensi['tanggal_presensi'][item.id],
                datasets: [{
                        type: "bar",
                        label: "Hadir",
                        weight: 5,
                        tension: 0.4,
                        borderWidth: 0,
                        pointBackgroundColor: "#3A416F",
                        borderColor: "#3A416F",
                        backgroundColor: '#3A416F',
                        borderRadius: 4,
                        borderSkipped: false,
                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['hadir'] : [0],
                        maxBarThickness: 10,
                    },
                    {
                        type: "line",
                        label: "Ijin",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        pointBackgroundColor: "#5e72e4",
                        borderColor: "#5e72e4",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['ijin'] : 0,
                        fill: true,
                    },
                    {
                        type: "line",
                        label: "Alpha",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        pointBackgroundColor: "#f56565",
                        borderColor: "#f56565",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['alpha'] : 0,
                        fill: true,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: true,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 10,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    })

    $('#table-hadir').DataTable({
        order: [
            [10, 'asc']
        ],
        pageLength: 15
    });

    $('#table-grafik').DataTable({
        order: [],
        pageLength: 15
    });

    $('.select_tb').change((e) => {
        var angkatan = $('#select_angkatan').val();
        window.location.replace(`{{ url("/") }}/home/${$(e.currentTarget).val()}/` + angkatan + `/-`)
    })

    $('.select_angkatan').change((e) => {
        var tb = $('#select_tb').val();
        var periode = $('#select_periode').val();
        window.location.replace(`{{ url("/") }}/home/` + tb + `/${$(e.currentTarget).val()}/` + periode)
    })

    $('.select_periode').change((e) => {
        var angkatan = $('#select_angkatan').val();
        window.location.replace(`{{ url("/") }}/home/-/` + angkatan + `/${$(e.currentTarget).val()}`)
    })
</script>
@include('base.end')
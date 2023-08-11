@include('base.start', ['path' => '', 'title' => 'Dashboard', 'breadcrumbs' => ['Dashboard']])

<div class="card shadow-lg">
    <div class="card-body">
        <h6 class="mb-0">Selamat datang, {{ auth()->user()->fullname }}!</h6>
    </div>
</div>

@if(!auth()->user()->hasRole('ku'))
<div class="card shadow-lg mt-4 mb-4">
    <div class="card-body">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <!-- <div class="row mb-3">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Money</p>
                                    <h5 class="font-weight-bolder">
                                        $53,000
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-success text-sm font-weight-bolder">+55%</span>
                                        since yesterday
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Users</p>
                                    <h5 class="font-weight-bolder">
                                        2,300
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-success text-sm font-weight-bolder">+3%</span>
                                        since last week
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">New Clients</p>
                                    <h5 class="font-weight-bolder">
                                        +3,462
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-danger text-sm font-weight-bolder">-2%</span>
                                        since last quarter
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Sales</p>
                                    <h5 class="font-weight-bolder">
                                        $103,430
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-success text-sm font-weight-bolder">+5%</span> than last month
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        @endif
        <div class="p-0 d-flex">
            <select class="select_angkatan form-control" name="select_angkatan" id="select_angkatan">
                <option value="-">Pilih Angkatan</option>
                @foreach($list_angkatan as $la)
                <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">{{$la->angkatan}}</option>
                @endforeach
            </select>
            <select class="select_tb form-control" name="select_tb" id="select_tb">
                <option value="-">Keseluruhan</option>
                @foreach($tahun_bulan as $tbx)
                <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                @endforeach
            </select>
        </div>
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <div class="table-responsive mt-2">
            <table id="table-hadir" class="table align-items-center mb-0">
                <thead class="thead-light">
                    <tr style="background-color:#f6f9fc;">
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">NAMA</th>
                        @foreach($presence_group as $pg)
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">
                            {{$pg->name}}
                            <br>
                            H / I / A / T
                        </th>
                        @endforeach
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">TOTAL<br>HADIR</th>
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">TOTAL<br>IJIN</th>
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">TOTAL<br>ALPHA</th>
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">TOTAL<br>KBM</th>
                        <th class="text-uppercase text-center text-sm text-secondary font-weight-bolder">TOTAL<br>PERSENTASE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($view_usantri as $vu)
                    <tr data-toggle="tooltip" data-placement="top" title="Klik unutk melihat report" class="text-sm" onclick="getReport('<?php echo $vu->ids; ?>')" style="cursor:pointer;">
                        <td>
                            [{{ $vu->angkatan }}] {{ $vu->fullname }}
                        </td>
                        <?php
                        $all_persantase = 0;
                        $all_kbm = 0;
                        $all_hadir = 0;
                        $all_alpha = 0;
                        $all_ijin = 0;
                        ?>
                        @foreach($presence_group as $pg)
                        <td class="text-center">
                            @foreach($presences[$pg->id] as $listcp)
                            @if($listcp->santri_id==$vu->santri_id)
                            <?php
                            $ijin = 0;
                            if (isset($all_permit[$pg->id][$vu->santri_id])) {
                                $ijin = $all_permit[$pg->id][$vu->santri_id];
                            }
                            ?>
                            {{ $listcp->cp }} / {{ $ijin }} / {{ $all_presences[$pg->id][0]->c_all - ($listcp->cp + $ijin) }} / {{$all_presences[$pg->id][0]->c_all}}
                            <?php
                            if ($all_presences[$pg->id][0]->c_all == 0) {
                                $persentase = 0;
                            } else {
                                $persentase = number_format(($listcp->cp + $ijin) / $all_presences[$pg->id][0]->c_all * 100, 2);
                            }
                            $all_kbm = $all_kbm + $all_presences[$pg->id][0]->c_all;
                            $all_hadir = $all_hadir + $listcp->cp;
                            $all_ijin = $all_ijin + $ijin;
                            $all_alpha = $all_kbm - ($all_hadir + $all_ijin);
                            ?>
                            <span class="font-weight-bolder {{($persentase<80) ? 'text-danger' : '' }}">({{ $persentase }}%)</span>
                            @endif
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
                            $all_persantase = ($all_hadir + $all_ijin) / $all_kbm * 100;
                            ?>
                            <span class="font-weight-bolder {{ ($all_persantase<80) ? 'text-danger' : ''}}">
                                {{ number_format($all_persantase,2) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif(auth()->user()->hasRole('santri'))
        <div class="row">
            @foreach($presence_group as $pg)
            <div class="col-sm">
                <h6 class="mt-4 mb-0">
                    {{$pg->name}}
                    (<?php
                        if ($datapg[$pg->id]['loopr'] != 0) {
                            echo number_format(($datapg[$pg->id]['kehadiran'] / $datapg[$pg->id]['loopr']) *  100, 2) . "%";
                        } else {
                            echo "-";
                        }
                        ?>)
                </h6>
                <div class="table-responsive" style="background-color:#f9f9f9;border-radius:8px;">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Tanggal</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Kehadiran</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Terlambat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($presences))
                            @foreach($presences as $prs)
                            @if($pg->id==$prs->fkPresence_group_id)
                            <tr class="text-sm">
                                <td>
                                    {{ date_format(date_create($prs->event_date), 'd') }}
                                </td>
                                <td>
                                    @if($prs->fkSantri_id!="")
                                    Hadir
                                    @else
                                    <span style="color:red;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($prs->fkSantri_id!="")
                                    {{ ($prs->is_late) ? 'Ya' : 'Tidak' }}
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
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif
@include('base.end')

<script>
    $('#table-hadir').DataTable({
        order: [
            // [1, 'desc']
        ],
        pageLength: 15
    });

    $('.select_tb').change((e) => {
        var angkatan = $('#select_angkatan').val();
        window.location.replace(`{{ url("/") }}/home/${$(e.currentTarget).val()}/` + angkatan)
    })

    $('.select_angkatan').change((e) => {
        var tb = $('#select_tb').val();
        window.location.replace(`{{ url("/") }}/home/` + tb + `/${$(e.currentTarget).val()}`)
    })
</script>
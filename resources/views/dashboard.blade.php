@include('base.start', ['path' => '', 'title' => 'Dashboard', 'breadcrumbs' => ['Dashboard']])

<div class="card shadow-lg">
    <div class="card-body">
        <h6 class="mb-0">Selamat datang, {{ auth()->user()->fullname }}!</h6>
    </div>
</div>

@if(!auth()->user()->hasRole('ku'))
<div class="card shadow-lg mt-4 mb-4">
    <!-- <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Presensi {{$tb}}</h6>
    </div> -->
    <div class="card-body pt-2 pb-2">
        @if(auth()->user()->hasRole('superadmin'))
        [ENHANCHEMENT]<br>
        - Monitoring Materi<br>
        - After INsert/Update User, add Mobile Number to sp_whatsapp_contact<br>
        - Insert to sp_whatsapp_schedules for Koor Lorong<br>
        - Case jika mahasiswa belum dapet koor lorong<br>
        <br>
        @endif
        <center>Pilih Tahun-Bulan:</center>
        <div class="p-0 d-flex">
            <select class="select_tb form-control" name="select_tb" id="select_tb">
                <option value="-">Filter Tahun Bulan</option>
                @foreach($tahun_bulan as $tbx)
                <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                @endforeach
            </select>
        </div>
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru'))
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">NAMA</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ANGKATAN</th>
                        @foreach($presence_group as $pg)
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$pg->name}}</th>
                        @endforeach
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">% ALL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($view_usantri as $vu)
                    <tr class="text-sm">
                        <td>
                            {{ $vu->fullname }}
                        </td>
                        <td>
                            {{ $vu->angkatan }}
                        </td>
                        <?php $all_persantase = 0; ?>
                        @foreach($presence_group as $pg)
                        <td>
                            @foreach($presences[$pg->id] as $listcp)
                            @if($listcp->santri_id==$vu->santri_id)
                            {{ $listcp->cp }} / {{$all_presences[$pg->id][0]->c_all}}
                            <?php
                            if ($all_presences[$pg->id][0]->c_all == 0) {
                                $persentase = 0;
                            } else {
                                $persentase = number_format(($listcp->cp / $all_presences[$pg->id][0]->c_all) * 100, 2);
                            }
                            $all_persantase = $all_persantase + $persentase;
                            ?>
                            <span class="font-weight-bolder {{($persentase<75) ? 'text-danger' : '' }}">({{ $persentase }}%)</span>
                            @endif
                            @endforeach
                        </td>
                        @endforeach
                        <td>
                            <span class="font-weight-bolder {{ (($all_persantase/3)<75) ? 'text-danger' : ''}}">
                                {{ number_format($all_persantase/3,2) }}%
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
                <div class="table-responsive" style="background-color:#efefef;border-radius:8px;">
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
    $('.select_tb').change((e) => {
        window.location.replace(`{{ url("/") }}/home/${$(e.currentTarget).val()}`)
    })
</script>
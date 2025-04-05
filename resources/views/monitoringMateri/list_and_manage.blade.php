<?php
function printMateriOptions($materis, $santri)
{
    foreach ($materis as $materi) {
        if ($materi->for == 'mubalegh' && !$santri->user->hasRole('mubalegh'))
            continue;
        if ($materi->for != 'mubalegh' && $santri->user->hasRole('mubalegh'))
            continue;
        $completedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'complete')->count();
        $partiallyCompletedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'partial')->count();
        $totalPages = $completedPages + ($partiallyCompletedPages / 2);
?>
        <tr class="text-sm">
            <td class="p-1 ps-2">{{ $materi->name }}</td>
            <td class="p-1 ps-2">{{ $totalPages."/".$materi->pageNumbers." page = ".number_format((float) $totalPages / $materi->pageNumbers * 100, 2, '.', '') }}%</td>
            <td class="p-1 ps-2">
                <a {{ (!auth()->user()->santri) ? 'target="_blank"' : '' }}' href="{{ route('edit monitoring materi', [$materi->id, $santri->id])}}" class="btn btn-success btn-sm mb-0">Lihat</a>
            </td>
        </tr>
<?php
    }
}

?>
<style>
    .santris-list {
        display: none;
    }

    .lorongs-list-item h6 {
        user-select: none;
        cursor: pointer;
    }
</style>

<div class="card border mb-2">
    <div class="card-body p-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bolder">Daftar Monitoring Materi</h6>
        @if(!auth()->user()->hasRole('santri'))
        <a href="{{ route('create materi') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat Materi
        </a>
        @endif
    </div>
</div>

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi kurikulum') || auth()->user()->hasRole('dewan guru'))
    <div class="row">
        <div class="col-md-6 mb-2">
            <div class="card">
                <div class="card-header p-0">
                    <h6 class="text-center text-lg text-white mb-0 bg-secondary p-2">Kelas Reguler</h6>
                </div>
                <div class="card-body p-2">
                    @can('view monitoring materis list')
                    <div class="datatable datatable-sm" data-mdb-pagination="false">
                        <table id="table-mhs-reg" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">NAMA</th>
                                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    @if(!$user->santri->user->hasRole('mubalegh'))
                                    <tr style="cursor:pointer;">
                                        <td>
                                            {{$user->fullname}}
                                            <br><small>
                                                <i>{{ $user->santri->fkLorong_id!='' ? $user->santri->lorong->name : ($user->santri->lorongUnderLead ? $user->santri->lorongUnderLead->name : '') }}</i>
                                            </small>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-warning" onclick="getMateri('<?php echo $user->santri->id; ?>','<?php echo $user->fullname; ?>')" block-id="return-false">Cari</a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header p-0">
                    <h6 class="text-center text-lg text-white mb-0 bg-secondary p-2">Kelas Muballigh</h6>
                </div>
                <div class="card-body p-2">
                    <ul class="list-group">
                        @can('view monitoring materis list')
                        <div class="datatable datatable-sm" data-mdb-pagination="false">
                            <table id="table-mhs-mt" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">NAMA</th>
                                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    @if($user->santri->user->hasRole('mubalegh'))
                                    <tr>
                                        <td>
                                            {{$user->fullname}}
                                            <br><small>
                                                <i>{{ $user->santri->fkLorong_id!='' ? $user->santri->lorong->name : ($user->santri->lorongUnderLead ? $user->santri->lorongUnderLead->name : '') }}</i>
                                            </small>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-warning" onclick="getMateri('<?php echo $user->santri->id; ?>','<?php echo $user->fullname; ?>')" block-id="return-false">Cari</a>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card shadow border mb-3">
        <div class="card-body p-2">
            @if (session('success'))
            <div class="alert alert-success text-white">
                {{ session('success') }}
            </div>
            @endif

            @if(sizeof($lorongs) >= 0)
                <div class="datatable datatable-sm">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">MATERI SAYA</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">PENCAPAIAN</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php printMateriOptions($materis, auth()->user()->santri) ?>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endif

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    $('#closeMateri').click(function() {
        $('#exampleModalMateri').fadeOut();
        $('#contentMateri').html('<td colspan="3"><span class="text-center">Loading...</span></td>');
    });
</script>
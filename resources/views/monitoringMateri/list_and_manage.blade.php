@include('base.start', ['path' => 'materi/monitoring/list', 'title' => 'Daftar Monitoring Materi', 'breadcrumbs' => ['Daftar Monitoring Materi']])

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
        <tr class="text-xs">
            <td class="p-0">{{ $materi->name }}</td>
            <td class="p-0">{{ $totalPages."/".$materi->pageNumbers." page = ".number_format((float) $totalPages / $materi->pageNumbers * 100, 2, '.', '') }}%</td>
            <td class="p-0">
                <a {{ (!auth()->user()->santri) ? 'target="_blank"' : '' }}' href="{{ route('edit monitoring materi', [$materi->id, $santri->id])}}" class="btn btn-success btn-xs mb-0">Lihat</a>
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

<div class="card mb-3">
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Monitoring Materi</h6>
        @if(!auth()->user()->hasRole('santri'))
        <a href="{{ route('create materi') }}" class="btn btn-primary">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat Materi
        </a>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-body pt-0 p-3">
        @if (session('success'))
        <div class="alert alert-success text-white">
            {{ session('success') }}
        </div>
        @endif
        @if(!auth()->user()->hasRole('santri'))
        <!-- <input id="search" placeholder="Cari nama..." class="form-control mb-4" type="text"> -->
        @endif
        @if(sizeof($lorongs) <= 0) Belum ada data. @endif @if(auth()->user()->santri)
            <div class="row">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">MATERI SAYA</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">PENCAPAIAN</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php printMateriOptions($materis, auth()->user()->santri) ?>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
    </div>
</div>

<div class="card">
    <div class="card-body p-3">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group">
                    @can('view monitoring materis list')
                    @foreach($users as $user)
                    @if(!$user->santri->user->hasRole('mubalegh'))
                    <li class="list-group-item lorongs-list-item border-0 pt-1 pb-1 pl-2 mb-2 bg-gray-100 border-radius-lg">
                        <div class="d-flex flex-column">
                            <h6 class="text-sm mb-0">{{ $user->fullname }} <i class="fas fa-caret-down ms-2" aria-hidden="true"></i></h6>
                        </div>
                        <ul class="list-group santris-list">
                            <li class="list-group-item members-list-item pt-0" style="background:none;border:none">
                                <div class="row">
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table align-items-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">MATERI</th>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">PENCAPAIAN</th>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        printMateriOptions($materis, $user->santri);
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @endforeach
                    @endcan
                </ul>
            </div>

            <div class="col-md-6">
                <ul class="list-group">
                    @can('view monitoring materis list')
                    @foreach($users as $user)
                    @if($user->santri->user->hasRole('mubalegh'))
                    <li class="list-group-item lorongs-list-item border-0 pt-1 pb-1 pl-2 mb-2 bg-gray-100 border-radius-lg">
                        <div class="d-flex flex-column">
                            <h6 class="text-sm mb-0">{{ $user->fullname }} <i class="fas fa-caret-down ms-2" aria-hidden="true"></i></h6>
                        </div>
                        <ul class="list-group santris-list">
                            <li class="list-group-item members-list-item pt-0" style="background:none;border:none">
                                <div class="row">
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table align-items-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">MATERI</th>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">PENCAPAIAN</th>
                                                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        printMateriOptions($materis, $user->santri);
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @endforeach
                    @endcan
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    $('.lorongs-list-item h6').click((e) => {
        if ($(e.currentTarget).parent().parent().find('.santris-list').css('display') == 'none')
            $(e.currentTarget).parent().parent().find('.santris-list').show();
        else
            $(e.currentTarget).parent().parent().find('.santris-list').hide();

        if ($(e.currentTarget).find('.fa-caret-down').length > 0)
            $(e.currentTarget).find('.fa-caret-down').removeClass('fa-caret-down').addClass('fa-caret-up');
        else
            $(e.currentTarget).find('.fa-caret-up').removeClass('fa-caret-up').addClass('fa-caret-down');
    })
</script>
@include('base.end')
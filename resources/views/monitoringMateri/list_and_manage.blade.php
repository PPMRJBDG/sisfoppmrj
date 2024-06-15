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

<div class="card bg-primary text-light mb-3">
    <div class="card-body p-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Monitoring Materi</h6>
        @if(!auth()->user()->hasRole('santri'))
        <a href="{{ route('create materi') }}" class="btn btn-primary">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat Materi
        </a>
        @endif
    </div>
</div>

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi kurikulum'))
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-2">
                <h6 class="text-center text-lg text-primary">Kelas Reguler</h6>
                @can('view monitoring materis list')
                <div class="table-responsive">
                    <table id="table-mhs-reg" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">NAMA</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">LORONG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            @if(!$user->santri->user->hasRole('mubalegh'))
                            <tr onclick="getMateri('<?php echo $user->santri->id; ?>','<?php echo $user->fullname; ?>')" style="cursor:pointer;">
                                <td>{{$user->fullname}}</td>
                                <td>{{ $user->santri->fkLorong_id!='' ? $user->santri->lorong->name : ($user->santri->lorongUnderLead ? $user->santri->lorongUnderLead->name : '') }}</td>
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
            <div class="card-body p-2">
                <h6 class="text-center text-lg text-primary">Kelas MT</h6>
                <ul class="list-group">
                    @can('view monitoring materis list')
                    <div class="table-responsive">
                        <table id="table-mhs-mt" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">NAMA</th>
                                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">LORONG</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                @if($user->santri->user->hasRole('mubalegh'))
                                <tr onclick="getMateri('<?php echo $user->santri->id; ?>','<?php echo $user->fullname; ?>')" style="cursor:pointer;">
                                    <td>{{$user->fullname}}</td>
                                    <td>{{ $user->santri->fkLorong_id!='' ? $user->santri->lorong->name : ($user->santri->lorongUnderLead ? $user->santri->lorongUnderLead->name : '') }}</td>
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
<div class="card mb-3">
    <div class="card-body p-2">
        @if (session('success'))
        <div class="alert alert-success text-white">
            {{ session('success') }}
        </div>
        @endif

        @if(sizeof($lorongs) >= 0)
        <div class="row">
            <div class="table-responsive">
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
        </div>
        @endif
    </div>
</div>
@endif

<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel">Pencapaian Materi</h6>
                    <h5 class="modal-title" id="exampleModalLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">MATERI</th>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">PENCAPAIAN</th>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="contentMateri">
                        <tr>
                            <td colspan="3">
                                <span class="text-center">
                                    Loading...
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
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

    function getMateri(santri_id, nama) {
        $('#exampleModal').fadeIn();
        $('#exampleModal').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModal').css('z-index', '10000');
        $('#exampleModalLabel span#nm').text(nama);
        $.post("{{ route('materi santri') }}", {
                santri_id: santri_id
            },
            function(data) {
                $('#contentMateri').html(data);
            }
        );
    }

    $('#close').click(function() {
        $('#exampleModal').fadeOut();
        $('#contentMateri').html('<td colspan="3"><span class="text-center">Loading...</span></td>');
    });

    $('#table-mhs-reg').DataTable({
        order: [
            // [1, 'desc']
        ],
        pageLength: 25
    });
    $('#table-mhs-mt').DataTable({
        order: [
            // [1, 'desc']
        ],
        pageLength: 25
    });
</script>
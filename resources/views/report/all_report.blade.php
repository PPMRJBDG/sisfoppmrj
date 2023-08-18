@include('base.start_without_bars', ['path' => 'presensi/list', 'containerClass' => 'p-0', 'title' => "Laporan Mahasiswa"])

<style>
    @media only screen and (max-width: 600px) {

        body,
        h6 {
            font-size: 0.8rem !important;
        }
    }

    .py-4 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

@if($santri==null)
<div class="p-2">
    <div class="card mb-2">
        <div class="card-body p-2">
            <center>
                <h6 class="m-0" style="font-size:16px!important;">Data Mahasiswa Tidak Ditemukan</h6>
            </center>
        </div>
    </div>
</div>
@else
<div class="p-2">
    <div class="card mb-2">
        <div class="card-body p-2">
            <center>
                <h4 class="m-0">{{ $santri->user->fullname }}</h4>
            </center>
        </div>
    </div>
    @if(count($pelanggaran)>0)
    <h4 class="text-white text-center mb-0">Riwayat Pelanggaran</h4>
    <div class="card mb-2">
        <div class="card-body p-2">
            <h6>Berdasarkan catatan ketertiban, an. {{ $santri->user->fullname }} terdapat pelanggaran:</h6>
            <div class="table-responsive p-0">
                <table id="recap-table" class="table align-items-center mb-0">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase ps-0 font-weight-bolder">Jenis</th>
                            <th class="text-uppercase text-center font-weight-bolder">SP</th>
                            <th class="text-uppercase text-center font-weight-bolder">Tanggal</th>
                            <!-- <th class="text-uppercase text-center font-weight-bolder">Status</th> -->
                            <!-- <th class="text-uppercase text-center font-weight-bolder"></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggaran as $plg)
                        <tr>
                            <td class="ps-0">
                                <h6 class="mb-0">{{ $plg->jenis->jenis_pelanggaran }}</h6>
                            </td>
                            <td class="text-center">
                                <h6 class="mb-0">SP {{ $plg->keringanan_sp }}</h6>
                            </td>
                            <td class="text-center">
                                @if($plg->tanggal_melanggar!='')
                                <h6 class="mb-0">{{ date_format(date_create($plg->tanggal_melanggar),'d M Y') }}</h6>
                                @endif
                            </td>
                            <!-- <td class="text-center">
                                @if($plg->is_archive==1)
                                <i class="ni ni-satisfied"></i>
                                @else
                                <i class="ni ni-notification-70 text-danger"></i>
                                @endif
                            </td> -->
                            <!-- <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary mb-0">Detil</button>
                            </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- <i class="ni ni-satisfied"></i>: Pemutihan, namun tetap harus dikontrol dan selalu dinasehati
            <br>
            <i class="ni ni-notification-70 text-danger"></i>: Harus dikontrol dan selalu dinasehati agar tidak terulang lagi -->
        </div>
    </div>
    @endif

    <?php
    $all_kbm = 0;
    $all_hadir = 0;
    $all_ijin = 0;
    $all_alpha = 0;
    ?>
    <h4 class="text-white text-center mb-0">Presensi Kehadiran</h4>
    @if($datapg!=null)
    <div class="card mb-2">
        <div class="card-body p-2 text-center">
            <table class=" table align-items-center mb-0">
                <thead>
                    <th class="text-center p-1">Total Keseluruhan</th>
                </thead>
                <tbody>
                    <tr>
                        <table class=" table align-items-center mb-0">
                            <thead>
                                <th class="text-center p-1">Total KBM</th>
                                <th class="text-center p-1">H</th>
                                <th class="text-center p-1">I</th>
                                <th class="text-center p-1">A</th>
                                <th class="text-center p-1">%</th>
                            </thead>
                            <tbody id="total-all">
                            </tbody>
                        </table>
                    </tr>
                </tbody>
            </table>
            <h6 class="mb-0" id="warning-ortu" class="bg-secondary" style="display:none;">
                Perhatian untuk Orang Tua, Amalsholih selalu dinasehati dan dimotivasi agar kehadiran dan kefahaman tambah meningkat
            </h6>
        </div>
    </div>
    <div class="card mb-2">
        <div class="card-body p-2">
            @foreach($tahun as $th)
            <div id="toggle-th font-weight-builder" class="mb-2">
                <button onclick="toggleTh('th{{$th->y}}')" style="width:100%!important;" type="button" class="mb-0 btn btn-outline-primary btn-small btn-block">Klik Presensi Tahun {{ $th->y }}</button>
            </div>
            <div class="table-responsive p-0" id="th{{$th->y}}" style="display:none;">
                <table id="recap-table" class="table align-items-center mb-0">
                    <tbody>
                        <?php
                        ?>
                        @foreach($tahun_bulan as $tb)
                        @if(substr($tb->ym,0,4)==$th->y)
                        <tr>
                            <th class="text-uppercase ps-0 font-weight-bolder" style="background-color:#f6f9fc;">Periode {{date_format(date_create($tb->ym),'M Y')}}</th>
                        </tr>
                        <tr>
                            <td class="pa-0">
                                <table class=" table align-items-center mb-0">
                                    <thead>
                                        <th class="text-center p-1">JUMLAH KBM</th>
                                        <th class="text-center p-1">H</th>
                                        <th class="text-center p-1">I</th>
                                        <th class="text-center p-1">A</th>
                                        <th class="text-center p-1">%</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_kbm = 0;
                                        $total_hadir = 0;
                                        $total_ijin = 0;
                                        $total_alpha = 0;
                                        $persentase = 0;
                                        $content = '';
                                        ?>
                                        @foreach($presence_group as $pg)
                                        <?php
                                        $total_kbm = $total_kbm + $datapg[$tb->ym][$pg->id]['kbm'];
                                        $total_hadir = $total_hadir + $datapg[$tb->ym][$pg->id]['hadir'];
                                        $total_ijin = $total_ijin + $datapg[$tb->ym][$pg->id]['ijin'];
                                        $total_alpha = $total_alpha + $datapg[$tb->ym][$pg->id]['alpha'];
                                        $content = $content . '
                                        <tr>
                                            <td class="ps-0">
                                                <h6 class="mb-0">' . $pg->name . '</h6>
                                            </td>
                                            <td class="text-center">
                                            ' . $datapg[$tb->ym][$pg->id]['kbm'] . '
                                            </td>
                                            <td class="text-center text-success">
                                            ' . $datapg[$tb->ym][$pg->id]['hadir'] . '
                                            </td>
                                            <td class="text-center text-warning">
                                            ' . $datapg[$tb->ym][$pg->id]['ijin'] . '
                                            </td>
                                            <td class="text-center text-danger">
                                            ' . $datapg[$tb->ym][$pg->id]['alpha'] . '
                                            </td>
                                        </tr>';
                                        ?>
                                        @endforeach
                                        <?php
                                        if ($total_hadir == 0 && $total_ijin == 0) {
                                            $persentase = 0;
                                        } else {
                                            $persentase = ($total_hadir + $total_ijin) / $total_kbm * 100;
                                        }
                                        $color = 'primary';
                                        if ($persentase < 80) {
                                            $color = 'danger';
                                        }
                                        $all_kbm = $all_kbm + $total_kbm;
                                        $all_hadir = $all_hadir + $total_hadir;
                                        $all_ijin = $all_ijin + $total_ijin;
                                        $all_alpha = $all_alpha + $total_alpha;
                                        ?>
                                        <th class="text-center p-1">{{ $total_kbm }}</th>
                                        <th class="text-center p-1">{{ $total_hadir }}</th>
                                        <th class="text-center p-1">{{ $total_ijin }}</th>
                                        <th class="text-center p-1">{{ $total_alpha }}</th>
                                        <th class="text-center text-{{$color}} font-weight-bolder p-1">{{ number_format($persentase,2) }}%</th>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card mb-2">
        <div class="card-body p-2 text-center">
            Belum dimulai KBM
        </div>
    </div>
    @endif

    <h4 class="text-white text-center mb-0">Pencapaian Materi</h4>
    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="table-responsive p-0">
                <table id="recap-table" class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-sm font-weight-bolder ps-0">MATERI</th>
                            <th class="text-uppercase text-sm font-weight-bolder ps-0">PENCAPAIAN</th>
                            <th class="text-uppercase text-sm font-weight-bolder ps-0">%</th>
                        </tr>
                    </thead>
                    <tbody id="contentMateri">
                        <?php echo $data_materi; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$all_percent = 0;
if ($all_kbm > 0) {
    $all_percent = number_format(($all_hadir + $all_ijin) / $all_kbm * 100, 2);
}
?>
<script>
    function toggleTh(idtoggleth) {
        $('#' + idtoggleth).toggle();
    }
    $('main.main-content').attr('class', 'main-content position-relative border-radius-lg bg-primary');
    var all_percent = <?php echo $all_percent; ?>;
    $('#total-all').html(
        '<tr>' +
        '<th class="text-center p-1"><h5 class="mb-0"><?php echo $all_kbm; ?></h5></th>' +
        '<th class="text-center p-1"><h5 class="mb-0"><?php echo $all_hadir; ?></h5></th>' +
        '<th class="text-center p-1"><h5 class="mb-0"><?php echo $all_ijin; ?></h5></th>' +
        '<th class="text-center p-1"><h5 class="mb-0"><?php echo $all_alpha; ?></h5></th>' +
        '<th class="text-center p-1"><h3 class="mb-0" id="all-percent">' + all_percent + '%</h3></th>' +
        '</tr>'
    );
    if (all_percent < 80) {
        $('#all-percent').attr('class', 'text-danger mb-0');
        $('#warning-ortu').show();
    }
</script>
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
                <table class="table align-items-center">
                    <thead>
                        <th class="text-uppercase ps-0 font-weight-bolder">Jadwal</th>
                        <th class="text-uppercase text-center font-weight-bolder">Jumlah KBM</th>
                        <th class="text-uppercase text-center font-weight-bolder">H</th>
                        <th class="text-uppercase text-center font-weight-bolder">I</th>
                        <th class="text-uppercase text-center font-weight-bolder">A</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
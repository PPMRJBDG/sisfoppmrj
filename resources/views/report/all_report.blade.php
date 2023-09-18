@include('base.start_without_bars', ['path' => 'presensi/list', 'containerClass' => 'p-0', 'title' => "Laporan Mahasiswa"])

<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

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

    /* Style the tab */
    .tab {
        overflow: hidden;
        background-color: #f1f1f1;
        border-radius: 8px 8px 0 0;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px !important;
        transition: 0.3s;
        font-size: 17px;
    }

    .tablinks {
        font-weight: bold !important;
        font-size: 14px !important;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
        border-bottom: solid 4px #5e72e4;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border-top: none;
        background-color: #fff;
        border-radius: 0 0 8px 8px;
    }
</style>

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

    @if(count($sodaqoh)>0)
    <h4 class="text-white text-center mb-0">Pembayaran Sodaqoh</h4>
    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="table-responsive p-0">
                <table id="recap-table" class="table align-items-center mb-0">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase text-center font-weight-bolder">Periode<br>Nominal</th>
                            <th class="text-uppercase text-center font-weight-bolder">Status<br>Kekurangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bulan = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags', 'sep', 'okt', 'nov', 'des'];
                        ?>
                        @foreach($sodaqoh as $sdq)
                        <?php
                        $kekurangan = 0;
                        $terbayar = 0;
                        foreach ($bulan as $b) {
                            $terbayar = $terbayar + $sdq->$b;
                        }
                        $kekurangan = $sdq->nominal - $terbayar;
                        ?>
                        <tr class="text-center">
                            <td>
                                {{ $sdq->periode }}
                                <br>
                                <b>{{ number_format($sdq->nominal,0) }}</b>
                            </td>
                            <td>
                                <b>{{ ($sdq->status_lunas==1) ? 'Lunas' : 'Belum Lunas' }}</b>
                                <br>
                                {{ ($sdq->status_lunas==1) ? 'Alhamdulillah' : 'Kurang '.number_format($kekurangan,0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            <th class="text-center p-1">Total Keseluruhan</th>
            <table class=" table align-items-center mb-0">
                <thead>
                    <tr style="background-color:#f6f9fc;">
                        <th class="text-center p-1">KBM</th>
                        <th class="text-center p-1">H</th>
                        <th class="text-center p-1">I</th>
                        <th class="text-center p-1">A</th>
                        <th class="text-center p-1">%</th>
                    </tr>
                </thead>
                <tbody id="total-all">
                </tbody>
            </table>
            <h6 class="mb-0" id="warning-ortu" class="bg-secondary" style="display:none;">
                Perhatian untuk Orang Tua, Amalsholih selalu dinasehati dan dimotivasi agar kehadiran dan kefahaman tambah meningkat
            </h6>
        </div>

        <div class="tab">
            @foreach($tahun as $th)
            <?php
            $now = date("Y");
            $act = '';
            if ($now == $th->y) {
                $act = 'active';
            }
            ?>
            <button class="tablinks {{$act}}" onclick="openPresensi(event, 'th{{ $th->y }}')">{{ $th->y }}</button>
            @endforeach
        </div>
        @foreach($tahun as $th)
        <?php
        $now = date("Y");
        $display = 'none';
        if ($now == $th->y) {
            $display = 'block';
        }
        ?>
        <div class="table-responsive p-0 tabcontent" id="th{{$th->y}}" style="display:{{$display}};">
            <table id="recap-table" class="table align-items-center mb-0">
                <thead>
                    <tr style="background-color:#f6f9fc;">
                        <th class="text-center p-1">BULAN</th>
                        <th class="text-center p-1">KBM</th>
                        <th class="text-center p-1">H</th>
                        <th class="text-center p-1">I</th>
                        <th class="text-center p-1">A</th>
                        <th class="text-center p-1">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    ?>
                    @foreach($tahun_bulan as $tb)
                    @if(substr($tb->ym,0,4)==$th->y)
                    <tr>
                        <?php
                        $total_kbm = 0;
                        $total_hadir = 0;
                        $total_ijin = 0;
                        $total_alpha = 0;
                        $persentase = 0;
                        ?>
                        @foreach($presence_group as $pg)
                        <?php
                        $total_kbm = $total_kbm + $datapg[$tb->ym][$pg->id]['kbm'];
                        $total_hadir = $total_hadir + $datapg[$tb->ym][$pg->id]['hadir'];
                        $total_ijin = $total_ijin + $datapg[$tb->ym][$pg->id]['ijin'];
                        $total_alpha = $total_alpha + $datapg[$tb->ym][$pg->id]['alpha'];
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
                        <th class="text-center p-1">
                            <h6 class="mb-0">{{ date_format(date_create($tb->ym), 'M') }}</h6>
                        </th>
                        <th class="text-center p-1">
                            <h6 class="mb-0">{{ $total_kbm }}</h6>
                        </th>
                        <th class="text-center p-1">
                            <h6 class="mb-0">{{ $total_hadir }}</h6>
                        </th>
                        <th class="text-center p-1">
                            <h6 class="mb-0">{{ $total_ijin }}</h6>
                        </th>
                        <th class="text-center p-1">
                            <h6 class="mb-0">{{ $total_alpha }}</h6>
                        </th>
                        <th class="text-center font-weight-bolder p-1">
                            <h6 class="mb-0 text-{{$color}}">{{ number_format($persentase,2) }}%</h6>
                        </th>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
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
                            <th class="text-uppercase font-weight-bolder ps-0">MATERI</th>
                            <th class="text-uppercase font-weight-bolder ps-0">PENCAPAIAN</th>
                            <th class="text-uppercase font-weight-bolder ps-0">%</th>
                        </tr>
                    </thead>
                    <tbody id="contentMateri">
                        <?php echo $data_materi; ?>
                    </tbody>
                </table>
            </div>
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
                            <th class="text-uppercase ps-0 font-weight-bolder">[SP] Pelanggaran</th>
                            <th class="text-uppercase text-center font-weight-bolder">Tanggal SP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggaran as $plg)
                        <tr>
                            <td class="ps-0">
                                <h6 class="mb-0">[SP {{ $plg->keringanan_sp }}] {{ $plg->jenis->jenis_pelanggaran }}</h6>
                            </td>
                            <td class="text-center">
                                @if($plg->is_surat_peringatan!='')
                                <h6 class="mb-0">{{ date_format(date_create($plg->is_surat_peringatan),'d M Y') }}</h6>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
<?php
$all_percent = 0;
if ($all_kbm > 0) {
    $all_percent = number_format(($all_hadir + $all_ijin) / $all_kbm * 100, 2);
}
?>
<script>
    function openPresensi(evt, tahun) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tahun).style.display = "block";
        evt.currentTarget.className += " active";
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
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">ClKeluarose</button>
            </div>
        </div>
    </div>
</div>
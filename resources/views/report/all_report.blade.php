@include('base.start_without_bars', ['path' => 'presensi/list', 'containerClass' => 'p-0', 'title' => "Laporan Mahasiswa"])

<style>
    .datatable.datatable-sm td,
    .datatable.datatable-sm th,
    .datatablex.datatable-sm td,
    .datatablex.datatable-sm th {
        padding: .5rem 0.5rem !important;
    }
    .datatable thead,
    .datatable thead tr {
        background-color: #f6f9fc !important;
    }
    .nav-link {
        display: block;
        padding: .5rem 1rem !important;
    }
    .table tbody {
        font-weight: 400;
    }
    .select-arrow {
      font-size: .5rem !important;
    }
</style>

@if($santri==null)
<div class="p-2">
    <div class="card border mb-2">
        <div class="card-body p-2">
            <center>
                <h6 class="m-0" style="font-size:16px!important;">Data Mahasiswa Tidak Ditemukan</h6>
            </center>
        </div>
    </div>
</div>
@else
<div class="p-2 pb-0 pt-0">
    <div class="card border mb-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <div class="card-body p-2">
            <center>
                <h6 class="m-0">{{ $santri->user->fullname }}</h6>
            </center>
        </div>
    </div>

    <center>
    <div class="mb-2 position-relative">
        <i class="fas fa-user fa-2x {{$score_text}} mb-0"></i>
        <h5 class="{{$score_text}} fw-bold mb-0"><small>SCORE</small> {{ number_format($score, 2) }}</h5>
        <h6 class="fw-normal {{$score_text}} mb-0">{{$score_desc}}</h6>
    </div>
    </center>

    @if(count($sodaqoh)>0)
    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <h6 class="text-center mb-1">Pembayaran Sodaqoh</h6>
        <div class="card-body p-0">
            <div class="datatable datatable-sm p-0" data-mdb-pagination="false">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr style="background-color:#f6f9fc;">
                            <th class="text-uppercase text-center font-weight-bolder">Periode</th>
                            <th class="text-uppercase text-center font-weight-bolder">Nominal</th>
                            <th class="text-uppercase text-center font-weight-bolder">Status</th>
                            <th class="text-uppercase text-center font-weight-bolder">Kekurangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
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
                        <tr class="text-center font-weight-bolder">
                            <td>
                                {{ $sdq->periode }}
                            </td>
                            <td>
                                {{ number_format($sdq->nominal,0) }}
                            </td>
                            <td>
                                {{ ($sdq->status_lunas==1) ? 'Lunas' : 'Belum Lunas' }}
                            </td>
                            <td>
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
    @if($datapg!=null)
    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <h6 class="text-center mb-1">Presensi Kehadiran</h6>
        <div class="card-body p-0 text-center">
            <th class="text-center p-1">Total Keseluruhan</th>
            <div class="table-responsive p-0" data-mdb-pagination="false">
                <table class="table align-items-center mb-1">
                    <thead>
                        <tr style="background-color:#f6f9fc;">
                            <th class="font-weight-bolder text-center p-1">KBM</th>
                            <th class="font-weight-bolder text-center p-1">H</th>
                            <th class="font-weight-bolder text-center p-1">I</th>
                            <th class="font-weight-bolder text-center p-1">A</th>
                            <th class="font-weight-bolder text-center p-1">%</th>
                        </tr>
                    </thead>
                    <tbody id="total-all">
                    </tbody>
                </table>
            </div>
            <quote class="mb-0" id="warning-ortu" class="bg-secondary" style="display:none;">
                Perhatian untuk Orang Tua, Amalsholih selalu dinasehati dan dimotivasi agar kehadiran dan kefahaman tambah meningkat
            </quote>
        </div>

        <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
            <?php $no = 1; ?>
            @foreach($tahun as $th)
                <?php
                $now = date("Y");
                $act = '';
                if ($now == $th->y) {
                    $act = 'active';
                }
                ?>
                @if($no <= (count($tahun)-1))
                <a data-mdb-ripple-init class="nav-link {{$act}} font-weight-bolder" id="nav-th{{ $th->y }}-tab" data-bs-toggle="tab" href="#nav-th{{ $th->y }}" role="tab" aria-controls="nav-th{{ $th->y }}" aria-selected="true">
                    {{ ($th->y-1).'-'.$th->y }}
                </a>
                @endif
                <?php $no++; ?>
            @endforeach
        </div>
        <div class="tab-content p-0 mt-2" id="nav-tabContent">
            @foreach($tahun as $th)
            <?php
            $now = date("Y");
            $act = '';
            if ($now == $th->y) {
                $act = 'active';
            }
            ?>
            <div class="tab-pane fade show {{$act}}" id="nav-th{{ $th->y }}" role="tabpanel" aria-labelledby="nav-th{{ $th->y }}-tab">
                <div class="datatablex datatable-sm p-0 mt-1 tabcontent" id="th{{$th->y}}" data-mdb-pagination="false">
                    <table id="recap-kehadiran" class="table align-items-center mb-0">
                        <thead>
                            <tr style="background-color:#f6f9fc;">
                                <th class="font-weight-bolder text-center p-1">BULAN</th>
                                <th class="font-weight-bolder text-center p-1">KBM</th>
                                <th class="font-weight-bolder text-center p-1">H</th>
                                <th class="font-weight-bolder text-center p-1">I</th>
                                <th class="font-weight-bolder text-center p-1">A</th>
                                <th class="font-weight-bolder text-center p-1">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $m1 = [9, 10, 11, 12];
                            $m2 = [1, 2, 3, 4, 5, 6, 7, 8];
                            $periode_total_kbm = 0;
                            $periode_total_hadir = 0;
                            $periode_total_ijin = 0;
                            $periode_total_alpha = 0;
                            $periode_persentase = 0;
                            ?>
                            @foreach($tahun_bulan as $tb)
                            @if(substr($tb->ym,0,4)==($th->y-1) && in_array(intval(substr($tb->ym,5,2)), $m1) || substr($tb->ym,0,4)==$th->y && in_array(intval(substr($tb->ym,5,2)), $m2))
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
                                $color = '';
                                if ($persentase < 80) {
                                    $color = 'danger';
                                }

                                $periode_total_kbm = $periode_total_kbm + $total_kbm;
                                $periode_total_hadir = $periode_total_hadir + $total_hadir;
                                $periode_total_ijin = $periode_total_ijin + $total_ijin;
                                $periode_total_alpha = $periode_total_alpha + $total_alpha;
                                $periode_persentase = ($periode_total_hadir + $periode_total_ijin) / $periode_total_kbm * 100;

                                $all_kbm = $all_kbm + $total_kbm;
                                $all_hadir = $all_hadir + $total_hadir;
                                $all_ijin = $all_ijin + $total_ijin;
                                $all_alpha = $all_alpha + $total_alpha;
                                ?>
                                <td class="text-center p-1">
                                    {{ date_format(date_create($tb->ym), 'M Y') }}
                                </td>
                                <td class="text-center p-1">
                                    {{ $total_kbm }}
                                </td>
                                <td class="text-center p-1">
                                    {{ $total_hadir }}
                                </td>
                                <td class="text-center p-1">
                                    {{ $total_ijin }}
                                </td>
                                <td class="text-center p-1">
                                    {{ $total_alpha }}
                                </td>
                                <td class="text-center font-weight-bolder p-1">
                                    <span class="text-{{$color}}">{{ number_format($persentase,2) }}%</span>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color:#f6f9fc;">
                                <th class="font-weight-bolder text-center p-1">Total Periode</th>
                                <th class="font-weight-bolder text-center p-1">{{$periode_total_kbm}}</th>
                                <th class="font-weight-bolder text-center p-1">{{$periode_total_hadir}}</th>
                                <th class="font-weight-bolder text-center p-1">{{$periode_total_ijin}}</th>
                                <th class="font-weight-bolder text-center p-1">{{$periode_total_alpha}}</th>
                                <th class="font-weight-bolder text-center p-1">
                                    <?php
                                    $colorp = '';
                                    if ($periode_persentase < 80) {
                                        $colorp = 'danger';
                                    }
                                    ?>
                                    <span class="text-{{$colorp}}">{{ number_format($periode_persentase,2) }}%</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <div class="card-body p-2 text-center">
            Belum dimulai KBM
        </div>
    </div>
    @endif

    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <h6 class="text-center mb-1">Pencapaian Materi</h6>
        <div class="card-body p-0">
            <div class="datatable" data-mdb-sm="true" data-mdb-pagination="false">
                <table id="recap-materi" class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase font-weight-bolder ps-2">MATERI</th>
                            <th class="text-uppercase font-weight-bolder ps-2">PENCAPAIAN</th>
                            <th class="text-uppercase font-weight-bolder ps-2">%</th>
                        </tr>
                    </thead>
                    <tbody id="contentMateri">
                        <?php echo $data_materi; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- @if($catatan_penghubungs!=null)
    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(66, 209, 181)!important;">
        <h6 class="text-center mb-1">Catatan Penghubung</h6>
        <div class="card-body p-2">
            <p class="font-weight-bolder">Kepribadian:</p>
            <span>{{ $catatan_penghubungs->cat_kepribadian }}</span>
            <hr>
            <p class="font-weight-bolder">Sholat:</p>
            <span>{{ $catatan_penghubungs->cat_sholat }}</span>
            <hr>
            <p class="font-weight-bolder">KBM:</p>
            <span>{{ $catatan_penghubungs->cat_kbm }}</span>
            <hr>
            <p class="font-weight-bolder">Asmara:</p>
            <span>{{ $catatan_penghubungs->cat_asmara }}</span>
            <hr>
            <p class="font-weight-bolder">Umum:</p>
            <span>{{ $catatan_penghubungs->cat_umum }}</span>
        </div>
    </div>
    @endif -->

    @if(count($pelanggaran)>0)
    <div class="card border mb-2 p-2" style="border-bottom:solid 2px rgb(209, 66, 119)!important;">
        <h6 class="text-center mb-1">Riwayat Pelanggaran</h6>
        <div class="card-body p-0">
            <p class="p-2 font-weight-bolder">Berdasarkan catatan ketertiban, an. {{ $santri->user->fullname }} terdapat pelanggaran:</p>
            <div class="datatable datatable-sm p-0">
                <table id="recap-pelanggaran" class="table align-items-center mb-0">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase ps-2 font-weight-bolder">Pelanggaran</th>
                            <th class="text-uppercase ps-2 text-center font-weight-bolder">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggaran as $plg)
                        <tr>
                            <td class="p-1 ps-2">
                                <!-- [SP {{ $plg->keringanan_sp }}]  -->
                                {{ $plg->jenis->jenis_pelanggaran }} <small class="text-primary">{{ ($plg->is_archive==1) ? '[Pemutihan]' : '[Hati-hati]' }}</small>
                            </td>
                            <td class="p-1 ps-2 text-center">
                                @if($plg->tanggal_melanggar!='')
                                {{ date_format(date_create($plg->tanggal_melanggar),'d M Y') }}
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
    $('main.main-content').attr('class', 'main-content position-relative border-radius-lg');
    var all_percent = <?php echo $all_percent; ?>;
    $('#total-all').html(
        '<tr>' +
        '<td class="text-center"><?php echo $all_kbm; ?></td>' +
        '<td class="text-center"><?php echo $all_hadir; ?></td>' +
        '<td class="text-center"><?php echo $all_ijin; ?></td>' +
        '<td class="text-center"><?php echo $all_alpha; ?></td>' +
        '<td class="text-center"><span class="mb-0" id="all-percent">' + all_percent + '%</span></td>' +
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
                    <h6 class="modal-title" id="exampleModalLabel"><span id="nm"></span></h6>
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
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
</main>
</body>

<!-- New Material Design -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.umd.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>

<script>
    $(document).ready(() => {
        new WOW().init();
    });
</script>
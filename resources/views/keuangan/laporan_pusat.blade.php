@if($print)
    @include('base.start_without_bars', ['title' => "LPJ BULAN ".date_format(date_create($select_bulan),'M Y')." & RAB BULAN ".date_format(date_create($nextmonth),'M Y')." - PPM RJ BS2"])
    <script>
    // function passwordCheck(){
    //     var password = prompt("Masukkan kode rahasia!");
    //     if (password !== "RJBS2@354"){
    //         passwordCheck();
    //     }
    // }
    // window.onbeforeunload = passwordCheck();
    </script>
@endif

<style>
    .form-control {
        border-radius: 0px !important;
        padding: 3px !important;
    }

    .table> :not(:first-child) {
        border-top: 1px solid #e9ecef;
    }

    .new-td {
        padding: 0px 10px !important;
    }
</style>

@if($print)
<div class="card border p-2 mb-4" style="border-bottom:solid 2px #42c19c!important;">
    <div class="row align-items-center justify-content-center text-center">
        <div class="row">
            <div class="col-md-3">
                <img src="{{ url('storage/logo-apps/' . App\Helpers\CommonHelpers::settings()->logoImgUrl) }}" height="48" alt="PPM Logo" loading="lazy" />
            </div>
            <div class="col-md-6">
                <h6 class="m-0 mb-2 text-uppercase font-weight-bolder">PPM ROUDHOTUL JANNAH BANDUNG SELATAN 2</h6>
                <h6 class="m-0 text-uppercase font-weight-bolder">LPJ BULAN {{date_format(date_create($select_bulan),'M Y')}} & RAB BULAN {{date_format(date_create($nextmonth),'M Y')}}</h6>
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </div>
</div>

<div class="card border mb-2 p-5" style="border:solid 2px #42c19c!important;">
    <div class="p-2 text-start h6">
        Kepada Yang Terhormat<br>
        Bapak Imam / Wakil 4<br>
        Di Tempat
    </div>
    <div class="p-2 text-center h6">
        السلام عليكم ورحمة الله وبر كاته
    </div>
    <div class="p-2 text-start h6">
        Yang bertandatangan dibawah ini kami Kyai Daerah beserta Pengurus PPM Bandung Selatan 2, 
        dengan ini kami melaporkan Laporan Pertanggungjawaban Keuangan Bulan {{date_format(date_create($select_bulan),'M Y')}} dengan 
        Total Pengeluaran Senilai Rp {{number_format($total_out_rutin+$total_out_nonrutin,0, ',', '.')}}.<br>
        Serta kami mengajukan RAB Bulan {{date_format(date_create($nextmonth),'M Y')}} dengan estimasi pengeluaran senilai <span id="estimasi-out-nextmonth"></span>.
        <br>
        <br>
        Demikian LPJ Bulan {{date_format(date_create($select_bulan),'M Y')}} dan RAB Bulan {{date_format(date_create($nextmonth),'M Y')}} yang dapat kami sampaikan, adapun rinciannya ada dibawah ini.
    </div>
    <div class="p-2 text-center h6">
        الحمد لله جزاكم الله خيرا 
        <br>
        و السلام عليكم و رحمة اللّٰه و بركاته
    </div>
    <div class="row mt-4">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="row text-center h6">
                <div class="col-md-4">
                    Ketua PPM
                    <br>
                    <img src="{{ url('storage/logo-apps/ttd_muhammad_yusuf.png') }}" height="48" alt="PPM Logo" loading="lazy" />
                    <br>
                    Muhammad Yusuf
                </div>
                <div class="col-md-4">
                    KU Daerah
                    <br>
                    <img src="{{ url('storage/logo-apps/ttd_kuda.png') }}" height="48" alt="PPM Logo" loading="lazy" />
                    <br>
                    H. Sutrisno
                </div>
                <div class="col-md-4">
                    Kyai Daerah
                    <br>
                    <img src="{{ url('storage/logo-apps/ttd_kida.png') }}" height="48" alt="PPM Logo" loading="lazy" />
                    <br>
                    H. Yayat Hernawan
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
@else
<div class="card border p-2 mb-2">
    <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-5">
        </div>
        <div class="col-md-2">
            <h6 class="m-0 mb-2 text-uppercase font-weight-bolder">Laporan Keuangan {{ App\Helpers\CommonHelpers::periode() }}</h6>
            <select data-mdb-filter="true" class="select form-control" value="" id="periode_bulan" name="periode_bulan" onchange="filterOnchange()">
                <option value=""></option>
                @foreach($bulans as $bulan)
                    <option {{ ($select_bulan==$bulan->ym) ? 'selected' : ''; }}>{{$bulan->ym}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5 text-end">
            <script>
                function openTab(x){
                    if(x=="public"){
                        window.open("{{route('print laporan pusat public',[$select_bulan,true])}}", "_blank");
                    }else{
                        window.open("{{route('print laporan pusat',[$select_bulan,true])}}", "_blank");
                    }
                }
            </script>
            <a onclick="openTab('')" href="#" class="btn btn-sm btn-primary"><i class="fas fa-print" aria-hidden="true"></i></a>
            <a onclick="openTab('public')" href="#" class="btn btn-sm btn-primary"><i class="fas fa-earth-asia" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
@endif

<h6 class="text-uppercase font-weight-bolder">Laporan Posisi Keuangan {{date_format(date_create($select_bulan),'M Y')}}</h6>
<div class="card border mt-2">
    <table class="table align-items-center justify-content-center mb-0 text-center table-bordered text-sm text-uppercase">
        <thead style="background-color:#f6f9fc;">
            <tr>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Saldo Akhir Bulan Lalu<br><small>(KU-BMT + BENDAHARA)</small></th>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Penerimaan</th>
                <th colspan="2" width="40%" class="text-uppercase font-weight-bolder ps-2">Pengeluaran</th>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Sisa Saldo<br><small>(by system)</small></th>
            </tr>
            <tr>
                <th width="20%" class="text-uppercase font-weight-bolder ps-2">Rutin</th>
                <th width="20%" class="text-uppercase font-weight-bolder ps-2">Non Rutin</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($saldo_awal_kubmt+$saldo_awal_bendahara,0, ',', '.')}}</th>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($total_in,0, ',', '.')}}</th>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($total_out_rutin,0, ',', '.')}}</th>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($total_out_nonrutin,0, ',', '.')}}</th>
                <?php $posisi_total = ($saldo_awal_kubmt+$saldo_awal_bendahara)+$total_in-$total_out_rutin-$total_out_nonrutin; ?>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($posisi_total,0, ',', '.')}}</th>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-uppercase font-weight-bolder ps-2">
                    <textarea rows="3" class="form-control"></textarea>
                </th>
                <th class="text-uppercase font-weight-bolder ps-2">
                    <table class="table align-items-center justify-content-center mb-0 text-center table-bordered text-sm text-uppercase">
                        <tr>   
                            <td>KU-BMT<br><b id="saldo_kubmt"></b></td>
                            <td>BENDAHARA<br><b id="saldo_bendahara"></b></td>
                        </tr>
                    </table>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<h6 class="mt-2 text-uppercase font-weight-bolder">Estimasi Posisi Keuangan {{date_format(date_create($nextmonth),'M Y')}}</h6>
<div class="card border mt-2">
    <table class="table align-items-center justify-content-center mb-0 text-center table-bordered text-sm text-uppercase">
        <thead style="background-color:#f6f9fc;">
            <tr>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Saldo Awal<br><small>(KU-BMT + BENDAHARA)</small></th>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Penerimaan</th>
                <th colspan="2" width="40%" class="text-uppercase font-weight-bolder ps-2">Pengeluaran</th>
                <th rowspan="2" width="20%" class="text-uppercase font-weight-bolder ps-2">Sisa Saldo</th>
            </tr>
            <tr>
                <th width="20%" class="text-uppercase font-weight-bolder ps-2">Rutin</th>
                <th width="20%" class="text-uppercase font-weight-bolder ps-2">Non Rutin</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="text-uppercase font-weight-bolder ps-2">RP {{number_format($posisi_total,0, ',', '.')}}</th>
                <th class="text-uppercase font-weight-bolder ps-2">RP 0</th>
                <th class="text-uppercase font-weight-bolder ps-2" id="estimasi-posisi-rutin"></th>
                <th class="text-uppercase font-weight-bolder ps-2" id="estimasi-posisi-nonrutin"></th>
                <th class="text-uppercase font-weight-bolder ps-2" id="estimasi-posisi-total"></th>
            </tr>
        </tbody>
    </table>
</div>

<br>
<div class="card border p-2" style="border-bottom:solid 2px #d96262!important;">
    <div class="row align-items-center justify-content-center text-center">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <h6 class="m-0 text-uppercase font-weight-bolder">LAPORAN PERTANGGUNGJAWABAN {{strtoupper(date_format(date_create($select_bulan),'M Y'))}}</h6>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

<br>
<h6 class="text-uppercase font-weight-bolder">Jurnal Keuangan {{date_format(date_create($select_bulan),'M Y')}}</h6>
<div class="card border mt-2">
    <div class="card-body p-2">
        <div data-mdb-pagination="false" class="datatablex table-responsive datatable-sm text-uppercase">
            <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th colspan="7" class="text-uppercase font-weight-bolder ps-2">
                           KU-BMT
                        </th>
                        <th colspan="4" class="text-uppercase font-weight-bolder text-center">
                            <small>Saldo Awal</small> RP {{number_format($saldo_awal_kubmt,0, ',', '.')}}
                        </th>
                    </tr>
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2">Bank</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Pos</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Divisi</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Kategori</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Rutin</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Tanggal</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Keterangan</th>
                        <th class="text-uppercase font-weight-bolder text-center">QTY</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Masuk</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Keluar</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Saldo</th>
                    </tr>
                </thead>
                <tbody id="rab-data">
                    <?php
                        $total_masuk = 0;
                        $total_keluar = 0;
                    ?>
                    @if(count($jurnals)>0)
                        @foreach ($jurnals->where('fkBank_id',2) as $jurnal)
                        <tr id="jurnal-{{$jurnal->id}}">
                            <td class="new-td text-uppercase">{{$jurnal->bank->name}}</td>
                            <td class="new-td text-uppercase">{{$jurnal->pos->name}}</td>
                            <td class="new-td text-uppercase">{{($jurnal->fkDivisi_id=='') ? '' : strtoupper($jurnal->divisi->divisi)}}</td>
                            <td class="new-td">{{($jurnal->rab) ? substr($jurnal->rab->keperluan, 0, 30) : ''}}</td>
                            <td class="new-td">
                                @if($jurnal->tipe_pengeluaran=="Rutin")
                                    <i class="fa fa-square-check text-info"></i>
                                @endif
                            </td>
                            <td class="new-td">{{date_format(date_create($jurnal->tanggal), "d/m/Y")}}</td>
                            <td class="new-td">
                                <span class="badge badge-{{($jurnal->jenis=='in') ? 'primary' : 'danger'}}">{{$jurnal->jenis}}</span>
                                @if($jurnal->fkRabManagBuilding_id!=0)
                                    <a onclick="document.getElementById('NR{{$jurnal->fkRabManagBuilding_id}}').scrollIntoView()" href="#NR{{$jurnal->fkRabManagBuilding_id}}" class="badge badge-secondary">#NR{{$jurnal->fkRabManagBuilding_id}}</a>
                                @elseif($jurnal->fkRabKegiatan_id!=0)
                                    <a onclick="document.getElementById('KR{{$jurnal->fkRabKegiatan_id}}').scrollIntoView()" href="#KR{{$jurnal->fkRabKegiatan_id}}" class="badge badge-secondary">#KR{{$jurnal->fkRabKegiatan_id}}</a>
                                @endif
                                {{substr(str_replace("sodaqoh tahunan","SOD THN",strtolower($jurnal->uraian)), 0, 40)}}
                            </td>
                            <td class="new-td text-start">{{($jurnal->qty=="") ? 1 : $jurnal->qty}} * {{number_format($jurnal->nominal,0, ',', '.')}}</td>
                            <td class="new-td text-end" id="nominal-in" val-in="{{($jurnal->jenis=='in') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='in') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                            <td class="new-td text-end" id="nominal-out" val-out="{{($jurnal->jenis=='out') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='out') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                            <td class="new-td text-end">
                                <?php 
                                    if($jurnal->jenis=="in"){
                                        $saldo_awal_kubmt = $saldo_awal_kubmt + ($jurnal->qty*$jurnal->nominal);
                                    }else if($jurnal->jenis=="out"){
                                        $saldo_awal_kubmt = $saldo_awal_kubmt - ($jurnal->qty*$jurnal->nominal);
                                    }
                                    echo number_format($saldo_awal_kubmt,0, ',', '.');
                                ?>
                            </td>
                            <?php
                            if($jurnal->jenis=='in'){
                                $total_masuk = $total_masuk + ($jurnal->qty*$jurnal->nominal);
                            }elseif($jurnal->jenis=='out'){
                                $total_keluar = $total_keluar + ($jurnal->qty*$jurnal->nominal);
                            }
                            ?>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot style="background-color:#f6f9fc;">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td id="total_masuk" class="font-weight-bolder text-end">RP {{number_format($total_masuk,0, ',', '.')}}</td>
                        <td id="total_keluar" class="font-weight-bolder text-end">RP {{number_format($total_keluar,0, ',', '.')}}</td>
                        <td class="font-weight-bolder text-end">RP {{number_format($saldo_awal_kubmt,0, ',', '.')}}</td>
                        <script>$("#saldo_kubmt").html("RP {{number_format($saldo_awal_kubmt,0, ',', '.')}}")</script>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div data-mdb-pagination="false" class="datatablex table-responsive datatable-sm text-uppercase mt-2">
            <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th colspan="7" class="text-uppercase font-weight-bolder ps-2">
                           BENDAHARA
                        </th>
                        <th colspan="4" class="text-uppercase font-weight-bolder text-center">
                            <small>Saldo Awal</small> RP {{number_format($saldo_awal_bendahara,0, ',', '.')}}
                        </th>
                    </tr>
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2">Bank</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Pos</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Divisi</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Kategori</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Rutin</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Tanggal</th>
                        <th class="text-uppercase font-weight-bolder ps-2">Keterangan</th>
                        <th class="text-uppercase font-weight-bolder text-center">QTY</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Masuk</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Keluar</th>
                        <th class="text-uppercase font-weight-bolder text-end pe-2">Saldo</th>
                    </tr>
                </thead>
                <tbody id="rab-data">
                    <?php
                        $total_masuk = 0;
                        $total_keluar = 0;
                    ?>
                    @if(count($jurnals)>0)
                        @foreach ($jurnals->where('fkBank_id',1) as $jurnal)
                        <tr id="jurnal-{{$jurnal->id}}">
                            <td class="new-td text-uppercase">{{$jurnal->bank->name}}</td>
                            <td class="new-td text-uppercase">{{$jurnal->pos->name}}</td>
                            <td class="new-td text-uppercase">{{($jurnal->fkDivisi_id=='') ? '' : strtoupper($jurnal->divisi->divisi)}}</td>
                            <td class="new-td">{{($jurnal->rab) ? substr($jurnal->rab->keperluan, 0, 30) : ''}}</td>
                            <td class="new-td">
                                @if($jurnal->tipe_pengeluaran=="Rutin")
                                    <i class="fa fa-square-check text-info"></i>
                                @endif
                            </td>
                            <td class="new-td">{{date_format(date_create($jurnal->tanggal), "d/m/Y")}}</td>
                            <td class="new-td">
                                <span class="badge badge-{{($jurnal->jenis=='in') ? 'primary' : 'danger'}}">{{$jurnal->jenis}}</span>
                                @if($jurnal->fkRabManagBuilding_id!=0)
                                    <a onclick="document.getElementById('NR{{$jurnal->fkRabManagBuilding_id}}').scrollIntoView()" href="#NR{{$jurnal->fkRabManagBuilding_id}}" class="badge badge-secondary">#NR{{$jurnal->fkRabManagBuilding_id}}</a>
                                @elseif($jurnal->fkRabKegiatan_id!=0)
                                    <a onclick="document.getElementById('KR{{$jurnal->fkRabKegiatan_id}}').scrollIntoView()" href="#KR{{$jurnal->fkRabKegiatan_id}}" class="badge badge-secondary">#KR{{$jurnal->fkRabKegiatan_id}}</a>
                                @endif
                                {{substr(str_replace("sodaqoh tahunan","SOD THN",strtolower($jurnal->uraian)), 0, 40)}}
                            </td>
                            <td class="new-td text-start">{{($jurnal->qty=="") ? 1 : $jurnal->qty}} * {{number_format($jurnal->nominal,0, ',', '.')}}</td>
                            <td class="new-td text-end" id="nominal-in" val-in="{{($jurnal->jenis=='in') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='in') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                            <td class="new-td text-end" id="nominal-out" val-out="{{($jurnal->jenis=='out') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='out') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                            <td class="new-td text-end">
                                <?php 
                                    if($jurnal->jenis=="in"){
                                        $saldo_awal_bendahara = $saldo_awal_bendahara + ($jurnal->qty*$jurnal->nominal);
                                    }else if($jurnal->jenis=="out"){
                                        $saldo_awal_bendahara = $saldo_awal_bendahara - ($jurnal->qty*$jurnal->nominal);
                                    }
                                    echo number_format($saldo_awal_bendahara,0, ',', '.');
                                ?>
                            </td>
                            <?php
                            if($jurnal->jenis=='in'){
                                $total_masuk = $total_masuk + ($jurnal->qty*$jurnal->nominal);
                            }elseif($jurnal->jenis=='out'){
                                $total_keluar = $total_keluar + ($jurnal->qty*$jurnal->nominal);
                            }
                            ?>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot style="background-color:#f6f9fc;">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td id="total_masuk" class="font-weight-bolder text-end">RP {{number_format($total_masuk,0, ',', '.')}}</td>
                        <td id="total_keluar" class="font-weight-bolder text-end">RP {{number_format($total_keluar,0, ',', '.')}}</td>
                        <td class="font-weight-bolder text-end">RP {{number_format($saldo_awal_bendahara,0, ',', '.')}}</td>
                        <script>$("#saldo_bendahara").html("RP {{number_format($saldo_awal_bendahara,0, ',', '.')}}")</script>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@if(count($rab_kegiatan)>0)
    <br>
    <h6 class="text-uppercase font-weight-bolder">Rincian Pengeluaran Kegiatan Rutin {{date_format(date_create($select_bulan),'M Y')}}</h6>
    @foreach($rab_kegiatan as $mngbuild)
        @if($mngbuild->fkRabKegiatan_id!=0)
            <div class="card border p-2 mt-2" id="KR{{$mngbuild->kegiatan->id}}">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-12 text-start mb-2">
                        <h6 class="m-0">
                            Kegiatan: <span class="badge badge-secondary">#KR{{$mngbuild->kegiatan->id}}</span> <b class="badge badge-primary">{{strtoupper($mngbuild->kegiatan->nama)}}</b> 
                        </h6>
                        <h6 class="mb-0">Budget: <b>Rp {{number_format($mngbuild->kegiatan->rab->biaya,0, ',', '.')}}</b></h6>
                        <p class="m-0 quote">
                            Deskripsi:<br>
                            {{ucwords($mngbuild->kegiatan->deskripsi)}}
                        </p>
                    </div>
                    
                    <div class="datatablex table-responsive datatable-sm">
                        <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                            <thead style="background-color:#f6f9fc;">
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #e7a2a2;" colspan="4">RAB</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #42c19c;" colspan="4">REALISASI</th>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                </tr>
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2">URAIAN</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">SELISIH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; $total_realisasi = 0; ?>
                                @foreach($mngbuild->detail_kegiatans() as $mb)
                                    <tr>
                                        <td class="new-td">{{$mb->uraian}}</td>
                                        <td class="new-td text-center">{{$mb->qty}}</td>
                                        <td class="new-td text-center">{{$mb->satuan}}</td>
                                        <td class="new-td text-end">{{number_format($mb->biaya,0, ',', '.')}}</td>
                                        <?php $total = $total + ($mb->qty*$mb->biaya); ?>
                                        <td class="new-td text-end">{{number_format(($mb->qty*$mb->biaya),0, ',', '.')}}</td>
                                        <td class="new-td text-center">{{$mb->qty_realisasi}}</td>
                                        <td class="new-td text-center">{{$mb->satuan_realisasi}}</td>
                                        <td class="new-td text-end">{{number_format($mb->biaya_realisasi,0, ',', '.')}}</td>
                                        <?php $total_realisasi = $total_realisasi + ($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                                        <td class="new-td text-end">{{number_format(($mb->qty_realisasi*$mb->biaya_realisasi),0, ',', '.')}}</td>
                                        <td class="new-td text-end">
                                            <?php
                                            $selisih = ($mb->qty*$mb->biaya)-($mb->qty_realisasi*$mb->biaya_realisasi);
                                            ?>
                                            {{number_format($selisih,0, ',', '.')}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfooter style="background-color:#f6f9fc;">
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total,0, ',', '.')}}</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total_realisasi,0, ',', '.')}}</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                </tr>
                                @if($mngbuild->kegiatan->rab->biaya<$total || $mngbuild->kegiatan->rab->biaya<$total_realisasi)
                                    <tr>
                                        <th class="text-uppercase font-weight-bolder ps-2">Justifikasi</th>
                                        <th colspan="4" class="text-uppercase font-weight-bolder ps-2 text-center">
                                            @if($mngbuild->kegiatan->rab->biaya<$total)
                                                Budget < Total RAB
                                                <textarea {{($mngbuild->kegiatan->status=="posted") ? 'readonly' : ''}} rows="3" class="form-control" name="justifikasi-rab" id="justifikasi-rab">{{$mngbuild->kegiatan->justifikasi_rab}}</textarea>
                                            @endif
                                        </th>
                                        <th colspan="4" class="text-uppercase font-weight-bolder ps-2 text-center">
                                            @if($total<$total_realisasi)
                                                Total RAB < Total Realisasi
                                                <textarea {{($mngbuild->kegiatan->status=="posted") ? 'readonly' : ''}} rows="3" class="form-control" name="justifikasi-realisasi" id="justifikasi-realisasi">{{$mngbuild->kegiatan->justifikasi_realisasi}}</textarea>
                                            @endif
                                        </th>
                                        <th colspan="2" class="text-uppercase font-weight-bolder ps-2"></th>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

@if(count($manag_building)>0)
    <br>
    <h6 class="text-uppercase font-weight-bolder">Rincian Pengeluaran Non Rutin {{date_format(date_create($select_bulan),'M Y')}}</h6>
    @foreach($manag_building as $mngbuild)
        @if($mngbuild->fkRabManagBuilding_id!=0)
            <div class="card border p-2 mt-2" id="NR{{$mngbuild->managBuilding->id}}">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-12 text-start mb-2">
                        <h6 class="m-0">
                            Management Building: <span class="badge badge-secondary">#NR{{$mngbuild->managBuilding->id}}</span> <b class="badge badge-primary">{{strtoupper($mngbuild->managBuilding->nama)}}</b> 
                        </h6>
                        <p class="m-0 quote">
                            Deskripsi:<br>
                            {{ucwords($mngbuild->managBuilding->deskripsi)}}
                        </p>
                    </div>
                    
                    <div class="datatablex table-responsive datatable-sm">
                        <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                            <thead style="background-color:#f6f9fc;">
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #e7a2a2;" colspan="4">RAB</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #42c19c;" colspan="4">REALISASI</th>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                </tr>
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2">URAIAN</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">SELISIH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; $total_realisasi = 0; ?>
                                @foreach($mngbuild->details() as $mb)
                                    <tr>
                                        <td class="new-td">{{$mb->uraian}}</td>
                                        <td class="new-td text-center">{{$mb->qty}}</td>
                                        <td class="new-td text-center">{{$mb->satuan}}</td>
                                        <td class="new-td text-end">{{number_format($mb->biaya,0, ',', '.')}}</td>
                                        <?php $total = $total + ($mb->qty*$mb->biaya); ?>
                                        <td class="new-td text-end">{{number_format(($mb->qty*$mb->biaya),0, ',', '.')}}</td>
                                        <td class="new-td text-center">{{$mb->qty_realisasi}}</td>
                                        <td class="new-td text-center">{{$mb->satuan_realisasi}}</td>
                                        <td class="new-td text-end">{{number_format($mb->biaya_realisasi,0, ',', '.')}}</td>
                                        <?php $total_realisasi = $total_realisasi + ($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                                        <td class="new-td text-end">{{number_format(($mb->qty_realisasi*$mb->biaya_realisasi),0, ',', '.')}}</td>
                                        <td class="new-td text-end">
                                            <?php
                                            $selisih = ($mb->qty*$mb->biaya)-($mb->qty_realisasi*$mb->biaya_realisasi);
                                            ?>
                                            {{number_format($selisih,0, ',', '.')}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfooter style="background-color:#f6f9fc;">
                                <tr>
                                    <th class="text-uppercase font-weight-bolder ps-2"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total,0, ',', '.')}}</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total_realisasi,0, ',', '.')}}</th>
                                    <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

<br>
<div class="card border p-2" style="border-bottom:solid 2px #d96262!important;">
    <div class="row align-items-center justify-content-center text-center">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <h6 class="m-0 text-uppercase font-weight-bolder">RAB {{strtoupper(date_format(date_create($nextmonth),'M Y'))}}</h6>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

<?php $estimasi_posisi_rutin = 0; $estimasi_posisi_nonrutin = 0; $estimasi_posisi_total = 0; ?>
<br>
<h6 class="text-uppercase font-weight-bolder">RAB RUTIN {{date_format(date_create($nextmonth),'M Y')}}</h6>
<div class="card border mt-2">
    <div class="datatablex table-responsive datatable-sm">
        <table class="table table-bordered align-items-center text-uppercase mb-0">
            <thead style="background-color:#f6f9fc;">
                <tr>
                    <th class="text-uppercase text-start font-weight-bolder ps-2">Divisi</th>
                    <th class="text-uppercase text-start font-weight-bolder ps-2">Pengeluaran</th>
                    <th class="text-uppercase text-center font-weight-bolder">Periode</th>
                    <th class="text-uppercase text-center font-weight-bolder">Biaya</th>
                    <?php for ($x = 1; $x <= 5; $x++) { ?>
                        <th class="text-uppercase text-center font-weight-bolder">M-{{$x}}</th>
                    <?php } ?>
                    <th class="text-uppercase text-center font-weight-bolder">Total</th>
                </tr>
            </thead>
            <?php $total_rab = 0; ?>
            <tbody id="rab-data">
                @if(count($rabs)>0)
                <?php $total_biaya = 0; ?>
                    @foreach ($rabs as $rab)
                        <?php
                            $month = intval(date_format(date_create($nextmonth),'m'));
                            $check_minggu = json_decode($rab['bulan_'.$month]);
                            $check = 0;
                            for ($x = 1; $x <= 5; $x++) {
                                if($check_minggu[$x-1][1]){
                                    $check = true;
                                }
                            }
                            $total = 0;
                        ?>
                        @if($check)
                            <tr>
                                <td class="new-td">{{strtoupper($rab->divisi->divisi)}}</td>
                                <td class="new-td">{{$rab->keperluan}}</td>
                                <td class="new-td text-center">{{$rab->periode}}</td>
                                <td class="new-td text-end">{{number_format($rab->biaya,0)}}</td>
                                <?php for ($x = 1; $x <= 5; $x++) { ?>
                                    <td class="new-td text-center">
                                        <?php
                                            $month = intval(date_format(date_create($nextmonth),'m'));
                                            $check_minggu = json_decode($rab['bulan_'.$month]);
                                            echo ($check_minggu[$x-1][1]) ? '<i class="fa fa-square-check text-primary"></i>' : '';

                                            if($check_minggu[$x-1][1]){
                                                $total = $total + ($rab->biaya);
                                            }
                                        ?>
                                    </td>
                                <?php } $total_biaya = $total_biaya + $total; ?>
                                <td class="new-td text-end">{{number_format($total,0)}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="9" class="text-uppercase text-center font-weight-bolder">Total RAB</th>
                    <th class="text-uppercase text-center font-weight-bolder">{{number_format($total_biaya,0)}}</th>
                    <?php $estimasi_posisi_rutin = $total_biaya; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if(count($pengajuan_manag_buildings)>0)
    <br>
    <h6 class="text-uppercase font-weight-bolder">RAB Pengajuan Non Rutin {{date_format(date_create($select_bulan),'M Y')}}</h6>
    @foreach($pengajuan_manag_buildings as $mngbuild)
        <div class="card border p-2 mt-2">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-12 text-start mb-2">
                    <h6 class="m-0">
                        Management Building: <b class="badge badge-primary">{{strtoupper($mngbuild->nama)}}</b> 
                    </h6>
                    <p class="m-0 quote">
                        Deskripsi:<br>
                        {{ucwords($mngbuild->deskripsi)}}
                    </p>
                </div>
                
                <div class="datatablex table-responsive datatable-sm">
                    <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                        <thead style="background-color:#f6f9fc;">
                            <tr>
                                <th class="text-uppercase font-weight-bolder ps-2"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #e7a2a2;" colspan="4">RAB</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #42c19c;" colspan="4">REALISASI</th>
                                <th class="text-uppercase font-weight-bolder ps-2"></th>
                            </tr>
                            <tr>
                                <th class="text-uppercase font-weight-bolder ps-2">URAIAN</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center">SAT</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">SELISIH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_nonrutin = 0; $total_realisasi = 0; ?>
                            @foreach($mngbuild->details as $mb)
                                <tr>
                                    <td class="new-td">{{$mb->uraian}}</td>
                                    <td class="new-td text-center">{{$mb->qty}}</td>
                                    <td class="new-td text-center">{{$mb->satuan}}</td>
                                    <td class="new-td text-end">{{number_format($mb->biaya,0, ',', '.')}}</td>
                                    <?php $total_nonrutin = $total_nonrutin + ($mb->qty*$mb->biaya); ?>
                                    <td class="new-td text-end">{{number_format(($mb->qty*$mb->biaya),0, ',', '.')}}</td>
                                    <td class="new-td text-center">{{$mb->qty_realisasi}}</td>
                                    <td class="new-td text-center">{{$mb->satuan_realisasi}}</td>
                                    <td class="new-td text-end">{{number_format($mb->biaya_realisasi,0, ',', '.')}}</td>
                                    <?php $total_realisasi = $total_realisasi + ($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                                    <td class="new-td text-end">{{number_format(($mb->qty_realisasi*$mb->biaya_realisasi),0, ',', '.')}}</td>
                                    <td class="new-td text-end">
                                        <?php
                                        $selisih = ($mb->qty*$mb->biaya)-($mb->qty_realisasi*$mb->biaya_realisasi);
                                        ?>
                                        {{number_format($selisih,0, ',', '.')}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfooter style="background-color:#f6f9fc;">
                            <tr>
                                <th class="text-uppercase font-weight-bolder ps-2"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total_nonrutin,0, ',', '.')}}</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total_realisasi,0, ',', '.')}}</th>
                                <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                            </tr>
                            <?php
                            $estimasi_posisi_nonrutin = $estimasi_posisi_nonrutin + $total_nonrutin;
                            ?>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endif

@if($print)
    <script type="text/javascript" src="{{ asset('js/app-custom.js') }}"></script>
@endif
<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    var posisi_total = "<?php echo $posisi_total; ?>"
    var estimasi_posisi_rutin = "<?php echo $estimasi_posisi_rutin; ?>"
    var estimasi_posisi_nonrutin = "<?php echo $estimasi_posisi_nonrutin; ?>"
    $("#estimasi-posisi-rutin").html(toNumber(estimasi_posisi_rutin))
    $("#estimasi-posisi-nonrutin").html(toNumber(estimasi_posisi_nonrutin))
    $("#estimasi-posisi-total").html(toNumber(posisi_total-estimasi_posisi_rutin-estimasi_posisi_nonrutin))
    $("#estimasi-out-nextmonth").html(toNumber(parseFloat(estimasi_posisi_rutin)+parseFloat(estimasi_posisi_nonrutin)))

    function filterOnchange(tipe=false){
        var periode = $('#periode_bulan').val();
        getPage(`{{ url("/") }}/keuangan/laporan-pusat/` + periode);
    }
</script>
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

<div class="card border p-2 mb-2">
    <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-12">
            <h6 class="m-0">Jurnal Operasional {{ App\Helpers\CommonHelpers::periode() }}</h6>
        </div>
    </div>
</div>
<div class="card border p-2">
    <nav>
        <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
            <a data-mdb-ripple-init class="nav-link active font-weight-bolder" id="nav-pengeluaran-tab" data-bs-toggle="tab" href="#nav-pengeluaran" role="tab" aria-controls="nav-pengeluaran" aria-selected="true">
                Pengeluaran
            </a>
            <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-penerimaan-tab" data-bs-toggle="tab" href="#nav-penerimaan" role="tab" aria-controls="nav-penerimaan">
                Penerimaan
            </a>
            <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-pengambilan-tab" data-bs-toggle="tab" href="#nav-pengambilan" role="tab" aria-controls="nav-pengambilan">
                Pengambilan OP
            </a>
        </div>

        <input class="form-control" type="hidden" value="" id="jurnal_id" />

        <div class="tab-content p-0 mt-2" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-pengeluaran" role="tabpanel" aria-labelledby="nav-pengeluaran-tab">
                <div class="card border" id="pengeluaran">
                    <div class="card-header p-2">
                        <div class="datatablex table-responsive datatable-sm">
                            <table class="table align-items-center mb-0 text-sm">
                                <tbody>
                                    <tr class="">
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Keluar Dari</label>
                                            <select class="form-control" value="" id="fkBank_id_out" name="fkBank_id_out" required>
                                                @foreach($banks as $bank)
                                                <option {{(auth()->user()->hasRole('ku') && !isset(auth()->user()->santri) && $bank->id==2) ? 'selected' : ''}} value="{{$bank->id}}">{{strtoupper($bank->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Pos</label>
                                            <select class="form-control" value="" id="pos-out" name="pos-out" required>
                                                @foreach($poses as $pos)
                                                <option value="{{$pos->id}}">{{strtoupper($pos->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Divisi</label>
                                            <select class="form-control" value="" id="fkDivisi_id-out" name="fkDivisi_id-out" required onchange="reloadKategori(this,'out')">
                                                <option value="">--pilih divisi--</option>
                                                @foreach($divisis as $divisi)
                                                    @if($select_divisi!="all")
                                                        @if($select_divisi==$divisi->id)
                                                            <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Kategori</label>
                                            <select class="form-control" value="" id="fkRab_id-out" name="fkRab_id-out" required onchange="reloadRab(this)">
                                                <option value="">--pilih kategori--</option>
                                                    @foreach($rabs as $rab)
                                                    <option value="{{$rab->id}}">{{strtoupper($rab->keperluan)}}</option>
                                                    @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Tanggal</label>
                                            <input class="form-control" type="datetime-local" value="{{date('Y-m-d H:i:s')}}" id="tanggal-out" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:20%;">
                                            <label>Keterangan <small>(kosongkan jika tidak ada klarifikasi)</small></label>
                                            <input class="form-control" type="text" value="" id="keterangan-out">
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Pengeluaran</label>
                                            <select class="form-control" value="" id="tipe_pengeluaran-out" name="tipe_pengeluaran-out" required>
                                                <option value="Rutin">Rutin</option>
                                                <option value="Non Rutin">Non Rutin</option>
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>QTY</label>
                                            <input class="form-control" type="number" step="0.01" value="1" id="qty-out" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Nominal</label>
                                            <input class="form-control" type="number" value="" id="nominal-out" required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="ms-auto text-end p-0 mt-2">
                                <div class="m-0 p-0" style="border:none;">
                                    <div id="info-rab-out" class="text-sm alert-success text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                    <div id="alert-success-out" class="text-sm alert alert-success text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                    <div id="alert-danger-out" class="text-sm alert alert-danger text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                </div>
                                <a href="#" id="btn-batal-out" class="btn btn-danger btn-sm mb-0" onclick="clearAll('out')" style="display:none;">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                    BATAL
                                </a>
                                <a href="#" id="simpan-pengeluaran" class="btn btn-primary btn-sm mb-0" onclick="simpanJurnal('out')">
                                    <i class="fas fa-save" aria-hidden="true"></i>
                                    SIMPAN PENGELUARAN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="nav-penerimaan" role="tabpanel" aria-labelledby="nav-penerimaan-tab">
                <div class="card border" id="penerimaan">
                    <div class="card-header p-2">
                        <div class="datatablex table-responsive datatable-sm">
                            <table class="table align-items-center mb-0 text-sm">
                                <tbody>
                                    <tr class="">
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Masuk Ke</label>
                                            <select class="form-control" value="" id="fkBank_id_in" name="fkBank_id_in" required>
                                                @foreach($banks as $bank)
                                                <option {{(auth()->user()->hasRole('ku') && !isset(auth()->user()->santri) && $bank->id==2) ? 'selected' : ''}} value="{{$bank->id}}">{{strtoupper($bank->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Pos</label>
                                            <select class="form-control" value="" id="pos-in" name="pos-in" required>
                                                @foreach($poses as $pos)
                                                <option value="{{$pos->id}}">{{strtoupper($pos->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Tanggal</label>
                                            <input class="form-control" type="datetime-local" value="{{date('Y-m-d H:i:s')}}" id="tanggal-in" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Penerimaan</label>
                                            <select class="form-control" value="" id="tipe_penerimaan-in" name="tipe_penerimaan-in" required onchange="changeIfTahunan(this.value)">
                                                <option value="">--pilih penerimaan--</option>
                                                <!-- <option value="Sodaqoh Tahunan">Sodaqoh Tahunan</option> -->
                                                <option value="Sodaqoh Fasilitas">Sodaqoh Fasilitas</option>
                                                <option value="Sodaqoh Ramadhan">Sodaqoh Ramadhan</option>
                                                <option value="Sodaqoh Lainnya">Sodaqoh Lainnya</option>
                                                <option value="Kembalian">Kembalian</option>
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;display:none;" id="td_fkDivisi_id-in">
                                            <label>Divisi</label>
                                            <select class="form-control" value="" id="fkDivisi_id-in" name="fkDivisi_id-in" required>
                                                <option value="">--pilih divisi--</option>
                                                @foreach($divisis as $divisi)
                                                <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;display:none;" id="td_fkSodaqoh_id-in">
                                            <label>Ortu-Mahasiswa</label>
                                            <select data-mdb-filter="true" class="select form-control" value="" id="fkSodaqoh_id-in" name="fkSodaqoh_id-in">
                                                <option value="">--pilih penerimaan--</option>
                                                @if(count($sodaqohs)>0)
                                                @foreach($sodaqohs as $sodaqoh)
                                                <option value="{{$sodaqoh->santri_id}}">{{$sodaqoh->nama_ortu}} - {{$sodaqoh->fullname}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Keterangan</label>
                                            <input class="form-control" type="text" value="" id="keterangan-in">
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Nominal</label>
                                            <input class="form-control" type="number" value="" id="nominal-in" required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="ms-auto text-end p-0 mt-2">
                                <div class="m-0 p-0" style="border:none;">
                                    <div id="alert-success-in" class="text-sm alert alert-success text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                    <div id="alert-danger-in" class="text-sm alert alert-danger text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                </div>
                                <a href="#" id="btn-batal-in" class="btn btn-danger btn-sm mb-0" onclick="clearAll('in')" style="display:none;">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                    BATAL
                                </a>
                                <a href="#" id="simpan-penerimaan" class="btn btn-primary btn-sm mb-0" onclick="simpanJurnal('in')">
                                    <i class="fas fa-save" aria-hidden="true"></i>
                                    SIMPAN PENERIMAAN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="nav-pengambilan" role="tabpanel" aria-labelledby="nav-pengambilan-tab">
                <div class="card border" id="pengambilan">
                    <div class="card-header p-2">
                        <div class="datatablex table-responsive datatable-sm">
                            <table class="table align-items-center mb-0 text-sm">
                                <tbody>
                                    <tr class="">
                                        <td class="m-0 p-0 pb-2" style="width:15%;">
                                            <label>Pos</label>
                                            <select class="form-control" value="" id="pos-kuop" name="pos-kuop" required>
                                                @foreach($poses as $pos)
                                                <option value="{{$pos->id}}">{{strtoupper($pos->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:15%;">
                                            <label>Divisi</label>
                                            <select class="form-control" value="" id="fkDivisi_id-kuop" name="fkDivisi_id-kuop" required onchange="reloadKategori(this,'kuop')">
                                                <option value="">--pilih divisi--</option>
                                                @foreach($divisis as $divisi)
                                                    @if($select_divisi!="all")
                                                        @if($select_divisi==$divisi->id)
                                                            <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:15%;">
                                            <label>Kategori</label>
                                            <select class="form-control" value="" id="fkRab_id-kuop" name="fkRab_id-kuop" required onchange="reloadRab(this)">
                                                <option value="">--pilih kategori--</option>
                                                    @foreach($rabs as $rab)
                                                    <option value="{{$rab->id}}">{{strtoupper($rab->keperluan)}}</option>
                                                    @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:15%;">
                                            <label>Tanggal</label>
                                            <input class="form-control" type="datetime-local" value="{{date('Y-m-d H:i:s')}}" id="tanggal-kuop" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:15%;">
                                            <label>Nominal Pengambilan</label>
                                            <input class="form-control" type="number" value="" id="nominal-kuop" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:25%;">
                                            <label>Keterangan <small>(kosongkan jika tidak ada klarifikasi)</small></label>
                                            <input class="form-control" type="text" value="OP Masuk dari BMT ke Bendahara" id="keterangan-kuop">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="ms-auto text-end p-0 mt-2">
                                <div class="m-0 p-0" style="border:none;">
                                    <div id="alert-success-kuop" class="text-sm alert alert-success text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                    <div id="alert-danger-kuop" class="text-sm alert alert-danger text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                </div>
                                <a href="#" id="btn-batal-kuop" class="btn btn-danger btn-sm mb-0" onclick="clearAll('kuop')" style="display:none;">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                    BATAL
                                </a>
                                <a href="#" id="simpan-pengambilan" class="btn btn-primary btn-sm mb-0" onclick="simpanJurnal('kuop')">
                                    <i class="fas fa-save" aria-hidden="true"></i>
                                    SIMPAN PENGAMBILAN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="card border mt-2">
        <div class="card-body p-0">
            <div data-mdb-pagination="false" class="datatablex table-responsive datatable-sm text-uppercase">
                <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase" style="font-size:0.8rem !important">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase font-weight-bolder ps-2">
                                <select data-mdb-filter="true" class="select form-control" value="" id="filter-fkBank_id" name="filter-fkBank_id" onchange="filterOnchange()">
                                    <option value="all">--filter bank--</option>
                                    @foreach($banks as $bank)
                                    <option {{ ($select_bank==$bank->id) ? 'selected' : '' }} value="{{$bank->id}}">{{strtoupper($bank->name)}}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2"></th>
                            <th class="text-uppercase font-weight-bolder ps-2">
                                <select data-mdb-filter="true" class="select form-control" id="filter-fkDivisi_id" name="filter-fkDivisi_id" onchange="filterOnchange()">
                                    <option value="all">--filter divisi--</option>
                                    @foreach($divisis as $divisi)
                                    <option {{ ($select_divisi==$divisi->id) ? 'selected' : '' }} value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2">
                                <select data-mdb-filter="true" class="select form-control" id="filter-fkRab_id" name="filter-fkRab_id" onchange="filterOnchange()">
                                    <option value="all">--filter kategori--</option>
                                        @foreach($rabs as $rab)
                                        <option {{ ($select_rab==$rab->id) ? 'selected' : '' }} value="{{$rab->id}}">{{strtoupper($rab->keperluan)}}</option>
                                        @endforeach
                                </select>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2">
                            <select data-mdb-filter="true" class="select form-control" value="" id="periode_bulan" name="periode_bulan" onchange="filterOnchange()">
                                <option value="all">--seluruh tahun-bulan--</option>
                                @foreach($bulans as $bulan)
                                <option {{ ($select_bulan==$bulan->ym) ? 'selected' : ''; }}>{{$bulan->ym}}</option>
                                @endforeach
                            </select>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2">
                                <select data-mdb-filter="true" class="select form-control" value="" id="filter-tipe_penerimaan" name="filter-tipe_penerimaan" onchange="filterOnchange(true)">
                                    <option {{ ($select_penerimaan=='all') ? 'selected' : ''; }} value="all">--filter penerimaan--</option>
                                    <option {{ ($select_penerimaan=='Sodaqoh Tahunan') ? 'selected' : ''; }} value="Sodaqoh Tahunan">Sodaqoh Tahunan</option>
                                    <option {{ ($select_penerimaan=='Sodaqoh Fasilitas') ? 'selected' : ''; }} value="Sodaqoh Fasilitas">Sodaqoh Fasilitas</option>
                                    <option {{ ($select_penerimaan=='Sodaqoh Ramadhan') ? 'selected' : ''; }} value="Sodaqoh Ramadhan">Sodaqoh Ramadhan</option>
                                    <option {{ ($select_penerimaan=='Sodaqoh Lainnya') ? 'selected' : ''; }} value="Sodaqoh Lainnya">Sodaqoh Lainnya</option>
                                    <option {{ ($select_penerimaan=='Kembalian') ? 'selected' : ''; }} value="Kembalian">Kembalian</option>
                                </select>
                            </th>
                            <th colspan="5" class="text-uppercase font-weight-bolder text-center">
                                <small>Saldo Awal</small> RP {{number_format($saldo,0, ',', '.')}}
                                <?php $saldo_awal = $saldo; ?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-uppercase font-weight-bolder ps-2">Bank</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Pos</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Divisi</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Kategori</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Tanggal</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Keterangan</th>
                            <th class="text-uppercase font-weight-bolder text-center">QTY</th>
                            <th class="text-uppercase font-weight-bolder text-end pe-2">Masuk</th>
                            <th class="text-uppercase font-weight-bolder text-end pe-2">Keluar</th>
                            <th class="text-uppercase font-weight-bolder text-end pe-2">Saldo</th>
                            <th class="text-uppercase font-weight-bolder ps-2"></th>
                        </tr>
                    </thead>
                    <tbody id="rab-data">
                        <?php
                            $total_masuk = 0;
                            $total_keluar = 0;
                        ?>
                        @if(count($jurnals)>0)
                            @foreach ($jurnals as $jurnal)
                            <tr id="jurnal-{{$jurnal->id}}">
                                <td class="new-td text-uppercase">{{$jurnal->bank->name}}</td>
                                <td class="new-td text-uppercase">{{$jurnal->pos->name}}</td>
                                <td class="new-td text-uppercase">{{($jurnal->fkDivisi_id=='') ? '' : strtoupper($jurnal->divisi->divisi)}}</td>
                                <td class="new-td">{{($jurnal->rab) ? substr($jurnal->rab->keperluan, 0, 30) : ''}}</td>
                                <td class="new-td">{{date_format(date_create($jurnal->tanggal), "d/m/Y")}}</td>
                                <td class="new-td">
                                    <span class="badge badge-{{($jurnal->jenis=='in') ? 'primary' : 'danger'}}">{{$jurnal->jenis}}</span>
                                    @if($jurnal->fkRabManagBuilding_id!=0)
                                        <a href="{{route('rab management building id',$jurnal->fkRabManagBuilding_id)}}" class="badge badge-secondary">#NR{{$jurnal->fkRabManagBuilding_id}}</a>
                                    @elseif($jurnal->fkRabKegiatan_id!=0)
                                        <a href="{{route('rab kegiatan id',$jurnal->fkRabKegiatan_id)}}" class="badge badge-secondary">#KR{{$jurnal->fkRabKegiatan_id}}</a>
                                    @endif
                                    {{substr(str_replace("sodaqoh tahunan","SOD THN",strtolower($jurnal->uraian)), 0, 40)}}
                                </td>
                                <td class="new-td text-start">{{($jurnal->qty=="") ? 1 : $jurnal->qty}} * {{number_format($jurnal->nominal,0, ',', '.')}}</td>
                                <td class="new-td text-end" id="nominal-in" val-in="{{($jurnal->jenis=='in') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='in') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                                <td class="new-td text-end" id="nominal-out" val-out="{{($jurnal->jenis=='out') ? $jurnal->nominal : 0 }}">{{($jurnal->jenis=='out') ? 'RP '.number_format($jurnal->qty*$jurnal->nominal,0, ',', '.') : ''}}</td>
                                <td class="new-td text-end">
                                    <?php 
                                        if($jurnal->jenis=="in"){
                                            $saldo = $saldo + ($jurnal->qty*$jurnal->nominal);
                                        }else if($jurnal->jenis=="out"){
                                            $saldo = $saldo - ($jurnal->qty*$jurnal->nominal);
                                        }
                                        echo number_format($saldo,0, ',', '.');
                                    ?>
                                </td>
                                <td class="p-0 text-center">
                                    @if($jurnal->tipe_penerimaan!='Sodaqoh Tahunan')
                                        @if($jurnal->sub_jenis=="" && ($jurnal->fkRabManagBuilding_id==0 && $jurnal->fkRabKegiatan_id==0))
                                        <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Edit" onclick="ubahJurnal({{$jurnal}})">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        @endif
                                        <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Hapus" onclick="hapusJurnal({{$jurnal}})">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </a>
                                    @endif
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
                            <td id="total_masuk" class="font-weight-bolder text-end">RP {{number_format($total_masuk,0, ',', '.')}}</td>
                            <td id="total_keluar" class="font-weight-bolder text-end">RP {{number_format($total_keluar,0, ',', '.')}}</td>
                            <td id="total_sisa_saldo" class="font-weight-bolder text-end">RP {{number_format($saldo,0, ',', '.')}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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

    function filterOnchange(tipe=false){
        var bank = $('#filter-fkBank_id').val();
        var periode = $('#periode_bulan').val();
        var divisi = $('#filter-fkDivisi_id').val();
        var rab = $('#filter-fkRab_id').val();
        var penerimaan = $('#filter-tipe_penerimaan').val();

        if(tipe){
            var divisi = 'all';
            var rab = 'all';
        }
        getPage(`{{ url("/") }}/keuangan/jurnal/` + bank + `/` + divisi + `/` + rab + `/` + periode +`/` + penerimaan);
    }

    function changeIfTahunan(data) {
        if (data == 'Kembalian') {
            $("#td_fkSodaqoh_id-in").hide();
            $("#td_fkDivisi_id-in").show();
        } else {
            $("#td_fkSodaqoh_id-in").show();
            $("#td_fkDivisi_id-in").hide();
        }
    }

    function reloadKategori(data, x) {
        $("#info-rab-out").hide();
        $("#info-rab-out").html("");
        if(data.value==13){
            $("#qty-out").prop('readonly', true);
            $("#nominal-out").prop('readonly', true);
            $("#info-rab-out").show();
            $("#info-rab-out").html("Untuk Pengeluaran Ukhro Akan Digenerate Otomatis");
            $("#fkRab_id-" + x).html('<option value="">--pilih kategori--</option>')
        }else{
            $("#qty-" + x).prop('readonly', false);
            $("#nominal-" + x).prop('readonly', false);
            var rabs = <?php echo $rabs; ?>;
            var option = '<option value="">--pilih kategori--</option>';
            rabs.forEach(function(rab) {
                if (data.value == rab.fkDivisi_id) {
                    option += '<option value="' + rab.id + '">' + rab.keperluan + '</option>';
                }
            })
            $("#fkRab_id-" + x).html(option)
        }
    }

    function reloadRab(id) {
        var rabs = <?php echo $rabs; ?>;
        rabs.forEach(function(rab) {
            if (id.value == rab.id) {
                $("#info-rab-out").show();
                $("#info-rab-out").html("<b>Periode "+rab.periode+" dengan estimasi biaya "+toNumber(rab.biaya)+"</b>");
            }
        })
    }

    function clearAll(x) {
        $("#btn-batal-" + x).hide();
        clear();
    }

    function clear() {
        $("#rab_id").val('');

        $("#fkDivisi_id-out").val('');
        $("#fkRab_id-out").val('');
        $("#keterangan-out").val('');
        $("#qty-out").val('1');
        $("#nominal-out").val('');

        $("#tipe_penerimaan-in").val('');
        $("#fkDivisi_id-in").val('');
        $("#fkSodaqoh_id-in").val('');
        $("#keterangan-in").val('');
        $("#nominal-in").val('');

        $("#keterangan-kuop").val('');
        $("#nominal-kuop").val('');
    }

    function ubahJurnal(data) {
        var elem = document.getElementById("section-top");
        elem.scrollIntoView();
        
        $("#jurnal_id").val(data.id);
        if(data.jenis=="in"){
            $("#nav-penerimaan-tab").tab("show");

            $("#btn-batal-in").show();
            $("#fkBank_id_in").val(data.fkBank_id);
            $("#pos-in").val(data.fkPos_id);
            $("#tanggal-in").val(data.tanggal);
            $("#tipe_penerimaan-in").val(data.tipe_penerimaan);
            if(data.fkDivisi_id!="" && data.tipe_penerimaan=="Kembalian"){
                $("#td_fkDivisi_id-in").show();
            }
            if(data.tipe_penerimaan!="" && data.tipe_penerimaan!="Kembalian"){
                $("#td_fkSodaqoh_id-in").show();
            }
            
            $("#fkSodaqoh_id-in").val(data.fkSodaqoh_id);

            $("#fkDivisi_id-in").val(data.fkDivisi_id);
            $("#keterangan-in").val(data.uraian);
            $("#nominal-in").val(data.nominal);
        }else if(data.jenis=="out"){
            $("#nav-pengeluaran-tab").tab("show");

            $("#btn-batal-out").show();
            $("#fkBank_id_out").val(data.fkBank_id);
            $("#pos-out").val(data.fkPos_id);
            $("#fkDivisi_id-out").val(data.fkDivisi_id);
            $("#fkRab_id-out").val(data.fkRab_id);
            $("#tanggal-out").val(data.tanggal);
            $("#tipe_pengeluaran-out").val(data.tipe_pengeluaran);
            $("#keterangan-out").val(data.uraian);
            $("#nominal-out").val(data.nominal);
            $("#qty-out").val(data.qty);
        }
    }

    function simpanJurnal(x) {
        $("#alert-success-" + x).fadeOut();
        $("#alert-danger-" + x).fadeOut();
        $("#alert-success-" + x).html('');
        $("#alert-danger-" + x).html('');

        var datax = {};
        datax['jurnal_id'] = $("#jurnal_id").val();

        if (x == 'out') {
            if ($("#fkDivisi_id-out").val() == '') {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Divisi harap dipilih');
                return false;
            }
            
            if($("#fkDivisi_id-out").val()!=13){
                if ($("#qty-out").val() == '' || $("#qty-out").val() == 0) {
                    $("#alert-danger-" + x).fadeIn();
                    $("#alert-danger-" + x).html('QTY harap diisi');
                    return false;
                }
                if ($("#nominal-out").val() == '' || $("#nominal-out").val() == 0) {
                    $("#alert-danger-" + x).fadeIn();
                    $("#alert-danger-" + x).html('Nominal harap diisi');
                    return false;
                }
            }

            datax['fkBank_id'] = $("#fkBank_id_out").val();
            datax['fkPos_id'] = $("#pos-out").val();
            datax['fkDivisi_id'] = $("#fkDivisi_id-out").val();
            datax['fkRab_id'] = $("#fkRab_id-out").val();
            datax['tanggal'] = $("#tanggal-out").val();
            datax['jenis'] = x;
            datax['keterangan'] = $("#keterangan-out").val();
            datax['tipe_pengeluaran'] = $("#tipe_pengeluaran-out").val();
            datax['qty'] = $("#qty-out").val();
            datax['nominal'] = $("#nominal-out").val();

            postJurnal(datax, x)
        } else if (x == 'in') {
            if ($("#nominal-in").val() == '' || $("#nominal-in").val() == 0) {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Nominal harap diisi');
                return false;
            }

            datax['fkBank_id'] = $("#fkBank_id_in").val();
            datax['fkPos_id'] = $("#pos-in").val();
            datax['tanggal'] = $("#tanggal-in").val();
            datax['jenis'] = x;
            datax['tipe_penerimaan'] = $("#tipe_penerimaan-in").val();
            datax['fkDivisi_id'] = $("#fkDivisi_id-in").val();
            datax['fkSodaqoh_id'] = $("#fkSodaqoh_id-in").val();
            datax['keterangan'] = $("#keterangan-in").val();
            datax['nominal'] = $("#nominal-in").val();

            postJurnal(datax, x)
        } else if (x == 'kuop') {
            if ($("#nominal-kuop").val() == '' || $("#nominal-kuop").val() == 0) {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Nominal harap diisi');
                return false;
            }

            var kuop = ['out', 'in'];

            kuop.forEach(function(dt) {
                var ket = $("#keterangan-kuop").val();
                if (dt == 'in') {
                    datax['fkBank_id'] = 1;
                } else {
                    datax['fkBank_id'] = 2;
                    ket = 'PENGAMBILAN UNTUK OP';
                }
                datax['fkDivisi_id'] = $("#fkDivisi_id-kuop").val();
                datax['fkRab_id'] = $("#fkRab_id-kuop").val();
                datax['status'] = dt;
                datax['fkPos_id'] = $("#pos-kuop").val();
                datax['tanggal'] = $("#tanggal-kuop").val();
                datax['jenis'] = x;
                datax['keterangan'] = ket;
                datax['nominal'] = $("#nominal-kuop").val();

                postJurnal(datax, x)
            })
        }
    }

    function postJurnal(datax, x) {
        $("#loadingSubmit").show();
        $("#info-rab-out").html("");
        $("#info-rab-out").hide();
        $.post("{{ route('store jurnal') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    window.location.reload();
                } else {
                    $("#alert-danger-" + x).fadeIn();
                    $("#alert-danger-" + x).html(return_data.message);
                }

                $("#loadingSubmit").hide();
                reCalculate();
            }
        )
    }

    function hapusJurnal(value) {
        if (confirm('Apakah jurnal ini yakin akan dihapus ?')) {
            var datax = {};
            datax['id'] = value.id;
            $.post("{{ route('delete jurnal') }}", datax,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        const element = document.getElementById("jurnal-" + value.id);
                        element.remove();
                        reCalculate();
                    } else {
                        alert(return_data.message);
                    }
                }
            )
        }
    }

    function reCalculate(){
        const nom_in = document.querySelectorAll("#nominal-in");
        var total_masuk = 0;
        for (let i = 0; i < nom_in.length; i++) {
            var x = nom_in[i].getAttribute("val-in")
            total_masuk = total_masuk + Number(x)
        }

        const nom_out = document.querySelectorAll("#nominal-out");
        var total_keluar = 0;
        for (let i = 0; i < nom_out.length; i++) {
            var y = nom_out[i].getAttribute("val-out")
            total_keluar = total_keluar + Number(y)
        }
        $("#total_masuk").html(toNumber(total_masuk));
        $("#total_keluar").html(toNumber(total_keluar));

        var saldo_awal = <?php echo $saldo_awal; ?>;
        $("#total_sisa_saldo").html(toNumber(saldo_awal+total_masuk-total_keluar));
    }
</script>
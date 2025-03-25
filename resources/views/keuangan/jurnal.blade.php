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
    <div class="row">
        <div class="col-md-2">
            <label class="ms-0">Periode Tahun-Bulan</label>
            <select data-mdb-filter="true" class="select form-control" value="" id="periode_bulan" name="periode_bulan">
                <option value="all">Seluruh Tahun-Bulan</option>
                @foreach($bulans as $bulan)
                <option {{ ($select_bulan==$bulan->ym) ? 'selected' : ''; }}>{{$bulan->ym}}</option>
                @endforeach
            </select>
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
                                                <option value="{{$bank->id}}">{{strtoupper($bank->name)}}</option>
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
                                                <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:10%;">
                                            <label>Kategori</label>
                                            <select class="form-control" value="" id="fkRab_id-out" name="fkRab_id-out" required>
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
                                            <label>Keterangan</label>
                                            <input class="form-control" type="text" value="" id="keterangan-out" required>
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
                                            <input class="form-control" type="number" value="1" id="qty-out" required>
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
                                    <div id="alert-success-out" class="text-sm alert alert-success text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                    <div id="alert-danger-out" class="text-sm alert alert-danger text-white mb-0 mb-2" style="display:none;padding:8px!important;border-radius:0;">

                                    </div>
                                </div>
                                <a href="#" id="btn-batal-out" class="btn btn-danger btn-sm mb-0" onclick="clearAll('out')" style="display:none;">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                    BATAL
                                </a>
                                <a href="#" id="simpan-pengeluaran" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('out')">
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
                                                <option value="{{$bank->id}}">{{strtoupper($bank->name)}}</option>
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
                                            <select data-mdb-filter="true" class="select form-control" value="" id="fkSodaqoh_id" name="fkSodaqoh_id-in">
                                                <option value="">--pilih penerimaan--</option>
                                                @if(count($sodaqohs)>0)
                                                @foreach($sodaqohs as $sodaqoh)
                                                <option value="{{$sodaqoh->id}}">{{$sodaqoh->santri->nama_ortu}} - {{$sodaqoh->santri->user->fullname}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:12%;">
                                            <label>Keterangan</label>
                                            <input class="form-control" type="text" value="" id="keterangan-in" required>
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
                                <a href="#" id="simpan-penerimaan" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('in')">
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
                                        <td class="m-0 p-0 pb-2" style="width:25%;">
                                            <label>Pos</label>
                                            <select class="form-control" value="" id="pos-kuop" name="pos-kuop" required>
                                                @foreach($poses as $pos)
                                                <option value="{{$pos->id}}">{{strtoupper($pos->name)}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:25%;">
                                            <label>Tanggal</label>
                                            <input class="form-control" type="datetime-local" value="{{date('Y-m-d H:i:s')}}" id="tanggal-kuop" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:25%;">
                                            <label>Nominal Pengambilan</label>
                                            <input class="form-control" type="number" value="" id="nominal-kuop" required>
                                        </td>
                                        <td class="m-0 p-0 pb-2" style="width:25%;">
                                            <label>Keterangan</label>
                                            <input class="form-control" type="text" value="" id="keterangan-kuop" required>
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
                                <a href="#" id="simpan-pengambilan" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('kuop')">
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
            <div class="datatablex table-responsive datatable-sm">
                <table class="table align-items-center mb-0 table-bordered text-sm text-uppercase" style="font-size:0.8rem !important">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase font-weight-bolder ps-2">Bank</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Untuk</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Divisi</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Kategori</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Tanggal</th>
                            <th class="text-uppercase font-weight-bolder ps-2">Keterangan</th>
                            <th class="text-uppercase font-weight-bolder text-center">QTY</th>
                            <th class="text-uppercase font-weight-bolder text-end pe-2">Masuk</th>
                            <th class="text-uppercase font-weight-bolder text-end pe-2">Keluar</th>
                            <th class="text-uppercase font-weight-bolder ps-2"></th>
                        </tr>
                    </thead>
                    <tbody id="rab-data">
                        @if(count($jurnals)>0)
                        @foreach ($jurnals as $jurnal)
                        <tr id="jurnal-{{$jurnal->id}}">
                            <td class="new-td text-uppercase">{{$jurnal->bank->name}}</td>
                            <td class="new-td text-uppercase">{{$jurnal->pos->name}}</td>
                            <td class="new-td text-uppercase">{{($jurnal->fkDivisi_id=='') ? '' : strtoupper($jurnal->divisi->divisi)}}</td>
                            <td class="new-td">{{($jurnal->fkRab_id=='') ? '' : $jurnal->rab->keperluan}}</td>
                            <td class="new-td">{{date_format(date_create($jurnal->tanggal), "d-m-Y")}}</td>
                            <td class="new-td">{{$jurnal->uraian}}</td>
                            <td class="new-td text-center">{{$jurnal->qty}}</td>
                            <td class="new-td text-end">{{($jurnal->jenis=='in') ? number_format($jurnal->nominal,0) : ''}}</td>
                            <td class="new-td text-end">{{($jurnal->jenis=='out') ? number_format($jurnal->nominal,0) : ''}}</td>
                            <td class="p-0 text-center">
                                <!-- <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:5px 15px;border-radius:0px;" type="submit" value="Edit" onclick="ubahInout({{$jurnal}})">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </a> -->
                                @if($jurnal->tipe_penerimaan!='Sodaqoh Tahunan')
                                    <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:5px 15px;border-radius:0px;font-size: .5rem !important;" type="submit" value="Hapus" onclick="hapusInout({{$jurnal->id}})">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </td>
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
                            <td></td>
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

    $('#periode_bulan').change((e) => {
        getPage(`{{ url("/") }}/keuangan/jurnal/${$(e.currentTarget).val()}`)
    })

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
        var rabs = <?php echo $rabs; ?>;
        var option = '';
        rabs.forEach(function(rab) {
            if (data.value == rab.fkDivisi_id) {
                option += '<option value="' + rab.id + '">' + rab.keperluan + '</option>';
            }
        })
        $("#fkRab_id-" + x).html(option)
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
        $("#qty-out").val('');
        $("#nominal-out").val('');

        $("#tipe_penerimaan-in").val('');
        $("#fkDivisi_id-in").val('');
        $("#fkSodaqoh_id-in").val('');
        $("#keterangan-in").val('');
        $("#nominal-in").val('');

        $("#keterangan-kuop").val('');
        $("#nominal-kuop").val('');
    }

    function ubahInout(data) {
        $("#btn-batal").show();
        $("#rab_id").val(data.id);
        $("#divisi").val(data.fkDivisi_id);
        $("#keperluan").val(data.keperluan);
        $("#periode").val(data.periode);
        $("#biaya").val(data.biaya);

        for (var i = 1; i <= 12; i++) {
            for (var x = 1; x <= 5; x++) {
                const el = document.querySelector("#bln-" + i + "-mg-" + x);
                if (data['bulan_' + i] != null) {
                    const data_bulan = JSON.parse(data['bulan_' + i]);
                    el.checked = data_bulan[(x - 1)][1];
                }
            }
        }
    }

    function simpanInOut(x) {
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
            if ($("#keterangan-out").val() == '') {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Keterangan harap diisi');
                return false;
            }
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

            postInOut(datax, x)
        } else if (x == 'in') {
            if ($("#keterangan-in").val() == '') {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Keterangan harap diisi');
                return false;
            }
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

            postInOut(datax, x)
        } else if (x == 'kuop') {
            if ($("#keterangan-kuop").val() == '') {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Keterangan harap diisi');
                return false;
            }
            if ($("#nominal-kuop").val() == '' || $("#nominal-kuop").val() == 0) {
                $("#alert-danger-" + x).fadeIn();
                $("#alert-danger-" + x).html('Nominal harap diisi');
                return false;
            }

            var kuop = ['out', 'in'];

            kuop.forEach(function(dt) {
                if (dt == 'in') {
                    datax['fkBank_id'] = 1;
                } else {
                    datax['fkBank_id'] = 2;
                }
                datax['status'] = dt;
                datax['fkPos_id'] = $("#pos-kuop").val();
                datax['tanggal'] = $("#tanggal-kuop").val();
                datax['jenis'] = x;
                datax['keterangan'] = $("#keterangan-kuop").val();
                datax['nominal'] = $("#nominal-kuop").val();

                postInOut(datax, x)
            })
        }
    }

    function postInOut(datax, x) {
        document.getElementById("simpan-pengeluaran").disabled = true;
        document.getElementById("simpan-penerimaan").disabled = true;
        document.getElementById("simpan-pengambilan").disabled = true;
        $.post("{{ route('store jurnal') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    $("#btn-batal-" + x).hide();
                    $("#alert-success-" + x).fadeIn();
                    $("#alert-success-" + x).html(return_data.message);
                    clear();
                    if (datax['jurnal_id'] == '') {
                        $("#rab-data").html($("#rab-data").html() + return_data.content);
                    }
                } else {
                    $("#alert-danger-" + x).fadeIn();
                    $("#alert-danger-" + x).html(return_data.message);
                }

                document.getElementById("simpan-pengeluaran").disabled = false;
                document.getElementById("simpan-penerimaan").disabled = false;
                document.getElementById("simpan-pengambilan").disabled = false;
            }
        )
    }

    function hapusInout(id) {
        if (confirm('Apakah jurnal ini yakin akan dihapus ?')) {
            var datax = {};
            datax['id'] = id;
            $.post("{{ route('delete jurnal') }}", datax,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        const element = document.getElementById("jurnal-" + id);
                        element.remove();
                    } else {
                        alert(return_data.message);
                    }
                }
            )
        }
    }
</script>
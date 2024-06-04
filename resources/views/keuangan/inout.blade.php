@include('base.start', ['path' => 'keuangan/in-out', 'title' => 'Keuangan - In Out', 'breadcrumbs' => ['Keuangan - In Out']])
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
<div class="row">
    <div class="col-md-2">
        <label class="text-white ms-0">Periode Tahun</label>
        <select class="form-control" value="" id="periode_tahun" name="periode_tahun">
            @foreach($periodes as $periode)
            <option {{ ($select_periode==$periode->periode_tahun) ? 'selected' : ''; }}>{{$periode->periode_tahun}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="text-white ms-0">Periode Bulan</label>
        <select class="form-control" value="" id="periode_bulan" name="periode_bulan">
            <option value="all">Seluruh Bulan</option>
            @foreach($bulans as $bulan)
            <option {{ ($select_bulan==$bulan) ? 'selected' : ''; }}>{{$bulan}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="tab mt-2">
    <button class="tablinks active" onclick="openTab(event, 'pengeluaran')">Pengeluaran</button>
    <button class="tablinks" onclick="openTab(event, 'penerimaan')">Penerimaan</button>
    <button class="tablinks" onclick="openTab(event, 'kuop')">Pengambilan OP</button>
</div>

<input class="form-control" type="hidden" value="" id="inout_id" />

<div class="card tabcontent" id="pengeluaran" style="display:block;">
    <div class="card-header p-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 text-xs">
                <tbody>
                    <tr class="">
                        <td class="m-0 p-0 pb-2" style="width:120px;">
                            <label>Keluar Dari</label>
                            <select class="form-control" value="" id="posisi-out" name="posisi-out" required>
                                <option value="BENDAHARA">BENDAHARA</option>
                                <option value="KU">KU</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:80px;">
                            <label>Pos</label>
                            <select class="form-control" value="" id="pos-out" name="pos-out" required>
                                <option value="PPM">PPM</option>
                                <option value="PPM 1">PPM 1</option>
                                <option value="PPM 2">PPM 2</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;">
                            <label>Divisi</label>
                            <select class="form-control" value="" id="fkDivisi_id-out" name="fkDivisi_id-out" required onchange="reloadKategori(this,'out')">
                                <option value="">--pilih divisi--</option>
                                @foreach($divisis as $divisi)
                                <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:250px;">
                            <label>Kategori</label>
                            <select class="form-control" value="" id="fkRab_id-out" name="fkRab_id-out" required>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Tanggal</label>
                            <input class="form-control" type="date" value="{{date('Y-m-d')}}" id="tanggal-out" required>
                        </td>
                        <td class="m-0 p-0 pb-2">
                            <label>Keterangan</label>
                            <input class="form-control" type="text" value="" id="keterangan-out" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;">
                            <label>Pengeluaran</label>
                            <select class="form-control" value="" id="tipe_pengeluaran-out" name="tipe_pengeluaran-out" required>
                                <option value="Rutin">Rutin</option>
                                <option value="Non Rutin">Non Rutin</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:80px;">
                            <label>QTY</label>
                            <input class="form-control" type="number" value="" id="qty-out" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Nominal</label>
                            <input class="form-control" type="number" value="" id="nominal-out" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="ms-auto text-end p-0">
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
                <a href="#" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('out')">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    SIMPAN PENGELUARAN
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card tabcontent" id="penerimaan" style="display:none;">
    <div class="card-header p-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 text-xs">
                <tbody>
                    <tr class="">
                        <td class="m-0 p-0 pb-2" style="width:120px;">
                            <label>Masuk Ke</label>
                            <select class="form-control" value="" id="posisi-in" name="posisi-in" required>
                                <option value="KU">KU</option>
                                <option value="BENDAHARA">BENDAHARA</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="display:none;">
                            <label>Pos</label>
                            <select class="form-control" value="" id="pos-in" name="pos-in" required>
                                <option selected value="PPM">PPM</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Tanggal</label>
                            <input class="form-control" type="date" value="{{date('Y-m-d')}}" id="tanggal-in" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;">
                            <label>Penerimaan</label>
                            <select class="form-control" value="" id="tipe_penerimaan-in" name="tipe_penerimaan-in" required onchange="changeIfTahunan(this.value)">
                                <option value="">--pilih penerimaan--</option>
                                <option value="Sodaqoh Tahunan">Sodaqoh Tahunan</option>
                                <option value="Sodaqoh Fasilitas">Sodaqoh Fasilitas</option>
                                <option value="Sodaqoh Ramadhan">Sodaqoh Ramadhan</option>
                                <option value="Sodaqoh Lainnya">Sodaqoh Lainnya</option>
                                <option value="Kembalian">Kembalian</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;display:none;" id="td_fkDivisi_id-in">
                            <label>Divisi</label>
                            <select class="form-control" value="" id="fkDivisi_id-in" name="fkDivisi_id-in" required>
                                <option value="">--pilih divisi--</option>
                                @foreach($divisis as $divisi)
                                <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;display:none;" id="td_fkSodaqoh_id-in">
                            <label>Ortu-Mahasiswa</label>
                            <select class="form-control" value="" id="fkSodaqoh_id" name="fkSodaqoh_id-in">
                                <option value="">--pilih penerimaan--</option>
                                @if(count($sodaqohs)>0)
                                @foreach($sodaqohs as $sodaqoh)
                                <option value="{{$sodaqoh->id}}">{{$sodaqoh->santri->nama_ortu}} - {{$sodaqoh->santri->user->fullname}}</option>
                                @endforeach
                                @endif
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;">
                            <label>Keterangan</label>
                            <input class="form-control" type="text" value="" id="keterangan-in" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Nominal</label>
                            <input class="form-control" type="number" value="" id="nominal-in" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="ms-auto text-end p-0">
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
                <a href="#" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('in')">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    SIMPAN PENERIMAAN
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card tabcontent" id="kuop" style="display:none;">
    <div class="card-header p-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 text-xs">
                <tbody>
                    <tr class="">
                        <td class="m-0 p-0 pb-2" style="display:none;">
                            <label>Pos</label>
                            <select class="form-control" value="" id="pos-kuop" name="pos-kuop" required>
                                <option selected value="PPM">PPM</option>
                            </select>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Tanggal</label>
                            <input class="form-control" type="date" value="{{date('Y-m-d')}}" id="tanggal-kuop" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:150px;">
                            <label>Nominal Pengambilan</label>
                            <input class="form-control" type="number" value="" id="nominal-kuop" required>
                        </td>
                        <td class="m-0 p-0 pb-2" style="width:100px;">
                            <label>Keterangan</label>
                            <input class="form-control" type="text" value="" id="keterangan-kuop" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="ms-auto text-end p-0">
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
                <a href="#" class="btn btn-primary btn-sm mb-0" onclick="simpanInOut('kuop')">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    SIMPAN PENGAMBILAN
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 text-xs text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Posisi</th>
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Untuk</th>
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Divisi</th>
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Kategori</th>
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Tanggal</th>
                        <!-- <th class="text-uppercase text-secondary font-weight-bolder text-center">IN/OUT</th> -->
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2">Keterangan</th>
                        <th class="text-uppercase text-secondary font-weight-bolder text-center">QTY</th>
                        <th class="text-uppercase text-secondary font-weight-bolder text-end pe-2">Masuk</th>
                        <th class="text-uppercase text-secondary font-weight-bolder text-end pe-2">Keluar</th>
                        <!-- <th class="text-uppercase text-secondary font-weight-bolder">Sisa Saldo</th> -->
                        <th class="text-uppercase text-secondary font-weight-bolder ps-2"></th>
                    </tr>
                </thead>
                <tbody id="rab-data">
                    @if(count($rabinouts)>0)
                    @foreach ($rabinouts as $inout)
                    <tr id="inout-{{$inout->id}}">
                        <td class="new-td text-uppercase">{{$inout->posisi}}</td>
                        <td class="new-td text-uppercase">{{$inout->pos}}</td>
                        <td class="new-td text-uppercase">{{($inout->fkDivisi_id=='') ? '' : $inout->divisi->divisi}}</td>
                        <td class="new-td">{{($inout->fkRab_id=='') ? '' : $inout->rab->keperluan}}</td>
                        <td class="new-td">{{date_format(date_create($inout->tanggal), "Y-m-d")}}</td>
                        <!-- <td class="text-uppercase text-center">{{$inout->jenis}}</td> -->
                        <td class="new-td">{{$inout->uraian}}</td>
                        <td class="new-td text-center">{{$inout->qty}}</td>
                        <td class="new-td text-end">{{($inout->jenis=='in') ? number_format($inout->nominal,0) : ''}}</td>
                        <td class="new-td text-end">{{($inout->jenis=='out') ? number_format($inout->nominal,0) : ''}}</td>
                        <!-- <td></td> -->
                        <td class="p-0 text-center" style="width:50px;">
                            <a class="btn btn-success btn-sm mb-0" style="padding:5px 15px;border-radius:0px;" type="submit" value="Edit" onclick="ubahInout({{$inout}})">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-danger btn-sm mb-0" style="padding:5px 15px;border-radius:0px;" type="submit" value="Hapus" onclick="hapusInout({{$inout->id}})">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </a>
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
                        <!-- <td></td> -->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td></td> -->
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@include('base.end')

<script>
    $('#periode_tahun').change((e) => {
        var periode_bulan = $('#periode_bulan').val();
        window.location.replace(`{{ url("/") }}/keuangan/in-out/${$(e.currentTarget).val()}/` + periode_bulan)
    })
    $('#periode_bulan').change((e) => {
        var periode_tahun = $('#periode_tahun').val();
        window.location.replace(`{{ url("/") }}/keuangan/in-out/` + periode_tahun + `/${$(e.currentTarget).val()}`)
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
        $("#periode_tahun").val();

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
        $("#periode_tahun").focus();
        $("#btn-batal").show();
        $("#rab_id").val(data.id);
        $("#periode_tahun").val(data.periode_tahun);
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
        datax['inout_id'] = $("#inout_id").val();
        datax['periode_tahun'] = $("#periode_tahun").val();

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

            datax['posisi'] = $("#posisi-out").val();
            datax['pos'] = $("#pos-out").val();
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

            datax['posisi'] = $("#posisi-in").val();
            datax['pos'] = $("#pos-in").val();
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

            var kuop = ['in', 'out'];

            kuop.forEach(function(dt) {
                if (dt == 'in') {
                    datax['posisi'] = 'BENDAHARA';
                } else {
                    datax['posisi'] = 'KU';
                }
                datax['status'] = dt;
                datax['pos'] = $("#pos-kuop").val();
                datax['tanggal'] = $("#tanggal-kuop").val();
                datax['jenis'] = x;
                datax['keterangan'] = $("#keterangan-kuop").val();
                datax['nominal'] = $("#nominal-kuop").val();

                postInOut(datax, x)
            })
        }
    }

    function postInOut(datax, x) {
        $.post("{{ route('store inout') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    $("#btn-batal-" + x).hide();
                    $("#alert-success-" + x).fadeIn();
                    $("#alert-success-" + x).html(return_data.message);
                    clear();
                    if (datax['inout_id'] == '') {
                        $("#rab-data").html($("#rab-data").html() + return_data.content);
                    }
                } else {
                    $("#alert-danger-" + x).fadeIn();
                    $("#alert-danger-" + x).html(return_data.message);
                }
            }
        )
    }

    function hapusInout(id) {
        if (confirm('Apakah catatan keuangan ini yakin akan dihapus ?')) {
            $.get(`{{ url("/") }}/keuangan/in-out/delete/` + id,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        const element = document.getElementById("inout-" + id);
                        element.remove();
                    } else {
                        alert(return_data.message);
                    }
                }
            )
        }
    }
</script>
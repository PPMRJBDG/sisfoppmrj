<style>
    .table>:not(caption)>*>* {
        padding: 0;
    }
    .table>:not(caption)>*>* {
        border-bottom-width: 0;
    }
</style>

<input class="form-control" type="hidden" value="" id="rab_id" />
<div class="card">
    <div class="card-header p-2">
        <div class="">
            <table class="table align-items-center mb-0">
                <tbody>
                    <tr class="">
                        <td>
                            <label>Periode Tahun</label>
                            <select data-mdb-filter="true" class="select form-control" value="" id="periode_tahun" name="periode_tahun">
                                @foreach($periodes as $periode)
                                <option {{ ($select_periode==$periode->periode_tahun) ? 'selected' : ''; }}>{{$periode->periode_tahun}}</option>
                                @endforeach
                                <?php
                                $year1 = date('Y');
                                $year2 = date('Y') + 1;
                                $year_periode = $year1 . "-" . $year2;
                                ?>
                                <option {{ ($select_periode==$year_periode) ? 'selected' : ''; }}>{{$year_periode}}</option>
                            </select>
                        </td>
                        <td>
                            <label id="status" style="display:none;">Status</label>
                            <div class="alert alert-success text-white mb-0" style="display:none;padding:12px!important;">

                            </div>
                            <div class="alert alert-danger text-white mb-0" style="display:none;padding:12px!important;">

                            </div>
                        </td>
                    </tr>
                    <tr class="">
                        <td>
                            <label>Divisi</label>
                            <select data-mdb-filter="true" class="select form-control" value="" id="divisi" name="divisi" required>
                                @foreach($divisis as $divisi)
                                <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <label>Keperluan</label>
                            <input class="form-control" type="text" value="" id="keperluan" name="keperluan" required />
                        </td>
                        <td>
                            <label>Periode</label>
                            <select data-mdb-filter="true" class="select form-control" value="" id="periode" name="periode" required>
                                <option value="tahunan">Tahunan</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="duamingguan">2 Mingguan</option>
                            </select>
                        </td>
                        <td>
                            <label>Jumlah</label>
                            <input class="form-control btn-warning" type="submit" value="Set" id="set" name="set" onclick="setPeriode(1)" />
                        </td>
                        <td>
                            <label>Biaya</label>
                            <input class="form-control" type="number" value="" id="biaya" name="biaya" required />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="ms-auto text-end p-2">
                <a href="#" id="btn-batal" class="btn btn-danger" onclick="clearAll()" style="display:none;">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                    BATAL
                </a>
                <a href="#" class="btn btn-primary" onclick="simpanRab()">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    SIMPAN
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="datatable datatable-sm" data-mdb-entries="200">
            <table class="table align-items-center mb-4 text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-start text-secondary font-weight-bolder ps-2">Divisi</th>
                        <th class="text-uppercase text-start text-secondary font-weight-bolder ps-2">Pengeluaran</th>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Periode</th>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Jumlah</th>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Biaya</th>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Total/Tahun</th>
                        <!-- <th class="text-uppercase text-center text-secondary font-weight-bolder">Realisasi</th> -->
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <tbody id="rab-data">
                    @if(count($rabs)>0)
                    @foreach ($rabs as $rab)
                    <?php
                    $total = 0;
                    $jumlah = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        $bulan = json_decode($rab['bulan_' . $i]);
                        if ($bulan != null)
                            for ($x = 1; $x <= 5; $x++) {
                                if ($bulan[$x - 1][1]) {
                                    $jumlah++;
                                    $total += $rab->biaya;
                                }
                            }
                    }
                    ?>
                    <tr id="rab-{{$rab->id}}">
                        <td>{{strtoupper($rab->divisi->divisi)}}</td>
                        <td>{{$rab->keperluan}}</td>
                        <td class="text-center">{{$rab->periode}}</td>
                        <td class="text-center">
                            <a block-id="return-false" class="btn btn-warning btn-sm mb-0" style="padding:5px 15px;" id="lihat-{{$rab->id}}" type="submit" onclick="setPeriode(2, {{$rab}})">
                                ({{$jumlah}})
                            </a>
                        </td>
                        <td class="new-td text-end">{{number_format($rab->biaya,0)}}</td>
                        <td class="new-td text-end">
                            {{number_format($total,0)}}
                        </td>
                        <!-- <td></td> -->
                        <td class="text-center">
                            <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:5px 15px;" type="submit" value="Edit" onclick="ubahRab({{$rab}})">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                            <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:5px 15px;" type="submit" value="Hapus" onclick="hapusRab({{$rab->id}})">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    $('#periode_tahun').change((e) => {
        getPage(`{{ url("/") }}/keuangan/rab-tahunan/${$(e.currentTarget).val()}`)
    })

    $('#close_1').click(function() {
        $('#modalPeriode').fadeOut();
    });
    $('#close_2').click(function() {
        $('#modalPeriode').fadeOut();
        clear();
    });

    function setPeriode(st, data = null) {
        $("#close_1").hide();
        $("#close_2").hide();
        $("#close_" + st).show();
        $('#modalPeriode').fadeIn();
        $('#modalPeriode').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalPeriode').css('z-index', '10000');
        // $('#modalPeriode').css('display', 'inline-table');

        for (var i = 1; i <= 12; i++) {
            for (var x = 1; x <= 5; x++) {
                const el = document.querySelector("#bln-" + i + "-mg-" + x);
                if (data == null) {
                    // el.checked = data_bulan[(x - 1)][1];
                    el.disabled = false;
                } else {
                    if (data['bulan_' + i] != null) {
                        const data_bulan = JSON.parse(data['bulan_' + i]);
                        el.checked = data_bulan[(x - 1)][1];
                    }
                    el.disabled = true;
                }
            }
        }
    }

    function clearAll() {
        $("#btn-batal").hide();
        clear();
    }

    function clear() {
        const el = document.querySelectorAll(".form-check-input");
        for (var i = 0; i < el.length; i++) {
            el[i].checked = false;
        }

        $("#rab_id").val('');
        $("#periode_tahun").val();
        $("#divisi").val(1);
        $("#keperluan").val('');
        $("#periode").val('Tahunan');
        $("#biaya").val('');
    }

    function ubahRab(data) {
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

    function simpanRab() {
        $("#status").fadeOut();
        $(".alert-success").fadeOut();
        $(".alert-success").html('');
        $(".alert-danger").fadeOut();
        $(".alert-danger").html('');

        // validate
        if ($("#keperluan").val() == '') {
            $("#status").fadeIn();
            $(".alert-danger").fadeIn();
            $(".alert-danger").html('Keperluan harap diisi');
            return false;
        }
        if ($("#biaya").val() == '') {
            $("#status").fadeIn();
            $(".alert-danger").fadeIn();
            $(".alert-danger").html('Biaya harap diisi');
            return false;
        }

        var datax = {};
        datax['rab_id'] = $("#rab_id").val();
        datax['periode_tahun'] = $("#periode_tahun").val();
        datax['divisi'] = $("#divisi").val();
        datax['keperluan'] = $("#keperluan").val();
        datax['periode'] = $("#periode").val();
        datax['biaya'] = $("#biaya").val();

        for (var i = 1; i <= 12; i++) {
            datax['bulan_' + i] = [];
            const ival = []
            for (var x = 1; x <= 5; x++) {
                const el = document.querySelector("#bln-" + i + "-mg-" + x);
                ival.push([x, el.checked]);
            }
            datax['bulan_' + i] = JSON.stringify(ival);
        }

        $.post("{{ route('store rab tahunan') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                $("#status").fadeIn();
                if (return_data.status) {
                    $("#btn-batal").hide();
                    $(".alert-success").fadeIn();
                    $(".alert-success").html(return_data.message);
                    clear();
                    window.location.reload();
                } else {
                    $(".alert-danger").fadeIn();
                    $(".alert-danger").html(return_data.message);
                }
            }
        )
    }

    function hapusRab(id) {
        if (confirm('Apakah RAB ini yakin akan dihapus ?')) {
            $.get(`{{ url("/") }}/keuangan/rab-tahunan/delete/` + id,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    $("#status").fadeIn();
                    if (return_data.status) {
                        $(".alert-success").fadeIn();
                        $(".alert-success").html(return_data.message);

                        const element = document.getElementById("rab-" + id);
                        element.remove();
                    } else {
                        $(".alert-danger").fadeIn();
                        $(".alert-danger").html(return_data.message);
                    }
                }
            )
        }
    }
</script>
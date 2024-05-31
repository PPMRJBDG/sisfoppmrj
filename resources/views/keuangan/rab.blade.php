@include('base.start', ['path' => 'rab', 'title' => 'RAB', 'breadcrumbs' => ['RAB']])

<input class="form-control" type="hidden" value="" id="rab_id" />
<div class="card shadow-lg">
    <div class="card-header p-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 text-xs">
                <tbody>
                    <tr class="">
                        <td>
                            <label>Periode Tahun</label>
                            <select class="form-control" value="" id="periode_tahun" name="periode_tahun">
                                @foreach($periodes as $periode)
                                <option {{ ($select_periode==$periode->periode_tahun) ? 'selected' : ''; }}>{{$periode->periode_tahun}}</option>
                                @endforeach
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
                            <select class="form-control" value="" id="divisi" name="divisi" required>
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
                            <select class="form-control" value="" id="periode" name="periode" required>
                                <option value="Tahunan">Tahunan</option>
                                <option value="Bulanan">Bulanan</option>
                                <option value="Mingguan">Mingguan</option>
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
            <div class="ms-auto text-end p-2 pt-0">
                <a href="#" id="btn-batal" class="btn btn-danger btn-sm" onclick="clearAll()" style="display:none;">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                    BATAL
                </a>
                <a href="#" class="btn btn-primary btn-sm" onclick="simpanRab()">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    SIMPAN
                </a>
            </div>
        </div>
    </div>
    <div class="card-body shadow-lg p-0">
        <div class="table-responsive">
            <table class="table align-items-center mb-4 text-sm">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Divisi</th>
                        <th class="text-uppercase text-center text-secondary font-weight-bolder">Pengeluaran</th>
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
                    <tr class="text-center" id="rab-{{$rab->id}}">
                        <td>{{strtoupper($rab->divisi->divisi)}}</td>
                        <td>{{$rab->keperluan}}</td>
                        <td>{{$rab->periode}}</td>
                        <td>
                            <input class="btn btn-warning btn-sm mb-0" type="submit" value="Lihat" onclick="setPeriode(2, {{$rab}})" />
                        </td>
                        <td>{{number_format($rab->biaya,0)}}</td>
                        <td>
                            <?php
                            $total = 0;
                            for ($i = 1; $i <= 12; $i++) {
                                $bulan = json_decode($rab['bulan_' . $i]);
                                for ($x = 1; $x <= 5; $x++) {
                                    if ($bulan[$x - 1][1]) {
                                        $total += $rab->biaya;
                                    }
                                }
                            }
                            ?>
                            {{number_format($total,0)}}
                        </td>
                        <!-- <td></td> -->
                        <td>
                            <input class="btn btn-success btn-sm mb-0" type="submit" value="Edit" onclick="ubahRab({{$rab}})" />
                            <input class="btn btn-danger btn-sm mb-0" type="submit" value="Hapus" onclick="hapusRab({{$rab->id}})" />
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modalPeriodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered p-4" role="document" style="max-width:100% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalPeriodeLabel">Set Periode</h6>
            </div>
            <div class="modal-body p-0">
                <!-- <div class="tab">
                    @for($i=1; $i<=12; $i++) <button class="tablinks {{($i==1) ? 'active' : ''}}" onclick="openTab(event, 'bln_{{$i}}')">{{$i}}</button>@endfor
                </div> -->
                <?php
                for ($i = 1; $i <= 12; $i++) {
                ?>
                    <div class="card tabcontent" id="bln_{{$i}}" style="{{($i==1) ? 'display:block;' : ''}}">
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table id="table" class="table align-items-center mb-4">
                                    <thead class="text-center">
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <th colspan="5" style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">Bulan {{$i}}</th>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                    <th style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">{{$x}}</th>
                                                <?php } ?>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                    <td style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">
                                                        <div class="form-group form-check mb-0" style="margin-left:10px!important;">
                                                            <input class="form-check-input" type="checkbox" id="bln-{{$i}}-mg-{{$x}}" name="bln-{{$i}}-mg-{{$x}}">
                                                        </div>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="close_1" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="close_2" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
@include('base.end')

<script>
    $('#periode_tahun').change((e) => {
        window.location.replace(`{{ url("/") }}/rab/${$(e.currentTarget).val()}`)
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

        for (var i = 1; i <= 12; i++) {
            for (var x = 1; x <= 5; x++) {
                const el = document.querySelector("#bln-" + i + "-mg-" + x);
                if (data == null) {
                    // el.checked = data_bulan[(x - 1)][1];
                    el.disabled = false;
                } else {
                    const data_bulan = JSON.parse(data['bulan_' + i]);
                    el.checked = data_bulan[(x - 1)][1];
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
                const data_bulan = JSON.parse(data['bulan_' + i]);
                el.checked = data_bulan[(x - 1)][1];
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

        $.post("{{ route('store rab') }}", datax,
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
            $.get(`{{ url("/") }}/rab/delete/` + id,
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
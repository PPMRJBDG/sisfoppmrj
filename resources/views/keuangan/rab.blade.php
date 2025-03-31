@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if (session('success'))
<div class="alert alert-success text-white">
    {{ session('success') }}
</div>
@endif

<?php
$lock = 0;
if(count($rabs)>0){
    if($rabs[0]->is_lock==1){
        $lock = 1;
    }
}
?>

<input class="form-control" type="hidden" value="" id="rab_id" />
<div class="mb-2">
    <div class="alert alert-success text-white mb-0" style="display:none;padding:12px!important;"></div>
    <div class="alert alert-danger text-white mb-0" style="display:none;padding:12px!important;"></div>
</div>
<div class="card">
    <div class="card-header p-0">
        <div class="p-2">
            <div class="col-md-6 mb-2 text-start">
                @if(auth()->user()->hasRole('superadmin'))
                    <a href="#" class="btn btn-outline-secondary" onclick="duplicateRab()">
                        <i class="fas fa-clone" aria-hidden="true"></i>
                        DUPLICATE KE PERIODE BARU
                    </a>
                    <a href="#" class="btn btn-outline-secondary" onclick="lockUnlockRab('{{$lock}}','{{$select_periode}}')">
                        @if($lock)
                            <i class="fas fa-unlock" aria-hidden="true"></i>
                            UNLOCK RAB
                        @else
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            LOCK RAB
                        @endif
                    </a>
                @endif
            </div>

            
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
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
                    </div>  
                </div>
                @if(!$lock)
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <label>Divisi</label>
                        <select data-mdb-filter="true" class="select form-control" value="" id="divisi" name="divisi" required>
                            @foreach($divisis as $divisi)
                            <option value="{{$divisi->id}}">{{strtoupper($divisi->divisi)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <label>Keperluan</label>
                        <input class="form-control" type="text" value="" id="keperluan" name="keperluan" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <label>Periode</label>
                        <select class="form-control" value="" id="periode" name="periode" required>
                            <option value="tahunan">Tahunan</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="duamingguan">2 Mingguan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <label>Jumlah</label>
                        <input class="form-control btn-warning" type="submit" value="Set" id="set" name="set" onclick="setPeriode(1)" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <label>Biaya</label>
                        <input class="form-control" type="number" value="" id="biaya" name="biaya" required />
                    </div>
                </div>
                @endif
            </div>
            
            @if(!$lock)
            <div id="submit-rab">
                <div class="col-md-6 ms-auto text-end">
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
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="datatablex table-responsive datatable-sm" data-mdb-entries="200">
            <table class="table table-bordered align-items-center mb-4 text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-center font-weight-bolder ps-2">RAB</th>
                        <th class="text-uppercase text-start font-weight-bolder ps-2">Divisi</th>
                        <th class="text-uppercase text-start font-weight-bolder ps-2">Pengeluaran</th>
                        <th class="text-uppercase text-center font-weight-bolder">Periode</th>
                        <th class="text-uppercase text-center font-weight-bolder">Jumlah</th>
                        <th class="text-uppercase text-center font-weight-bolder">Biaya</th>
                        <th class="text-uppercase text-center font-weight-bolder">Total</th>
                        <!-- <th class="text-uppercase text-center font-weight-bolder">Realisasi</th> -->
                        <th class="text-uppercase text-center font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <?php $total_rab = 0; ?>
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
                            $total_rab = $total_rab + $total;
                            ?>
                            <tr id="rab-{{$rab->id}}">
                                <td class="text-center">
                                    <div class="mb-0">
                                        <input onclick="setCreateRab({{$rab->id}})" {{($lock) ? 'disabled' : ''}} class="form-check-input m-0" type="checkbox" {{($rab->create_rab) ? 'checked' : ''}} id="create-rab-{{$rab->id}}" name="create-rab-{{$rab->id}}">
                                    </div>
                                </td>
                                <td>{{strtoupper($rab->divisi->divisi)}}</td>
                                <td>{{$rab->keperluan}}</td>
                                <td class="text-center">{{$rab->periode}}</td>
                                <td class="text-center">
                                    <a block-id="return-false" class="btn btn-warning btn-sm mb-0 text-black" style="padding:5px;" id="lihat-{{$rab->id}}" type="submit" onclick="setPeriode(2, {{$rab}})">
                                        ({{$jumlah}})
                                    </a>
                                </td>
                                <td class="new-td text-end">{{number_format($rab->biaya,0)}}</td>
                                <td class="new-td text-end">
                                    {{number_format($total,0)}}
                                </td>
                                <!-- <td></td> -->
                                <td class="text-center">
                                    @if($rab->is_lock==0)
                                        <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:5px 15px;" type="submit" value="Edit" onclick="ubahRab({{$rab}})">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:5px 15px;" type="submit" value="Hapus" onclick="hapusRab({{$rab->id}})">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-uppercase text-start font-weight-bolder ps-2"></th>
                        <th class="text-uppercase text-start font-weight-bolder ps-2"></th>
                        <th class="text-uppercase text-start font-weight-bolder ps-2"></th>
                        <th class="text-uppercase text-center font-weight-bolder"></th>
                        <th class="text-uppercase text-center font-weight-bolder"></th>
                        <th class="text-uppercase text-center font-weight-bolder"></th>
                        <th class="text-uppercase text-center font-weight-bolder">{{number_format($total_rab,0)}}</th>
                        <!-- <th class="text-uppercase text-center font-weight-bolder">Realisasi</th> -->
                        <th class="text-uppercase text-center font-weight-bolder"></th>
                    </tr>
                </tfoot>
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

        if(st==1){
            $("#btn-check-x").show();
        }else{
            $("#btn-check-x").hide();
        }

        for (var i = 1; i <= 12; i++) {
            for (var x = 1; x <= 5; x++) {
                const el = document.querySelector("#bln-" + i + "-mg-" + x);
                if (data == null) {
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
        var elem = document.getElementById("section-top");
        elem.scrollIntoView();

        $("#periode_tahun").focus();
        $("#btn-batal").show();
        $("#rab_id").val(data.id);
        $("#periode_tahun").val(data.periode_tahun);
        $("#divisi").val(data.fkDivisi_id);
        $("#keperluan").val(data.keperluan);
        document.getElementById('periode').value = data.periode;
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

        $("#loadingSubmit").show();
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

    function setCreateRab(id){
        var datax = {};
        datax['rab_id'] = id;
        $("#loadingSubmit").show();
        $.post("{{ route('set create rab') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                $("#loadingSubmit").hide();
                if (!return_data.status) {
                    alert("Gagal update")
                }
            }
        )
    }

    function hapusRab(id) {
        if (confirm('Apakah RAB ini yakin akan dihapus ?')) {
            $("#loadingSubmit").show();
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
                    $("#loadingSubmit").hide();
                }
            )
        }
    }
</script>
@include('base.start', ['path' => 'list_sodaqoh', 'title' => 'Daftar Sodaqoh', 'breadcrumbs' => ['Daftar Sodaqoh']])
<?php
$bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
?>
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
<div class="card shadow-lg">
    <div class="card-header p-2">
        <small><i>Last update: {{date_format(date_create($last_update->updated_at), 'd M Y H:i:s')}}</i></small>
        <div class="card shadow-lg">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Periode</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Mahasiswa</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Sudah Lunas</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Belum Lunas</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Penerimaan</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Kekurangan</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Total Estimasi</th>
                        </tr>
                    </thead>
                    <?php
                    $total_vlunas = 0;
                    $total_xlunas = 0;
                    $total_penerimaan = 0;
                    $total_kekurangan = 0;
                    $total_estimasi_penerimaan = 0;
                    ?>
                    <tbody>
                        @if(count($list_periode)>0)
                        <?php
                        foreach ($list_periode as $per) {
                        ?>
                            <tr class="text-center text-bold text-sm">
                                <?php
                                $v = App\Models\Sodaqoh::where('status_lunas', 1)->where('periode', $per->periode)->get();
                                $x = App\Models\Sodaqoh::whereNull('status_lunas')->where('periode', $per->periode)->get();
                                $total = 0;
                                $total_x = 0;
                                $total_nominal = 0;
                                foreach ($v as $data_vlunas) {
                                    foreach ($bulan as $b) {
                                        $total = $total + $data_vlunas->$b;
                                    }
                                }
                                foreach ($x as $data_xlunas) {
                                    foreach ($bulan as $b) {
                                        $total = $total + $data_xlunas->$b;
                                        $total_x = $total_x + $data_xlunas->$b;
                                    }
                                    $total_nominal = $data_vlunas->nominal + $total_nominal;
                                }
                                ?>
                                <td>{{ $per->periode }}</td>
                                <td>{{ count($v)+count($x) }}</td>
                                <td>
                                    {{ count($v) }}
                                </td>
                                <td>
                                    {{ count($x) }}
                                </td>
                                <td class="font-weight-bolder text-right">
                                    {{ number_format($total, 0) }}
                                </td>
                                <td class="font-weight-bolder text-right">
                                    {{ number_format($total_nominal-$total_x, 0) }}
                                </td>
                                <td class="font-weight-bolder text-right">
                                    {{ number_format($total+($total_nominal-$total_x), 0) }}
                                </td>
                            </tr>
                        <?php
                            $total_vlunas += count($v);
                            $total_xlunas += count($x);
                            $total_penerimaan = $total_penerimaan + $total;
                            $total_kekurangan = $total_kekurangan + ($total_nominal - $total_x);
                            $total_estimasi_penerimaan = ($total_penerimaan + $total_kekurangan);
                        }
                        ?>
                        @endif
                        <tr>
                            <td class="text-uppercase text-sm text-center font-weight-bolder"></td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder"></td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder">{{ $total_vlunas }}</td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder">{{ $total_xlunas }}</td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_penerimaan, 0) }}</td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_kekurangan, 0) }}</td>
                            <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_estimasi_penerimaan, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-body p-2">
        @if(count($datax)>0)
        <center><b>Periode {{ isset($periode) ? $periode : '' }}</b></center><br>
        @endif
        <div class="d-flex">
            <select class="select_angkatan form-control" name="" id="select_angkatan">
                <option value="-">Filter Angkatan</option>
                @foreach($list_angkatan as $angkatan)
                <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
                @endforeach
            </select>
            <select class="select_periode form-control" name="select_periode" id="select_periode">
                <option value="-">Filter Periode</option>
                @if(count($list_periode)>0)
                @foreach($list_periode as $per)
                <option {{ ($periode == $per->periode) ? 'selected' : '' }} value="{{$per->periode}}">{{$per->periode}}</option>
                @endforeach
                @endif
            </select>
            <select class="select_lunas form-control" name="select_lunas" id="select_lunas">
                <option {{ ($select_lunas == 2) ? 'selected' : '' }} value="2">Semua</option>
                <option {{ ($select_lunas == 1) ? 'selected' : '' }} value="1">Sudah Lunas</option>
                <option {{ ($select_lunas == 0) ? 'selected' : '' }} value="0">Belum Lunas (sama sekali)</option>
                <option {{ ($select_lunas == 3) ? 'selected' : '' }} value="3">Belum Lunas (baru dicicil)</option>
            </select>
        </div>
        <div class="table-responsive mt-2">
            <table id="table" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Nama</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Periode</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Nominal</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Terbayar</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Kekurangan</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datax))
                    @foreach($datax as $data)
                    <?php
                    $st = 0;
                    foreach ($bulan as $b) {
                        $st = $st + intval($data->$b);
                    }
                    ?>
                    @if(($st==0 && $select_lunas==0) || ($st>0 && $select_lunas==3) || $select_lunas==1 || $select_lunas==2)
                    <tr class="text-sm" id="data{{$data->fkSantri_id}}">
                        <td>
                            <a onclick="openSodaqoh({{$data}},'[{{$data->santri->angkatan}}] {{$data->santri->user->fullname}}',{{json_encode($bulan)}})" class="btn btn-primary btn-xs mb-0">Bayar</a>
                            <b>[{{ $data->santri->angkatan }}]</b> {{ $data->santri->user->fullname }}
                        </td>
                        <td>
                            {{ $data->periode }}
                        </td>
                        <td>
                            {{ number_format($data->nominal,0) }}
                        </td>
                        <?php
                        $status_lunas = 0;
                        $kekurangan = 0;
                        foreach ($bulan as $bl) {
                            $status_lunas = $status_lunas + intval($data->$bl);
                        }
                        $kekurangan = $data->nominal - $status_lunas;
                        $text_error = '';
                        if (isset($data->periode)) {
                            if ($status_lunas < $data->nominal) {
                                $text_error = 'text-warning';
                            }
                        }
                        ?>
                        <td id="terbayar{{$data->fkSantri_id}}" class="{{ $text_error }}">
                            <b>{{ $text_error == '' ? 'Lunas' : number_format($status_lunas,0) }}</b>
                        </td>
                        <td id="kekurangan{{$data->fkSantri_id}}" class="{{ $text_error }}">
                            <b>{{ number_format($kekurangan,0) }}</b>
                        </td>
                        <td>
                            <a href="{{ route('delete sodaqoh', [$data->id, $periode, $select_angkatan, $select_lunas])}}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                            @if($data->status_lunas=='')
                            <a onclick="reminderSodaqoh({{$data}})" id="ingatkan{{$data->id}}" class="btn btn-warning btn-xs mb-0">Ingatkan</a>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="modalSodaqoh" tabindex="-1" role="dialog" aria-labelledby="modalSodaqohLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalSodaqohLabel">Input Sodaqoh</h6>
                    <h5 class="modal-title" id="modalSodaqohLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="p-2" style="background:#f9f9ff;border:1px #ddd solid;">
                    <input class="form-control" readonly type="hidden" id="sodaqoh_id" name="sodaqoh_id" value="">
                    <input class="form-control" readonly type="hidden" id="periode" name="periode" value="">
                    <input class="form-control" readonly type="hidden" id="santri_id" name="santri_id" value="">

                    <label class="form-control-label">Default Sodaqoh / Tahun</label>
                    <input class="form-control" <?php if (!auth()->user()->hasRole('superadmin')) {
                                                    echo 'disabled';
                                                } ?> type="number" id="nominal" name="nominal" value="">
                    <hr>

                    <label class="custom-control-label m-0">Periode Bulan</label>
                    <select class="form-control" name="periode_bulan" id="periode_bulan">
                        <?php foreach ($bulan as $bl) { ?>
                            <option value="{{$bl}}">{{ucfirst($bl)}}</option>
                        <?php } ?>
                    </select>

                    <label class="custom-control-label m-0">Tanggal</label>
                    <input class="form-control" type="date" id="date" name="date">

                    <label class="custom-control-label m-0">Nominal</label>
                    <input class="form-control" type="number" id="nominal_bayar" name="nominal_bayar" placeholder="0">

                    <label class="form-control-label">Keterangan</label>
                    <input class="form-control" type="text" id="ket" name="ket" value="">

                    <br>
                    <label class="form-control-label">*Jika terdapat pembayaran lebih dari 1x dalam 1 bulan, harap yang diinput adalah total dari jumlah pembayaran tersebut</label>
                    <label class="form-control-label">**Jika terdapat perubahan, dapat memilih bulan sesuai yang akan diubah</label>
                </div>

                <table class="table align-items-center mb-0">
                    <tbody>
                        <tr>
                            <td class="text-sm font-weight-bolder ps-0" colspan="2" style="border:none!important;">Riwayat Pembayaran</td>
                        </tr>
                        <?php foreach ($bulan as $bl) {
                        ?>
                            <tr class="text-sm" id="idx{{$bl}}">
                                <td class="p-0" style="border-bottom-width:0!important;width:20%;">
                                    <input class="form-control" disabled type="text" value="{{$bl}}">
                                </td>
                                <td class="p-0" style="border-bottom-width:0!important;width:40%;">
                                    <input class="form-control" disabled type="date" id="{{$bl}}_date" name="{{$bl}}_date">
                                </td>
                                <td class="p-0" style="border-bottom-width:0!important;">
                                    <input class="form-control" disabled type="text" id="{{$bl}}" name="{{$bl}}" placeholder="0">
                                </td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td class="text-sm font-weight-bolder ps-0 text-warning" colspan="2" style="border:none!important;">Kekurangan: <span id="kekurangan"></span></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="form-group form-check mb-0">
                    <label class="custom-control-label">Info via WA</label>
                    <input class="form-check-input" type="checkbox" id="info-wa" name="info-wa">
                </div>
            </div>
            <div class="card-header p-2" id="info-update-sodaqoh" style="display:none;border-radius:4px;">
                <h6 id="bg-warning" class="mb-0 bg-warning p-1 text-white" style="display:none;">
                    <span id="info-warning"></span>
                </h6>
                <h6 id="bg-success" class="mb-0 bg-success p-1 text-white" style="display:none;">
                    <span id="info-success"></span>
                </h6>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="save" class="btn btn-primary mb-0" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#table').DataTable({
        order: [
            [4, 'desc']
        ],
        pageLength: 25
    });
    $('.select_angkatan').change((e) => {
        var periode = $('#select_periode').val()
        var lunas = $('#select_lunas').val()
        window.location.replace(`{{ url("/") }}/list_sodaqoh/` + periode + `/${$(e.currentTarget).val()}/` + lunas)
    })
    $('.select_periode').change((e) => {
        var angkatan = $('#select_angkatan').val()
        var lunas = $('#select_lunas').val()
        window.location.replace(`{{ url("/") }}/list_sodaqoh/${$(e.currentTarget).val()}/` + angkatan + `/` + lunas)
    })
    $('.select_lunas').change((e) => {
        var angkatan = $('#select_angkatan').val()
        var periode = $('#select_periode').val()
        window.location.replace(`{{ url("/") }}/list_sodaqoh/` + periode + `/` + angkatan + `/${$(e.currentTarget).val()}`)
    })

    function openSodaqoh(data, nm, bulan) {
        $('#modalSodaqoh').fadeIn();
        $('#modalSodaqoh').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalSodaqoh').css('z-index', '10000');
        $('#sodaqoh_id').val(data.id);
        $('#periode').val(data.periode);
        $('#nominal').val(data.nominal);
        $('#santri_id').val(data.fkSantri_id);
        $('#ket').val(data.keterangan);
        $('#nm').text(nm + ' | ' + data.periode);

        setHistory(data, bulan)
    }

    async function setHistory(data, bulan) {
        $("#kekurangan").html('');
        var kekurangan = 0;
        var terbayar = 0;
        bulan.forEach(function(item, b) {
            document.getElementById('idx' + item).setAttribute('style', 'display:none;');
            if (data[item] > 0) {
                terbayar = terbayar + parseInt(data[item]);
                $('#' + item).val(new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR"
                }).format(parseInt(data[item])));
                $('#' + item + '_date').val(data[item + '_date']);
                document.getElementById('idx' + item).setAttribute('style', 'display:block;');
            }
        })
        kekurangan = parseInt(data['nominal']) - terbayar;
        $("#kekurangan").html(new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
        }).format(parseInt(kekurangan)));
    }

    function reminderSodaqoh(data) {
        if (confirm("Ingatkan sekarang ?")) {
            $("#ingatkan" + data.id).html('Loading');
            var datax = {};
            datax['id'] = data.id;
            $.post("{{ route('reminder sodaqoh') }}", datax,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    $("#ingatkan" + data.id).html('Ingatkan');
                    // alert(return_data.message);
                }
            );
        }
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $('#save').click(function() {
        var datax = {};
        $("#info-update-sodaqoh").hide();
        datax['id'] = $('#sodaqoh_id').val();
        datax['nominal'] = $('#nominal').val();
        datax['fkSantri_id'] = $('#santri_id').val();
        datax['keterangan'] = $('#ket').val();
        datax['info-wa'] = false;
        datax['periode_bulan'] = $("#periode_bulan").val();
        datax['date'] = $('#date').val();
        datax['nominal_bayar'] = $('#nominal_bayar').val();
        var checkBox = document.getElementById("info-wa");
        if (checkBox.checked == true) {
            datax['info-wa'] = true;
        }

        $.post("{{ route('store sodaqoh') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                // alert(return_data.message);
                $("#info-update-sodaqoh").show();
                if (return_data.status) {
                    $("#bg-success").show();
                    $("#info-success").html(return_data.message);
                    setHistory(return_data.data, return_data.bulan)

                    var kekurangan = 0;
                    var terbayar = 0;
                    return_data.bulan.forEach(function(item, b) {
                        if (return_data.data[item] > 0) {
                            terbayar = terbayar + parseInt(return_data.data[item]);
                        }
                        kekurangan = parseInt(return_data.data.nominal) - terbayar;
                    })
                    var text_style = 'text-error';
                    terbayar = numberWithCommas(terbayar)
                    if (kekurangan <= 0) {
                        text_style = 'text-dark';
                        terbayar = 'Lunas';
                    }

                    $("#terbayar" + return_data.data.fkSantri_id).html('<b class="' + text_style + '">' + terbayar + '</b>');
                    $("#kekurangan" + return_data.data.fkSantri_id).html('<b class="' + text_style + '">' + numberWithCommas(kekurangan) + '</b>');
                } else {
                    $("#bg-warning").show();
                    $("#info-warning").html(return_data.message);
                }
            }
        );
    })

    $('#close').click(function() {
        $('#modalSodaqoh').fadeOut();
        clear()
    });

    function clear() {
        $("#kekurangan").html('');
        $("#info-update-sodaqoh").hide();
        $("#bg-warning").hide();
        $("#bg-success").hide();
        $('#sodaqoh_id').val('');
        $('#periode').val('');
        $('#nominal').val('');
        $('#santri_id').val('');
        $('#ket').val('');
        $('#nm').text('');
        var bulan = <?php echo json_encode($bulan); ?>;
        bulan.forEach(function(item, b) {
            $('#' + item).val('');
            $('#' + item + '_date').val('');
        })
    }
</script>
@include('base.end')
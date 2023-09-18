@include('base.start', ['path' => 'sodaqoh/list', 'title' => 'Daftar Sodaqoh', 'breadcrumbs' => ['Daftar Sodaqoh']])
<?php
$bulan = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags', 'sep', 'okt', 'nov', 'des'];
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
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-primary">Sudah Lunas</h5>
                        <h5>{{ $vlunas }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-danger">Belum Lunas</h5>
                        <h5>{{ $xlunas }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body pt-0">
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
        </div>
        <div class="table-responsive mt-2">
            <table id="table" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Periode</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nominal</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Terbayar</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Kekurangan</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datax))
                    @foreach($datax as $data)
                    <tr class="text-sm" id="data{{$data->fkSantri_id}}">
                        <td>
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
                        <td class="{{ $text_error }}">
                            <b>{{ $text_error == '' ? 'Lunas' : number_format($status_lunas,0) }}</b>
                        </td>
                        <td class="{{ $text_error }}">
                            <b>{{ number_format($kekurangan,0) }}</b>
                        </td>
                        <td>
                            <a onclick="openSodaqoh({{$data}},'[{{$data->santri->angkatan}}] {{$data->santri->user->fullname}}',{{json_encode($bulan)}})" class="btn btn-primary btn-xs mb-0">Bayar</a>
                            <a href="{{ route('delete sodaqoh', [$data->id, $periode, $select_angkatan])}}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                        </td>
                    </tr>
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
                <div class="p-2" style="background:#f9f9ff;">
                    <input class="form-control" readonly type="hidden" id="sodaqoh_id" name="sodaqoh_id" value="">
                    <input class="form-control" readonly type="hidden" id="periode" name="periode" value="">
                    <input class="form-control" readonly type="hidden" id="santri_id" name="santri_id" value="">
                    <label class="form-control-label">Sodaqoh / Tahun</label>
                    <input class="form-control" type="number" id="nominal" name="nominal" value="">
                </div>

                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">BULAN</th>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">TANGGAL</th>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">NOMINAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bulan as $bl) { ?>
                            <tr class="text-sm">
                                <td class="p-0 ps-2" style="border-bottom-width:0!important;">{{ucwords($bl)}}</td>
                                <td class="p-0" style="border-bottom-width:0!important;">
                                    <label class="custom-control-label m-0">Tanggal</label>
                                    <input class="form-control" type="date" id="{{$bl}}_date" name="{{$bl}}_date">
                                </td>
                                <td class="p-0" style="border-bottom-width:0!important;">
                                    <label class="custom-control-label m-0">Nominal</label>
                                    <input class="form-control" type="number" id="{{$bl}}" name="{{$bl}}" placeholder="0">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <label class="form-control-label">Keterangan</label>
                <input class="form-control" type="text" id="ket" name="ket" value="">
                <hr>
                <div class="form-group form-check">
                    <label class="custom-control-label">Info via WA</label>
                    <input class="form-check-input" type="checkbox" id="info-wa" name="info-wa">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save" class="btn btn-primary" data-dismiss="modal">Simpan</button>
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#table').DataTable({
        order: [],
        pageLength: 25
    });
    $('.select_angkatan').change((e) => {
        var periode = $('#select_periode').val()
        window.location.replace(`{{ url("/") }}/sodaqoh/list/` + periode + `/${$(e.currentTarget).val()}`)
    })
    $('.select_periode').change((e) => {
        var angkatan = $('#select_angkatan').val()
        window.location.replace(`{{ url("/") }}/sodaqoh/list/${$(e.currentTarget).val()}/` + angkatan)
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

        bulan.forEach(function(item, b) {
            $('#' + item).val(data[item]);
            $('#' + item + '_date').val(data[item + '_date']);
        })
    }

    $('#save').click(function() {
        var datax = {};
        datax['id'] = $('#sodaqoh_id').val();
        datax['nominal'] = $('#nominal').val();
        datax['fkSantri_id'] = $('#santri_id').val();
        datax['keterangan'] = $('#ket').val();
        datax['info-wa'] = false;
        var checkBox = document.getElementById("info-wa");
        if (checkBox.checked == true) {
            datax['info-wa'] = true;
        }

        var bulan = <?php echo json_encode($bulan); ?>;
        bulan.forEach(function(item, b) {
            datax[item] = $('#' + item).val();
            datax[item + '_date'] = $('#' + item + '_date').val();
        })
        // console.log(datax);

        $.post("{{ route('store sodaqoh') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                alert(return_data.message);
                if (return_data.status) {
                    location.reload();
                }
            }
        );
    })

    $('#close').click(function() {
        $('#modalSodaqoh').fadeOut();
        clear()
    });

    function clear() {
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
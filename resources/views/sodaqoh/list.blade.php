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
        <center><b>Periode {{ isset($periode) ? $periode.' : Nominal Rp '.number_format($datax[0]->nominal,0).',- / Tahun' : '' }}</b></center><br>
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
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$i}}</th>
                        <?php } ?>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Terbayar</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Kekurangan</th>
                        <!-- <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Ket</th> -->
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
                            <a href="#" onclick="openSodaqoh({{$data->id}},'{{$data->periode}}','nominal',{{$data->fkSantri_id}})">
                                {{ number_format($data->nominal,0) }}
                            </a>
                        </td>
                        <?php
                        $status_lunas = 0;
                        $kekurangan = 0;
                        foreach ($bulan as $bl) {
                            $status_lunas = $status_lunas + intval($data->$bl);
                        ?>
                            <td>
                                <a href="#" onclick="openSodaqoh({{$data->id}},'{{$data->periode}}','{{$bl}}',{{$data->fkSantri_id}})">{{ ($data->$bl!=null) ? number_format($data->$bl,0) : '0' }}</a>
                            </td>
                        <?php
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
                        <!-- <td>
                            <a href="#" onclick="openSodaqoh({{$data->id}},'{{$data->periode}}','ket',{{$data->fkSantri_id}})">{{ ($data->keterangan!='') ? $data->keterangan : 'click' }}</a>
                        </td> -->
                        <td>
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

    function openSodaqoh(id, periode, bulan, idSantri) {
        if (bulan == 'ket') {
            var value = prompt("Masukkan keterangan sodaqoh!");
        } else if (bulan == 'nominal') {
            var value = prompt("Masukkan nominal sodaqoh!");
        } else {
            var value = prompt("Masukkan jumlah sodaqoh!");
        }

        if (value != null && value != "") {
            if (bulan != 'ket' && bulan != 'nominal') {
                value = parseInt(value);
            }
            if (Number.isInteger(value) || bulan == 'ket' || bulan == 'nominal') {
                $.post("{{ route('store sodaqoh') }}", {
                        id: id,
                        periode: periode,
                        bulan: bulan,
                        santri_id: idSantri,
                        jumlah: value
                    },
                    function(data, status) {
                        var return_data = JSON.parse(data);
                        alert(return_data.message);
                        if (return_data.status) {
                            location.reload();
                        }
                    }
                );
            } else {
                alert("Silahkan input berupa angka!");
            }
        }
    }
</script>
@include('base.end')
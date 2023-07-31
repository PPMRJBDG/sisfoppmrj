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
    <div class="card-body">
        <center><b>Periode {{ isset($periode) ? $periode.': Nominal Rp '.number_format($datax[0]->nominal,0).',- / Tahun' : '' }}</b></center>
        <div class="d-flex">
            <select class="select_periode form-control" name="select_periode" id="select_periode">
                <option value="-">Filter Periode</option>
                @if(count($list_periode)>0)
                @foreach($list_periode as $per)
                <option {{ ($periode == $per->periode) ? 'selected' : '' }} value="{{$per->periode}}">{{$per->periode}}</option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Angkatan</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama</th>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$i}}</th>
                        <?php } ?>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Total</th>
                        <!-- <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Rukhso</th> -->
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datax))
                    @foreach($datax as $data)
                    <tr class="text-sm" id="data{{$data->fkSantri_id}}">
                        <td>
                            {{ $data->santri->angkatan }}
                        </td>
                        <td>
                            {{ $data->santri->user->fullname }}
                        </td>
                        <?php
                        $status_lunas = 0;
                        foreach ($bulan as $bl) {
                            $status_lunas = $status_lunas + intval($data->$bl);
                        ?>
                            <td>
                                <a href="#" onclick="openSodaqoh({{$data->id}},'{{$periode}}','{{$bl}}',{{$data->fkSantri_id}})">{{ ($data->$bl!=null) ? number_format($data->$bl,0) : '0' }}</a>
                            </td>
                        <?php
                        }
                        ?>
                        <td>
                            <b>{{ number_format($status_lunas,0) }}</b>
                        </td>
                        <!-- <td>
                            {{ $data->status_rukhso }}
                        </td> -->
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('.select_periode').change((e) => {
        window.location.replace(`{{ url("/") }}/sodaqoh/list/${$(e.currentTarget).val()}`)
    })

    function openSodaqoh(id, periode, bulan, idSantri) {
        var value = prompt("Masukkan jumlah sodaqoh!");
        if (value != null && value != "") {
            value = parseInt(value);
            if (Number.isInteger(value)) {
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
                    });
            } else {
                alert("Silahkan input berupa angka!");
            }
        }
    }
</script>
@include('base.end')
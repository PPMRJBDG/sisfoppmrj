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

<small><i>Last update: {{date_format(date_create($last_update->updated_at), 'd M Y H:i:s')}}</i></small>
<div class="card border">
    <div class="datatablex datatable-sm" data-mdb-pagination="false">
        <table class="table align-items-center mb-0">
            <thead>
                <tr>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Periode</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Mahasiswa</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Sudah Lunas</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Belum Lunas</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Total Estimasi</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Penerimaan</th>
                    <th class="text-uppercase text-sm text-center font-weight-bolder">Kekurangan</th>
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
                        ?>
                        <td>{{ $per->periode }}</td>
                        <td>{{ count($v)+count($x) }}</td>
                        <td>{{ count($v) }}</td>
                        <td>{{ count($x) }}</td>
                        <?php
                        $total = 0;
                        foreach ($v as $data_vlunas) {
                            $penerimaan = App\Models\SodaqohHistoris::where('fkSodaqoh_id',$data_vlunas->id)->where('status','approved')->get();
                            foreach ($penerimaan as $b) {
                                $total = $total + $b->nominal;
                            }
                        }
                        $totalv = 0;
                        foreach ($v as $data_vlunas) {
                            $totalv = $totalv + $data_vlunas->nominal;
                        }
                        $totalx = 0;
                        foreach ($x as $data_xlunas) {
                            $totalx = $totalx + $data_xlunas->nominal;
                        }

                        $total_nominal = $totalv+$totalx;
                        ?>
                        <td class="font-weight-bolder text-right">{{ number_format($total_nominal, 0) }}</td>
                        <td class="font-weight-bolder text-right">{{ number_format($total, 0) }}</td>
                        <td class="font-weight-bolder text-right">{{ number_format($total_nominal-$total, 0) }}</td>
                    </tr>
                <?php
                    $total_vlunas += count($v);
                    $total_xlunas += count($x);
                    $total_penerimaan = $total_penerimaan + $total;
                    $total_kekurangan = $total_kekurangan + ($total_nominal-$total);
                    $total_estimasi_penerimaan = ($total_penerimaan + $total_kekurangan);
                }
                ?>
                @endif
                <tr>
                    <td class="text-uppercase text-sm text-center font-weight-bolder"></td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder"></td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder">{{ $total_vlunas }}</td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder">{{ $total_xlunas }}</td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_estimasi_penerimaan, 0) }}</td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_penerimaan, 0) }}</td>
                    <td class="text-uppercase text-sm text-center font-weight-bolder">{{ number_format($total_kekurangan, 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@if(count($datax)>0)
<div class="p-2">
    <center><b>Periode {{ isset($periode) ? $periode : '' }}</b></center>
</div>
@endif

<div class="card border p-2 mb-2">
    <div class="row">
        <div class="col-md-4 mb-2">
            <select data-mdb-filter="true" class="select select_angkatan form-control" name="select_angkatan" id="select_angkatan">
                <option value="-">Filter Angkatan</option>
                @foreach($list_angkatan as $angkatan)
                <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <select data-mdb-filter="true" class="select select_periode form-control" name="select_periode" id="select_periode">
                <option value="-">Filter Periode</option>
                @if(count($list_periode)>0)
                @foreach($list_periode as $per)
                <option {{ ($periode == $per->periode) ? 'selected' : '' }} value="{{$per->periode}}">{{$per->periode}}</option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-4">
            <select data-mdb-filter="true" class="select select_lunas form-control" name="select_lunas" id="select_lunas">
                <option {{ ($select_lunas == 2) ? 'selected' : '' }} value="2">Semua</option>
                <option {{ ($select_lunas == 1) ? 'selected' : '' }} value="1">Sudah Lunas</option>
                <option {{ ($select_lunas == 0) ? 'selected' : '' }} value="0">Belum Lunas (sama sekali)</option>
                <option {{ ($select_lunas == 3) ? 'selected' : '' }} value="3">Belum Lunas (baru dicicil)</option>
            </select>
        </div>
    </div>
</div>

<div class="card border">
    <div class="datatable datatable-sm align-items-center justify-content-center" data-mdb-entries="50">
        <table id="table" class="table mb-0">
            <thead>
                <tr>
                    <th class="text-uppercase text-sm font-weight-bolder"></th>
                    <th class="text-uppercase text-sm font-weight-bolder">Nama</th>
                    <th class="text-uppercase text-sm font-weight-bolder">Periode</th>
                    <th class="text-uppercase text-sm font-weight-bolder">Nominal</th>
                    <th class="text-uppercase text-sm font-weight-bolder">Terbayar</th>
                    <th class="text-uppercase text-sm font-weight-bolder">Kekurangan</th>
                    <th class="text-uppercase text-sm font-weight-bolder"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($datax))
                    @foreach($datax as $data)
                        <?php
                            $st = 0;
                            $penerimaan = App\Models\SodaqohHistoris::where('fkSodaqoh_id',$data->id)->where('status','approved')->get();
                            foreach ($penerimaan as $b) {
                                $st = $st + intval($b->nominal);
                            }
                        ?>
                        @if(($st==0 && $select_lunas==0) || ($st>0 && $select_lunas==3) || $select_lunas==1 || $select_lunas==2)
                            <tr class="text-sm" id="data{{$data->fkSantri_id}}">
                                <td>
                                    <a block-id="return-false" onclick="openSodaqoh({{$data}},'[{{$data->santri->angkatan}}] {{$data->santri->user->fullname}}',{{json_encode($penerimaan)}})" class="btn btn-{{($data->status_lunas) ? 'secondary' : 'primary'}} btn-sm mb-0">
                                        {{($data->status_lunas) ? 'Riwayat' : 'Bayar'}}
                                    </a>
                                </td>
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
                                foreach ($penerimaan as $bl) {
                                    $status_lunas = $status_lunas + intval($bl->nominal);
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
                                    <a block-id="return-false" href="{{ route('delete sodaqoh', [$data->id, $periode, $select_angkatan, $select_lunas])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                                    @if($data->status_lunas=='')
                                    <a block-id="return-false" onclick="reminderSodaqoh({{$data}})" id="ingatkan{{$data->id}}" class="btn btn-warning btn-sm mb-0">Ingatkan</a>
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

<script>


$('.select_angkatan').change((e) => {
    var periode = $('#select_periode').val()
    var lunas = $('#select_lunas').val()
    getPage(`{{ url("/") }}/keuangan/sodaqoh/` + periode + `/${$(e.currentTarget).val()}/` + lunas)
});

$('.select_periode').change((e) => {
    var angkatan = $('#select_angkatan').val()
    var lunas = $('#select_lunas').val()
    getPage(`{{ url("/") }}/keuangan/sodaqoh/${$(e.currentTarget).val()}/` + angkatan + `/` + lunas)
});

$('.select_lunas').change((e) => {
    var angkatan = $('#select_angkatan').val()
    var periode = $('#select_periode').val()
    getPage(`{{ url("/") }}/keuangan/sodaqoh/` + periode + `/` + angkatan + `/${$(e.currentTarget).val()}`)
});
</script>
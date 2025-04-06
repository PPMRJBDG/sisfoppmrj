<small><i>Last update: {{date_format(date_create($last_update->updated_at), 'd M Y H:i:s')}}</i></small>
<div class="card border">
    <div class="datatablex table-responsive datatable-sm" data-mdb-pagination="false">
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
<?php $GLOBALS['total_kekurangan_sodaqoh_tahunan'] = $total_kekurangan; ?>
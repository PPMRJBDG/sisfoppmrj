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

<div class="card shadow border p-2">
    <div class="card-body p-2 pt-0">
        <div class="datatable justify-content-between align-items-center" data-mdb-max-height="830" data-mdb-fixed-header="true" data-mdb-sm="true" data-mdb-entries="50" data-mdb-pagination="false" data-mdb-hover="true" data-mdb-bordered="true">
            <table id="table" class="table justify-content-between align-items-center text-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th data-mdb-width="300" data-mdb-fixed="true">NAMA</th>
                        @foreach($column_pelanggarans as $c)
                            <th><center>{{$c->jenis->jenis_pelanggaran}}</center></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($santris as $data)
                        <tr>
                            <td onclick="getReport('<?php echo base64_encode($data->santri_id); ?>')" style="cursor:pointer;">
                                <b>[{{ $data->angkatan }}] {{ $data->fullname }}</b>
                            </td>
                            @foreach($column_pelanggarans as $c)
                                <td><p class="text-center mb-0">
                                    <?php 
                                        $check = App\Models\Pelanggaran::where('fkSantri_id',$data->santri_id)->where('fkJenis_pelanggaran_id',$c->jenis->id)->where('is_archive',0)->first();
                                        if($check){
                                            echo '✅';
                                        }
                                    ?>
                                </p></td>
                            @endforeach
                        </tr>
                    @endforeach
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
</script>
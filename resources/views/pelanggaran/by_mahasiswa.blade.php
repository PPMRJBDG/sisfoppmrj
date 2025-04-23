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

<div class="row">
    <div class="col-sm-8">
        <div class="row">
            <div class="col-5">
                <input type="datetime-local" value="{{date('Y-m-d H:i:s')}}" class="form-control mb-2" value="" id="datetime-all">
            </div>
            <div class="col-5">
                <input type="text" placeholder="Tempat" class="form-control mb-2" value="" id="tempat">
            </div>
            <div class="col-2">
                <a href="#" onclick="callByWa(1)" class="btn btn-outline-warning text-end btn-sm mb-1"><i class="fa fa-comment"> WA SEMUA</i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-4 text-end">
        <a href="{{route('pelanggaran tm1')}}" class="btn btn-outline-secondary text-end btn-sm mb-1">Kembali</a>
    </div>
</div>
<div class="card shadow border p-2">
    <div class="card-body p-2 pt-0">
        <div class="datatable justify-content-between align-items-center" data-mdb-max-height="800" data-mdb-fixed-header="true" data-mdb-sm="true" data-mdb-entries="50" data-mdb-pagination="false" data-mdb-hover="true" data-mdb-bordered="true">
            <table id="table" class="table justify-content-between align-items-center text-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th data-mdb-width="200" data-mdb-fixed="true">WA</th>
                        <th data-mdb-width="300" data-mdb-fixed="true">NAMA</th>
                        @foreach($column_pelanggarans as $c)
                            <th><center>{{$c->jenis->jenis_pelanggaran}}</center></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($santris as $data)
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-8 p-0">
                                        <input type="datetime-local" value="{{date('Y-m-d H:i:s')}}" class="form-control" value="" id="datetime-{{$data->santri->id}}">
                                    </div>
                                    <div class="col-4 pr-0">
                                        <a href="#" onclick="callByWa(0,{{$data->santri->id}})" class="btn btn-outline-warning text-end btn-sm mb-1"><i class="fa fa-comment"> WA</i></a>
                                    </div>
                                </div>
                            </td>
                            <td onclick="getReport('<?php echo base64_encode($data->santri->id); ?>')" style="cursor:pointer;">
                                <b>[{{ $data->santri->angkatan }}] {{ $data->santri->user->fullname }}</b>
                            </td>
                            @foreach($column_pelanggarans as $c)
                                <td><p class="text-center mb-0">
                                    <?php 
                                        $check = App\Models\Pelanggaran::where('fkSantri_id',$data->santri->id)->where('fkJenis_pelanggaran_id',$c->jenis->id)->where('is_archive',0)->first();
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

    function callByWa(all,santri_id=null) {
        if (confirm("Lakukan pemanggilan ?")) {
            if($("#tempat").val()==""){
                alert("Tempat pemanggilan harap diisi.")
                return false;
            }
            $("#loadingSubmit").show();
            var datax = {};
            if(all==1){
                datax['all'] = 1;
            }else{
                datax['all'] = 0;
            }
            datax['santri_id'] = santri_id;
            datax['tempat'] = $("#tempat").val();
            datax['datetime'] = $("#datetime-all").val();
            $.post("{{ route('pelanggaran wa') }}", datax,
                function(dataz, status) {
                    $("#loadingSubmit").hide();
                }
            );
        }
    }
</script>
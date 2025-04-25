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

<div class="col-sm-12 text-end">
    <a href="{{route('pelanggaran tm1')}}" class="btn btn-outline-secondary text-end btn-sm mb-1">Kembali</a>
</div>
<div class="card shadow border p-2 mb-2">
    <div class="row">
        <div class="col-sm-6">
            <section class="text-center pt-2">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
                        <i class="fas fa-user fa-3x text-primary mb-4"></i>
                        <h5 class="text-primary fw-bold mb-3">{{count($santris)}}</h5>
                        <h6 class="fw-normal mb-0">Mahasiswa</h6>
                        <div class="vr vr-blurry position-absolute my-0 h-100 d-none d-md-block top-0 end-0"></div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
                        <i class="fas fa-user fa-3x text-warning mb-4"></i>
                        <h5 class="text-warning fw-bold mb-3">{{count($santri_l)}}</h5>
                        <h6 class="fw-normal mb-0">Laki-laki</h6>
                        <div class="vr vr-blurry position-absolute my-0 h-100 d-none d-md-block top-0 end-0"></div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
                        <i class="fas fa-user fa-3x text-secondary mb-4"></i>
                        <h5 class="text-secondary fw-bold mb-3">{{count($santri_p)}}</h5>
                        <h6 class="fw-normal mb-0">Perempuan</h6>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-12 mb-2">
                    <select data-mdb-filter="true" id="fkSantri_id" name="fkSantri_id" required class="select form-control">
                        <option value="0">Semua Santri</option>
                        @foreach($santris as $s)
                            <option value="{{ $s->santri->id }}">{{ $s->santri->user->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mb-2">
                    <input type="datetime-local" value="{{date('Y-m-d H:i:s')}}" class="form-control" value="" id="datetime-all">
                </div>
                <div class="col-12 mb-2">
                    <input type="text" placeholder="Tempat" class="form-control" value="" id="tempat">
                </div>
                <div class="col-12 text-end">
                    <a href="#" onclick="callByWa()" class="btn btn-outline-warning text-end btn-sm mb-1"><i class="fa fa-comment"></i> Infokan Pemanggilan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border p-2">
    <div class="p-2">
        <input class="form-control" placeholder="Search" type="text" id="search" onkeyup="searchDataSantri('santrix',this.value)">
    </div>
    <div class="card-body p-2 pt-0">
        <div id="santrix" class="datatable justify-content-between align-items-center" data-mdb-fixed-header="true" data-mdb-sm="true" data-mdb-entries="200" data-mdb-pagination="false" data-mdb-hover="true" data-mdb-bordered="true">
            <table id="table" class="table justify-content-between align-items-center text-wrap mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th data-mdb-width="200" data-mdb-fixed="true">NAMA</th>
                        @foreach($column_pelanggarans as $c)
                            <th><center><div class="text-wrap" style="width:50px;font-size:8px;">{{$c->jenis->jenis_pelanggaran}}</div></center></th>
                        @endforeach
                        <th data-mdb-fixed="true">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($santris as $data)
                        <tr>
                            <td>
                                <span class="santri-name text-left" santri-name="{{ $data->santri->user->fullname }}" onclick="getReport('<?php echo base64_encode($data->santri->id); ?>')" style="cursor:pointer;">
                                    <b>[{{ $data->santri->angkatan }}] {{ $data->santri->user->fullname }}</b>
                                </span>
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
                            <td>
                                <a href="#" onclick="selesaiKafaroh({{$data->santri->id}})" class="btn btn-outline-secondary text-end btn-sm mb-1"><i class="fa fa-archive"></i> DONE</a>
                            </td>
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

    function callByWa() {
        if (confirm("Lakukan pemanggilan ?")) {
            if($("#tempat").val()==""){
                alert("Tempat pemanggilan harap diisi.")
                return false;
            }
            $("#loadingSubmit").show();
            var datax = {};
            datax['santri_id'] = $("#fkSantri_id").val();;
            datax['tempat'] = $("#tempat").val();
            datax['datetime'] = $("#datetime-all").val();
            $.post("{{ route('pelanggaran wa') }}", datax,
                function(dataz, status) {
                    $("#loadingSubmit").hide();
                }
            );
        }
    }

    function selesaiKafaroh(santri_id=null) {
        if (confirm("Sudah dipastikan menyelesaikan Kafaroh ?")) {
            $("#loadingSubmit").show();
            var datax = {};
            datax['santri_id'] = santri_id;
            $.post("{{ route('selesai kafaroh') }}", datax,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    if (return_data.status) {
                        window.location.reload();
                    }
                }
            );
        }
    }
</script>
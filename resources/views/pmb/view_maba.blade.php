<div class="row mb-2">
    <div class="col-md-6">
        <h5 class=""><b>Daftar Calon Mahasiswa Baru</b></h5>
    </div>
    <div class="col-md-6">
        <select data-mdb-filter="true" class="select select_angkatan form-control bg-white" name="select_angkatan" id="select_angkatan">
            @foreach($list_angkatan as $la)
            <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="card shadow border mb-2">
    <div class="card-body p-2">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary"></th>
                        <th class="text-uppercase text-sm text-secondary">NAMA</th>
                        <th class="text-uppercase text-sm text-secondary">KELAMIN</th>
                        <th class="text-uppercase text-sm text-secondary">TEMPAT TGL LAHIR</th>
                        <th class="text-uppercase text-sm text-secondary">GOL DARAH</th>
                        <th class="text-uppercase text-sm text-secondary">NOMOR WA</th>
                        <th class="text-uppercase text-sm text-secondary">STATUS</th>
                        <!-- <th class="text-uppercase text-sm text-secondary">RIWAYAT PENYAKIT</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($camabas as $camaba)
                        <tr class="text-sm">
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" onclick="detilMaba({{$camaba}})">DETIL</a>
                            </td>
                            <td>
                                {{ strtoupper($camaba->fullname) }}
                            </td>
                            <td>
                                {{ strtoupper($camaba->gender) }}
                            </td>
                            <td>
                                {{ strtoupper($camaba->place_of_birth).', '.date_format(date_create($camaba->birthday), 'd-m-Y') }}
                            </td>
                            <td>
                                {{ $camaba->blood_group }}
                            </td>
                            <td>
                                {{ $camaba->nomor_wa }}
                            </td>
                            <td>
                            <select class="form-control" id="status" name="status" onchange="return changeStatus({{$camaba->id}},this.value)">
                                <option {{ ($camaba->status=='in-review') ? 'selected' : '' }} value="in-review">REVIEW</option>
                                <option {{ ($camaba->status=='interview') ? 'selected' : '' }} value="interview">INTERVIEW</option>
                                <option {{ ($camaba->status=='pass') ? 'selected' : '' }} value="pass">LOLOS</option>
                            </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left" id="modalCamaba" tabindex="-1" role="dialog" aria-labelledby="modalCamabaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title font-weight-bolder" id="modalCamabaLabel">DATA LENGKAP</h6>
                </div>
            </div>
            <div class="modal-body">
                <table class="table" id="data-camaba">

                </table>
            </div>
            <div class="modal-footer">
                <a href="#" onclick="hideCamaba()" class="btn btn-sm btn-primary">KELUAR</a>
            </div>
        </div>
    </div>
</div>

<script>
    function detilMaba(camaba){
        var data_maba = '';
        Object.keys(camaba).forEach(function(item, index) {
            if(item!="created_at" && item!="updated_at"){
                var new_item = item.replace(/_/g, " ");
                data_maba = data_maba+'<tr style="border-bottom:1px solid rgb(220 220 220);"><th class="p-2" style="background-color:#f6f9fc;">'+new_item.toUpperCase()+'</th><td class="p-2">'+camaba[item]+'</td></tr><hr>';
            }
        })
        $("#modalCamaba").fadeIn();
        $('#modalCamaba').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalCamaba').css('z-index', '10000');
        $('#modalCamaba').css('display', 'inline-table');
        $("#data-camaba").html(data_maba);
    }
    function changeStatus(id,val){
        var datax = {};
        datax['id'] = id;
        datax['status'] = val;
        $.post("{{ route('change status maba') }}", datax,
            function(dataz, status) {
                
            }
        );
    }
    function hideCamaba(){
        $("#modalCamaba").fadeOut();
        $("#data-camaba").html("");
    }
</script>
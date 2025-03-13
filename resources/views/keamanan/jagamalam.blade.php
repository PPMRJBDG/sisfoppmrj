<div class="card mb-2">
    <div class="card-body">
        <div class="card-title font-weight">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" value="" id="ppm" name="ppm" required>
                        <option value="1">PPM 1</option>
                        <option value="2">PPM 2</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select data-mdb-filter="true" class="select form-control" value="" id="putaran_ke" name="putaran_ke" required>
                        @for($i=1; $i<=20; $i++)
                        <option value="{{$i}}">Putaran {{$i}}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <select data-mdb-filter="true" class="select form-control" value="" id="anggota" name="anggota" required onchange="selectAnggota(this)">
                        <option value="">--pilih anggota--</option>
                        @foreach($santris as $s)
                        <option value="{{$s->id}}">{{$s->fullname}}</option>
                        @endforeach
                    </select>
                    <div class="bg-primary">

                    </div>
                </div>

                <div class="col-md-3">
                    <a href="#" class="btn btn-primary btn-block mb-0" onclick="simpanJagaMalam()">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        SIMPAN
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">PUTARAN</th>
                        <th class="text-uppercase text-sm text-secondary">ANGOTA</th>
                        <th class="text-uppercase text-sm text-secondary">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $d)
                    <tr class="text-sm">
                        <td>
                            {{ $d->putaran_ke }}
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            {{ $d->status }}
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

    function selectAnggota(t){
        alert(t.innerHTML)
    }

    function simpanJagaMalam() {
        var datax = {};
        datax['anggota'] = $("#anggota").val();
        alert(datax['anggota']);
        // $.post("{{ route('store jagamalam') }}", datax,
        //     function(data, status) {
                // var return_data = JSON.parse(data);
                // if (return_data.status) {
                //     $("#btn-batal-" + x).hide();
                //     $("#alert-success-" + x).fadeIn();
                //     $("#alert-success-" + x).html(return_data.message);
                //     clear();
                //     if (datax['inout_id'] == '') {
                //         $("#rab-data").html($("#rab-data").html() + return_data.content);
                //     }
                // } else {
                //     $("#alert-danger-" + x).fadeIn();
                //     $("#alert-danger-" + x).html(return_data.message);
                // }
            // }
        // )
    }
</script>
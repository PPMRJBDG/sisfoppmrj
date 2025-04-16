<div class="card mb-2">
    <div class="card-body pb-0">
        <div class="card-title font-weight">
            <div class="row">
                <input type="hidden" name="id" id="id" value="">
                <div class="col-md-3 p-1">
                    <select class="form-control mb-0" value="" id="ppm" name="ppm" required>
                        <option value="1">PPM 1</option>
                        <option value="2">PPM 2</option>
                    </select>
                </div>

                <div class="col-md-3 p-1">    
                    <input type="number" class="form-control" value="" placeholder="putaran ke" id="putaran_ke" name="putaran_ke">
                </div>

                <div class="col-md-3 mb-2 p-1">
                    <select data-mdb-filter="true" class="select form-control" value="" id="anggota" name="anggota" required onchange="selectAnggota(this)">
                        <option value="">--pilih anggota--</option>
                        @foreach($santris as $s)
                        <option value="{{$s->santri_id}}">{{$s->fullname}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" value="" id="anggota_terpilih" name="anggota_terpilih">
                    <div class="border mt-2 p-2" id="daftar_pilihan" style="display:none;background-color:#f6f9fc;"></div>
                </div>

                <div class="col-md-3 mb-2">
                    <dv class="row">
                        <div class="col-md-6 p-0">
                            <a href="#" class="btn btn-primary mb-2 btn-block" onclick="simpanJagaMalam()">
                                <i class="fas fa-save" aria-hidden="true"></i>
                                SIMPAN
                            </a>
                        </div>
                        <div class="col-md-6 p-0">
                            <a href="#" class="btn btn-warning mb-0 hidden btn-block" id="batal" onclick="batalUpdateJagaMalam(this)" >
                                <i class="fas fa-save" aria-hidden="true"></i>
                                BATAL
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        @for($p=1; $p<=2; $p++)
        <a class="btn btn-warning btn-sm mb-2">PPM {{$p}}</a>
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">PUTARAN</th>
                        <th class="text-uppercase text-sm text-secondary">ANGOTA</th>
                        <th class="text-uppercase text-sm text-secondary">STATUS</th>
                        <th class="text-uppercase text-sm text-secondary"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $d)
                        @if($d->ppm==$p)
                            <tr class="text-sm">
                                <td>
                                    {{ $d->putaran_ke }}
                                </td>
                                <td>
                                    <?php
                                        $daftar_pilihan = "";
                                        $split_team = explode(",", $d->anggota);
                                        foreach($split_team as $st){
                                            if($st!=""){
                                                $nama = App\Models\Santri::find($st)->user->fullname;
                                                $daftar_pilihan .= '<div id="mhs'.$st.'"><button onclick="delMhs('.$st.')" type="button" href="#" class="btn btn-sm btn-danger btn-floating"><i class="fa fa-trash"></i></button> '.$nama.'</div>';
                                                echo $nama.'<br>';
                                            }
                                        }
                                    ?>
                                </td>
                                <td>
                                    {{ ($d->status==1) ? 'Next' : '-' }}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <a href="#" class="btn btn-secondary btn-sm mb-0" onclick="ubahJagaMalam({{$d}},'{{$daftar_pilihan}}')">Ubah</a>
                                    <a href="{{ route('delete jagamalam', [$d->id])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endfor
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    function ubahJagaMalam(data,daftar_pilihan){
        document.getElementById('body-top').scrollIntoView()
        $("#daftar_pilihan").fadeIn();
        $("#id").val(data.id);
        $("#ppm").val(data.ppm);
        $("#putaran_ke").val(data.putaran_ke);
        $("#daftar_pilihan").html(daftar_pilihan);
        $("#anggota_terpilih").val(data.anggota);
    }

    function delMhs(id){
        const element = document.getElementById("mhs"+id);
        element.remove();

        var pilihan = $("#anggota_terpilih").val().split(",");
        var new_pilihan = "";
        pilihan.forEach(function(pil){
            if(pil!=id && pil!=""){
                new_pilihan += pil+',';
            }
        })
        $("#anggota_terpilih").val(new_pilihan)
    }

    function batalUpdateJagaMalam(t){
        $("#id").val('');
        $("#ppm").val(1);
        $("#putaran_ke").val('');
        $("#daftar_pilihan").html('');
        $("#anggota_terpilih").val('');
        $("#daftar_pilihan").fadeOut();
    }

    function selectAnggota(t){
        $("#daftar_pilihan").fadeIn();
        var id = $("#anggota option:selected").val()
        $("#daftar_pilihan").html($("#daftar_pilihan").html()+"<div id='mhs"+id+"'><button onclick='delMhs("+id+")' type='button' href='#' class='btn btn-sm btn-danger btn-floating'><i class='fa fa-trash'></i></button>  "+$("#anggota option:selected").text()+'</div>');
        $("#anggota_terpilih").val($("#anggota_terpilih").val()+$("#anggota option:selected").val()+',');
    }

    function simpanJagaMalam() {
        var datax = {};
        datax['id'] = $("#id").val();
        datax['ppm'] = $("#ppm").val();
        datax['putaran_ke'] = $("#putaran_ke").val();
        datax['anggota'] = $("#anggota_terpilih").val();
        if(datax['anggota']==""){
            alert("Silahkan pilih anggota");
        }else{
            $("#loadingSubmit").show();
            $.post("{{ route('store jagamalam') }}", datax,
                function(data, status) {
                    window.location.reload();
                }
            )
        }
    }
</script>
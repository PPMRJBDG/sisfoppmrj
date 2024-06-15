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

<div class="card shadow-lg p-2">
    <div class="table-responsive">
        <table id="table" class="table align-items-center mb-0">
            <thead style="background-color:#f6f9fc;">
                <tr>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Mahasiswa</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Kepribadian</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Sholat</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">KBM</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Asmara</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Akhlaq</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Umum</th>
                </tr>
            </thead>
            <tbody>
                @if(count($cat_penghubung)>0)
                @foreach($cat_penghubung as $cp)
                <tr class="text-sm">
                    <td>
                        <a href="#" onclick="openCatatan('{{$cp->id}}','{{$cp->santri_id}}','[{{$cp->angkatan}}] {{$cp->fullname}}','{{$cp->cat_kepribadian}}','{{$cp->cat_sholat}}','{{$cp->cat_kbm}}','{{$cp->cat_asmara}}','{{$cp->cat_akhlaq}}','{{$cp->cat_umum}}')" class="btn btn-primary btn-xs mb-0">INPUT</a>
                        [{{$cp->angkatan}}] {{$cp->fullname}}
                    </td>
                    <td id="kepribadian{{$cp->santri_id}}">{{substr($cp->cat_kepribadian,0,20);}}</td>
                    <td id="sholat{{$cp->santri_id}}">{{substr($cp->cat_sholat,0,20);}}</td>
                    <td id="kbm{{$cp->santri_id}}">{{substr($cp->cat_kbm,0,20);}}</td>
                    <td id="asmara{{$cp->santri_id}}">{{substr($cp->cat_asmara,0,20);}}</td>
                    <td id="akhlaq{{$cp->santri_id}}">{{substr($cp->cat_akhlaq,0,20);}}</td>
                    <td id="umum{{$cp->santri_id}}">{{substr($cp->cat_umum,0,20);}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="modalCatatan" tabindex="-1" role="dialog" aria-labelledby="modalCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalCatatanLabel">Input Catatan Penghubung</h6>
                    <h5 class="modal-title" id="modalCatatanLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="p-2">
                    <input class="form-control" readonly type="hidden" id="cat_id" name="cat_id" value="">
                    <input class="form-control" readonly type="hidden" id="santri_id" name="santri_id" value="">

                    <label class="form-control-label">Kepribadian</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="kepribadian{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_kepribadian" name="cat_kepribadian"></textarea>

                    <label class="form-control-label">Sholat 5 Waktu</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="sholat{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_sholat" name="cat_sholat"></textarea>

                    <label class="form-control-label">KBM</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="kbm{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_kbm" name="cat_kbm"></textarea>

                    <label class="form-control-label">Asmara</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="asmara{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_asmara" name="cat_asmara"></textarea>

                    <label class="form-control-label">Akhlaq</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="akhlaq{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_akhlaq" name="cat_akhlaq"></textarea>

                    <label class="form-control-label">Umum</label>
                    <!-- <?php for ($i = 1; $i < 10; $i++) { ?> <i class="fa fa-star" id="umum{{$i}}" aria-hidden="true"></i> <?php } ?> -->
                    <textarea class="form-control" type="text" id="cat_umum" name="cat_umum"></textarea>
                    @if(auth()->user()->hasRole('superadmin'))
                    <hr>
                    <div class="form-group form-check">
                        <input class="form-check-input" type="checkbox" id="info-wa" name="info-wa">
                        <label class="form-check-label" for="info-wa">Kirim melalui WA</label>
                    </div>
                    @endif
                </div>
                <div class="card-header p-0 mt-2" id="info-update-catatan" style="display:none;border-radius:4px;">
                    <h6 id="bg-warning" class="mb-0 bg-warning p-1 text-white" style="display:none;">
                        <span id="info-warning"></span>
                    </h6>
                    <h6 id="bg-success" class="mb-0 bg-success p-1 text-white" style="display:none;">
                        <span id="info-success"></span>
                    </h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="save" class="btn btn-primary mb-0" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    $('#table').DataTable({
        order: [],
        pageLength: 25
    });

    function openCatatan(id, santri_id, nm, kepribadian, sholat, kbm, asmara, akhlaq, umum) {
        $('#modalCatatan').fadeIn();
        $('#modalCatatan').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalCatatan').css('z-index', '10000');
        $('#nm').text(nm);
        $('#cat_id').val(id);
        $('#santri_id').val(santri_id);

        if ($("#kepribadian" + santri_id).html() != '') {
            $("#cat_kepribadian").val($("#kepribadian" + santri_id).html())
        } else {
            $("#cat_kepribadian").val(kepribadian);
        }
        if ($("#sholat" + santri_id).html() != '') {
            $("#cat_sholat").val($("#sholat" + santri_id).html())
        } else {
            $("#cat_sholat").val(sholat);
        }
        if ($("#kbm" + santri_id).html() != '') {
            $("#cat_kbm").val($("#kbm" + santri_id).html())
        } else {
            $("#cat_kbm").val(kbm);
        }
        if ($("#asmara" + santri_id).html() != '') {
            $("#cat_asmara").val($("#asmara" + santri_id).html())
        } else {
            $("#cat_asmara").val(asmara);
        }
        if ($("#akhlaq" + santri_id).html() != '') {
            $("#cat_akhlaq").val($("#akhlaq" + santri_id).html())
        } else {
            $("#cat_akhlaq").val(akhlaq);
        }
        if ($("#umum" + santri_id).html() != '') {
            $("#cat_umum").val($("#umum" + santri_id).html())
        } else {
            $("#cat_umum").val(umum);
        }
    }

    $('#save').click(function() {
        var datax = {};
        $("#info-update-catatan").hide();

        var santri_id = $("#santri_id").val();
        datax['id'] = $("#cat_id").val();
        datax['santri_id'] = $("#santri_id").val();
        datax['cat_kepribadian'] = $("#cat_kepribadian").val();
        datax['cat_sholat'] = $("#cat_sholat").val();
        datax['cat_kbm'] = $("#cat_kbm").val();
        datax['cat_asmara'] = $("#cat_asmara").val();
        datax['cat_akhlaq'] = $("#cat_akhlaq").val();
        datax['cat_umum'] = $("#cat_umum").val();

        var checkBox = document.getElementById("info-wa");
        if (checkBox.checked == true) {
            datax['info_wa'] = true;
        }

        $.post("{{ route('store catatan') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                $("#info-update-catatan").show();
                checkBox.checked = false
                if (return_data.status) {
                    $("#kepribadian" + santri_id).html($("#cat_kepribadian").val());
                    $("#sholat" + santri_id).html($("#cat_sholat").val());
                    $("#kbm" + santri_id).html($("#cat_kbm").val());
                    $("#asmara" + santri_id).html($("#cat_asmara").val());
                    $("#akhlaq" + santri_id).html($("#cat_akhlaq").val());
                    $("#umum" + santri_id).html($("#cat_umum").val());

                    $("#bg-success").show();
                    $("#info-success").html(return_data.message);
                } else {
                    $("#bg-warning").show();
                    $("#info-warning").html(return_data.message);
                }
            }
        );
    })

    $('#close').click(function() {
        $('#modalCatatan').fadeOut();
        $("#info-update-catatan").hide();
        $('#nm').text('');
        $('#cat_id').val('');
        $('#santri_id').val('');
        $("#cat_kepribadian").val('');
        $("#cat_sholat").val('');
        $("#cat_kbm").val('');
        $("#cat_asmara").val('');
        $("#cat_akhlaq").val('');
        $("#cat_umum").val('');
    });
</script>
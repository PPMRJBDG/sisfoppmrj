<div class="modal" id="modalCatatan" tabindex="-1" role="dialog" aria-labelledby="modalCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalCatatanLabel">Input Catatan Penghubung</h6>
                    <h5 class="modal-title" id="modalCatatanLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body p-4">
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
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="info-wa" name="info-wa">
                    <label class="form-check-label" for="info-wa">Kirim melalui WA</label>
                </div>
                @endif
                <div class="card-header p-0 mt-2" id="info-update-catatan" style="display:none;border-radius:4px;">
                    <h6 id="bg-warning-catatan" class="mb-0 bg-warning p-1 text-white" style="display:none;">
                        <span id="info-warning-catatan"></span>
                    </h6>
                    <h6 id="bg-success-catatan" class="mb-0 bg-success p-1 text-white" style="display:none;">
                        <span id="info-success-catatan"></span>
                    </h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-penghubung" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="save-penghubng" class="btn btn-primary mb-0" data-dismiss="modal">Simpan</button>
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

    function openCatatan(id, santri_id, nm, kepribadian, sholat, kbm, asmara, akhlaq, umum) {
        $('#modalCatatan').fadeIn();
        $('#nm').text(nm);
        $('#cat_id').val(id);
        $('#santri_id').val(santri_id);

        $("#cat_kepribadian").val(kepribadian);
        $("#cat_sholat").val(sholat);
        $("#cat_kbm").val(kbm);
        $("#cat_asmara").val(asmara);
        $("#cat_akhlaq").val(akhlaq);
        $("#cat_umum").val(umum);
    }

    $('#save-penghubng').click(function() {
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
            datax['info-wa'] = true;
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

                    $("#bg-success-catatan").show();
                    $("#info-success-catatan").html(return_data.message);
                } else {
                    $("#bg-warning-catatan").show();
                    $("#info-warning-catatan").html(return_data.message);
                }
            }
        );
    })

    $('#close-penghubung').click(function() {
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
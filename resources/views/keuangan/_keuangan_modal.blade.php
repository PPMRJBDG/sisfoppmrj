<?php
$bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
?>
<style>
.table>:not(caption)>*>* {
    padding: 8px;
}
#table-periode>:not(caption)>*>* {
    padding: 0px;
}
</style>

<div class="modal" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left" id="modalPreviewBukti" tabindex="-1" role="dialog" aria-labelledby="modalPreviewBuktiLabel">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body pb-0">
                <div id="dialog-preview-bukti" class="card border p-2 mt-2 mb-2" style="display:none;">
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="$('#modalPreviewBukti').hide()" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left" id="modalSodaqoh" tabindex="-1" role="dialog" aria-labelledby="modalSodaqohLabel">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalSodaqohLabel">Input Sodaqoh</h6>
                    <p class="modal-title" id="modalSodaqohLabel">
                        <span id="nm"></span><br>
                        <span id="nm-per"></span>
                    </p>
                </div>
            </div>
            <div class="modal-body p-2">
                <div id="form-bayar" class="p-2" style="background:#f9f9ff;border:1px #ddd solid;">
                    <input class="form-control" readonly type="hidden" id="sodaqoh_id" name="sodaqoh_id" value="">
                    <input class="form-control" readonly type="hidden" id="periode" name="periode" value="">
                    <input class="form-control" readonly type="hidden" id="santri_id" name="santri_id" value="">

                    <label class="form-control-label">Kewajiban Sodaqoh / Tahun</label>
                    <input class="form-control" <?php if (!auth()->user()->hasRole('superadmin')) {
                                                    echo 'disabled';
                                                } ?> type="number" id="nominal" name="nominal" value="">
                    <hr>

                    <div class="form-group">
                        <label class="form-label m-0">Tanggal Transfer Pembayaran</label>
                        <input class="form-control" type="date" id="date" value="{{date('Y-m-d')}}" name="date">
                    </div>

                    <div class="form-group">
                        <label class="form-label m-0">Nominal</label>
                        <input class="form-control" type="number" id="nominal_bayar" name="nominal_bayar" placeholder="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label mb-0" for="bukti_transfer">Bukti Transfer</label>
                        <input class="form-control" type="file" id="bukti_transfer" name="bukti_transfer">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input class="form-control" type="text" id="ket" name="ket" value="">
                    </div>
                </div>

                <div id="preview-bukti" class="card border p-2 mt-2 mb-2" style="display:none;">
                
                </div>

                <p class="mt-2 mb-0 font-weight-bold">Riwayat Pembayaran</p>
                <div class="justify-content-center">
                    <table class="table align-items-center mb-0">
                        <tbody id="idx_histori">
                            
                        </tbody>
                    </table>
                </div>
                <p class="text-sm font-weight-bolder mt-3 pb-2 text-warning">Kekurangan: <span id="kekurangan"></span></p>
            </div>
            <div class="card-header p-2" id="info-update-sodaqoh" style="display:none;border-radius:4px;">
                <h6 id="bg-warning" class="mb-0 bg-warning p-1 text-white" style="display:none;">
                    <span id="info-warning"></span>
                </h6>
                <h6 id="bg-success" class="mb-0 bg-success p-1 text-white" style="display:none;">
                    <span id="info-success"></span>
                </h6>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="save" class="btn btn-primary mb-0" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modalPeriodeLabel">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="max-width:98%;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalPeriodeLabel">Set Periode</h6>
            </div>
            <div class="modal-body p-0">
                <div class="row p-2" id="btn-check-x">
                    <div class="col-md-6">
                        <label class="font-weight-bolder">Setiap Bulan Kalender Akademik</label>
                        <br>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(8,1)">Minggu 1</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(8,2)">Minggu 2</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(8,3)">Minggu 3</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(8,4)">Minggu 4</a>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bolder">Setiap Bulan Penuh</label>
                        <br>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(12,1)">Minggu 1</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(12,2)">Minggu 2</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(12,3)">Minggu 3</a>
                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="clickPeriode(12,4)">Minggu 4</a>
                    </div>
                </div>
                <div class="card shadow border tabcontent">
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table id="table-periode" class="table align-items-center mb-4">
                                <thead class="text-center">
                                    <tr>
                                        <?php for ($i = 1; $i <= 6; $i++) { ?>
                                            <th colspan="5" style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">Bulan {{$i}}</th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 1; $i <= 6; $i++) { ?>
                                            <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                <th style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">{{$x}}</th>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <?php for ($i = 1; $i <= 6; $i++) { ?>
                                            <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                <td style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">
                                                    <div class="form-group form-check mb-0" style="margin-left:10px!important;">
                                                        <input class="form-check-input" type="checkbox" id="bln-{{$i}}-mg-{{$x}}" name="bln-{{$i}}-mg-{{$x}}">
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow border tabcontent">
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table id="table-periode" class="table align-items-center mb-4">
                                <thead class="text-center">
                                    <tr>
                                        <?php for ($i = 7; $i <= 12; $i++) { ?>
                                            <th colspan="5" style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">Bulan {{$i}}</th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 7; $i <= 12; $i++) { ?>
                                            <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                <th style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">{{$x}}</th>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <?php for ($i = 7; $i <= 12; $i++) { ?>
                                            <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                <td style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">
                                                    <div class="form-group form-check mb-0" style="margin-left:10px!important;">
                                                        <input class="form-check-input" type="checkbox" id="bln-{{$i}}-mg-{{$x}}" name="bln-{{$i}}-mg-{{$x}}">
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close_1" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="close_2" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="alertModalPayment" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <div>
            <h6 class="modal-title font-weight-bolder badge badge-warning" id="alertModalLabel">Pemberitahuan</h6>
        </div>
        <div>
            <a style="cursor:pointer;" id="close"><i class="ni ni-fat-remove text-lg"></i></a>
        </div>
        </div>
        <div class="modal-body" id="contentAlertPayment">

        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="window.location.reload();">Keluar</button>
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

    function openSodaqoh(data, nm, histori) {
        $('#modalSodaqoh').fadeIn();
        $('#sodaqoh_id').val(data.id);
        $('#periode').val(data.periode);
        $('#nominal').val(data.nominal);
        $('#santri_id').val(data.fkSantri_id);
        $('#ket').val(data.keterangan);
        $('#nm').text(nm);
        $('#nm-per').text('Periode: '+data.periode);
        if(data.status_lunas){
            $("#form-bayar").hide();
        }else{
            $("#form-bayar").show();
        }

        setHistory(data, histori)
    }

    function clickPeriode(tipe,minggu){
        const elx = document.querySelectorAll(".form-check-input");
        for (var i = 0; i < elx.length; i++) {
            elx[i].checked = false;
        }

        for (var i = 1; i <= 12; i++) {
            var checked = true;
            if(tipe==8){
                if(i==2 || i==7 || i==8 || i==9){
                    checked = false;
                }
            }
            const el = document.querySelector("#bln-" + i + "-mg-" + minggu);
            el.checked = checked;
        }
    }

    async function setHistory(data, histori) {
        $("#kekurangan").html('');
        var kekurangan = 0;
        var terbayar = 0;
        var content =   '<tr style="font-size:0.7rem;background:#f9f9ff;">'+
                            '<td class="text-uppercase font-weight-bolder">Tanggal</th>'+
                            '<th class="text-uppercase font-weight-bolder">Nominal</th>'+
                            '<th class="text-uppercase font-weight-bolder">Status</th>'+
                            '<th class="text-uppercase font-weight-bolder">Bukti</th>'+
                        '</tr>';
        document.getElementById('idx_histori').setAttribute('style', 'display:none;');
        $("#idx_histori").html("");

        histori.forEach(function(item, b) {
            if(item['status']=='approved'){
                terbayar = terbayar + parseInt(item['nominal']);
            }
            var nominal = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(parseInt(item['nominal']));
            var status = '<span class="badge badge-secondary">Pending</span>';
            if(item['status']=='approved'){
                status = '<span class="badge badge-success">Approved</span>';
            }
            var image = '';
            if(item['bukti_transfer']!=null && item['bukti_transfer']!=""){
                var show = item['bukti_transfer'];
                image = '<button  type="button" class="btn btn-primary btn-floating btn-sm" data-mdb-ripple-init onclick="showBukti(\''+show+'\')"><i class="fa fa-image"></i></button>';
            }
            
            content = content+  '<tr style="font-size:0.7rem;">'+
                                    '<td>'+item["pay_date"]+
                                    '<td>'+nominal+
                                    '<td>'+status+
                                    '<td>'+image+
                                '</tr>';
        })

        $("#idx_histori").html(content);
        document.getElementById('idx_histori').setAttribute('style', 'display:block;');

        kekurangan = parseInt(data['nominal']) - terbayar;
        $("#kekurangan").html(new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
        }).format(parseInt(kekurangan)));

        if(kekurangan<=0){
            $("#save").hide();
        }
    }

    function reminderSodaqoh(data) {
        if (confirm("Ingatkan sekarang ?")) {
            $("#ingatkan" + data.id).html('Loading');
            var datax = {};
            datax['id'] = data.id;
            $.post("{{ route('reminder sodaqoh') }}", datax,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    $("#ingatkan" + data.id).html('Ingatkan');
                }
            );
        }
    }

    function showBukti(image){
        $("#preview-bukti").focus();
        $("#preview-bukti").show();
        $("#preview-bukti").html('<img loading="lazy" style="width:100%;" src="/storage/bukti_transfer/'+image+'"><a href="#" class="mt-2 btn btn-outline-warning btn-sm" onclick="$(\'#preview-bukti\').hide()">Tutup Bukti</a>');
    }

    function showBuktiPreview(image){
        $('#modalPreviewBukti').fadeIn();
        $("#dialog-preview-bukti").focus();
        $("#dialog-preview-bukti").show();
        $("#dialog-preview-bukti").html('<img loading="lazy" style="width:100%;" src="/storage/bukti_transfer/'+image+'">');
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $('#save').click(function(e) {
        e.preventDefault();
        var formData = new FormData();
        $("#info-update-sodaqoh").hide();
        var files = $('#bukti_transfer')[0].files;

        if($('#nominal_bayar').val() == "" || $('#nominal_bayar').val() <= 0){
            alert("Silahkan masukkan nominal pembayaran");
        }else if(files.length > 0 ){
            formData.append("id", $('#sodaqoh_id').val());
            formData.append("nominal", $('#nominal').val());
            formData.append("fkSantri_id", $('#santri_id').val());
            formData.append("keterangan", $('#keterangan').val());
            formData.append("date", $('#date').val());
            formData.append("nominal_bayar", $('#nominal_bayar').val());
            formData.append("bukti_transfer", files[0]);
            var role_santri = "<?php echo auth()->user()->hasRole('santri'); ?>";
            var routex = "{{ route('store sodaqoh') }}";
            if(role_santri){
                routex = "{{ route('store sodaqoh santri') }}";
            }
            $("#loadingSubmit").show();
            $.ajax({
              url: routex,
              type: "POST",
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                var return_data = JSON.parse(response);
                $("#info-update-sodaqoh").show();
                if (return_data.status) {
                    alert(return_data.message)
                    window.location.reload();
                }else{
                    $("#bg-warning").show();
                    $("#info-warning").html(return_data.message);
                }
              },
           });
        }else{
           alert("Please select a file.");
        }
    })

    function duplicateRab(){
        if (confirm("Apakah yakin menduplikasi RAB Tahunan ini ?")) {
            $("#loadingSubmit").show();
            $.post("{{ route('duplicate rab') }}", null,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    alert(return_data.message);
                    $("#loadingSubmit").show();
                    window.location.reload();
                }
            );
        }
    }

    function lockUnlockRab(lock,periode){
        $("#loadingSubmit").show();
        var datax = {};
        datax['periode_tahun'] = periode;
        datax['lock'] = parseInt(lock);
        $.post("{{ route('lock unlock rab') }}", datax,
            function(dataz, status) {
                var return_data = JSON.parse(dataz);
                alert(return_data.message);
                $("#loadingSubmit").show();
                window.location.reload();
            }
        );
    }

    function actionPayment(tipe,id,msg){
        if (confirm("Apakah yakin "+msg+" pembayaran ini ?")) {
            $("#loadingSubmit").show();
            var datax = {};
            datax['id'] = id;
            datax['tipe'] = tipe;
            $.post("{{ route('approve payment') }}", datax,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    $("#alertModalPayment").show();
                    $("#contentAlertPayment").html(return_data.message);
                    $("#loadingSubmit").hide();
                }
            );
        }
    }

    $('#close').click(function() {
        $('#modalSodaqoh').fadeOut();
        $("#preview-bukti").hide();
        $("#preview-bukti").html('');
        clear()
    });

    function clear() {
        $("#kekurangan").html('');
        $("#info-update-sodaqoh").hide();
        $("#bg-warning").hide();
        $("#bg-success").hide();
        $('#sodaqoh_id').val('');
        $('#periode').val('');
        $('#nominal').val('');
        $('#santri_id').val('');
        $('#ket').val('');
        $('#nm').text('');
        $('#nm-per').text('');
        var bulan = <?php echo json_encode($bulan); ?>;
        bulan.forEach(function(item, b) {
            $('#' + item).val('');
            $('#' + item + '_date').val('');
        })
    }
</script>
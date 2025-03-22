<?php 
$panitias = App\Models\PmbPanitias::where('angkatan',date('Y'))->get();
?>
<div class="modal" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left" id="modalCamaba" tabindex="-1" role="dialog" aria-labelledby="modalCamabaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
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
                <input type="hidden" id="maba_id" name="maba_id">
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
                var val = camaba[item];

                if(camaba[item]!="" && item=="foto_pas"){
                    val = '<img class="p-0" style="width: 256px" src="/storage/users/'+camaba[item]+'">';
                }

                data_maba = data_maba+'<tr style="border-bottom:1px solid rgb(220 220 220);"><th class="p-2" style="background-color:#f6f9fc;">'+new_item.toUpperCase()+'</th><td class="p-2">'+val+'</td></tr><hr>';
            }
        })
        $("#maba_id").val(camaba['id']);
        $("#modalCamaba").fadeIn();
        $("#data-camaba").html(data_maba);
    }
    function changeStatus(id,val){
        var datax = {};
        datax['id'] = id;
        datax['status'] = val;
        $.post("{{ route('change status maba') }}", datax,
            function(dataz, status) {
                var return_data = JSON.parse(dataz);
                if(return_data.status){
                    $("select#status-maba"+id).val(val);
                    $("select#status-nilai-maba"+id).val(val);

                    $("select#status-maba"+id).css('background','#fff');
                    $("select#status-nilai-maba"+id).css('background','#fff');

                    if(return_data.change_status=='tes'){
                        $("select#status-maba"+id).css('background','#b5bbfb');
                        $("select#status-nilai-maba"+id).css('background','#b5bbfb');
                    }else if(return_data.change_status=='diterima'){
                        $("select#status-maba"+id).css('background','#b5fbb6');
                        $("select#status-nilai-maba"+id).css('background','#b5fbb6');
                    }else if(return_data.change_status=='ditolak'){
                        $("select#status-maba"+id).css('background','#fbb5b5');
                        $("select#status-nilai-maba"+id).css('background','#fbb5b5');
                    }
                }
            }
        );
    }
    function changeMentor(mentor_id,maba_id,val){
        var datax = {};
        datax['id'] = maba_id;
        datax['mentor'] = mentor_id;
        datax['value'] = val;
        $.post("{{ route('change mentor maba') }}", datax,
            function(dataz, status) {
                var return_data = JSON.parse(dataz);
                if(return_data.status){
                    
                }
            }
        );
    }
    function changeNilai(tipe,id,val){
        var datax = {};
        datax['id'] = id;
        datax['tipe'] = tipe;
        datax['val'] = val;
        $("#spin"+id).show();
        $.post("{{ route('store nilai maba') }}", datax,
            function(dataz, status) {
                var return_data = JSON.parse(dataz);
                if(return_data.status){
                    $("#spin"+id).hide();
                }
            }
        );
    }
    function hideCamaba(){
        $("#modalCamaba").fadeOut();
        $("#data-camaba").html("");
    }
</script>
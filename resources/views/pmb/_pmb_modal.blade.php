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
        $("#modalCamaba").fadeIn();
        // $('#modalCamaba').css('background', 'rgba(0, 0, 0, 0.7)');
        // $('#modalCamaba').css('z-index', '10000');
        // $('#modalCamaba').css('display', 'inline-table');
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
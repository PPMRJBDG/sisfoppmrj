<footer class="footer pt-2 pb-2">
    <div class="container-fluid p-0">
        <div class="col-md-12">
            <div class="copyright text-center text-sm text-muted text-lg-start">
                <div class="card">
                    <div class="card-body text-primary text-center font-weight-bold">
                        Tim IT PPMRJ © {{ date('Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel">Report</h6>
                </div>
                <div>
                    <a style="cursor:pointer;" id="close"><i class="ni ni-fat-remove text-lg"></i></a>
                </div>
            </div>
            <div class="modal-body" id="contentReport" style="height:600px!important;">
                <tr>
                    <td colspan="3">
                        <span class="text-center">
                            Loading...
                        </span>
                    </td>
                </tr>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeb" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function openTab(evt, tahun) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tahun).style.display = "block";
        evt.currentTarget.className += " active";

        if (tahun == 'tabtable' || tahun == 'tabgrafik') {
            var angkatan = $('#select_angkatan').val();
            var tb = $('#select_tb').val();
            var periode = $('#select_periode').val();

            $.get(`{{ url("/") }}/tabgraf/` + tb + `/` + angkatan + `/` + periode,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    console.log(return_data);
                }
            );
        }
    }

    function getReport(ids) {
        $('#exampleModal').fadeIn();
        $('#exampleModal').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModal').css('z-index', '10000');
        $('#contentReport').html('<iframe src="{{ url("/") }}/report/' + ids + '"  style="height:100%;width:100%;"></iframe>');
    }
    $('#close').click(function() {
        $('#exampleModal').fadeOut();
        $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    });
    $('#closeb').click(function() {
        $('#exampleModal').fadeOut();
        $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    });
</script>
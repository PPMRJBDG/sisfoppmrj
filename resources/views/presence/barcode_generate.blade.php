<!DOCTYPE html>
<html lang="en">

<head>
    <title>Generate Barcode - Presence</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; user-scalable=no" />
    <link id="pagestyle" href="{{ asset('css/argon-dashboard.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.9.0.min.js" integrity="sha256-f6DVw/U4x2+HjgEqw5BZf67Kq/5vudRZuRkljnbF344=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js" type="text/javascript"></script>
</head>
<style>
    .drawingBuffer {
        position: absolute;
        top: 0;
        left: 0;
    }
</style>

<body class="bg-primary p-2 pt-3">
    <form id="form" action="{{ url('logout') }}" method="post">
        @csrf
        <a class="dropdown-item border-radius-md mb-2 text-center" style="cursor:pointer;" onclick="document.getElementById('form').submit()">
            Log out
        </a>
    </form>

    <a href="#" id="presence-name" class="btn btn-warning mb-2 w-100">

    </a>
    <div class="card border shadow-lg p-2 text-center">
        <div class="row">
            <div class="col-md-6 col-sm-12 p-0">
                <br>
                <span class="text-bold">GENERATE BARCODE</span>
                <br>
                <img id="barcode1" class="mb-2" style="width:80%;border:solid 1px #ddd;"></img>
                <br>
                <a href="#" class="btn btn-primary mb-2" onclick="refreshBarcode(1,6)">
                    REFRESH (<span id="seconds1">10</span>)
                </a>
            </div>
            <div class="col-md-6 col-sm-12 p-0">
                <br>
                <span class="text-bold">GENERATE BARCODE</span>
                <br>
                <img id="barcode2" class="mb-2" style="width:80%;border:solid 1px #ddd;"></img>
                <br>
                <a href="#" class="btn btn-primary mb-2" onclick="refreshBarcode(2,6)">
                    REFRESH (<span id="seconds2">10</span>)
                </a>
            </div>
        </div>
    </div>
    <input type="hidden" value="" id="hide_barcode1" disabled>
    <input type="hidden" value="" id="hide_barcode2" disabled>
</body>

<script>
    setInterval(function() {
        checkBarcode();
    }, 1000);

    setInterval(function() {
        checkBarcode()
    }, 10000);

    function checkBarcode() {
        // check barcode
        var datax = {};
        datax['barcode1'] = $("#hide_barcode1").val();
        datax['barcode2'] = $("#hide_barcode2").val();
        $.post(`{{ url("/") }}/presensi/barcode/check`, datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.presence) {
                    $("#presence-name").html(return_data.presence_name.toUpperCase());
                    if (return_data.barcode1) {
                        refreshBarcode(1, 6);
                    }
                    if (return_data.barcode2) {
                        refreshBarcode(2, 6);
                    }
                } else {
                    $("#presence-name").html('SEDANG TIDAK ADA KBM');
                    refreshBarcode(1, 0);
                    refreshBarcode(2, 0);
                }
            }
        )

        // timer code
        var seconds1 = parseInt($("#seconds1").html());
        seconds1 = seconds1 - 1;
        if (seconds1 == 0)
            seconds1 = '10'
        $("#seconds1").html(seconds1)

        var seconds2 = parseInt($("#seconds2").html());
        seconds2 = seconds2 - 1;
        if (seconds2 == 0)
            seconds2 = '10'
        $("#seconds2").html(seconds2)
    }

    function refreshBarcode(x, lt) {
        if (lt == 0) {
            $("#barcode1").hide();
            $("#barcode2").hide();
            return false;
        } else {
            $("#barcode1").show();
            $("#barcode2").show();
        }
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < lt) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        $("#hide_barcode" + x).val(result);
        JsBarcode("#barcode" + x, result);
    }
</script>

</html>
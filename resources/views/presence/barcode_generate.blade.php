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

<body class="bg-primary">
    <div class="col-12 p-2">
        <div class="card shadow-lg p-2 text-center">
            <div class="row">
                <div class="col-md-1 col-sm-12 p-0"></div>
                <div class="col-md-10 col-sm-12 p-0">
                    <a href="#" class="btn btn-primary mb-2" onclick="refreshBarcode(6)">
                        @if($presence!=null)
                        {{$presence->name}}
                        @else
                        Sedang Tidak Ada KBM
                        @endif
                    </a>
                    <br>
                    @if($presence!=null)
                    <span class="text-bold">GENERATE BARCODE</span>
                    <br>
                    <img id="barcode" class="mb-2" style="border:solid 1px #ddd;"></img>
                    <br>
                    <a href="#" class="btn btn-primary mb-2" onclick="refreshBarcode(6)">
                        REFRESH (<span id="seconds">10</span>)
                    </a>
                    @endif
                </div>
                <div class="col-md-1 col-sm-12 p-0"></div>
            </div>
        </div>
    </div>
    <input type="hidden" value="" id="hide_barcode" disabled>
</body>

<script>
    refreshBarcode(6);

    setInterval(function() {
        // check barcode
        var datax = {};
        datax['barcode'] = $("#hide_barcode").val();
        $.post(`{{ url("/") }}/presensi/barcode/check`, datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    refreshBarcode(6);
                }
            }
        )

        // timer code
        var seconds = parseInt($("#seconds").html());
        seconds = seconds - 1;
        if (seconds == 0)
            seconds = '10'
        $("#seconds").html(seconds)
    }, 1000);

    setInterval(function() {
        refreshBarcode(6);
    }, 10000);

    function refreshBarcode(lt) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < lt) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        $("#hide_barcode").val(result);
        JsBarcode("#barcode", result);
    }
</script>

</html>
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
            <a href="#" class="btn btn-primary mb-2" onclick="refreshBarcode(6)">REFRESH</a>
            <span class="text-bold">GENERATE BARCODE</span>
            <img id="barcode"></img>
        </div>
    </div>
</body>

<script>
    refreshBarcode(6);

    function refreshBarcode(lt) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < lt) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        JsBarcode("#barcode", result);
    }
</script>

</html>
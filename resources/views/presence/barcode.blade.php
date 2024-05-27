<!DOCTYPE html>
<html lang="en">

<head>
    <title>Barcode - Presence</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; user-scalable=no" />
    <link id="pagestyle" href="{{ asset('css/argon-dashboard.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.9.0.min.js" integrity="sha256-f6DVw/U4x2+HjgEqw5BZf67Kq/5vudRZuRkljnbF344=" crossorigin="anonymous"></script>
    <script src="https://webrtc.github.io/adapter/adapter-latest.js" type="text/javascript"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
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
            <span class="text-bold">{{ auth()->user()->fullname }}</span>
            <div id="interactive" class="viewport">
                <center>
                    <video autoplay="true" preload="auto"></video>
                </center>
                <div id="log-result"></div>
            </div>
            <a href="{{ url('/') }}" class="btn btn-primary mb-2" id="btn-act">Kembali</a>
        </div>
    </div>
    <script type="text/javascript">
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: screen.width,
                    height: 300,
                    facingMode: "environment",
                },
            },
            numOfWorkers: navigator.hardwareConcurrency,
            locate: true,
            frequency: 1,
            debug: {
                drawBoundingBox: true,
                showFrequency: true,
                drawScanline: true,
                showPattern: true
            },
            multiple: false,
            locator: {
                halfSample: false,
                patchSize: "large", // x-small, small, medium, large, x-large
                debug: {
                    showCanvas: false,
                    showPatches: false,
                    showFoundPatches: false,
                    showSkeleton: false,
                    showLabels: false,
                    showPatchLabels: false,
                    showRemainingPatchLabels: false,
                    boxFromPatches: {
                        showTransformed: false,
                        showTransformedBox: false,
                        showBB: false
                    }
                }
            },
            decoder: {
                readers: ["code_128_reader"]

            }
        }, function(err) {
            if (err) {
                $("#log-result").html("error: " + JSON.stringify(err));
            }

            Quagga.start();
        });

        Quagga.onProcessed(function(result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                $("#btn-act").html('Processing...');
                $("#log-result").html("processing: " + JSON.stringify(result));
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function(box) {
                        return box !== result.box;
                    }).forEach(function(box) {
                        Quagga.ImageDebug.drawPath(box, {
                            x: 0,
                            y: 1
                        }, drawingCtx, {
                            color: "green",
                            lineWidth: 2
                        });
                    });
                }

                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {
                        x: 0,
                        y: 1
                    }, drawingCtx, {
                        color: "#00F",
                        lineWidth: 2
                    });
                }

                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {
                        x: 'x',
                        y: 'y'
                    }, drawingCtx, {
                        color: 'red',
                        lineWidth: 3
                    });
                }
            }
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            $("#log-result").html("Detected: " + code);
            $("#btn-act").html('Kembali');
            Quagga.storePresent(code);
            Quagga.stop();
        });

        Quagga.storePresent(function(code) {
            var datax = {};
            datax['barcode'] = code;
            $.post(`{{ url("/") }}/presensi/barcode/store_present`, datax,
                function(data) {
                    alert('store');
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        alert(return_data.message);
                        window.location.replace(`{{ url("/") }}/home`)
                    } else {
                        $("#log-result").html("Error: " + JSON.stringify(return_data.message));
                        Quagga.start();
                    }
                }
            )
        })
    </script>
</body>

</html>
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

    <div class="modal" id="modalReason" tabindex="-1" role="dialog" aria-labelledby="modalReasonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h6 class="modal-title" id="modalReasonLabel">Alasan Pulang Sebelum Selesai KBM</h6>
                    </div>
                </div>
                <div class="modal-body">
                    <input class="form-control" type="hidden" id="code" name="code" value="">
                    <input class="form-control" type="text" id="reason" name="reason" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Batal</button>
                    <button type="button" id="save" class="btn btn-primary mb-0" data-dismiss="modal">Submit Alasan</button>
                </div>
            </div>
        </div>
</body>

<script type="text/javascript">
    $('#close').click(function() {
        $('#modalReason').fadeOut();
        $("#code").val('');
        $("#reason").val('');
        Quagga.start();
    });

    $('#save').click(function() {
        storePresent($("#code").val());
    });

    function openReason(code) {
        $('#modalReason').fadeIn();
        $('#modalReason').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalReason').css('z-index', '10000');
        $("#code").val(code);
    }

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
            $("#log-result").html("Message: " + JSON.stringify(err));
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
        storePresent(code);
        Quagga.stop();
    });

    async function storePresent(code) {
        var datax = {};
        datax['barcode'] = code;
        datax['reason'] = $("#reason").val();
        $.post(`{{ url("/") }}/presensi/barcode/store_present`, datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.sign == 'confirm_out') {
                    openReason(code);
                } else if (return_data.status) {
                    alert(return_data.message);
                    getPage(`{{ url("/") }}/home`)
                } else {
                    $("#log-result").html("Message: " + JSON.stringify(return_data.message));
                    Quagga.start();
                }
            }
        )
    }
</script>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Quagga Test</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; user-scalable=no" />
    <script src="https://code.jquery.com/jquery-1.9.0.min.js" integrity="sha256-f6DVw/U4x2+HjgEqw5BZf67Kq/5vudRZuRkljnbF344=" crossorigin="anonymous"></script>
    <script src="https://webrtc.github.io/adapter/adapter-latest.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
</head>

<body>
    <div id="interactive" class="viewport">
        <video autoplay="true" preload="auto"></video>
        <div id="log-result"></div>
    </div>
    <script type="text/javascript">
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 520,
                    height: 400,
                    facingMode: "environment" //"environment" for back camera, "user" front camera
                },
                area: { // defines rectangle of the detection/localization area
                    top: "0%", // top offset
                    right: "0%", // right offset
                    left: "0%", // left offset
                    bottom: "0%" // bottom offset
                },
                singleChannel: true
            },
            decoder: {
                readers: ["code_128_reader", "code_93_reader"]
            }
        }, function(err) {
            if (err) {
                console.log(err)
                $("#log-result").html("error: " + JSON.stringify(err));
                return
            }
            Quagga.start();
            Quagga.onDetected(function(result) {
                $("#log-result").html("detected: " + JSON.stringify(result));
                Quagga.stop();
            });
            Quagga.onProcessed(function(result) {
                $("#log-result").html("process: " + JSON.stringify(result));
            });
        });
    </script>
</body>

</html>
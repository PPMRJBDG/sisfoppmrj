<!-- START BARCODE -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
<script type="text/javascript" src="{{asset('quagga/dist/quagga.min.js')}}"></script>
<script type="text/javascript" src="{{asset('quagga/dist/quagga.js')}}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<style>
    #interactive.viewport {
        position: relative;
        width: 100%;
        height: auto;
        overflow: hidden;
        text-align: center;
    }

    #interactive.viewport>canvas,
    #interactive.viewport>video {
        max-width: 100%;
        width: 100%;
    }

    canvas.drawing,
    canvas.drawingBuffer {
        position: absolute;
        left: 0;
        top: 0;
    }
</style>
<script>
    // $(function() {
    //     // Create the QuaggaJS config object for the live stream
    //     var liveStreamConfig = {
    //         inputStream: {
    //             type: "LiveStream",
    //             constraints: {
    //                 width: {
    //                     min: 640
    //                 },
    //                 height: {
    //                     min: 480
    //                 },
    //                 aspectRatio: {
    //                     min: 1,
    //                     max: 100
    //                 },
    //                 facingMode: "environment" // or "user" for the front camera
    //             }
    //         },
    //         locator: {
    //             patchSize: "medium",
    //             halfSample: true
    //         },
    //         numOfWorkers: (navigator.hardwareConcurrency ? navigator.hardwareConcurrency : 4),
    //         decoder: {
    //             "readers": [{
    //                 "format": "code_128_reader",
    //                 "config": {}
    //             }]
    //         },
    //         locate: true
    //     };
    //     // The fallback to the file API requires a different inputStream option. 
    //     // The rest is the same 
    //     var fileConfig = $.extend({},
    //         liveStreamConfig, {
    //             inputStream: {
    //                 size: 800
    //             }
    //         }
    //     );
    //     // Start the live stream scanner when the modal opens
    //     $('#livestream_scanner').on('shown.bs.modal', function(e) {
    //         Quagga.init(
    //             liveStreamConfig,
    //             function(err) {
    //                 if (err) {
    //                     $('#livestream_scanner .modal-body .error').html('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i> ' + err.name + '</strong>: ' + err.message + '</div>');
    //                     Quagga.stop();
    //                     return;
    //                 }
    //                 Quagga.start();
    //             }
    //         );
    //     });

    //     // Make sure, QuaggaJS draws frames an lines around possible 
    //     // barcodes on the live stream
    //     Quagga.onProcessed(function(result) {
    //         var drawingCtx = Quagga.canvas.ctx.overlay,
    //             drawingCanvas = Quagga.canvas.dom.overlay;

    //         if (result) {
    //             if (result.boxes) {
    //                 drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
    //                 result.boxes.filter(function(box) {
    //                     return box !== result.box;
    //                 }).forEach(function(box) {
    //                     Quagga.ImageDebug.drawPath(box, {
    //                         x: 0,
    //                         y: 1
    //                     }, drawingCtx, {
    //                         color: "green",
    //                         lineWidth: 2
    //                     });
    //                 });
    //             }

    //             if (result.box) {
    //                 Quagga.ImageDebug.drawPath(result.box, {
    //                     x: 0,
    //                     y: 1
    //                 }, drawingCtx, {
    //                     color: "#00F",
    //                     lineWidth: 2
    //                 });
    //             }

    //             if (result.codeResult && result.codeResult.code) {
    //                 Quagga.ImageDebug.drawPath(result.line, {
    //                     x: 'x',
    //                     y: 'y'
    //                 }, drawingCtx, {
    //                     color: 'red',
    //                     lineWidth: 3
    //                 });
    //             }
    //         }
    //     });

    //     // Once a barcode had been read successfully, stop quagga and 
    //     // close the modal after a second to let the user notice where 
    //     // the barcode had actually been found.
    //     Quagga.onDetected(function(result) {
    //         if (result.codeResult.code) {
    //             $('#scanner_input').val(result.codeResult.code);
    //             Quagga.stop();
    //             setTimeout(function() {
    //                 $('#livestream_scanner').modal('hide');
    //             }, 1000);
    //         }
    //     });

    //     // Stop quagga in any case, when the modal is closed
    //     $('#livestream_scanner').on('hide.bs.modal', function() {
    //         if (Quagga) {
    //             Quagga.stop();
    //         }
    //     });

    //     // Call Quagga.decodeSingle() for every file selected in the 
    //     // file input
    //     $("#livestream_scanner input:file").on("change", function(e) {
    //         if (e.target.files && e.target.files.length) {
    //             Quagga.decodeSingle($.extend({}, fileConfig, {
    //                 src: URL.createObjectURL(e.target.files[0])
    //             }), function(result) {
    //                 alert(result.codeResult.code);
    //             });
    //         }
    //     });
    // });

    $(function() {
        var App = {
            init: function() {
                Quagga.init(this.state, function(err) {
                    if (err) {
                        console.log(err);
                        return;
                    }
                    App.attachListeners();
                    App.checkCapabilities();
                    Quagga.start();
                });
            },
            checkCapabilities: function() {
                var track = Quagga.CameraAccess.getActiveTrack();
                var capabilities = {};
                if (typeof track.getCapabilities === 'function') {
                    capabilities = track.getCapabilities();
                }
                this.applySettingsVisibility('zoom', capabilities.zoom);
                this.applySettingsVisibility('torch', capabilities.torch);
            },
            updateOptionsForMediaRange: function(node, range) {
                console.log('updateOptionsForMediaRange', node, range);
                var NUM_STEPS = 6;
                var stepSize = (range.max - range.min) / NUM_STEPS;
                var option;
                var value;
                while (node.firstChild) {
                    node.removeChild(node.firstChild);
                }
                for (var i = 0; i <= NUM_STEPS; i++) {
                    value = range.min + (stepSize * i);
                    option = document.createElement('option');
                    option.value = value;
                    option.innerHTML = value;
                    node.appendChild(option);
                }
            },
            applySettingsVisibility: function(setting, capability) {
                if (typeof capability === 'boolean') {
                    var node = document.querySelector('input[name="settings_' + setting + '"]');
                    if (node) {
                        node.parentNode.style.display = capability ? 'block' : 'none';
                    }
                    return;
                }
                if (window.MediaSettingsRange && capability instanceof window.MediaSettingsRange) {
                    var node = document.querySelector('select[name="settings_' + setting + '"]');
                    if (node) {
                        this.updateOptionsForMediaRange(node, capability);
                        node.parentNode.style.display = 'block';
                    }
                    return;
                }
            },
            initCameraSelection: function() {
                var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

                return Quagga.CameraAccess.enumerateVideoDevices()
                    .then(function(devices) {
                        function pruneText(text) {
                            return text.length > 30 ? text.substr(0, 30) : text;
                        }
                        var $deviceSelection = document.getElementById("deviceSelection");
                        while ($deviceSelection.firstChild) {
                            $deviceSelection.removeChild($deviceSelection.firstChild);
                        }
                        devices.forEach(function(device) {
                            var $option = document.createElement("option");
                            $option.value = device.deviceId || device.id;
                            $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
                            $option.selected = streamLabel === device.label;
                            $deviceSelection.appendChild($option);
                        });
                    });
            },
            attachListeners: function() {
                var self = this;

                self.initCameraSelection();
                $(".controls").on("click", "button.stop", function(e) {
                    e.preventDefault();
                    Quagga.stop();
                });

                $(".controls .reader-config-group").on("change", "input, select", function(e) {
                    e.preventDefault();
                    var $target = $(e.target),
                        value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
                        name = $target.attr("name"),
                        state = self._convertNameToState(name);

                    console.log("Value of " + state + " changed to " + value);
                    self.setState(state, value);
                });
            },
            _accessByPath: function(obj, path, val) {
                var parts = path.split('.'),
                    depth = parts.length,
                    setter = (typeof val !== "undefined") ? true : false;

                return parts.reduce(function(o, key, i) {
                    if (setter && (i + 1) === depth) {
                        if (typeof o[key] === "object" && typeof val === "object") {
                            Object.assign(o[key], val);
                        } else {
                            o[key] = val;
                        }
                    }
                    return key in o ? o[key] : {};
                }, obj);
            },
            _convertNameToState: function(name) {
                return name.replace("_", ".").split("-").reduce(function(result, value) {
                    return result + value.charAt(0).toUpperCase() + value.substring(1);
                });
            },
            detachListeners: function() {
                $(".controls").off("click", "button.stop");
                $(".controls .reader-config-group").off("change", "input, select");
            },
            applySetting: function(setting, value) {
                var track = Quagga.CameraAccess.getActiveTrack();
                if (track && typeof track.getCapabilities === 'function') {
                    switch (setting) {
                        case 'zoom':
                            return track.applyConstraints({
                                advanced: [{
                                    zoom: parseFloat(value)
                                }]
                            });
                        case 'torch':
                            return track.applyConstraints({
                                advanced: [{
                                    torch: !!value
                                }]
                            });
                    }
                }
            },
            setState: function(path, value) {
                var self = this;

                if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                    value = self._accessByPath(self.inputMapper, path)(value);
                }

                if (path.startsWith('settings.')) {
                    var setting = path.substring(9);
                    return self.applySetting(setting, value);
                }
                self._accessByPath(self.state, path, value);

                console.log(JSON.stringify(self.state));
                App.detachListeners();
                Quagga.stop();
                App.init();
            },
            inputMapper: {
                inputStream: {
                    constraints: function(value) {
                        if (/^(\d+)x(\d+)$/.test(value)) {
                            var values = value.split('x');
                            return {
                                width: {
                                    min: parseInt(values[0])
                                },
                                height: {
                                    min: parseInt(values[1])
                                }
                            };
                        }
                        return {
                            deviceId: value
                        };
                    }
                },
                numOfWorkers: function(value) {
                    return parseInt(value);
                },
                decoder: {
                    readers: function(value) {
                        if (value === 'ean_extended') {
                            return [{
                                format: "ean_reader",
                                config: {
                                    supplements: [
                                        'ean_5_reader', 'ean_2_reader'
                                    ]
                                }
                            }];
                        }
                        return [{
                            format: value + "_reader",
                            config: {}
                        }];
                    }
                }
            },
            state: {
                inputStream: {
                    type: "LiveStream",
                    constraints: {
                        width: {
                            min: 640
                        },
                        height: {
                            min: 480
                        },
                        aspectRatio: {
                            min: 1,
                            max: 100
                        },
                        facingMode: "environment" // or user
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 2,
                frequency: 10,
                decoder: {
                    readers: [{
                        format: "code_128_reader",
                        config: {}
                    }]
                },
                locate: true
            },
            lastResult: null
        };

        App.init();

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            Quagga.stop();
            window.location.href = "scannerview.php?barcode=" + code;
        });
    });
</script>
<!-- END BARCODE -->

<button id="opener">Barcode scanner</button>
<div id="modal" title="Barcode scanner">
    <span class="found"></span>
    <div id="interactive" class="viewport"></div>
</div>

<!-- <input id="scanner_input" class="form-control" style="width:20%;" placeholder="Barcode" type="text" />
<button class="btn btn-default" type="button" data-toggle="modal" data-target="#livestream_scanner"><i class="fas fa-barcode"></i> Scan</button> -->

<!-- <div class="modal" id="livestream_scanner">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Barcode Scanner</h4>
            </div>

            <div class="modal-footer">
                <label class="btn btn-default pull-left">
                    <i class="fa fa-camera"></i> Use camera app
                    <input type="file" accept="image/*;capture=camera" capture="camera" class="hidden" />
                </label>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->
<!DOCTYPE html>
<html lang="en">
<?php
$setting = App\Models\Settings::first();
?>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}">
  <title>
    {{ $title }} - {{$setting->apps_name}}
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('css/argon-dashboard.css') }}" rel="stylesheet" />
  <!-- <link id="pagestyle" href="{{ asset('css/argon-dashboard.min.css') }}" rel="stylesheet" /> -->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->

  <!-- START BARCODE -->
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
  <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
  <!-- <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet"> -->
  <script type="text/javascript" src="{{asset('quagga/dist/quagga.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('quagga/dist/quagga.js')}}"></script>

  <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
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
  <script type="module">
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

</head>
<style>
  @media (max-width: 576px) {

    .pl-4,
    .pr-4 {
      padding-left: 8px;
      padding-right: 8px;
    }
  }

  @media only screen and (max-width: 600px) {

    body,
    h6 {
      font-size: 0.8rem !important;
    }
  }

  .py-4 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* Style the tab */
  .tab {
    overflow: hidden;
    background-color: #f6f9fc;
    border-radius: 8px 8px 0 0;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px !important;
    transition: 0.3s;
    font-size: 17px;
  }

  .tablinks {
    font-weight: bold !important;
    font-size: 14px !important;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #e9eaeb;
  }

  /* Create an active/current tablink class */
  .tab button.active {
    background-color: #e9eaeb;
    border-bottom: solid 4px #c93779;
  }

  /* Style the tab content */
  .tabcontent {
    display: none;
    /* padding: 6px 12px; */
    border-top: none;
    background-color: #fff;
    border-radius: 0 0 8px 8px;
  }
</style>

<body class="g-sidenav-show bg-gray-100">

  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ url('storage/logo-apps/' . $setting->bgImage) }}'); background-size: cover;">
    <span class="mask bg-primary opacity-9"></span>
  </div>
  @include('base.sidebar', ['path' => $path, 'title' => $title, 'setting' => $setting])
  <main class="main-content position-relative border-radius-lg ps">
    @include('base.navbar', ['breadcrumbs' => $breadcrumbs])
    <div class="container-fluid pl-4 pr-4 pt-2 pb-2">
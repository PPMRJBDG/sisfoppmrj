<?php
$setting = App\Models\Settings::first();

if (auth()->user()->hasRole('barcode')) {
  header('Location: presensi/generate-barcode');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-mdb-theme="{{auth()->user()->themes}}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  @if($setting->logoImgUrl!='')
  <link rel="icon" type="image/png" href="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}">
  @endif
  <title>
    {{$setting->apps_name}}
  </title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('font-awesome/css/font-awesome.min.css') }}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-v2.min.css') }}" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<style>
  body, .table, .form-control, .select-option {
    font-size: .8rem !important;
  }
  .select-arrow {
    font-size: .5rem !important;
  }
  .btn {
    font-size: .7rem !important;
  }

  .form-group {
    margin-bottom: 15px !important;
  }

  .navbar {
    min-height: 60px !important;
  }

  /* .btn-primary, */
  .bg-primary,
  .badge-primary {
    background:  #48c6ef !important;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important;
    background: linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important
  }

  /* .btn-danger, */
  .badge-danger,
  .alert-danger,
  .bg-danger {
    background: #f093fb !important;
    background: -webkit-linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1)) !important;
    background: linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1)) !important
  }

  /* .btn-warning, */
  .badge-warning,
  .alert-warning,
  .partial,
  .bg-warning {
    background: #f6d365 !important;
    background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1)) !important;
    background: linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1)) !important
  }

  /* .btn-success, */
  .complete,
  .badge-success,
  .alert-success,
  .bg-success {
    background-color: #00c851 !important;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important;
    background: linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important
  }

  /* .btn-secondary, */
  .bg-secondary,
  .badge-secondary {
    background-color: #a6c !important;
    background: -webkit-linear-gradient(to right, rgb(218 186 233 / 100%), rgb(134 73 149 / 100%)) !important;
    background: linear-gradient(to right, rgb(218 186 233 / 100%), rgb(134 73 149 / 100%)) !important
  }

  .nav-tabs .nav-link {
    font-weight: 600 !important;
  }

  .btn {
    border-radius: 1.875rem !important;
    font-weight: 700 !important;
  }

  .font-weight-bolder {
    font-weight: 700 !important
  }

  .dropdown-menu {
    --mdb-dropdown-min-width: 12rem !important;
  }

  .dataTables_wrapper .dataTables_filter input {
    margin-bottom: 10px !important;
  }
  .modal{
      background: rgba(0, 0, 0, 0.7);
      z-index: 10000;
      width: 100% !important;
      height: 100% !important;
      overflow-x: auto; 
  }
</style>

<body data-mdb-spy="scroll" data-mdb-target="#scrollspy" data-mdb-offset="0" onload="getPage(getCookie('current_url'))">
  <input type="hidden" value="{{ url('/') }}" id="base-url">
  <input type="hidden" value="#" id="current-url">
  <main class="pt-5 mdb-docs-layout">

    <!-- HEADER -->
    @include('base.navbar', ['setting', $setting])

    <!-- CONTAINER / CONTENT -->
    <div class="container-fluid pt-4" id="content-app"></div>

    <div id="loading" class="pt-4 justify-content-between align-items-center" style="height: 300px; width: 100%;">
      <div class="text-center">
        <center>
          <div class="spinner-grow text-primary" role="status"></div>
          <div class="spinner-grow text-warning" role="status"></div>
          <div class="spinner-grow text-danger" role="status"></div>
        </center>
      </div>
    </div>

    <div id="al-danger" class="bg-danger p-2 m-2 text-center text-white" style="display:none;border-radius:10px;"></div>

    <footer class="footer text-center p-2">
      <span class="text-xs pb-2"><small style="font-size:10px;">Tim IT {{$setting->apps_name}} © {{ date('Y') }}</small></span>
    </footer>

    @include('pmb._pmb_modal')
    @include('keuangan._keuangan_modal')
    @include('catatanPenghubung._penghubung_modal')

    <div class="modal" id="cacahJiwaModal" tabindex="-1" role="dialog" aria-labelledby="cacahJiwaModalLabel" aria-hidden="true" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left">
      <div class="modal-dialog" role="document" style="max-width:600px !important;">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <h6 class="modal-title font-weight-bolder" id="cacahJiwaModalLabel">Cacah Jiwa</h6>
            </div>
            <div>
              <a style="cursor:pointer;" id="close"><i class="ni ni-fat-remove text-lg"></i></a>
            </div>
          </div>
          <div class="modal-body p-0" style="height:auto!important;">
            <?php echo $count_dashboard; ?>
          </div>
          <div class="modal-footer">
            <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal" onclick="$('#cacahJiwaModal').fadeOut();">Keluar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width:600px !important;">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <h6 class="modal-title font-weight-bolder" id="exampleModalLabel">Report</h6>
            </div>
            <div>
              <a block-id="return-false" href="#" style="cursor:pointer;" id="close"><i class="fa fa-times text-lg"></i></a>
            </div>
          </div>
          <div class="modal-body p-1" id="contentReport" style="height:500px!important;">
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

    <div class="modal" id="exampleModalMateri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelMateri" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width:650px !important;">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <h6 class="modal-title" id="exampleModalLabelMateri">Pencapaian Materi</h6>
              <h5 class="modal-title" id="exampleModalLabelMateri"><span id="nm"></span></h5>
            </div>
          </div>
          <div class="modal-body" style="height:500px;overflow:auto;">
            <div class="datatablex datatable-sm">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">MATERI</th>
                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">PENCAPAIAN</th>
                    <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-0">ACTION</th>
                  </tr>
                </thead>
                <tbody id="contentMateri">
                  <tr>
                    <td colspan="3">
                      <span class="text-center">
                        Loading...
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="closeMateri" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true" style="background:rgba(0, 0, 0, 0.7);z-index:999;" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <h6 class="modal-title font-weight-bolder badge badge-warning" id="alertModalLabel">Pemberitahuan</h6>
            </div>
            <div>
              <a style="cursor:pointer;" id="close"><i class="ni ni-fat-remove text-lg"></i></a>
            </div>
          </div>
          <div class="modal-body" id="contentAlert">

          </div>
          <div class="modal-footer">
            <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal" onclick="$('#alertModal').fadeOut();$('#contentAlert').html('');">Keluar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="loadingSubmit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background:rgba(0,0,0,0.8);">
      <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
        <center>
          <div class="spinner-grow text-primary" role="status"></div>
          <div class="spinner-grow text-warning" role="status"></div>
          <div class="spinner-grow text-danger" role="status"></div>
        </center>
      </div>
    </div>
  </main>

  <!-- Custom JS -->
  <script type="text/javascript" src="{{ asset('js/app-custom.js') }}"></script>
  <script>
    // CLEAR COOKIES
    // document.cookie.split(';').forEach(cookie => {
    //   const eqPos = cookie.indexOf('=');
    //   const name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
    //   document.cookie = name + '=;';
    // });

    $('body').on('click', '.sidenav-backdrop', function(e) {
      var sidenav = document.querySelectorAll(".sidenav-backdrop");
      if (sidenav != null) {
        for (var i = 0; i < sidenav.length; i++) {
          sidenav[i].remove();
        }
      }
    })

    $('body').on('click', 'a', function(e) {
      var list_tab = ['return-false', 'nav-mahasiswa-tab', 'nav-table-tab', 'nav-grafik-tab', 'nav-harian-tab', 'nav-berjangka-tab', 'nav-hadir-tab', 'nav-ijin-tab', 'nav-alpha-tab'];
      if (!matchArray(list_tab, e.target.id) && !matchArray(list_tab, e.target.getAttribute('block-id'))) {
        e.preventDefault();
        if (this.href != $("#base-url").val() + '/#' && !this.href.match("#")) {
          $("#sidenav-1").css('transform', 'translateX(-100%)');
          var sidenav = document.querySelectorAll(".sidenav-backdrop");
          if (sidenav != null) {
            for (var i = 0; i < sidenav.length; i++) {
              sidenav[i].remove();
            }
          }

          getPage(this.href);
          // $("#breadcrumb-item").html(e.target.innerText)
          return false;
        }
      }
    })
  </script>
  <!-- New Material Design -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>
  <script>
    $(document).ready(() => {
      new WOW().init();
    });
  </script>

</body>

</html>
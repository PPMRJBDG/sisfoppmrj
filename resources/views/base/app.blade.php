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

  <!-- New Material Design -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/jquery.min.js') }}"></script> -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

  <link rel="stylesheet" href="{{ asset('css/argon-dashboard.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-free.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb.min.css') }}" />
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-v2.min.css') }}" /> -->

  <!-- addons -->
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/datatables.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/datatables-select.min.css') }}" /> -->
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/directives.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/flag.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/jquery.zmd.hierarchical-display.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/rating.min.css') }}" /> -->
  <!-- addons pro-->
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/cards-extended.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/chat.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/multi-range.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/simple-charts.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/steppers.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons-pro/timeline.min.css') }}" /> -->
  <!-- modules -->
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/accordion-extended.min.css') }}" /> -->
  <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/animations-extended.min.css') }}" />
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/charts.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/lightbox.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/megamenu.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/parallax.min.css') }}" /> -->
</head>

<style>
  INPUT:-webkit-autofill,
  SELECT:-webkit-autofill,
  TEXTAREA:-webkit-autofill {
    animation-name: onautofillstart
  }

  INPUT:not(:-webkit-autofill),
  SELECT:not(:-webkit-autofill),
  TEXTAREA:not(:-webkit-autofill) {
    animation-name: onautofillcancel
  }

  @keyframes onautofillstart {
    from {}
  }

  @keyframes onautofillcancel {
    from {}
  }

  .form-control {
    margin-bottom: 15px !important;
  }

  .navbar {
    min-height: 60px !important;
  }

  .btn-primary,
  .badge-primary,
  .bg-primary {
    background: #48c6ef;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5));
    background: linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5))
  }

  .btn-danger,
  .badge-danger,
  .bg-danger {
    background: #f093fb;
    background: -webkit-linear-gradient(to right, rgba(240, 147, 251, 0.5), rgba(245, 87, 108, 0.5));
    background: linear-gradient(to right, rgba(240, 147, 251, 0.5), rgba(245, 87, 108, 0.5))
  }

  .btn-warning,
  .badge-warning,
  .partial,
  .bg-warning {
    background: #f6d365;
    background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 0.5), rgba(253, 160, 133, 0.5));
    background: linear-gradient(to right, rgba(246, 211, 101, 0.5), rgba(253, 160, 133, 0.5))
  }

  .btn-success,
  .complete,
  .bg-success {
    background-color: #00c851;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5));
    background: linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5))
  }

  .btn-secondary,
  .badge-secondary {
    background-color: #a6c;
    background: -webkit-linear-gradient(to right, rgb(218 186 233 / 54%), rgb(134 73 149 / 65%));
    background: linear-gradient(to right, rgb(218 186 233 / 54%), rgb(134 73 149 / 65%))
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
</style>

<body data-mdb-spy="scroll" data-mdb-target="#scrollspy" data-mdb-offset="0">
  <input type="hidden" value="{{ url('/') }}" id="base-url">
  <input type="hidden" value="#" id="current-url">
  <main class="pt-5 mdb-docs-layout">

    <!-- HEADER -->
    <header>
      @include('base.navbar', ['setting', $setting])
    </header>

    <!-- BREADCRUM -->
    <div class="d-flex p-2 mt-2">
      <div class="flex-fill">
        <button type="button" id="breadcrumb-item" class="btn btn-primary btn-outline btn-sm float-start m-0">Home</button>
      </div>
      <div class="flex-fill">
        <button type="button" class="btn btn-primary btn-outline btn-sm float-end m-0" onclick="refreshCurrentUrl()">Refresh</button>
      </div>
    </div>

    <!-- CONTAINER / CONTENT -->
    <div class="container-fluid pt-0" id="content-app">

    </div>

    <div id="loading" class="pt-4 justify-content-between align-items-center" style="height: 300px; width: 100%;">
      <div class="text-center">
        <center>
          <div class="spinner-grow text-primary" role="status"></div>
          <div class="spinner-grow text-warning" role="status"></div>
          <div class="spinner-grow text-danger" role="status"></div>
        </center>
      </div>
    </div>

    <div id="al-danger" class="bg-danger p-4 m-4 text-center text-{{auth()->user()->themes}}" style="display:none;border-radius:10px;"></div>

    <footer class="footer text-center p-2">
      <span class="text-xs pb-2"><small style="font-size:10px;">Tim IT {{$setting->apps_name}} © {{ date('Y') }}</small></span>
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

    <div class="modal" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true" style="background:rgba(0, 0, 0, 0.7);z-index:999;">
      <div class="modal-dialog modal-dialog-centered" role="document">
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
    // $(document.body).on('click', 'a', function(e) {
    $('body').on('click', 'a', function(e) {
      var list_tab = ['return-false', 'nav-mahasiswa-tab', 'nav-table-tab', 'nav-grafik-tab', 'nav-harian-tab', 'nav-berjangka-tab', 'nav-hadir-tab', 'nav-ijin-tab', 'nav-alpha-tab'];
      if (!matchArray(list_tab, e.target.id)) {
        e.preventDefault();
        if (this.href != $("#base-url").val() + '/#') {
          getPage(this.href);
          $("#breadcrumb-item").html(e.target.innerText)

          var NavShow = document.querySelector(".dropdown-menu.show");
          if (NavShow != null)
            NavShow.classList.remove('show');

          var collapsShow = document.querySelector(".navbar-collapse.show");
          if (collapsShow != null)
            collapsShow.classList.remove('show');

          return false;
        }
      }
    })
  </script>
  <!-- New Material Design --><!--Bootstrap Js-->
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/popper.min.js') }}"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb.umd.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb.min.js') }}"></script>
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script> -->
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/modules/forms-free.min.js') }}"></script> -->

  <!-- addons -->
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/addons/directives.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/flag.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/imagesloaded.pkgd.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/jquery.zmd.hierarchical-display.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/masonry.pkgd.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/rating.min.js') }}"></script> -->
  <!-- addons-pro -->
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/cards-extended.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/chat.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/multi-range.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/simple-charts.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/steppers.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons-pro/timeline.min.js') }}"></script> -->
  <!-- modules -->
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/modules/dropdown/dropdown.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/dropdown/dropdown-searchable.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/material-select/material-select.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/material-select/material-select-view.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/material-select/material-select-view-renderer.min.js') }}"></script> -->

  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/modules/accordion-extended.min.js') }}"></script> -->
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/animations-extended.min.js') }}"></script>
  <!-- <script type="text/javascript" src="{{ asset('ui-kit/js/modules/buttons.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/cards.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/character-counter.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/chips.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/collapsible.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/file-input.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/lightbox.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/mdb-autocomplete.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/megamenu.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/parallax.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/preloading.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/range-input.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/scrolling-navbar.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/sidenav.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/smooth-scroll.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/sticky.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/treeview.min.js') }}"></script> -->
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>
  <script>
    $(document).ready(() => {
      new WOW().init();
    });
  </script>

</body>

</html>
<?php
$setting = App\Models\Settings::first();
?>
<!DOCTYPE html>
<html lang="en" data-mdb-theme="{{isset(auth()->user()->themes)}}">

<head>
  <meta charset="utf-8" />
  <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimum-scale=1, maximum-scale=1" />

  @if($setting->logoImgUrl!='')
  <link rel="icon" type="image/png" href="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}">
  @endif
  <title class="text-uppercase">
    {{($title) ? strtoupper($title) : $setting->apps_name}}
  </title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('font-awesome/css/font-awesome.min.css') }}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-v2.min.css') }}" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<style>
  body, .table, .form-control, .select-option {
    font-size: .8rem !important;
  }

  .datatable tbody {
    font-weight: 400;
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
    background: #48c6ef !important;
    /* background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important;
    background: linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important */
  }

  /* .btn-danger, */
  .badge-danger,
  .alert-danger,
  .bg-danger {
    background: #f093fb !important;
    /* background: -webkit-linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1)) !important;
    background: linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1)) !important */
  }

  /* .btn-warning, */
  .badge-warning,
  .alert-warning,
  .partial,
  .bg-warning {
    background: #f6d365 !important;
    /* background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1)) !important;
    background: linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1)) !important */
  }

  /* .btn-success, */
  .complete,
  .badge-success,
  .alert-success,
  .bg-success {
    background-color: #00c851 !important;
    /* background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important;
    background: linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1)) !important */
  }

  /* .btn-secondary, */
  .bg-secondary,
  .badge-secondary {
    background-color: #a6c !important;
    /* background: -webkit-linear-gradient(to right, rgb(218 186 233 / 100%), rgb(134 73 149 / 100%)) !important;
    background: linear-gradient(to right, rgb(218 186 233 / 100%), rgb(134 73 149 / 100%)) !important */
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

<?php 
$periode = App\Helpers\CommonHelpers::periode();
$periode = explode("-",$periode);
$month = ['09','10','11','12','01','02','03','04','05','06','07','08'];
$currentMonth = date("m");
$year = [$periode[0],$periode[0],$periode[0],$periode[0],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1]];
?>
<footer id="footer-calendar" style="display:none;">
  <nav class="navbar fixed-bottom fixed bg-white align-items-center justify-content-center" style="border-top:solid 2px #d6d6d6">
    <div class="text-center">
      <?php
      for($i=0; $i<12; $i++){
      ?>
        <button onclick="document.getElementById('month-{{$i}}').scrollIntoView()" class="btn btn-floating btn-sm btn-{{($currentMonth==$month[$i]) ? 'danger' : 'primary'}}">{{$month[$i]}}</button>
      <?php
      }
      ?>
    </div>
  </nav>
</footer>

<body class="g-sidenav-show bg-white">
  <div class="min-height-300 position-absolute w-100" style="min-height:100% !important;"></div>
  <main class="main-content position-relative border-radius-lg ">
    <div class="container-fluid py-2 {{ isset($containerClass) ? $containerClass : '' }}">
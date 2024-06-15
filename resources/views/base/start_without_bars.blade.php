<?php
$setting = App\Models\Settings::first();
?>
<!DOCTYPE html>
<html lang="en" data-mdb-theme="{{auth()->user()->themes}}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
  <title>
    {{ $title }} - {{$setting->apps_name}}
  </title><!-- New Material Design -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
  <script type="text/javascript" src="{{ asset('ui-kit/js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/datatables.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/addons/datatables-select.min.js') }}"></script>

  <link rel="stylesheet" href="{{ asset('css/argon-dashboard.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-free.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb.min.css') }}" />
  <!-- <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-v2.min.css') }}" /> -->

  <!-- addons -->
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/datatables.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/addons/datatables-select.min.css') }}" />
</head>

<Style>
  .bg-primary {
    background: #48c6ef;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5));
    background: linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5))
  }

  .font-weight-bolder {
    font-weight: 700 !important
  }
</style>

<body class="g-sidenav-show bg-primary">
  <div class="min-height-300 position-absolute w-100" style="min-height:100% !important;"></div>
  <main class="main-content position-relative border-radius-lg ">
    <div class="container-fluid py-4 {{ isset($containerClass) ? $containerClass : '' }}">
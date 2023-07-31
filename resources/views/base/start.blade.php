<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
  <title>
    {{ $title }} - Sisfo PPMRJ 2022
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
  <link id="pagestyle" href="{{ asset('css/argon-dashboard.min.css') }}" rel="stylesheet" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->
</head>
<style>
  @media (max-width: 576px) {

    .pl-4,
    .pr-4 {
      padding-left: 8px;
      padding-right: 8px;
    }
  }
</style>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('img/foto-ppm.jpg') }}'); background-size: cover;">
    <span class="mask bg-primary opacity-9"></span>
  </div>
  @include('base.sidebar', ['path' => $path, 'title' => $title])
  <main class="main-content position-relative border-radius-lg ">
    @include('base.navbar', ['breadcrumbs' => $breadcrumbs])
    <div class="container-fluid pl-4 pr-4 pt-2 pb-2">
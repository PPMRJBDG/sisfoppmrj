<?php
$setting = App\Models\Settings::first();
?>
<!DOCTYPE html>
<html lang="en" data-mdb-theme="light">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
  <title>
    {{$setting->apps_name}} - LOGIN
  </title>
  <!-- New Material Design -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/argon-dashboard.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-v2.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('font-awesome/css/font-awesome.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/modules/animations-extended.min.css') }}" />
</head>

<style>
  html,
  body,
  header,
  .view {
    height: 100vh;
  }

  @media (max-width: 740px) {

    html,
    body,
    header,
    .view {
      height: 815px;
    }
  }

  @media (min-width: 800px) and (max-width: 850px) {

    html,
    body,
    header,
    .view {
      height: 650px;
    }
  }

  .waves-input-wrapper {
    width: 100%;
  }

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

  /* .btn-primary, */
  .text-primary,
  .bg-primary {
    background: #48c6ef;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1));
    background: linear-gradient(to right, rgba(72, 198, 239, 1), rgba(111, 134, 214, 1))
  }

  /* .btn-danger, */
  .text-danger,
  .bg-danger {
    background: #f093fb;
    background: -webkit-linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1));
    background: linear-gradient(to right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1))
  }

  /* .btn-warning, */
  .text-warning,
  .bg-warning {
    background: #f6d365;
    background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));
    background: linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1))
  }

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

  .btn {
    border-radius: 1.875rem;
    font-weight: 700;
  }

  .font-weight-bolder {
    font-weight: 700 !important
  }
</style>

<body>
  <section class="h-100 gradient-form">
    <div class="mask h-100 d-flex justify-content-center align-items-center">
      <div class="container">
        <section>
          <div class="bg-image bg-fixed text-center" style="
            background-image: url('img/bg.jpg');
            height: 460px;
          ">
            <div class="d-flex align-items-center h-100 mask" style="
              background: linear-gradient(
                45deg,
                rgba(255, 88, 88, 0.7),
                rgba(238, 66, 146, 0.8) 100%
              );
            ">
              <div class="container text-white">
                <p class="h1 mb-5 text-white">Some facts about us</p>
                <div class="row">
                  <div class="col-md-3">
                    <h2 class="h1 text-white">+350</h2>
                    <p class="mb-md-0">Alumni PPM</p>
                  </div>
                  <div class="col-md-3">
                    <h2 class="h1 text-white">+56</h2>
                    <p class="mb-md-0">Muballigh Lulusan PPM</p>
                  </div>
                  <div class="col-md-3">
                    <h2 class="h1 text-white">{{ count(Illuminate\Support\Facades\DB::table('v_user_santri')->where('gender','male')->get()) }}</h2>
                    <p class="mb-md-0">Mahasiswa</p>
                  </div>
                  <div class="col-md-3">
                    <h2 class="h1 text-white">{{ count(Illuminate\Support\Facades\DB::table('v_user_santri')->where('gender','female')->get()) }}</h2>
                    <p class="mb-0">Mahasiswi</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <div class="card border wow fadeIn shadow-lg" data-wow-delay="0.3s">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4"></div>
              <div class="col-md-4">
                <div class="p-3 ps-0">
                  <h5 class="mb-0">Welcome back! <b>User</b></h5>
                </div>
                <form role="form" action="{{ route('login') }}" method="POST">
                  @csrf
                  <div data-mdb-input-init class="mb-4 form-outline">
                    <input autocomplete="off" type="email" required name="email" id="orangeForm-email" class="form-control" placeholder="Email">
                    <label class="form-label" for="orangeForm-email">Email</label>
                  </div>

                  <div data-mdb-input-init class="mb-4 form-outline">
                    <input autocomplete="off" type="password" required id="orangeForm-pass" name="password" class="form-control" placeholder="Password">
                    <label class="form-label" for="orangeForm-pass">Password</label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                  </div>

                  <div class="text-center">
                    <input type="submit" class="btn btn-primary font-weight-bold btn-lg btn-block btn-rounded mt-4 mb-0" value="Sign in">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- New Material Design -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/animations-extended.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>
  <script>
    $(document).ready(() => {
      new WOW().init();
    });
  </script>
</body>

</html>
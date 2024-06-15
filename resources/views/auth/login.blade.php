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
  <script type="text/javascript" src="{{ asset('ui-kit/js/jquery.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/argon-dashboard.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb-free.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('ui-kit/css/mdb.min.css') }}" />
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

  .btn-primary,
  .text-primary,
  .bg-primary {
    background: #48c6ef;
    background: -webkit-linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5));
    background: linear-gradient(to right, rgba(72, 198, 239, 0.5), rgba(111, 134, 214, 0.5))
  }

  .btn-danger,
  .text-danger,
  .bg-danger {
    background: #f093fb;
    background: -webkit-linear-gradient(to right, rgba(240, 147, 251, 0.5), rgba(245, 87, 108, 0.5));
    background: linear-gradient(to right, rgba(240, 147, 251, 0.5), rgba(245, 87, 108, 0.5))
  }

  .btn-warning,
  .text-warning,
  .bg-warning {
    background: #f6d365;
    background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 0.5), rgba(253, 160, 133, 0.5));
    background: linear-gradient(to right, rgba(246, 211, 101, 0.5), rgba(253, 160, 133, 0.5))
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
        <div class="row">
          <div class="col-lg-6">
            <div class="card wow fadeIn" data-wow-delay="0.3s">
              <div class="card-body">
                <!-- <div class="text-center">
                  <img src="" style="width: 185px;margin-top:-20px;" alt="logo">
                </div> -->

                <div class="card form-header bg-danger">
                  <h5 class="mb-0">Welcome back! <b>User</b></h5>
                </div>

                <form role="form" action="{{ route('login') }}" method="POST">
                  @csrf
                  <div data-mdb-input-init class="mb-4 form-outline">
                    <input autocomplete="off" type="email" name="email" id="orangeForm-email" class="form-control" placeholder="Email">
                    <label class="form-label" for="orangeForm-email">Email</label>
                  </div>

                  <div data-mdb-input-init class="mb-4 form-outline">
                    <input autocomplete="off" type="password" id="orangeForm-pass" name="password" class="form-control" placeholder="Password">
                    <label class="form-label" for="orangeForm-pass">Password</label>
                  </div>

                  <div class="form-check form-switch text-light">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                  </div>

                  <div class="text-center">
                    <input type="submit" class="btn btn-primary font-weight-bold btn-block btn-rounded mt-4 mb-0" value="Sign in">
                  </div>
                </form>

              </div>
            </div>
          </div>
          <div class="card col-lg-6 d-flex align-items-center bg-primary d-none d-md-block d-sm-none">
            <div class="text-white p-4 text-center">
              <h4 class="mb-0">Pondok Pesantren Mahasiswa</h4>
              <p class="mb-4">
                "Roudhotul Jannah" - Kabupaten Bandung
              </p>
              <div class="row">
                <div class="col-md-6">
                  <b>VISI</b><br>
                  Membentuk generasi penerus yang profesional dan religius.
                </div>
                <div class="col-md-6">
                  <b>MISI</b><br>
                  Melaksanakan program pembinaan secara intensif dan berkesinambungan. Meningkatkan softskill santri dan melancarkan kuliah santri.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- New Material Design -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb.umd.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>
  <script>
    $(document).ready(() => {
      new WOW().init();
    });
  </script>
</body>

</html>
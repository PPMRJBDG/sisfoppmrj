<!--
=========================================================
* Argon Dashboard 2 - v2.0.2
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">
<?php
$setting = App\Models\Settings::first();
?>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
  <title>
    {{$setting->apps_name}} - REGISTRASI SANTRI BARU
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js') }}" crossorigin="anonymous"></script>
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('css/argon-dashboard.css?v=2.0.2') }}" rel="stylesheet" />
</head>

<body class="">
  <div class="container z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow mt-4 py-2 start-0 end-0 mx-4">
          <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.html">
              {{$setting->apps_name}}
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse d-flex justify-content-end" id="navigation">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link d-flex align-items-center me-2 active" aria-current="page" href="login">
                    <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                    Sign in
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content mt-3">
    <section>
      <div class="page-header min-vh-100 d-block">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card border card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Registrate new Santri</h4>
                  <p class="mb-0">Fill up the available fields</p>
                </div>
                <div class="card-body">
                  @if ($errors->any())
                  <div class="alert alert-danger text-white">
                    <ul>
                      @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif
                  <form role="form" action="{{ route('store user from public') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Nama lengkap</label>
                      <input type="text" name="fullname" class="form-control form-control-lg" placeholder="Nama lengkap" aria-label="Nama lengkap">
                    </div>
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Email</label>
                      <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email">
                    </div>
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Password</label>
                      <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password" aria-label="password">
                        <div style="position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center">
                          <small><button class="me-3" style="background: none; border: 0" id="show">Show</button></small>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Tanggal Lahir</label>
                      <input type="date" name="birthdate" class="form-control form-control-lg" placeholder="Tanggal lahir" aria-label="birthdate">
                    </div>
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Jenis Kelamin</label>
                      <select data-mdb-filter="true" class="select form-control" name="gender" required>
                        <option selected disabled>Jenis Kelamin</option>
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-laki</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="example-text-input" class="form-control-label">Lorong</label>
                      <select data-mdb-filter="true" class="select form-control" name="fkLorong_id">
                        @foreach($lorongs as $lorong)
                        <option value="{{ $lorong->id }}">{{ $lorong->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="text-center">
                      <input type="submit" id="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0" value="Register">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('js/argon-dashboard.min.js?v=2.0.2') }}"></script>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <script>
    $('#show').click((e) => {
      e.preventDefault();

      $('#show').text($('#show').text() == 'Hide' ? 'Show' : 'Hide');
      $('#password').prop('type', $('#password').prop('type') == 'text' ? 'password' : 'text');
    })
  </script>
</body>

</html>
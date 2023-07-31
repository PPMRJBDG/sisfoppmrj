<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl pt-3" id="navbarBlur" data-scroll="false">
  <div class="container-fluid p-0">
    <nav aria-label="breadcrumb" class="d-flex gap-1 align-items-center">
      @if(isset($backRoute))
      <a href="{{ $backRoute }}">
        <i class="fa fa-arrow-left text-white"></i>
      </a>
      @endif
      <div>
        <ol class="breadcrumb bg-transparent m-0 p-0 font-weight-bolder">
          <li class="breadcrumb-item text-sm text-white active" aria-current="page">
            <!-- <a href="{{ route('presence report') }}" class="text-white">Home</a> -->
            <a href="" class="text-white">Home</a>
          </li>
          @foreach($breadcrumbs as $breadcrumb)
          <li class="breadcrumb-item text-sm text-white active" aria-current="page">
            {{ ucfirst($breadcrumb) }}
          </li>
          @endforeach
        </ol>
        <!-- <h6 class="text-white font-weight-bolder ms-2">{{ $title }}</h6> -->
      </div>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 me-md-0 me-sm-4 justify-content-end" id="navbar">
      <ul class="navbar-nav  justify-content-end">
        <li class="nav-item d-xl-none pe-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
            </div>
          </a>
        </li>
        <li class="nav-item dropdown d-flex align-items-center pe-1">
          <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-user cursor-pointer"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" style="top: 0!important" aria-labelledby="dropdownMenuButton">
            <li class="mb-2">
              <a class="dropdown-item border-radius-md" href="{{ route('my profile') }}">
                <div class="d-flex py-1">
                  {{ auth()->user()->fullname }}
                </div>
                <div class="d-flex gap-2 text-xs">
                  @foreach(auth()->user()->getRoleNames() as $roleName)
                  <span>{{ $roleName }}</span>
                  @endforeach
                </div>
              </a>
            </li>
            <li class="mb-2">
              <a href="{{ route('my profile') }}" class="dropdown-item border-radius-md">
                Lihat profil
              </a>
            </li>
            <li class="mb-2">
              <a href="{{ route('edit my profile') }}" class="dropdown-item border-radius-md">
                Edit profil
              </a>
            </li>
            <li class="mb-2">
              <form id="form" action="{{ url('logout') }}" method="post">
                @csrf
                <a class="dropdown-item border-radius-md" onclick="document.getElementById('form').submit()">
                  Log out
                </a>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- End Navbar -->
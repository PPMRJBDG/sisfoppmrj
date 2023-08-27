<style>
  .navbar-vertical.navbar-expand-xs .navbar-collapse {
    height: 90% !important;
  }
</style>

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 shadow-lg" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href=" {{ url('') }}">
      <img src="{{ asset('img/logo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-1 font-weight-bold">SISFO PPMRJ</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto ps" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ $path == '' ? 'active' : '' }}" href="{{ url('') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('ku'))
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">PENGURUS</h6>
      </li>
      <li class="nav-item">
        @if(!auth()->user()->hasRole('ku'))
        <a class="nav-link {{ $path == 'setting' ? 'active' : '' }}" href="{{ url('setting') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-settings text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Setting</span>
        </a>
        @if(!auth()->user()->hasRole('rj1'))
        <a class="nav-link {{ $path == 'database' ? 'active' : '' }}" href="{{ url('database') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bag-17 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Database</span>
        </a>
        @endif
        <a class="nav-link {{ $path == 'pelanggaran' ? 'active' : '' }}" href="{{ url('pelanggaran') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-umbrella-13 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Pelanggaran</span>
        </a>
        @endif
        @if(!auth()->user()->hasRole('rj1'))
        <a class="nav-link {{ $path == 'sodaqoh/list' ? 'active' : '' }}" href="{{ url('sodaqoh/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Sodaqoh</span>
        </a>
        @endif
      </li>

      @if(!auth()->user()->hasRole('ku'))
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Message Tools</h6>
      </li>
      <a class="nav-link {{ $path == 'msgtools/contact' ? 'active' : '' }}" href="{{ url('msgtools/contact') }}">
        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
          <i class="ni ni-collection text-info text-sm opacity-10"></i>
        </div>
        <span class="nav-link-text ms-1">Contact & Bulk</span>
      </a>
      @endif
      @endif

      @can('view presences list')
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Presensi</h6>
      </li>
      <!-- <li class="nav-item">
          <a class="nav-link {{ $path == 'presensi/laporan' ? 'active' : '' }}" href="{{ url('presensi/laporan') }}">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Laporan Umum</span>
          </a>
        </li> -->
      <!-- <li class="nav-item">
          <a class="nav-link {{ $path == 'presensi/laporan-umum' ? 'active' : '' }}" href="{{ route('presence report') }}">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-chart-pie-35 text-success text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Laporan Umum</span>
          </a>
        </li> -->
      <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/list' ? 'active' : '' }}" href="{{ url('presensi/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-success text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Presensi</span>
        </a>
      </li>
      @endcan
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('santri'))
      <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/terbaru' ? 'active' : '' }}" href="{{ url('presensi/terbaru') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-app text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Presensi Terbaru</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('koor lorong') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('pengabsen'))
      <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/izin/persetujuan' ? 'active' : '' }}" href="{{ url('presensi/izin/persetujuan') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-check-bold text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Terima/Tolak Izin</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('santri'))
      <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/izin/saya' ? 'active' : '' }}" href="{{ url('presensi/izin/saya') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Izin Saya</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1'))
      <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/izin/list' ? 'active' : '' }}" href="{{ url('presensi/izin/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Izin</span>
        </a>
      </li>
      @endif
      @can('view lorongs list')
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Lorong</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $path == 'lorong/list' ? 'active' : '' }}" href="{{ url('lorong/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Lorong</span>
        </a>
      </li>
      @endcan
      @if(auth()->user()->hasRole('superadmin') || isset(auth()->user()->santri->fkLorong_id) || auth()->user()->hasRole('koor lorong'))
      <li class="nav-item">
        <a class="nav-link {{ $path == 'lorong/saya' ? 'active' : '' }}" href="{{ url('lorong/saya') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-copy-04 text-warning text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Lorong Saya</span>
        </a>
      </li>
      @endif
      @can('view users list')
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">User</h6>
      </li>
      <li class="nav-item">
        @if(auth()->user()->hasRole('superadmin'))
        <a class="nav-link {{ $path == 'user/list/camaba' ? 'active' : '' }}" href="{{ url('user/list/camaba') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Camaba</span>
        </a>
        @endif
        <a class="nav-link {{ $path == 'user/list/santri' ? 'active' : '' }}" href="{{ url('user/list/santri') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Mahasiswa</span>
        </a>
        <a class="nav-link {{ $path == 'user/list/alumni' ? 'active' : '' }}" href="{{ url('user/list/alumni') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Alumni</span>
        </a>
        <a class="nav-link {{ $path == 'user/list/muballigh' ? 'active' : '' }}" href="{{ url('user/list/muballigh') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Muballigh</span>
        </a>
        @if(auth()->user()->hasRole('superadmin'))
        <a class="nav-link {{ $path == 'user/list/others' ? 'active' : '' }}" href="{{ url('user/list/others') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Others</span>
        </a>
        @endif
      </li>
      @endcan
      @if(auth()->user()->hasRole('santri') || auth()->user()->hasRole('superadmin'))
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Materi</h6>
      </li>
      @can('view materis list')
      <li class="nav-item">
        <a class="nav-link {{ $path == 'materi/monitoring/matching' ? 'active' : '' }}" href="{{ url('materi/monitoring/matching') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Cari Materi Kosong</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $path == 'materi/list' ? 'active' : '' }}" href="{{ url('materi/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Materi</span>
        </a>
      </li>
      @endcan
      <li class="nav-item">
        <a class="nav-link {{ $path == 'materi/monitoring/list' ? 'active' : '' }}" href="{{ url('materi/monitoring/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Monitoring Materi Santri</span>
        </a>
      </li>
      @endif
    </ul>
  </div>
  <!-- <div class="sidenav-footer mx-3">
    <div class="card card-plain shadow-none" id="sidenavCard">
      <img class="w-50 mx-auto" src="{{ asset('img/illustrations/icon-documentation.svg') }}" alt="sidebar_illustration">
    </div>
  </div> -->
</aside>
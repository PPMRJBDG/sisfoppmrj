<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href=" {{ url('') }}">
      <img src="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-1 font-weight-bold">{{$setting->apps_name}}</span>
    </a>
  </div>

  <hr class="horizontal dark mt-0">

  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ $path == '' ? 'active' : '' }}" href="{{ url('') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('ku'))
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">PENGURUS</h6>
      </li>
      <li class="nav-item">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1'))
        <a class="nav-link {{ $path == 'setting' ? 'active' : '' }}" href="{{ url('setting') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-settings text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Setting</span>
        </a>
        <a class="nav-link {{ $path == 'pelanggaran' ? 'active' : '' }}" href="{{ url('pelanggaran') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-umbrella-13 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Pelanggaran</span>
        </a>
        @endif
        @if(!auth()->user()->hasRole('wk'))
        <a class="nav-link {{ $path == 'sodaqoh/list' ? 'active' : '' }}" href="{{ url('sodaqoh/list') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Sodaqoh</span>
        </a>
        @endif
        @if(!auth()->user()->hasRole('ku'))
        <a class="nav-link {{ $path == 'catatan-penghubung' ? 'active' : '' }}" href="{{ url('catatan-penghubung') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-books text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Catatan Penghubung</span>
        </a>
        @endif
        <!-- <a class="nav-link {{ $path == 'receipt' ? 'active' : '' }}" href="{{ url('receipt') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Input Penerimaan</span>
        </a>
        <a class="nav-link {{ $path == 'rab' ? 'active' : '' }}" href="{{ url('rab') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">RAB</span>
        </a>
        <a class="nav-link {{ $path == 'op/in-out' ? 'active' : '' }}" href="{{ url('op/in-out') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">In Out OP</span>
        </a> -->
      </li>

      @if(auth()->user()->hasRole('superadmin'))
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Message Tools</h6>
      </li>
      <a class="nav-link {{ $path == 'msgtools/contact' ? 'active' : '' }}" href="{{ url('msgtools/contact') }}">
        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
          <i class="ni ni-collection text-info text-sm opacity-10"></i>
        </div>
        <span class="nav-link-text ms-1">Contact & Bulk</span>
      </a>
      <a class="nav-link {{ $path == 'msgtools/report' ? 'active' : '' }}" href="{{ url('msgtools/report') }}">
        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
          <i class="ni ni-collection text-info text-sm opacity-10"></i>
        </div>
        <span class="nav-link-text ms-1">Link Laporan</span>
      </a>
      <a class="nav-link {{ $path == 'msgtools/report' ? 'active' : '' }}" href="{{ url('msgtools/report') }}">
        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
          <i class="ni ni-collection text-info text-sm opacity-10"></i>
        </div>
        <span class="nav-link-text ms-1">Scheduler</span>
      </a>
      @endif
      @endif

      <li class="nav-item">
        <a class="nav-link {{ $path == 'jadwal-kbm' ? 'active' : '' }}" href="{{ url('jadwal-kbm') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-collection text-success text-sm opacity-10"></i>
          </div>
          <span class="nav-calendar ms-1">Jadwal KBM</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Presensi</h6>
      </li>
      @can('view presences list')
      @if(auth()->user()->hasRole('superadmin'))
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
      @endif
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
      <!-- <li class="nav-item">
        <a class="nav-link {{ $path == 'presensi/terbaru' ? 'active' : '' }}" href="{{ url('presensi/terbaru') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-app text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Presensi Terbaru</span>
        </a>
      </li> -->
      @endif
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('koor lorong') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
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
      @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
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
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Kurikulum</h6>
      </li>
      @can('view materis list')
      <li class="nav-item">
        <a class="nav-link {{ $path == 'dewan-pengajar' ? 'active' : '' }}" href="{{ url('dewan-pengajar') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dewan Pengajar</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $path == 'dewan-pengajar/jadwal' ? 'active' : '' }}" href="{{ url('dewan-pengajar/jadwal') }}">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Jadwal Pengajar</span>
        </a>
      </li>
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
  <div class="sidenav-footer mx-3">
    <div class="card card-plain shadow-none" id="sidenavCard">
      <img class="w-50 mx-auto" src="{{ asset('img/illustrations/icon-documentation.svg') }}" alt="sidebar_illustration">
      <div class="card-body text-center p-3 w-100 pt-0">
        <div class="docs-info">
          <h6 class="mb-0">Butuh Bantuan?</h6>
          <p class="text-xs font-weight-bold mb-0">Amshol hubungi Tim IT {{$setting->apps_name}}</p>
        </div>
      </div>
    </div>
    <a href="#" class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
    <a class="btn btn-primary btn-sm mb-0 w-100" href="#" type="button">Upgrade to PRO</a>
  </div>

</aside>
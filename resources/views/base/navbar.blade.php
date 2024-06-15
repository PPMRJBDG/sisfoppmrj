<nav class="navbar navbar-expand-lg fixed-top navbar-{{auth()->user()->themes}} bg-body-tertiary shadow-5 border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand ps-2" href="{{ url('') }}">
      @if($setting->logoImgUrl!='')
      <img src="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}" height="24" alt="PPM Logo" loading="lazy" />
      @else
      No Logo
      @endif
    </a>

    <button data-mdb-collapse-init class="navbar-toggler" type="button" data-mdb-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse ps-2" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="{{ url('/home') }}">Home</a>
        </li>

        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-pengurus" role="button" aria-expanded="false">Pengurus</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-pengurus">
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1'))
            <li>
              <a class="nav-link" href="{{ url('setting') }}">
                <span class="nav-link-text ms-1">Setting</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('pelanggaran') }}">
                <span class="nav-link-text ms-1">Daftar Pelanggaran</span>
              </a>
            </li>
            @endif
            <li>
              <a class="nav-link" href="{{ url('catatan-penghubung') }}">
                <span class="nav-link-text ms-1">Catatan Penghubung</span>
              </a>
            <li>
          </ul>
        </li>
        @endif

        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-keuangan" role="button" aria-expanded="false">Keuangan</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-keuangan">
            <li>
              <a class="nav-link" href="{{ url('list_sodaqoh') }}">
                <span class="nav-link-text ms-1">Sodaqoh Tahunan</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('keuangan/rab-tahunan') }}">
                <span class="nav-link-text ms-1">RAB Tahunan</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('keuangan/rab-pengadaan') }}">
                <span class="nav-link-text ms-1">RAB Pengadaan</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('keuangan/rab-kegiatan') }}">
                <span class="nav-link-text ms-1">RAB Kegiatan</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('keuangan/jurnal') }}">
                <span class="nav-link-text ms-1">Jurnal</span>
              </a>
            </li>
          </ul>
        </li>
        @endif

        @if(auth()->user()->hasRole('superadmin'))
        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-msgtools" role="button" aria-expanded="false">Message Tools</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-msgtools">
            <li>
              <a class="nav-link" href="{{ url('msgtools/contact') }}">
                <span class="nav-link-text ms-1">Contact & Bulk</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('msgtools/report') }}">
                <span class="nav-link-text ms-1">Link Laporan</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('msgtools/report') }}">
                <span class="nav-link-text ms-1">Scheduler</span>
              </a>
            </li>
          </ul>
        </li>
        @endif

        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-jadwalkbm" role="button" aria-expanded="false">Jadwal KBM</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-jadwalkbm">
            <li>
              <a class="nav-link" href="{{ url('jadwal-kbm') }}">
                <span class="nav-calendar ms-1">Jadwal KBM</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-presensi" role="button" aria-expanded="false">Presensi</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-presensi">
            @can('view presences list')
            @if(auth()->user()->hasRole('superadmin'))
            @endif
            <li>
              <a class="nav-link" href="{{ url('presensi/list') }}">
                <span class="nav-link-text ms-1">Daftar Presensi</span>
              </a>
            </li>
            @endcan
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('koor lorong') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <li>
              <a class="nav-link" href="{{ url('presensi/izin/persetujuan') }}">
                <span class="nav-link-text ms-1">Terima/Tolak Izin</span>
              </a>
            </li>
            @endif
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('santri'))
            <li>
              <a class="nav-link" href="{{ url('presensi/izin/saya') }}">
                <span class="nav-link-text ms-1">Izin Saya</span>
              </a>
            </li>
            @endif
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <li>
              <a class="nav-link" href="{{ url('presensi/izin/list') }}">
                <span class="nav-link-text ms-1">Daftar Izin</span>
              </a>
            </li>
            @endif
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-lorong" role="button" aria-expanded="false">Lorong</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-lorong">
            @can('view lorongs list')
            <li>
              <a class="nav-link" href="{{ url('lorong/list') }}">
                <span class="nav-link-text ms-1">Daftar Lorong</span>
              </a>
            </li>
            @endcan
            @if(auth()->user()->hasRole('superadmin') || isset(auth()->user()->santri->fkLorong_id) || auth()->user()->hasRole('koor lorong'))
            <li>
              <a class="nav-link" href="{{ url('lorong/saya') }}">
                <span class="nav-link-text ms-1">Lorong Saya</span>
              </a>
            </li>
            @endif
          </ul>
        </li>

        @can('view users list')
        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-user" role="button" aria-expanded="false">User</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-user">
            @if(auth()->user()->hasRole('superadmin'))
            <li>
              <a class="nav-link" href="{{ url('user/list/camaba') }}">
                <span class="nav-link-text ms-1">Daftar Camaba</span>
              </a>
            </li>
            @endif
            <li>
              <a class="nav-link" href="{{ url('user/list/santri') }}">
                <span class="nav-link-text ms-1">Daftar Mahasiswa</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('user/list/alumni') }}">
                <span class="nav-link-text ms-1">Daftar Alumni</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('user/list/muballigh') }}">
                <span class="nav-link-text ms-1">Daftar Muballigh</span>
              </a>
            </li>
            @if(auth()->user()->hasRole('superadmin'))
            <li>
              <a class="nav-link" href="{{ url('user/list/others') }}">
                <span class="nav-link-text ms-1">Others</span>
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endcan

        @if(auth()->user()->hasRole('santri') || auth()->user()->hasRole('superadmin'))
        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-kurikulum" role="button" aria-expanded="false">Kurikulum</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-kurikulum">
            @can('view materis list')
            <li>
              <a class="nav-link" href="{{ url('dewan-pengajar') }}">
                <span class="nav-link-text ms-1">Dewan Pengajar</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('dewan-pengajar/jadwal') }}">
                <span class="nav-link-text ms-1">Jadwal Pengajar</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('materi/monitoring/matching') }}">
                <span class="nav-link-text ms-1">Cari Materi Kosong</span>
              </a>
            </li>
            <li>
              <a class="nav-link" href="{{ url('materi/list') }}">
                <span class="nav-link-text ms-1">Daftar Materi</span>
              </a>
            </li>
            @endcan
            <li>
              <a class="nav-link" href="{{ url('materi/monitoring/list') }}">
                <span class="nav-link-text ms-1">Monitoring Materi</span>
              </a>
            </li>
          </ul>
        </li>
        @endif

        <li class="nav-item dropdown">
          <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink-profile" role="button" aria-expanded="false">Profile</a>
          <ul class="dropdown-menu shadow-lg" aria-labelledby="navbarDropdownMenuLink-profile">
            <li>
              <a class="nav-link" href="{{ route('my profile') }}">
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
            <li>
              <a href="{{ route('my profile') }}" class="nav-link">
                Lihat profil
              </a>
            </li>
            <li>
              <a href="{{ route('edit my profile') }}" class="nav-link">
                Edit profil
              </a>
            </li>
            <li>
              <a href="{{ route('edit version') }}" class="nav-link">
                {{(auth()->user()->themes=='dark') ? 'Light version' : 'Dark version'}}
              </a>
            </li>
            <li>
              <form id="form" action="{{ url('logout') }}" method="post">
                @csrf
                <a class="nav-link" onclick="document.getElementById('form').submit()">
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

<script>
  document.addEventListener("click", function(e) {
    if (e.target.matches('.nav-link.show')) {
      var NavShowAll = document.querySelectorAll(".dropdown-menu.show");
      for (var i = 0; i < NavShowAll.length; i++) {
        if (NavShowAll[i].getAttribute('aria-labelledby') != e.target.id) {
          NavShowAll[i].classList.remove('show');
        }
      }

      return false;
    }

    var NavShow = document.querySelector(".dropdown-menu.show");
    if (NavShow != null)
      NavShow.classList.remove('show');
  })
</script>
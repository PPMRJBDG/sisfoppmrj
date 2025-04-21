<header>
  <nav class="navbar navbar-expand-lg fixed-top navbar-{{auth()->user()->themes}} bg-body-tertiary shadow-1-strong border-bottom">
    <div class="container-fluid">
      <div class="d-flex">
        <a class="navbar-brand" href="#">
          @if($setting->logoImgUrl!='')
          <img src="{{ url('storage/logo-apps/' . $setting->logoImgUrl) }}" height="24" alt="PPM Logo" loading="lazy" />
          @else
          No Logo
          @endif
        </a>
        <button type="button" class="btn btn-primary btn-floating m-0" href="#" onclick="getPrevPage()">
            <i class="fas fa-chevron-left"></i>
        </button>
      </div>

      <div class="d-flex">
        <div class="flex-fill">
          <small id="breadcrumb-item" class="float-start font-weight-bolder m-0"></small>
        </div>
      </div>

      <div>
        <button type="button" class="btn btn-primary btn-floating m-0" onclick="refreshCurrentUrl()">
          <i class="fas fa-refresh"></i>
        </button>
        <button data-mdb-toggle="sidenav" data-mdb-target="#sidenav-1" class="btn btn-primary btn-floating m-0" aria-controls="#sidenav-1" aria-haspopup="true">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </nav>
</header>

<footer>
  <nav class="navbar navbar-expand-lg fixed-bottom bg-body-tertiary shadow-1-strong border-top">
    <div class="col-12">
      <div class="row justify-content-between align-items-center">
        <div class="col-3">
          <a href="{{ url('/home') }}" type="button" id="nav-bottom-home" class="btn btn-link btn-block nav-bottom-cl text-sm">
            <i class="fa fa-home fa-2x"></i>
          </a>
        </div>
        <div class="col-3">
          <a href="{{ (auth()->user()->hasRole('santri')) ? route('presence permit submission') : route('create presence permit') }}" id="nav-bottom-ijin" type="button" class="btn btn-link btn-block nav-bottom-cl text-sm">
            <i class="fa fa-square-pen fa-2x"></i>
          </a>
        </div>
        <div class="col-3">
          <a href="{{ url('materi/monitoring/list') }}" id="nav-bottom-target" type="button" class="btn btn-link btn-block nav-bottom-cl text-sm">
            <i class="fa fa-book fa-2x"></i>
          </a>
        </div>
        <div class="col-3">
          <a href="{{ route('my profile') }}" id="nav-bottom-profile" type="button" class="btn btn-link btn-block nav-bottom-cl text-sm">
            <i class="fa fa-user fa-2x"></i>
          </a>
        </div>
      </div>
    </div>
  </nav>
</footer>

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

<!-- Sidenav -->
<div id="sidenav-1" class="sidenav" role="navigation" data-mdb-right="true">
  <div class="text-center mb-2 justify-content-between align-items-center shadow-1-strong" style="height:60px;">
    <h5 class="p-3 mb-0" style="font-size: 1.5rem;">{{$setting->apps_name}}</h5>
  </div>
  <ul class="sidenav-menu">
    <li class="sidenav-item">
      <a class="sidenav-link d-flex" aria-current="page" href="{{ url('/home') }}">
        <i class="fa fa-home pe-3"></i>Home
      </a>
    </li>

    <li class="sidenav-item">
      <a class="sidenav-link d-flex" aria-current="page" href="#" onclick="showCacahJiwa()">
        <i class="fa fa-bar-chart pe-3"></i>Cacah Jiwa
      </a>
    </li>

    @if(auth()->user()->hasRole('santri'))
      <li class="sidenav-item">
        <a class="sidenav-link d-flex" aria-current="page" href="{{ url('/keuangan/tagihan') }}">
          <i class="fa fa-money-bill pe-3"></i>Tagihan
        </a>
      </li>
    @endif

    <li>
      <a class="sidenav-link" href="{{ url('kalender-ppm') }}">
        <i class="fa fa-calendar-day pe-3"></i>Kalender PPM
      </a>
    </li>

    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" href="#" block-id="return-false" id="navbarDropdownMenuLink-reporting" role="button" aria-expanded="false">
          <i class="fa fa-cog pe-3"></i>Laporan
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-reporting">
          <li>
            <a class="sidenav-link" href="{{ url('reporting/link_ortu') }}">
              <span class="sidenav-link-text ms-1">Link Laporan Ortu</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ route('view daily public presences recaps',[date('Y-m'),1]) }}">
              <span class="sidenav-link-text ms-1">Per Harian</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="#">
              <span class="sidenav-link-text ms-1">Dewan Guru</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" href="#" block-id="return-false" id="navbarDropdownMenuLink-pengurus" role="button" aria-expanded="false">
          <i class="fa fa-cog pe-3"></i>Pengurus
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-pengurus">
          @if(auth()->user()->hasRole('superadmin'))
          <li>
            <a class="sidenav-link" href="{{ url('setting') }}">
              <span class="sidenav-link-text ms-1">Setting</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('stdbot/contact') }}">
              <span class="sidenav-link-text ms-1">Contact & Bulk</span>
            </a>
          </li>
          @endif
          <li>
            <a class="sidenav-link" href="{{ url('pelanggaran') }}">
              <span class="sidenav-link-text ms-1">Daftar Pelanggaran</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('catatan-penghubung') }}">
              <span class="sidenav-link-text ms-1">Catatan Penghubung</span>
            </a>
          <li>
        </ul>
      </li>
    @endif

    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || App\Helpers\CommonHelpers::isKetuaBendahara())
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-keuangan" role="button" aria-expanded="false">
          <i class="fa fa-money pe-3"></i>Keuangan
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-keuangan">
          <li>
            <a class="sidenav-link" href="{{ url('/keuangan/mekanisme') }}">
              <span class="sidenav-link-text ms-1">Mekanisme Keuangan</span>
            </a>
          </li>
          @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku'))
            @if(!isset(auth()->user()->santri))
              <li>
                <a class="sidenav-link" href="{{ url('/keuangan/tagihan') }}">
                  <span class="sidenav-link-text ms-1">Tagihan (Approval)</span>
                </a>
              </li>
              <li>
                <a class="sidenav-link" href="{{ url('/keuangan/sodaqoh') }}">
                  <span class="sidenav-link-text ms-1">Sodaqoh Tahunan</span>
                </a>
              </li>
              <li>
                <a class="sidenav-link" href="{{ url('/keuangan/laporan-pusat') }}">
                  <span class="sidenav-link-text ms-1">Laporan Pusat</span>
                </a>
              </li>
            @endif
            <li>
              <a class="sidenav-link" href="{{ url('keuangan/jurnal') }}">
                <span class="sidenav-link-text ms-1">Jurnal Harian</span>
              </a>
            </li>
          @endif
          @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <li>
              <a class="sidenav-link" href="{{ url('keuangan/rab-tahunan') }}">
                <span class="sidenav-link-text ms-1">RAB Tahunan</span>
              </a>
            </li>
          @endif
          @if(!isset(auth()->user()->santri))
            <li>
              <a class="sidenav-link" href="{{ url('keuangan/rab-management-building') }}">
                <span class="sidenav-link-text ms-1">RAB Manag. Building</span>
              </a>
            </li>
          @endif
          @if(App\Helpers\CommonHelpers::isKetuaBendahara())
            <li>
              <a class="sidenav-link" href="{{ url('keuangan/rab-kegiatan') }}">
                <span class="sidenav-link-text ms-1">RAB Kegiatan</span>
              </a>
            </li>
            @endif
        </ul>
      </li>
    @endif

    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi keamanan'))
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-keamanan" role="button" aria-expanded="false">
          <i class="fa fa-warning pe-3"></i>Keamanan
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-keamanan">
          <li>
            <a class="sidenav-link" href="{{ url('keamanan') }}">
              <span class="sidenav-link-text ms-1">Daftar Jaga Malam</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('keamanan/pulang-malam') }}">
              <span class="sidenav-link-text ms-1">Pulang Malam < 23:00</span>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @if((auth()->user()->hasRole('ku') && isset(auth()->user()->santri)) || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru'))
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-jadwalkbm" role="button" aria-expanded="false">
          <i class="fa fa-calendar pe-3"></i>Jadwal KBM
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-jadwalkbm">
          <li>
            <a class="sidenav-link" href="{{ url('jadwal-kbm') }}">
              <span class="nav-calendar ms-1">Jadwal KBM</span>
            </a>
          </li>
        </ul>
      </li>
    @endif

    <li class="sidenav-item">
      <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-presensi" role="button" aria-expanded="false">
        <i class="fa fa-file-text pe-3"></i>Presensi
      </a>
      <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-presensi">
        @can('view presences list')
        @if(auth()->user()->hasRole('superadmin'))
        @endif
        <li>
          <a class="sidenav-link" href="{{ url('presensi/list') }}">
            <span class="sidenav-link-text ms-1">Daftar Presensi</span>
          </a>
        </li>
        @endcan
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('koor lorong') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <li>
          <a class="sidenav-link" href="{{ url('presensi/izin/persetujuan') }}">
            <span class="sidenav-link-text ms-1">Terima/Tolak Izin</span>
          </a>
        </li>
        @endif
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('santri'))
        <li>
          <a class="sidenav-link" href="{{ url('presensi/izin/saya') }}">
            <span class="sidenav-link-text ms-1">Izin Saya</span>
          </a>
        </li>
        @endif
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
        <li>
          <a class="sidenav-link" href="{{ url('presensi/izin/list') }}">
            <span class="sidenav-link-text ms-1">Daftar Izin</span>
          </a>
        </li>
        @endif
      </ul>
    </li>

    @if(!auth()->user()->hasRole('dewan guru'))
    <li class="sidenav-item">
      <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-lorong" role="button" aria-expanded="false">
        <i class="fa fa-users pe-3"></i>Lorong
      </a>
      <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-lorong">
        @can('view lorongs list')
        <li>
          <a class="sidenav-link" href="{{ url('lorong/list') }}">
            <span class="sidenav-link-text ms-1">Daftar Lorong</span>
          </a>
        </li>
        @endcan
        @if(auth()->user()->hasRole('superadmin') || isset(auth()->user()->santri->fkLorong_id) || auth()->user()->hasRole('koor lorong'))
        <li>
          <a class="sidenav-link" href="{{ url('lorong/saya') }}">
            <span class="sidenav-link-text ms-1">Lorong Saya</span>
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif
    
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || isset(auth()->user()->santri->panitiaPmb))
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-pmb" role="button" aria-expanded="false">
          <i class="fas fa-address-book pe-3"></i>PMB
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-pmb">
          <li>
            <a class="sidenav-link" href="{{ url('pmb/konfigurasi') }}">
              <span class="sidenav-link-text ms-1">Konfigurasi</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('pmb/panitia') }}">
              <span class="sidenav-link-text ms-1">Panitia</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('pmb/list_maba') }}">
              <span class="sidenav-link-text ms-1">Data Camaba</span>
            </a>
          </li>
        </ul>
      </li>
    @endif

    @can('view users list')
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-user" role="button" aria-expanded="false">
          <i class="fa fa-user pe-3"></i>User
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-user">
          <li>
            <a class="sidenav-link" href="{{ url('user/list/santri') }}">
              <span class="sidenav-link-text ms-1">Daftar Mahasiswa</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('user/list/alumni') }}">
              <span class="sidenav-link-text ms-1">Daftar Alumni</span>
            </a>
          </li>
          <li>
            <a class="sidenav-link" href="{{ url('user/list/muballigh') }}">
              <span class="sidenav-link-text ms-1">Daftar Muballigh</span>
            </a>
          </li>
          @if(auth()->user()->hasRole('superadmin'))
          <li>
            <a class="sidenav-link" href="{{ url('user/list/others') }}">
              <span class="sidenav-link-text ms-1">Others</span>
            </a>
          </li>
          @endif
        </ul>
      </li>
    @endcan

    @if(auth()->user()->hasRole('santri') || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru') || auth()->user()->hasRole('divisi kurikulum'))
      <li class="sidenav-item">
        <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-kurikulum" role="button" aria-expanded="false">
          <i class="fa fa-book pe-3"></i>Kurikulum
        </a>
        <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-kurikulum">
          @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('dewan guru') || auth()->user()->hasRole('divisi kurikulum'))
            @if(!auth()->user()->hasRole('divisi kurikulum'))
              <li>
                <a class="sidenav-link" href="{{ url('dewan-pengajar') }}">
                  <span class="sidenav-link-text ms-1">Pemateri</span>
                </a>
              </li>
              <li>
                <a class="sidenav-link" href="{{ url('kalender-ppm/template') }}">
                  <span class="sidenav-link-text ms-1">Template Kalender</span>
                </a>
              </li>
            @endif
            <li>
              <a class="sidenav-link" href="{{ url('materi/monitoring/matching') }}">
                <span class="sidenav-link-text ms-1">Cari Materi Kosong</span>
              </a>
            </li>
            <li>
              <a class="sidenav-link" href="{{ url('materi/list') }}">
                <span class="sidenav-link-text ms-1">Daftar Materi</span>
              </a>
            </li>
          @endif
          <li>
            <a class="sidenav-link" href="{{ url('materi/monitoring/list') }}">
              <span class="sidenav-link-text ms-1">Monitoring Materi</span>
            </a>
          </li>
        </ul>
      </li>
    @endif

    <li class="sidenav-item">
      <a data-mdb-dropdown-init class="sidenav-link d-flex" block-id="return-false" href="#" id="navbarDropdownMenuLink-profile" role="button" aria-expanded="false">
        <i class="fa fa-user-circle pe-3"></i>Profile
      </a>
      <ul class="sidenav-collapse" aria-labelledby="navbarDropdownMenuLink-profile">
        <!-- <li>
          <a class="sidenav-link" href="{{ route('my profile') }}">
            <div class="d-flex py-1">
              {{ auth()->user()->fullname }}
            </div>
          </a>
        </li> -->
        <li>
          <a href="{{ route('my profile') }}" class="sidenav-link">
            Lihat profil
          </a>
        </li>
        <li>
          <a href="{{ route('edit my profile') }}" class="sidenav-link">
            Edit profil
          </a>
        </li>
        <!-- <li>
          <a href="{{ route('edit version') }}" class="sidenav-link">
            {{(auth()->user()->themes=='dark') ? 'Light version' : 'Dark version'}}
          </a>
        </li> -->
        <li>
          <form id="form" action="{{ url('logout') }}" method="post">
            @csrf
            <a class="sidenav-link" onclick="document.getElementById('form').submit()">
              Log out
            </a>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</div>
<!-- Sidenav -->

<script>
  // document.addEventListener("click", function(e) {
  //   if (e.target.matches('.sidenav-link.show')) {
  //     var NavShowAll = document.querySelectorAll(".dropdown-menu.show");
  //     for (var i = 0; i < NavShowAll.length; i++) {
  //       if (NavShowAll[i].getAttribute('aria-labelledby') != e.target.id) {
  //         NavShowAll[i].classList.remove('show');
  //       }
  //     }

  //     return false;
  //   }

  //   var NavShow = document.querySelector(".dropdown-menu.show");
  //   if (NavShow != null)
  //     NavShow.classList.remove('show');
  // })
</script>
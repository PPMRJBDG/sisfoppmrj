@include('base.start', [
'path' => 'presensi/list',
'title' => 'Presensi ' . (isset($presence) ? $presence->name : ''),
'breadcrumbs' => ['Daftar Presensi', 'Presensi ' . (isset($presence) ? $presence->name : '')],
'backRoute' => isset($presence) ? ($presence->presenceGroup ? route('view presence group', $presence->presenceGroup->id) : route('presence tm')) : ''
])
<style>
  @media only screen and (max-width: 600px) {

    body,
    h6 {
      font-size: 0.8rem !important;
    }
  }

  .py-4 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* Style the tab */
  .tab {
    overflow: hidden;
    background-color: #f1f1f1;
    border-radius: 8px 8px 0 0;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px !important;
    transition: 0.3s;
    font-size: 17px;
  }

  .tablinks {
    font-weight: bold !important;
    font-size: 14px !important;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #ddd;
  }

  /* Create an active/current tablink class */
  .tab button.active {
    background-color: #ccc;
    border-bottom: solid 4px #c93779;
  }

  /* Style the tab content */
  .tabcontent {
    display: none;
    /* padding: 6px 12px; */
    border-top: none;
    background-color: #fff;
    border-radius: 0 0 8px 8px;
  }
</style>
@if(isset($presence))
<div class="card">
  <div class="card-body p-3 d-flex">
    <div class="">
      <h6 class="text-sm">Presensi {{ $presence->name }}</h6>
      @include('components.presence_summary', ['presence' => $presence])
    </div>
    <div class="ms-auto text-end">
      @can('delete presences')
      <!-- <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a> -->
      @endcan
      @can('update presences')
      <!-- <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a> -->
      @endcan
    </div>
  </div>
</div>

<div class="text-white text-sm font-weight-bolder text-center mt-2">
  <span>Jumlah {{ (auth()->user()->hasRole('koor lorong')) ? 'Anggota' : 'Mahasiswa' }} {{ $jumlah_mhs }}</span>
</div>

<div class="tab mt-2">
  <button class="tablinks active" onclick="openPresensi(event, 'hadir')">Hadir {{count($presents)}}</button>
  <button class="tablinks" onclick="openPresensi(event, 'ijin')">Ijin {{count($permits)}}</button>
  <button class="tablinks" onclick="openPresensi(event, 'alpha')">Alpha {{count($mhs_alpha)}}</button>
</div>

<div class="card tabcontent" id="hadir" style="display:block;">
  <div class="card-header p-2 d-flex justify-content-between align-items-center">
    <h6>Daftar hadir: {{count($presents)}}</h6>
    @if($update)
    @can('create presents')
    <!-- <a href="{{ route('create present', $presence->id) }}" class="btn btn-primary mb-0">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Tambah kehadiran
    </a> -->
    @endcan
    @endif
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    @if (session('successes'))
    <div class="px-4">
      <div class="alert alert-success text-white">
        <?php echo session('successes') ?>
      </div>
    </div>
    @endif
    @if (session('errors'))
    <div class="px-4">
      <div class="alert alert-danger text-white">
        <?php echo session('errors') ?>
      </div>
    </div>
    @endif
    <div class="table-responsive p-0">
      <table id="table" class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($presents as $present)
          <tr>
            <td class="text-sm">
              <b>{{ $present->santri->user->fullname }}</b>
              <br>
              <small>{{ $present->created_at }} | <b>{{ $present->is_late ? 'Telat' : 'Tidak telat' }}</b></small>
            </td>
            <td class="align-middle text-center text-sm">
              @if($update)
              <a class="btn btn-danger btn-block btn-xs mb-0" href="{{ route('delete present', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak hadir?')">Alpha</a>
              @if($present->is_late)
              <a class="btn btn-primary btn-xs mb-0" href="{{ route('is not late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak telat?')">Tidak Telat</a>
              @else
              <a class="btn btn-warning btn-xs mb-0" href="{{ route('is late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin telat?')">Telat</a>
              @endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card tabcontent" id="ijin" style="display:none;">
  @if(count($permits)>0)
  <div class="card-header p-2 d-flex justify-content-between align-items-center">
    <h6>Daftar ijin: {{count($permits)}}</h6>
  </div>

  <div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($permits as $permit)
        <tr>
          <td class="text-sm">
            <b>{{ $permit->santri->user->fullname }}</b>
          </td>
          <td class="align-middle text-center text-sm">
            @if($permit->status=='approved')
            <span class="text-primary font-weight-bolder">{{ $permit->status }}</span>
            @else
            <span class="text-danger font-weight-bolder">{{ $permit->status }}</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="p-2 text-center text-sm">Tidak ada yang ijin</div>
  @endif
</div>

<div class="card tabcontent" id="alpha" style="display:none;">
  @if(count($mhs_alpha)>0)
  <div class="card-header p-2 d-flex justify-content-between align-items-center">
    <h6>Daftar alpha: {{count($mhs_alpha)}}</h6>
  </div>

  <div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mhs_alpha as $mhs)
        <tr>
          <td class="text-sm">
            <b>{{ $mhs['name'] }}</b>
          </td>
          <td class="text-sm">
            <a class="btn btn-primary btn-block btn-xs mb-0" href="{{ route('is present', ['id' => $mhs['presence_id'], 'santriId' => $mhs['santri_id']]) }}" onclick="return confirm('Yakin hadir?')">Hadir</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="p-2 text-center text-sm">Tidak ada yang alpha</div>
  @endif
</div>

@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
  </div>
</div>
@endif

<script>
  $('#table').DataTable({
    order: [
      // [1, 'desc']
    ]
  });

  function openPresensi(evt, tahun) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tahun).style.display = "block";
    evt.currentTarget.className += " active";
  }
</script>
@include('base.end')
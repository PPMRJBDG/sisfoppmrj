@include('base.start', [
'path' => 'presensi/list',
'title' => 'Presensi ' . (isset($presence) ? $presence->name : ''),
'breadcrumbs' => ['Daftar Presensi', 'Presensi ' . (isset($presence) ? $presence->name : '')],
'backRoute' => isset($presence) ? ($presence->presenceGroup ? route('view presence group', $presence->presenceGroup->id) : route('presence tm')) : ''
])

@if(isset($presence))
<div class="card">
  <div class="card-body p-3 d-flex">
    <div class="">
      <h6 class="text-sm">Presensi {{ $presence->name }}</h6>
      @include('components.presence_summary', ['presence' => $presence])
    </div>
    <div class="ms-auto text-end">
      @can('delete presences')
      <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
      @endcan
      @can('update presences')
      <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
      @endcan
    </div>
  </div>
</div>

<div class="text-white text-sm font-weight-bolder text-center mt-2">
  <span>Jumlah {{ (auth()->user()->hasRole('koor lorong')) ? 'Anggota' : 'Mahasiswa' }} {{ $jumlah_mhs }}</span>
</div>

@if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
<div class="row">
  <div class="col-sm-12 col-md-12 mt-2">
    <select class="select_lorong form-control" name="select_lorong" id="select_lorong">
      <option value="-">Semua Lorong</option>
      @foreach($data_lorong as $l)
      <option {{ ($lorong==$l->id) ? 'selected' : '' }} value="{{$l->id}}">{{$l->name}}</option>
      @endforeach
    </select>
  </div>
</div>
@endif

<div class="tab mt-2">
  <button class="tablinks active" onclick="openTab(event, 'hadir')">Hadir {{count($presents)}}</button>
  <button class="tablinks" onclick="openTab(event, 'ijin')">Ijin {{count($permits)}}</button>
  <button class="tablinks" onclick="openTab(event, 'alpha')">Alpha {{count($mhs_alpha)}}</button>
</div>

<div class="card tabcontent" id="hadir" style="display:block;">
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
    <div class="table-responsive p-2">
      <table id="table" class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($presents as $present)
          @if($present->santri->fkLorong_id==$lorong || $lorong=='-')
          <tr>
            <td class="text-sm">
              <b>{{ $present->santri->user->fullname }}</b>
              <br>
              <small>{{ $present->created_at }}</small>
              <!-- | <b>{{ $present->is_late ? 'Telat' : 'Tidak telat' }}</b> -->
            </td>
            <td class="align-middle text-center text-sm">
              @if($update)
              <a class="btn btn-danger btn-block btn-xs mb-0" href="{{ route('delete present', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak hadir?')">Alpha</a>
              <!-- @if($present->is_late) -->
              <!-- <a class="btn btn-primary btn-xs mb-0" href="{{ route('is not late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak telat?')">Tidak Telat</a> -->
              <!-- @else -->
              <!-- <a class="btn btn-warning btn-xs mb-0" href="{{ route('is late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin telat?')">Telat</a> -->
              <!-- @endif -->
              @endif
            </td>
          </tr>
          @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card tabcontent" id="ijin" style="display:none;">
  @if(count($permits)>0 || count($need_approval)>0)
  <div class="card-header p-2 pb-0">
    <h6 class="mb-0 bg-warning p-1 text-white">
      Perlu persetujuan/ditolak: {{count($need_approval)}}
    </h6>
  </div>

  <div class="table-responsive p-2">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
        </tr>
      </thead>
      <tbody>
        <!-- need approval -->
        @foreach($need_approval as $na)
        @if($na->santri->fkLorong_id==$lorong || $lorong=='-')
        <tr>
          <td class="text-sm">
            <b>{{ $na->santri->user->fullname }}</b>
            <br>
            <small>[{{ $na->reason_category }}] - {{ $na->reason }}</small>
          </td>
          <td class="align-middle text-center text-sm">
            <span class="text-danger font-weight-bolder">{{ $na->status }}</span>
            @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <br>
            <a class="btn btn-primary btn-xs mb-0" href="{{ route('approve presence permit', ['presenceId' => $na->fkPresence_id, 'santriId' => $na->fkSantri_id]) }}" onclick="return confirm('Yakin disetujui?')">Setujui ?</a>
            @endif
          </td>
        </tr>
        @endif
        @endforeach

        @foreach($permits as $permit)
        @if($permit->santri->fkLorong_id==$lorong || $lorong=='-')
        <tr>
          <td class="text-sm">
            <b>{{ $permit->santri->user->fullname }}</b>
            <br>
            <small>[{{ $permit->reason_category }}] - {{ $permit->reason }}</small>
          </td>
          <td class="align-middle text-center text-sm">
            <span class="text-primary font-weight-bolder">{{ $permit->status }}</span>
            <br>
            <a class="btn btn-warning btn-xs mb-0" href="{{ route('reject presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" onclick="return confirm('Yakin ditolak?')">Tolak ?</a>
          </td>
        </tr>
        @endif
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
  <div class="table-responsive p-2">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mhs_alpha as $mhs)
        @if($mhs['fkLorong_id']==$lorong || $lorong=='-')
        <tr>
          <td class="text-sm">
            <b>{{ $mhs['name'] }}</b>
          </td>
          <td class="text-sm">
            @if($update)
            <a class="btn btn-primary btn-block btn-xs mb-0" href="{{ route('is present', ['id' => $mhs['presence_id'], 'santriId' => $mhs['santri_id']]) }}" onclick="return confirm('Yakin hadir?')">Hadir</a>
            @endif
          </td>
        </tr>
        @endif
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
  // $('#table').DataTable({
  //   order: [
  // [1, 'desc']
  //   ]
  // });

  $('.select_lorong').change((e) => {
    window.location.replace(`{{ url("/") }}/presensi/list/<?php echo $id; ?>/${$(e.currentTarget).val()}`)
  })
</script>
@include('base.end')
@include('base.start', [
'path' => 'presensi/list',
'title' => 'Presensi ' . (isset($presence) ? $presence->name : ''),
'breadcrumbs' => ['Daftar Presensi', 'Presensi ' . (isset($presence) ? $presence->name : '')],
'backRoute' => $presence->presenceGroup ? route('view presence group', $presence->presenceGroup->id) : route('presence tm')
])
@if(isset($presence))
<div class="card">
  <div class="card-body pt-4 p-3 d-flex">
    <div class="d-flex flex-column">
      <h6>Presensi {{ $presence->name }}</h6>
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
<div class="card mt-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar hadir</h6>
    @if($update)
    @can('create presents')
    <a href="{{ route('create present', $presence->id) }}" class="btn btn-primary">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Tambah kehadiran
    </a>
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
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waktu Presensi</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($presents as $present)
          <tr>
            <td>
              <div class="d-flex px-2 py-1">
                <div>
                  <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ $present->santri->user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td>
              {{ $present->created_at }}
            </td>
            <td>
              {{ $present->is_late ? 'Telat' : 'Tidak telat' }}
            </td>
            <td class="align-middle text-center text-sm">
              <a class="btn btn-danger btn-sm" href="{{ route('delete present', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin menghapus?')">Hapus</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
  </div>
</div>
@endif

<?php
$males = [];
$females = [];

foreach ($presents as $present) {
  if ($present->santri->user->gender == 'male')
    array_push($males, $present);
  else
    array_push($females, $present);
}

$malePermits = [];
$femalePermits = [];

foreach ($permits as $permit) {
  if ($permit->santri->user->gender == 'male')
    array_push($malePermits, $permit);
  else
    array_push($femalePermits, $permit);
}
?>
<!-- <div class="card mt-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar hadir untuk WhatsApp</h6>
  </div>
  <div class="card-body pt-4 p-3 d-flex">
    <textarea style="width:100%; min-height: 500px">
*Laki-laki*
@foreach($males as $male)
{{ $male->santri->user->fullname }} {{ $male->is_late ? '(Telat)' : '' }}       
@endforeach
{{ sizeof($males) == 0 ? 'Tidak ada' : ''}}
*Perempuan*
@foreach($females as $female)
{{ $female->santri->user->fullname }} {{ $female->is_late ? '(Telat)' : '' }}     
@endforeach
{{ sizeof($females) == 0 ? 'Tidak ada' : ''}}
--------------------
*IZIN Laki-laki*
@foreach($malePermits as $permit)
{{ $permit->santri->user->fullname }} ({{ $permit->reason_category == 'dll' ? $permit->reason : $permit->reason_category }})        
@endforeach
{{ sizeof($malePermits) == 0 ? 'Tidak ada' : ''}}
*IZIN Perempuan*
@foreach($femalePermits as $permit)
{{ $permit->santri->user->fullname }} ({{ $permit->reason_category == 'dll' ? $permit->reason : $permit->reason_category }})        
@endforeach
{{ sizeof($femalePermits) == 0 ? 'Tidak ada' : ''}}
      </textarea>
  </div>
</div> -->
@include('base.end')
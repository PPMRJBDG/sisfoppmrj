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
    <h6>Daftar hadir: {{count($presents)}}</h6>
    @if($update)
    @can('create presents')
    <a href="{{ route('create present', $presence->id) }}" class="btn btn-primary mb-0">
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
    <div class="table-responsive p-2">
      <table id="table" class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
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
              <a class="btn btn-danger btn-xs mb-0" href="{{ route('delete present', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin menghapus?')">Alpha</a>
              @if($present->is_late)
              <a class="btn btn-primary btn-xs mb-0" href="{{ route('is not late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak telat?')">Tidak Telat</a>
              @else
              <a class="btn btn-warning btn-xs mb-0" href="{{ route('is late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin telat?')">Telat</a>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@if(count($permits)>0)
<div class="card mt-2">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar ijin: {{count($permits)}}</h6>
  </div>

  <div class="table-responsive p-2">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
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
            <a class="btn btn-primary btn-xs mb-0" href="#">{{ $permit->status }}</a>
            @else
            <a class="btn btn-danger btn-xs mb-0" href="#">{{ $permit->status }}</a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
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
</script>
@include('base.end')
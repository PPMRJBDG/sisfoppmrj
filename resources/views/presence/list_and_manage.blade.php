<div class="card shadow border p-2 mb-2">
  <div class="row">
    <div class="col-md-4">
      <h6 class="mb-sm-0 btn btn-secondary btn-block btn-sm m-0 mb-2">Daftar Presensi</h6>
    </div>
    @can('create presences')
    <div class="col-md-4">
      <a href="{{ route('create presence') }}" class="btn btn-primary btn-block btn-sm m-0 mb-2">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat presensi
      </a>
    </div>
    <div class="col-md-4">
      <a href="{{ route('create presence group') }}" class="btn btn-primary btn-block btn-sm m-0">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat grup presensi
      </a>
    </div>
    @endcan
  </div>
</div>

<div class="row">
  @foreach($presenceGroups as $presenceGroup)
    <div class="col-md-6">
      <div class="card shadow border mb-2">
        <div class="card-header">
          <p class="mb-0 font-weight-bolder">{{ $presenceGroup->name }} (Grup)</p>
        </div>
        <div class="card-body">
          <p class="text-sm mb-0">Jadwal: {{ ucwords($presenceGroup->days_in_bahasa()) }}</p>
          <p class="text-sm mb-0">Mulai KBM: {{ $presenceGroup->start_hour }}</p>
          <p class="text-sm mb-0">Selesai KBM: {{ $presenceGroup->end_hour }}</p>
          <p class="text-sm mb-0">Display Fingerprint (On): {{ $presenceGroup->presence_start_hour }}</p>
          <p class="text-sm mb-0">Display Fingerprint (Off): {{ $presenceGroup->presence_end_hour }}</p>
        </div>
        <div class="card-footer text-end">
          <a class="btn btn-success btn-sm m-0" href="{{ route('view presence group', $presenceGroup->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
          @can('delete presences')
          <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
          @endcan
          @can('update presences')
          <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
          @endcan
        </div>
      </div>
    </div>
  @endforeach
</div>

@if(sizeof($presences) == 0 && sizeof($presenceGroups) == 0)
<div class="card shadow border mb-3 p-2">
  Belum ada data.
</div>
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
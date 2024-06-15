<div class="card">
  <div class="card-header p-3 d-sm-flex justify-content-between align-items-center">
    <h6 class="mb-sm-0 btn btn-secondary">Daftar Presensi</h6>
    <div>
      @can('create presences')
      <a href="{{ route('create presence') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat presensi
      </a>
      <a href="{{ route('create presence group') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat grup presensi
      </a>
      @endcan
    </div>
  </div>
  <div class="card-body p-3">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    @foreach($presenceGroups as $presenceGroup)
    <div class="card mb-3 p-3">
      <div class="">
        <h6 class="mb-3 text-sm">{{ $presenceGroup->name }} (Grup)</h6>
        <span class="mb-2 text-xs">Jadwal: <span class="font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span></span>
        <br>
        <span class="mb-2 text-xs">Mulai KBM: <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span></span>
        -
        <span class="mb-2 text-xs">Selesai KBM: <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour }}</span></span>
        <br>
        <span class="mb-2 text-xs">Display Barcode (On): <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->presence_start_hour }}</span></span>
        -
        <span class="mb-2 text-xs">Display Barcode (Off): <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->presence_end_hour }}</span></span>
      </div>
      <div class="ms-auto text-end">
        <a class="btn btn-success m-0" href="{{ route('view presence group', $presenceGroup->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
        @can('delete presences')
        <a class="btn btn-danger m-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
        @endcan
        @can('update presences')
        <a class="btn btn-warning m-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
        @endcan
      </div>
    </div>
    @endforeach

    @if(sizeof($presences) == 0 && sizeof($presenceGroups) == 0)
    Belum ada data.
    @endif
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
<div class="card p-2 mb-2">
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

@if (session('success'))
<div class="alert alert-success text-white">
  {{ session('success') }}
</div>
@endif

@foreach($presenceGroups as $presenceGroup)
<div class="card mb-2 p-2">
  <div class="">
    <h6 class="mb-3 text-sm font-weight-bolder">{{ $presenceGroup->name }} (Grup)</h6>
    <span class="mb-2 text-xs font-weight-bolder">Jadwal:</span> <span class="font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span>
    <br>
    <hr>
    <span class="mb-2 text-xs font-weight-bolder">Mulai KBM:</span> <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span>
    <br>
    <span class="mb-2 text-xs font-weight-bolder">Selesai KBM:</span> <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour }}</span>
    <br>
    <hr>
    <span class="mb-2 text-xs font-weight-bolder">Display Barcode (On):</span> <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->presence_start_hour }}</span>
    <br>
    <span class="mb-2 text-xs font-weight-bolder">Display Barcode (Off):</span> <span class="ms-sm-2 font-weight-bold">{{ $presenceGroup->presence_end_hour }}</span>
  </div>
  <div class="ms-auto text-end mt-2">
    <a class="btn btn-success btn-sm m-0" href="{{ route('view presence group', $presenceGroup->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
    @can('delete presences')
    <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
    @endcan
    @can('update presences')
    <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
    @endcan
  </div>
</div>
@endforeach

@if(sizeof($presences) == 0 && sizeof($presenceGroups) == 0)
<div class="card mb-3 p-2">
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
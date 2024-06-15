<div>
  @if(isset($presenceGroup))
  <div class="card">
    <div class="card-body p-2">
      <div class="">
        <h6 class="text-sm font-weight-bolder">Grup Presensi {{ $presenceGroup->name }}</h6>
        <span class="mb-2 text-xs font-weight-bolder">Jadwal:</span> <span class="font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span>
        <br>
        <span class="mb-2 text-xs font-weight-bolder">Jam buka:</span> <span class="ms-sm-2 badge badge-secondary font-weight-bold">{{ $presenceGroup->start_hour }}</span>
        <br>
        <span class="text-xs font-weight-bolder">Jam tutup:</span> <span class="ms-sm-2 badge badge-secondary font-weight-bold">{{ $presenceGroup->end_hour  }}</span>
      </div>
      <div class="ms-auto text-end mt-3">
        @can('delete presences')
        <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
        @endcan
        @can('update presences')
        <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
        @endcan
      </div>
    </div>
  </div>

  <h6 class="text-sm m-2 font-weight-bolder text-center">Daftar Presensi</h6>
  @if (session('success'))
  <div class="alert alert-success ">
    {{ session('success') }}
  </div>
  @endif

  <div class="card mt-2 mb-2 p-2">
    <a href="{{ route('create presence in group', $presenceGroup->id) }}" class="btn btn-primary btn-block btn-sm m-0">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat presensi di grup ini
    </a>
  </div>

  <?php if (sizeof($presenceGroup->presences()->paginate(13)) <= 0) { ?>
    <div class="card">
      <div class="card-body p-2">
        <div class="alert alert-danger">Tidak ada hasil.</div>
      </div>
    </div>
  <?php } ?>

  @foreach($presenceGroup->presences()->where('is_deleted',0)->orderBy('event_date', 'DESC')->paginate(13) as $presence)
  <div class="card mb-2 p-2">
    <div class="">
      <h6 class=" mb-0 text-sm font-weight-bolder">{{ $presence->name }}</h6>
      @include('components.presence_summary', ['presence' => $presence])
    </div>
    <div class="ms-auto text-end mt-3">
      <a class="btn btn-success btn-sm m-0" href="{{ route('view presence', $presence->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
      @can('delete presences')
      <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
      @endcan
      @can('update presences')
      <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
      @endcan
    </div>
  </div>
  @endforeach

  @include('components.paginator', ['page' => $page])

  @else
  <div class="card">
    <div class="card-body p-2">
      <div class="alert alert-danger ">Grup presensi tidak ditemukan.</div>
    </div>
  </div>
  @endif
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
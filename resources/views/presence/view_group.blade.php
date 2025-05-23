
<div>
  @if(isset($presenceGroup))
  <div class="card shadow border">
    <div class="card-body p-2">
      <div class="">
        <h6 class="mb-0 text-sm font-weight-bolder">Grup Presensi {{ $presenceGroup->name }}</h6>
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

  <div class="card shadow border mt-0 mb-2 p-2">
    <a href="{{ route('create presence in group', $presenceGroup->id) }}" class="btn btn-primary btn-block btn-sm m-0">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat presensi di grup ini
    </a>
  </div>

  <?php if (sizeof($presenceGroup->presences()->paginate(13)) <= 0) { ?>
    <div class="card shadow border">
      <div class="card-body p-2">
        <div class="alert alert-danger">Tidak ada hasil.</div>
      </div>
    </div>
  <?php } ?>

  <div class="card shadow border">
    <nav>
      <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
          <a data-mdb-ripple-init class="nav-link active font-weight-bolder" id="nav-aktif-tab" data-bs-toggle="tab" href="#nav-aktif" role="tab" aria-controls="nav-aktif" aria-selected="true">
              Aktif
          </a>
          <a data-mdb-ripple-init class="nav-link font-weight-bolder" id="nav-draft-tab" data-bs-toggle="tab" href="#nav-draft" role="tab" aria-controls="nav-draft">
              Draft
          </a>
      </div>

      <div class="tab-content p-0 mt-2" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-aktif" role="tabpanel" aria-labelledby="nav-aktif-tab">
          @foreach($presenceGroup->presences()->where('is_deleted',0)->orderBy('event_date', 'DESC')->paginate(13) as $presence)
            <div class="card shadow border mb-2 p-2">
              <div class="row">
                <div class="col-md-6">
                  <p class="mb-0 font-weight-bolder">{{ $presence->name }}</p>
                  @include('components.presence_summary', ['presence' => $presence])
                </div>
                <div class="col-md-6 ms-auto text-end mt-3">
                  <a class="btn btn-success btn-sm m-0" href="{{ route('view presence', $presence->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
                  @can('delete presences')
                  <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
                  @endcan
                  @can('update presences')
                  <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
                  @endcan
                </div>
              </div>
            </div>
          @endforeach

          @include('components.paginator', ['page' => $page])
      </div>

      <div class="tab-pane fade show" id="nav-draft" role="tabpanel" aria-labelledby="nav-draft-tab">
        @foreach($presenceGroup->presences()->where('is_deleted',2)->orderBy('event_date', 'ASC')->paginate(13) as $presence)
          <div class="card shadow border mb-2 p-2">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-0 font-weight-bolder">{{ $presence->name }}</p>
                @include('components.presence_summary', ['presence' => $presence])
              </div>
              <div class="col-md-6 ms-auto text-end mt-3">
                <a class="btn btn-success btn-sm m-0" href="{{ route('view presence', $presence->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
                @can('delete presences')
                <a class="btn btn-danger btn-sm m-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
                @endcan
                @can('update presences')
                <a class="btn btn-warning btn-sm m-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
                @endcan
              </div>
            </div>
          </div>
          @endforeach

          @include('components.paginator', ['page' => $page])
      </div>
    </div>
    </nav>
  </div>

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
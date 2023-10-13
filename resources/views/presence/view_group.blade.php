@include('base.start', [
'path' => 'presensi/list',
'title' => isset($presenceGroup) ? $presenceGroup->name : 'Presensi tidak ditemukan',
'breadcrumbs' => ['Daftar Presensi', isset($presenceGroup) ? $presenceGroup->name : 'Presensi tidak ditemukan'],
'backRoute' => route('presence tm')
])
@if(isset($presenceGroup))
<div class="card">
  <div class="card-body p-3">
    <div class="">
      <h6 class="text-sm">Grup Presensi {{ $presenceGroup->name }}</h6>
      <span class="mb-2 text-xs">Jadwal: <span class="text-dark font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span></span>
      <br>
      <span class="mb-2 text-xs">Jam buka: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span></span>
      -
      <span class="text-xs">Jam tutup: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour  }}</span></span>
    </div>
    <div class="ms-auto text-end">
      @can('delete presences')
      <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
      @endcan
      @can('update presences')
      <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
      @endcan
      <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('select presence group recap', $presenceGroup->id) }}"><i class="fas fa-eye text-dark me-2" aria-hidden="true"></i>Lihat rekap</a>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-header p-3 pb-0 justify-content-between align-items-center">
    <h6 class="text-sm">Daftar Presensi</h6>
    <a href="{{ route('create presence in group', $presenceGroup->id) }}" class="btn btn-primary form-control m-0">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat presensi di grup ini
    </a>
  </div>
  <div class="card-body pt-4 p-3">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    <ul class="list-group">
      <!-- <div class="alert alert-info text-white text-sm">
        Presensi akan dibuat otomatis sesuai jadwal setiap harinya. Apabila tetap tidak terbuat,
        pastikan CRON JOB berjalan.
      </div> -->
      @if(sizeof($presenceGroup->presences()->paginate(13)) <= 0) Tidak ada hasil. @endif @foreach($presenceGroup->presences()->orderBy('event_date', 'DESC')->paginate(13) as $presence)
        <li class="list-group-item border-0 p-3 mb-2 bg-gray-100 border-radius-lg shadow-sm">
          <div class="">
            <h6 class=" mb-0 text-sm">{{ $presence->name }}</h6>
            @include('components.presence_summary', ['presence' => $presence])
          </div>
          <div class="ms-auto text-end">
            <a class="btn btn-link text-dark text-gradient px-3 mb-0" href="{{ route('view presence', $presence->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
            @can('delete presences')
            <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
            @endcan
            @can('update presences')
            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
            @endcan
          </div>
        </li>
        @endforeach
    </ul>
    @include('components.paginator', ['page' => $page])
  </div>
</div>
@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">Grup presensi tidak ditemukan.</div>
  </div>
</div>
@endif
@include('base.end')
@include('base.start', ['path' => 'presensi/list', 'title' => 'Daftar Presensi', 'breadcrumbs' => ['Daftar Presensi']])
<div class="card">
  <div class="card-header pb-0 p-3 d-sm-flex justify-content-between align-items-center">
    <h6 class="mb-sm-0">Daftar Presensi</h6>
    <div>
      @can('create presences')
      <a href="{{ route('create presence') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat presensi
      </a>
      <a href="{{ route('create presence group') }}" class="btn btn-outline-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat grup presensi
      </a>
      @endcan
      <!-- <a tooltip="TEsT" href="{{ route('check presence schedules') }}" class="btn btn-success">
          Generate pengajian terjadwal
        </a>     -->
    </div>
  </div>
  <div class="card-body pt-4 p-3">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    <ul class="list-group">
      @foreach($presenceGroups as $presenceGroup)
      <li class="list-group-item border-0 p-3 mb-2 bg-gray-100 border-radius-lg shadow-sm">
        <div class="">
          <h6 class="mb-3 text-sm">{{ $presenceGroup->name }} (Grup)</h6>
          <span class="mb-2 text-xs">Jadwal: <span class="text-dark font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span></span>
          <br>
          <span class="mb-2 text-xs">Jam buka: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span></span>
          -
          <span class="mb-2 text-xs">Jam tutup: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour }}</span></span>
        </div>
        <div class="ms-auto text-end">
          <a class="btn btn-link text-dark text-gradient px-3 mb-0" href="{{ route('view presence group', $presenceGroup->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
          @can('delete presences')
          <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete presence group', $presenceGroup->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
          @endcan
          @can('update presences')
          <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit presence group', $presenceGroup->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
          @endcan
        </div>
      </li>
      @endforeach
      @foreach($presences as $presence)
      <!-- <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
        <div class="d-flex flex-column">
          <h6 class="mb-3 text-sm">{{ $presence->name }}</h6>
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
      </li> -->
      @endforeach
      @if(sizeof($presences) == 0 && sizeof($presenceGroups) == 0)
      Belum ada data.
      @endif
    </ul>
  </div>
</div>
@include('base.end')
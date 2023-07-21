@include('base.start', ['path' => 'materi/list', 'title' => 'Daftar Materi', 'breadcrumbs' => ['Daftar Materi']])
  <div class="card">
    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Daftar Materi</h6>
      <a href="{{ route('create materi') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat materi
      </a>     
    </div>
    <div class="card-body pt-4 p-3">
      @if (session('success'))
          <div class="alert alert-success text-white">
          {{ session('success') }}
        </div>
      @endif
      @if(sizeof($materis) <= 0)
        Belum ada data.
      @endif
      <ul class="list-group">
        @foreach($materis as $materi)
          <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
            <div class="d-flex flex-column">
              <h6 class="mb-3 text-sm">{{ $materi->name }}</h6>
              <span class="mb-2 text-xs">Halaman: <span class="text-dark font-weight-bold ms-sm-2">{{ $materi->pageNumbers }}</span></span>
              <span class="mb-2 text-xs">Untuk: <span class="text-dark font-weight-bold ms-sm-2">{{ $materi->for ? ucfirst($materi->for) : 'Reguler' }}</span></span>
            </div>
            <div class="ms-auto text-end">
              <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete materi', $materi->id) }}" onclick="return confirm('Yakin ingin menghapus? Seluruh data terkait materi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
              <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit materi', $materi->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
            </div>
          </li>   
        @endforeach
      </ul>
    </div>
  </div>
@include('base.end')

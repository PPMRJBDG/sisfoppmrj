@include('base.start', ['path' => 'lorong/list', 'title' => 'Daftar Lorong', 'breadcrumbs' => ['Daftar Lorong']])
  <div class="card">
    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Daftar Lorong</h6>
      <a href="{{ route('create lorong') }}" class="btn btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat lorong
      </a>     
    </div>
    <div class="card-body pt-4 p-3">
      @if (session('success'))
          <div class="alert alert-success text-white">
          {{ session('success') }}
        </div>
      @endif
      @if(sizeof($lorongs) <= 0)
        Belum ada data.
      @endif
      <ul class="list-group">
        @foreach($lorongs as $lorong)
          <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
            <div class="d-flex flex-column">
              <h6 class="mb-3 text-sm">{{ $lorong->name }}</h6>
              <span class="mb-2 text-xs">Koor: <span class="text-dark font-weight-bold ms-sm-2">{{ $lorong->leader->user->fullname }}</span></span>
              <span class="mb-2 text-xs">Anggota: <span class="text-dark ms-sm-2 font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
            </div>
            <div class="ms-auto text-end">
              <a class="btn btn-link text-dark text-gradient px-3 mb-0" href="{{ route('view lorong', $lorong->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
              <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete lorong', $lorong->id) }}" onclick="return confirm('Yakin ingin menghapus? Seluruh data terkait lorong ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
              <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit lorong', $lorong->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
            </div>
          </li>   
        @endforeach
      </ul>
    </div>
  </div>
@include('base.end')

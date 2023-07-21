@include('base.start', ['path' => 'lorong/list', 'title' => (isset($lorong) ? $lorong->name : ''), 'breadcrumbs' => ['Daftar Lorong', (isset($lorong) ? $lorong->name : '')]
, 'backRoute' => url()->previous() ? (url()->previous() != url()->current() ? url()->previous() : route('lorong tm')) : route('lorong tm')])
  @if(isset($lorong))
    <div class="card">
      <div class="card-body pt-4 p-3 d-flex">
        <div class="d-flex flex-column">
          <h6>{{ $lorong->name }}</h6>
          <span class="mb-2 text-xs">Koor: <span class="text-dark ms-sm-2 font-weight-bold">{{ $lorong->leader->user->fullname }}</span></span>
          <span class="text-xs">Anggota: <span class="text-dark ms-sm-2 font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
        </div>
        <div class="ms-auto text-end">
          <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete lorong', $lorong->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait lorong ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
          <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit lorong', $lorong->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Daftar anggota</h6>
        <a href="{{ route('add lorong member', $lorong->id) }}" class="btn btn-primary mb-0">
          <i class="fas fa-plus" aria-hidden="true"></i>
          Tambah anggota
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        @if (session('success'))
          <div class="p-4">
            <div class="alert alert-success text-white">
              {{ session('success') }}
            </div>
          </div>
        @endif
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Angkatan PPM</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($lorong->members as $member)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $member->user->fullname }}</h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    {{ $member->angkatan }}
                  </td>
                  <td class="align-middle text-center text-sm">
                    <a href="{{ route('delete lorong member', [$lorong->id, $member->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin menghapus?')">Keluarkan</a>
                    @can('update users')
                      <a href="{{ route('edit user', $member->user->id) }}" class="btn btn-primary btn-sm">Ubah</a>              
                    @endcan
                  </td>
                </tr>
              @endforeach              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @else
    <div class="card">
      <div class="card-body pt-4 p-3">
        <div class="alert alert-danger text-white">Lorong tidak ditemukan.</div>
      </div>
    </div>
  @endif
@include('base.end')

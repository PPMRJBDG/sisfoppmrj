@if(isset($lorong))
<div class="card shadow border bg-primary text-light">
  <div class="card-header">
    <h6 class="mb-0">{{ $lorong->name }}</h6>
  </div>
  <div class="card-body p-2 d-flex">
    <div class="d-flex flex-column">
      <span class="mb-2 text-xs">Koor: <span class="ms-sm-2 font-weight-bold">{{ $lorong->leader->user->fullname }}</span></span>
      <span class="text-xs">Anggota: <span class="ms-sm-2 font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
    </div>
  </div>
  <div class="card-footer text-end">
    <a class="btn btn-link text-light px-3 mb-0" href="{{ route('delete lorong', $lorong->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait lorong ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
    <a class="btn btn-link text-light px-3 mb-0" href="{{ route('edit lorong', $lorong->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
  </div>
</div>

<div class="card shadow border mt-2">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="text-sm mb-0">Daftar anggota</h6>
    <a href="{{ route('add lorong member', $lorong->id) }}" class="btn btn-sm btn-primary mb-0">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Tambah anggota
    </a>
  </div>

  <div class="card-body px-0 pt-0 pb-2">
    @if (session('success'))
    <div class="p-2">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif

    <div class="datatable datatable-sm p-0">
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Angkatan PPM</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($lorong->members as $member)
          <tr>
            <td>
              <h6 class="mb-0 text-sm">{{ $member->user->fullname }}</h6>
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
  <div class="card-body pt-4 p-2">
    <div class="alert alert-danger text-white">Lorong tidak ditemukan.</div>
  </div>
</div>
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
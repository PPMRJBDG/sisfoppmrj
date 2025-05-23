@if(isset($lorong))
<div class="card shadow border bg-primary text-light">
  <div class="card-body p-2 d-flex">
    <div class="d-flex flex-column">
      <h6 class="mb-0">{{ $lorong->name }}</h6>
      <span class="mb-0 text-xs">Koor: <span class="ms-sm-2 font-weight-bold">{{ $lorong->leader->user->fullname }}</span></span>
      <span class="text-xs">Anggota: <span class="ms-sm-2 badge badge-warning font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
    </div>
    <div class="ms-auto text-end">
      @can('delete lorongs')
      <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete lorong', $lorong->id) }}" onclick="return confirm('Yakin ingin menghapus? Seluruh data terkait lorong ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
      @endcan
      @can('update lorongs')
      <a class="btn btn-link px-3 mb-0" href="{{ route('edit lorong', $lorong->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
      @endcan
    </div>
  </div>
</div>

<h6 class="mt-2 mb-0 font-weight-bolder">Daftar anggota</h6>
@can('add lorong members')
<a href="{{ route('add lorong member', $lorong->id) }}" class="btn btn-primary mb-0">
  <i class="fas fa-plus" aria-hidden="true"></i>
  Tambah anggota
</a>
@endcan
<div class="card shadow border">
  <div class="card-body p-2">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    <div class="datatable datatable-sm p-0">
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Angkatan PPM</th>
            @can('remove lorong members')
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
            @endcan
            <th class="text-secondary opacity-7"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($lorong->members as $member)
          <tr>
            <td>
              <p class="mb-0 text-sm font-weight-bolder">{{ $member->user->fullname }}</p>
            </td>
            <td>
              {{ $member->angkatan }}
            </td>
            <td class="align-middle text-center text-sm">
              @can('remove lorong members')
              <a href="{{ route('delete lorong member', [$lorong->id, $member->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin menghapus?')">Keluarkan</a>
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
<div class="alert alert-danger text-white">
  Anda belum bergabung dengan lorong manapun. Kontak pengurus untuk menambahkan Anda.
</div>
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
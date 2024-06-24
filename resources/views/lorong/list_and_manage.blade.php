<div class="card shadow border mb-2">
  <div class="p-2 d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Daftar Lorong</h6>
    <a href="{{ route('create lorong') }}" class="btn btn-primary">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat lorong
    </a>
  </div>
  @if (session('success'))
  <div class="card-body p-2">
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
  </div>
  @endif
</div>

@if(count($lorongs) > 0)
@foreach($lorongs as $lorong)
<div class="card shadow border mb-2">
  <div class="card-header">
    <h6 class="mb-0 text-sm font-weight-bolder">{{ $lorong->name }}</h6>
  </div>
  <div class="card-body">
    <span class="mb-2 text-xs">Koor: <span class="text-dark font-weight-bold ms-sm-2">{{ $lorong->leader->user->fullname }}</span></span>
    <br>
    <span class="mb-2 text-xs">Anggota: <span class="text-dark ms-sm-2 font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
  </div>
  <div class="card-footer">
    <a class="btn btn-link text-primary text-primary px-3 mb-0" href="{{ route('view lorong', $lorong->id) }}"><i class="far fa-eye me-2" aria-hidden="true"></i>Lihat</a>
    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('delete lorong', $lorong->id) }}" onclick="return confirm('Yakin ingin menghapus? Seluruh data terkait lorong ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
    <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('edit lorong', $lorong->id) }}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Ubah</a>
  </div>
</div>
@endforeach
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
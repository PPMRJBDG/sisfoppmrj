@if ($errors->any())
<div class="alert alert-danger text-white">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
@if (session('success'))
<div class="alert alert-success text-white">
  {{ session('success') }}
</div>
@endif

<div class="card shadow border p-2">
  <a type="button" class="btn btn-rounded btn-outline-warning btn-block" href="{{ url('materi/list') }}">Kembali</a>
  <hr>
  <div class="card-body p-2">
    @if(isset($materi))
    <form action="{{ route('update materi', $materi->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">ID</label>
            <input class="form-control" type="text" value="{{ $materi->id }}" required readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama</label>
            <input class="form-control" type="text" name="name" value="{{ $materi->name }}" placeholder="Contoh: K. Nikah">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Jumlah halaman</label>
            <input class="form-control" type="text" name="pageNumbers" value="{{ $materi->pageNumbers }}" placeholder="Contoh: 150n">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary" type="submit" value="Ubah">
          </div>
        </div>
      </div>
    </form>
    @else
    <div class="alert alert-danger text-white">
      Materi tidak ditemukan.
    </div>
    @endif
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
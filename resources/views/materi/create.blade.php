@if ($errors->any())
<div class="alert alert-danger text-white">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="card shadow border p-2">
  <a type="button" class="btn btn-rounded btn-outline-warning btn-block" href="{{ url('materi/list') }}">Kembali</a>
  <hr>
  <div class="card-body p-2">
    <form action="{{ route('store materi') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama</label>
            <input class="form-control" type="text" name="name" placeholder="Contoh: K. Nikah" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Jumlah halaman</label>
            <input class="form-control" type="text" name="pageNumbers" placeholder="Contoh: 150" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Untuk</label>
            <select data-mdb-filter="true" class="select form-control" name="for" required>
              <option value="mubalegh">Mubalegh</option>
              <option value="reguler">Reguler</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary" type="submit" value="Submit">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
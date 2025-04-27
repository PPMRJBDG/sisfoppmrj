<div class="card border shadow">
  <div class="card-header py-3 p-2 d-flex justify-content-between align-items-center">
    <h6 class="mb-0 font-weight-bolder">Daftar Materi</h6>
    <a href="{{ route('create materi') }}" class="btn btn-primary btn-sm">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat materi
    </a>
  </div>
  <div class="card-body p-2">
    @if (session('success'))
    <div class="alert bg-success text-white">
      {{ session('success') }}
    </div>
    @endif

    <div class="datatable" data-mdb-sm="true" data-mdb-entries="100">
      <table class="table align-items-center">
        <thead>
          <tr>
            <th>MATERI</th>
            <th>HALAMAN</th>
            <th>KELAS</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @if(sizeof($materis) > 0)
          @foreach($materis as $materi)
          <tr>
            <td>{{ strtoupper($materi->name) }}</td>
            <td>{{ $materi->pageNumbers }}</td>
            <td>{{ $materi->for ? ucfirst($materi->for) : 'Reguler' }}</td>
            <td>
              <center>
              <a class="btn btn-outline-danger btn-sm" href="{{ route('delete materi', $materi->id) }}" onclick="return confirm('Yakin ingin menghapus? Seluruh data terkait materi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
              <a class="btn btn-outline-primary btn-sm" href="{{ route('edit materi', $materi->id) }}"><i class="fas fa-pencil-alt me-2" aria-hidden="true"></i>Ubah</a>
            </td>
          </tr>
          @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>

  <script>
    try {
      $(document).ready();
    } catch (e) {
      window.location.replace(`{{ url("/") }}`)
    }
  </script>
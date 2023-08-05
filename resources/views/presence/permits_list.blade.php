@include('base.start', ['path' => 'presensi/izin/list', 'title' => 'Daftar Izin', 'breadcrumbs' => ['Daftar Izin']])
<div class="card">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar Izin (add date filter)</h6>
  </div>
  <div class="card-body px-0 pt-0 pb-0">
    @if (session('success'))
    <div class="p-4">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif
    @if(sizeof($permits) <= 0) <div class="p-3">
      Belum ada data.
  </div>
  @endif
  <div class="table-responsive p-2">
    <table id="table" class="table align-items-center mb-0">
      <thead style="background-color:#f6f9fc;">
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presensi</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alasan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waktu Pengajuan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($permits as $permit)
        <tr>
          <td>
            <div class="d-flex px-2 py-1">
              <div>
                <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
              </div>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ ($permit->santri) ? $permit->santri->user->fullname : '-' }}</h6>
              </div>
            </div>
          </td>
          <td>
            {{ $permit->presence->name }}
          </td>
          <td>
            {{ ucfirst($permit->reason_category) }}
          </td>
          <td>
            {{ ucfirst($permit->reason) }}
          </td>
          <td>
            {{ $permit->updated_at }}
          </td>
          <td>
            <span class="badge {{ $permit->status == 'pending' ? 'bg-gradient-secondary' : ($permit->status == 'approved' ? 'bg-gradient-success' : ($permit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <!-- <div class="p-3">
      @include('components.paginator', ['page' => $page])
    </div> -->
  </div>
</div>
</div>
<script>
  $('#table').DataTable({
    order: [
      [1, 'desc']
    ],
    pageLength: 25
  });
</script>
@include('base.end')
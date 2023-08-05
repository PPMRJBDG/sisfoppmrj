@include('base.start', ['path' => 'presensi/izin/persetujuan', 'title' => 'Tolak / Terima Daftar Izin ' . (isset($lorong) ? $lorong->name : ''), 'breadcrumbs' => ['Daftar Izin', 'Daftar Izin ' . (isset($lorong) ? $lorong->name : '')]])
@if($santri && $lorong || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('superadmin'))
<div class="card">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar izin {{ isset($lorong) ? $lorong->name : '' }} (add date filter)</h6>

    @role('koor lorong|superadmin')
    <a href="{{ route('create presence permit') }}" class="btn btn-primary mb-0">
      <i class="fas fa-plus" aria-hidden="true"></i> Buat izin
    </a>
    @endrole
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    @if ($errors->any())
    <div class="p-4">
      <div class="alert alert-danger text-white">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif
    @if (session('success'))
    <div class="p-4">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif
    <div class="table-responsive p-2">
      <table id="table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presensi</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alasan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waktu Pengajuan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($permits))
          @foreach($permits as $permit)
          <tr>
            <td>
              <div class="d-flex px-2 py-1">
                <div>
                  <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ $permit->santri->user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td>
              {{ $permit->presence->name }}
            </td>
            <td>
              {{ $permit->reason }}
            </td>
            <td>
              {{ $permit->updated_at }}
            </td>
            <td>
              <span class="badge {{ $permit->status == 'pending' ? 'bg-gradient-secondary' : ($permit->status == 'approved' ? 'bg-gradient-success' : ($permit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
            </td>
            <td class="align-middle text-center text-sm">
              <a href="{{ route('approve presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id, 'page' => $page]) }}" class="btn btn-success btn-sm mb-0">Terima</a>
              <a href="{{ route('reject presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-danger btn-sm mb-0">Tolak</a>
              <a href="{{ route('delete presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-danger btn-sm mb-0 ms-6" onclick="return confirm('Yakin menghapus?')">Delete</a>
            </td>
            @endforeach
            @endif
          </tr>
        </tbody>
      </table>
      <!-- <div class="p-3">
        @include('components.paginator', ['page' => $page])
      </div> -->
    </div>
  </div>
</div>
@else
<div class="card">
  <div class="card-body p-2">
    <div class="alert alert-danger text-white m-2">
      User ini bukanlah santri atau bukan seorang koor lorong.
    </div>
  </div>
</div>
@endif
<script>
  $('#table').DataTable({
    order: [
      [1, 'desc']
    ],
    pageLength: 25
  });
</script>
@include('base.end')
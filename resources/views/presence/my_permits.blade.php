@include('base.start', ['path' => 'presensi/izin/saya', 'title' => 'Daftar Izin Saya', 'breadcrumbs' => ['Daftar Izin Saya']])
<div class="card">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar izin saya</h6>
    <div>
      <a href="{{ route('presence permit submission') }}" class="btn btn-primary mb-0">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat izin
      </a>
    </div>
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    @if (session('success'))
    <div class="p-4">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif
    @if(sizeof($myPermits) <= 0) <div class="p-3">
      Belum ada data.
  </div>
  @endif

  <div class="table-responsive p-2">
    <table id="table-izin" class="table align-items-center mb-0">
      <thead style="background-color:#f6f9fc;">
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presensi</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori Alasan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="width:20%;">Alasan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waktu Pengajuan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($myPermits as $myPermit)
        <tr>
          <td>
            <div class="d-flex px-2 py-1">
              <div>
                <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
              </div>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ ($myPermit->santri) ? $myPermit->santri->user->fullname : '-' }}</h6>
              </div>
            </div>
          </td>
          <td>
            {{ $myPermit->presence->name }}
          </td>
          <td>
            {{ ucfirst($myPermit->reason_category) }}
          </td>
          <td>
            {{ ucfirst(substr($myPermit->reason,0,30)) }}...
          </td>
          <td>
            {{ $myPermit->updated_at }}
          </td>
          <td>
            <span class="badge {{ $myPermit->status == 'pending' ? 'bg-gradient-secondary' : ($myPermit->status == 'approved' ? 'bg-gradient-success' : ($myPermit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($myPermit->status) }}</span>
          </td>
          <td class="align-middle text-center text-sm">
            @if($myPermit->status=='approved')
            <a href="{{ route('edit presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-primary btn-sm mb-0">Edit</a>
            @endif
            <a href="{{ route('delete my presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>
<div class="card mt-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Daftar izin Generator</h6>
    <div>
      <a href="{{ route('ranged presence permit submission') }}" class="btn btn-outline-primary mb-0">
        <i class="fas fa-plus" aria-hidden="true"></i>
        Buat izin berjangka
      </a>
    </div>
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    @if (session('success'))
    <div class="p-4">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif
    @if(sizeof($myPermits) <= 0) <div class="p-3">
      Belum ada data.
  </div>
  @endif
  <div class="table-responsive p-2">
    <table id="table-generator" class="table align-items-center mb-0">
      <thead style="background-color:#f6f9fc;">
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presensi</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alasan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori Alasan</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dari Tanggal</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sampai Tanggal</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
        </tr>
      </thead>
      <tbody>
        @isset($myRangedPermits)
        @foreach($myRangedPermits as $myRangedPermit)
        <tr>
          <td>
            <div class="d-flex px-2 py-1">
              <div>
                <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
              </div>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ ($myRangedPermit->santri) ? $myRangedPermit->santri->user->fullname : '-' }}</h6>
              </div>
            </div>
          </td>
          <td>
            {{ $myRangedPermit->presenceGroup->name }}
          </td>
          <td>
            {{ ucfirst($myRangedPermit->reason_category) }}
          </td>
          <td>
            {{ ucfirst(substr($myRangedPermit->reason,50)) }}
          </td>
          <td>
            {{ $myRangedPermit->from_date }}
          </td>
          <td>
            {{ $myRangedPermit->to_date }}
          </td>
          <td class="align-middle text-center text-sm">
            <a href="{{ route('delete my ranged presence permit', $myRangedPermit->id) }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
          </td>
        </tr>
        @endforeach
        @endisset
      </tbody>
    </table>
  </div>
</div>
</div>
<script>
  $('#table-izin').DataTable({
    order: [
      [1, 'desc']
    ],
    pageLength: 25
  });
  $('#table-generator').DataTable({
    order: [
      [1, 'desc']
    ],
    pageLength: 25
  });
</script>
@include('base.end')
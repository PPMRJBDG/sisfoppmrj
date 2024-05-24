@include('base.start', ['path' => 'presensi/izin/saya', 'title' => 'Daftar Izin Saya', 'breadcrumbs' => ['Daftar Izin Saya']])


<div class="card-body px-0 pt-0 pb-2">
  @if (session('success'))
  <div class="p-0">
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
  </div>
  @endif

  <div class="tab mt-2">
    <button class="tablinks active" onclick="openTab(event, 'harian')">Harian</button>
    <button class="tablinks" onclick="openTab(event, 'berjangka')">Berjangka</button>
  </div>

  <div class="card tabcontent" id="harian" style="display:block;">
    <div class="card-header p-2 pb-0 align-items-center">
      <h6>Daftar izin saya</h6>
      <div class="col-md-12">
        <div class="form-group">
          <a href="{{ (auth()->user()->hasRole('superadmin')) ? route('create presence permit') : route('presence permit submission') }}" class="btn btn-primary form-control mb-0">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat izin
          </a>
        </div>
      </div>
    </div>

    <div class="table-responsive p-2">
      <table id="table-izin" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2" style="width:20%;">Alasan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Status</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($myPermits as $myPermit)
          <tr class="text-sm">
            <td>
              <h6 class="mb-0 text-sm">{{ ($myPermit->santri) ? $myPermit->santri->user->fullname : '-' }}</h6>
              <small>{{ $myPermit->presence->name }}</small>
            </td>
            <td>
              <b><small>[{{ ucfirst($myPermit->reason_category) }}]</small></b>
              <br>
              {{ ucfirst(substr($myPermit->reason,0,30)) }}...
            </td>
            <td>
              <span class="badge {{ $myPermit->status == 'pending' ? 'bg-gradient-secondary' : ($myPermit->status == 'approved' ? 'bg-gradient-success' : ($myPermit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($myPermit->status) }}</span>
              <br>
              <small>{{ $myPermit->updated_at }}</small>
            </td>
            <td colspan=3>
              @if($myPermit->status!='rejected')
              <a href="{{ route('edit presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-primary btn-xs mb-0">Edit</a>
              @endif
              <a href="{{ route('delete my presence permit') }}?presenceId={{ $myPermit->fkPresence_id }}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card tabcontent" id="berjangka" style="display:none;">
    <div class="card-header p-2 pb-0 justify-content-between align-items-center">
      <h6>Daftar izin Generator</h6>
      <div class="col-md-12">
        <div class="form-group">
          <a href="{{ (auth()->user()->hasRole('superadmin')) ? route('create presence permit') : route('ranged presence permit submission') }}" class="btn btn-outline-primary form-control mb-0">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat izin berjangka
          </a>
        </div>
      </div>
    </div>
    <div class="table-responsive p-2">
      <table id="table-generator" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Alasan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Tanggal</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
          </tr>
        </thead>
        <tbody>
          @isset($myRangedPermits)
          @foreach($myRangedPermits as $myRangedPermit)
          <tr class="text-sm">
            <td>
              <h6 class="mb-0 text-sm">{{ ($myRangedPermit->santri) ? $myRangedPermit->santri->user->fullname : '-' }}</h6>
              {{ $myRangedPermit->presenceGroup->name }}
            </td>
            <td>
              <b><small>[{{ ucfirst($myRangedPermit->reason_category) }}]</small></b>
              <br>
              {{ $myRangedPermit->reason }}
            </td>
            <td><small>
                <span class="badge {{ $myRangedPermit->status == 'pending' ? 'bg-gradient-secondary' : ($myRangedPermit->status == 'approved' ? 'bg-gradient-success' : ($myRangedPermit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($myRangedPermit->status) }}</span>
                <br>
                {{ $myRangedPermit->from_date }} s.d {{ $myRangedPermit->to_date }}
              </small></td>
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
      // [1, 'desc']
    ],
    pageLength: 25
  });
  $('#table-generator').DataTable({
    order: [
      // [1, 'desc']
    ],
    pageLength: 25
  });
</script>
@include('base.end')
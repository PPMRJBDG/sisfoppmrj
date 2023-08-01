@include('base.start_without_bars', ['path' => 'presensi/list', 'containerClass' => 'p-0', 'title' => "Lihat Rekap Presensi $date/$month/$year", 'breadcrumbs' => ['Rekap Presensi', "$date/$month/$year"]])
<style>
  @media only screen and (max-width: 600px) {

    body,
    h6 {
      font-size: 0.8rem !important;
    }
  }

  .py-4 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }
</style>
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<div class="card m-1">
  <div class="card-body p-2">
    @if(sizeof($presencesInDate) > 0)
    <ul class="nav nav-tabs">
      @foreach($presencesInDate as $presenceInDate)
      <li class="nav-item">
        <a class="nav-link {{ $presence ? ($presence->id == $presenceInDate->id ? 'active' : '') : '' }}" aria-current="page" href="{{ route('view daily public presences recaps', [
                'year' => $year,
                'month' => $month,
                'date' => $date,
                'presenceId' => $presenceInDate->id
              ]) }}">{{ $presenceInDate->presenceGroup ? $presenceInDate->presenceGroup->name : $presenceInDate->name}}</a>
      </li>
      @endforeach
    </ul>
    @else
    Tidak ada presensi pada tanggal ini.
    @endif
  </div>
</div>

@if($presence)
<div class="card m-1">
  <div class="card-body p-2">
    <h6>Daftar Hadir | {{$date .'/'. $month .'/'. $year}}</h6>
    <div class="table-responsive p-0">
      <table id="recap-table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($presents as $present)
          <tr>
            <td>
              <div class="d-flex">
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ $present->santri->user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td>
              {{ $present->is_late ? 'Telat' : 'Tidak telat' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card m-1">
  <div class="card-body p-2">
    <h6>Daftar Izin</h6>
    <div class="table-responsive p-0">
      <table id="recap-permits-table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($permits as $permit)
          <tr>
            <td>
              {{ $permit->santri->user->fullname }} ({{ $permit->reason }})
            </td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@else
@if(sizeof($presencesInDate) > 0)
<div class="card m-1">
  <div class="card-body p-2">
    <div class="alert alert-info text-white">Pilih presensi untuk mulai melihat.</div>
  </div>
</div>
@endif
@endif

@if($presence)
<script>
  $('#recap-table').DataTable({
    order: [
      [1, 'desc']
    ],
    paging: false
  });

  $('#recap-permits-table').DataTable({
    order: [
      [1, 'desc']
    ],
    paging: false
  });
</script>
@endif
@include('base.end')
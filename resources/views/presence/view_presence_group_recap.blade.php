@include('base.start', ['path' => 'presensi/list', 'title' => 'Lihat Rekap Grup Presensi ' . $presenceGroup->name, 'breadcrumbs' => ['Daftar Presensi', 'Presensi Maghrib', 'Lihat Rekap'],
'backRoute' => route('view presence group', isset($presenceGroup) ? $presenceGroup->id : '')
])
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@if(isset($presenceGroup))
<div class="card">
  <div class="card-body pt-4 p-3 d-flex">
    <div class="d-flex flex-column">
      <h6>Grup Presensi: {{ $presenceGroup->name }}</h6>
      <span class="mb-2 text-xs">Jadwal: <span class="text-dark font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span></span>
      <span class="mb-2 text-xs">Jadwal jam buka: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span></span>
      <span class="text-xs">Jadwal jam tutup: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour  }}</span></span>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-header pb-0">
    <h6>Pilih bulan dan tahun</h6>
  </div>
  <div class="card-body pt-0 pb-2">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Dari tanggal</label>
          <input type="date" class="form-control" id="from-date" value="{{ isset($fromDate) ? $fromDate : '' }}">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Sampai tanggal</label>
          <input type="date" class="form-control" id="to-date" value="{{ isset($toDate) ? $toDate : '' }}">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Lorong</label>
          <select class="form-control" id="lorongId">
            <option value="all">Semua</option>
            @foreach($lorongs as $lorong)
            <option value="{{ $lorong->id }}" {{ isset($lorongId) ? ($lorong->id == $lorongId ? 'selected' : '') : '' }}>{{ $lorong->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <button class="form-control btn btn-primary" id="refresh">Refresh</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Rekap Umum {{ isset($fromDate, $toDate) ? $fromDate . ' - ' . $toDate : '' }}</h6>
    <button href="" onclick="download_table_as_csv('recap-table-general')" class="btn btn-primary">
      <i class="fas fa-download" aria-hidden="true"></i>
      Download CSV
    </button>
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    <div class="table-responsive p-0">
      <table class="table align-items-center mb-0" id="recap-table-general">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rata-rata Kehadiran</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Izin</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($fromDate, $toDate))
          <?php $summary = $presenceGroup->summary_in_range($fromDate, $toDate); ?>
          <tr>
            <td>
              {{ number_format((float)$summary['avg_present_percentage'], 2, '.', ''); }}%
            </td>
            <td>
              {{ $summary['total_permits'] }}
            </td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6>Rekap Per Orang {{ isset($fromDate, $toDate) ? $fromDate . ' - ' . $toDate : '' }}</h6>
    <button href="" onclick="download_table_as_csv('recap-table')" class="btn btn-primary">
      <i class="fas fa-download" aria-hidden="true"></i>
      Download CSV
    </button>
  </div>
  <div class="card-body px-0 pt-0 pb-2">
    <div class="table-responsive p-4">
      <table class="table align-items-center mb-0" id="recap-table">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Presensi</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Izin</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($fromDate, $toDate))
          @foreach($santris as $santri)
          <tr>
            <td>
              <div class="d-flex px-2 py-1">
                <div>
                  <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ $santri->user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td class="text-center">
              {{ number_format($santri->recapPresentsByRange($fromDate, $toDate, $presenceGroup->id)['percentage'], 2, '.', '') }}%
            </td>
            <td class="align-middle text-center text-sm">
              {{ $santri->recapPresentsByRange($fromDate, $toDate, $presenceGroup->id)['totalPermits'] }}
            </td>
          </tr>
          @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">Grup presensi tidak ditemukan.</div>
  </div>
</div>
@endif

@if(isset($presenceGroup))
<script>
  $('#recap-table').DataTable({
    // order: [[1, 'desc']],
    paging: false
  });

  $('#refresh').click(() => {
    const fromDate = $('#from-date').val();
    const toDate = $('#to-date').val();
    const lorongId = $('#lorongId').val();

    location.replace(`{{ route("select presence group recap", $presenceGroup->id) }}/${fromDate}/${toDate}/${lorongId}`);
  })
</script>
@endif

@if(isset($fromDate, $toDate))
<script>
  // Quick and simple export target #table_id into a csv
  function download_table_as_csv(table_id, separator = ',') {
    // Select rows from table_id
    var rows = document.querySelectorAll('table#' + table_id + ' tr');
    // Construct csv
    var csv = [];
    for (var i = 0; i < rows.length; i++) {
      var row = [],
        cols = rows[i].querySelectorAll('td, th');
      for (var j = 0; j < cols.length; j++) {
        // Clean innertext to remove multiple spaces and jumpline (break csv)
        var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ')
        // Escape double-quote with double-double-quote (see https://stackoverflow.com/questions/17808511/properly-escape-a-double-quote-in-csv)
        data = data.replace(/"/g, '""');
        // Push escaped string
        row.push('"' + data + '"');
      }
      csv.push(row.join(separator));
    }
    var csv_string = csv.join('\n');
    // Download it
    var filename = `Rekap Presensi {{ $presenceGroup->name }} {{ $fromDate }} - {{ $toDate }} ${new Date().toLocaleDateString()}.csv`;
    var link = document.createElement('a');
    link.style.display = 'none';
    link.setAttribute('target', '_blank');
    link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>
@endif
@include('base.end')
@include('base.start', ['path' => 'presensi/izin/persetujuan', 'title' => 'Tolak / Terima Daftar Izin ' . (isset($lorong) ? $lorong->name : ''), 'breadcrumbs' => ['Daftar Izin', 'Daftar Izin ' . (isset($lorong) ? $lorong->name : '')]])
@if($santri && $lorong || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('superadmin'))
<div class="card">
  <div class="card-header p-2 align-items-center" style="background-color:#f6f9fc;">
    @role('superadmin|rj1|wk|koor lorong')
    <div class="row">
      <div class="col-md-12 col-sm-6">
        <a href="{{ route('create presence permit') }}" class="btn btn-primary btn-xs m-2 mb-0" style="float:right;">
          <i class="fas fa-plus" aria-hidden="true"></i> Buat izin
        </a>
      </div>
    </div>
    @endrole
    <div class="row">
      <div class="col-sm-12 col-md-3">
        <small>Pilih Tahun-Bulan</small>
        <select class="select_tb form-control" name="select_tb" id="select_tb">
          <option value="-">Silahkan Pilih</option>
          @foreach($tahun_bulan as $tbx)
          <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-12 col-md-3">
        <small>Tampilkan</small>
        <select class="select_show form-control" name="select_show" id="select_show">
          <option {{ ($status=='pending') ? 'selected': '' }} value="pending">Pending</option>
          <option {{ ($status=='rejected') ? 'selected': '' }} value="rejected">Rejected</option>
          <option {{ ($status=='approved') ? 'selected': '' }} value="approved">Approved</option>
          <option {{ ($status=='all') ? 'selected': '' }} value="all">All</option>
        </select>
      </div>
    </div>
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
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Alasan</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($permits))
          @foreach($permits as $permit)
          <tr class="text-sm">
            <td>
              <b>{{ $permit->santri->user->fullname }}</b>
              <br>
              <span class="text-xxs">{{ $permit->updated_at }}</span>
              <br>
              <span class="badge {{ $permit->status == 'pending' ? 'bg-gradient-secondary' : ($permit->status == 'approved' ? 'bg-gradient-success' : ($permit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
            </td>
            <td class="text-xs">
              <i><b>{{ $permit->presence->name }}</b></i>
              <br>
              <div class="mt-1 mb-1"><span class="text-primary">[{{ ucfirst($permit->reason_category) }}]</span> {{ $permit->reason }}</div>

              @if($permit->status=='rejected' || $permit->status=='pending')
              @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
              <a href="{{ route('approve presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-success btn-xs mb-0">Terima</a>
              @endif
              @elseif($permit->status=='approved')
              <a href="{{ route('reject presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-warning btn-xs mb-0">Tolak</a>
              @endif
              <a href="{{ route('delete presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id, 'tb' => $tb]) }}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Delete</a>
            </td>
            @endforeach
            @endif
          </tr>
        </tbody>
      </table>
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
  $('.select_tb').change((e) => {
    var status = $('#select_show').val();
    window.location.replace(`{{ url("/") }}/presensi/izin/persetujuan/${$(e.currentTarget).val()}/` + status)
  })
  $('.select_show').change((e) => {
    var tb = $('#select_tb').val();
    window.location.replace(`{{ url("/") }}/presensi/izin/persetujuan/` + tb + `/${$(e.currentTarget).val()}`)
  })
  $('#table').DataTable({
    order: [],
    pageLength: 25
  });
</script>
@include('base.end')
@include('base.start', ['path' => 'presensi/izin/list', 'title' => 'Daftar Izin', 'breadcrumbs' => ['Daftar Izin']])
<div class="card">
  <div class="card-header pb-1 align-items-center" style="background-color:#f6f9fc;">
    <div class="row">
      <div class="col-3">
        Pilih Tahun-Bulan
        <select class="select_tb form-control mb-3" name="select_tb" id="select_tb">
          <option value="-">Silahkan Pilih</option>
          @foreach($tahun_bulan as $tbx)
          <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive p-2">
      <table id="table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alasan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($permits as $permit)
          <tr class="text-sm">
            <td>
              <div class="d-flex px-2 py-1">
                <div>
                  <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-xs me-3" alt="user1">
                </div>
                <div class="d-flex flex-column justify-content-center font-weight-bolder">
                  {{ ($permit->santri) ? $permit->santri->user->fullname : '-' }}
                </div>
              </div>
            </td>
            <td>
              <i><b>{{ $permit->presence->name }}</b></i>
              <br>
              <span class="text-primary">[{{ ucfirst($permit->reason_category) }}]</span> {{ $permit->reason }}
            </td>
            <td>
              {{ $permit->updated_at }}
              <br>
              <span class="badge {{ $permit->status == 'pending' ? 'bg-gradient-secondary' : ($permit->status == 'approved' ? 'bg-gradient-success' : ($permit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<script>
  $('.select_tb').change((e) => {
    window.location.replace(`{{ url("/") }}/presensi/izin/list/${$(e.currentTarget).val()}`)
  })
  $('#table').DataTable({
    order: [],
    pageLength: 25
  });
</script>
@include('base.end')
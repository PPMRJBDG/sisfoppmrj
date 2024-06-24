<div class="card">
  <div class="card-header pb-1 align-items-center" style="background-color:#f6f9fc;">
    <div class="row">
      <div class="col-md-3 col-sm-6">
        Pilih Tahun-Bulan
        <select data-mdb-filter="true" class="select select_tb form-control mb-3" name="select_tb" id="select_tb">
          <option value="-">Silahkan Pilih</option>
          @foreach($tahun_bulan as $tbx)
          <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="card-body p-2">
    <div class="datatable table-responsive p-2">
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
              <b>{{ ($permit->santri) ? $permit->santri->user->fullname : '-' }}</b>
              <br>
              {{ $permit->presence->name }}
            </td>
            <td>
              <span class="text-primary text-xs font-weight-bolder">[{{ ucfirst($permit->reason_category) }}]</span>
              <br>
              {{ $permit->reason }}
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
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $('.select_tb').change((e) => {
    getPage(`{{ url("/") }}/presensi/izin/list/${$(e.currentTarget).val()}`)
  })

  $('#table').DataTable({
    order: [],
    pageLength: 25
  });
</script>
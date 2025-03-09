@if(isset($presenceGroup))
<div class="card shadow border p-2">
  <div class="">
    <div class="">
      <h6>Tujuan Grup Presensi: {{ $presenceGroup->name }}</h6>
      <span class="mb-2 font-weight-bolder">Jadwal:</span> <span class="font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span>
      <br>
      <span class="mb-2 font-weight-bolder">Jadwal jam buka:</span> <span class="ms-sm-2 badge badge-secondary">{{ $presenceGroup->start_hour }}</span>
      <br>
      <span class="font-weight-bolder">Jadwal jam tutup:</span> <span class="ms-sm-2 badge badge-secondary">{{ $presenceGroup->end_hour  }}</span>
    </div>
  </div>
</div>
<div class="card shadow border mt-2">
  <div class="card-body p-2">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <form action="{{ route('store presence in group', $presenceGroup->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama presensi</label>
            <input class="form-control" type="text" required name="name" placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Tanggal acara</label>
            <input class="form-control" type="date" required name="event_date">
          </div>
        </div>
      </div>

      <hr class="horizontal dark">

      <div class="row">
        <div class="col-md-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="customCheck1" name="is_date_time_limited">
            <label class="custom-control-label" for="customCheck1">Atur waktu buka/tutup</label>
          </div>
        </div>
      </div>
      <div class="row mt-3" id="start-end-date-time-inputs" style="display: none">
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Mulai KBM</label>
            <input class="form-control" type="datetime-local" name="start_date_time">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Selesai KBM</label>
            <input class="form-control" type="datetime-local" name="end_date_time">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Display Fingerprint (On)</label>
            <input class="form-control" type="datetime-local" name="presence_start_date_time">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Display Fingerprint (Off)</label>
            <input class="form-control" type="datetime-local" name="presence_end_date_time">
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary btn-block" type="submit" value="Submit">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@else
<div class="card">
  <div class="card-body pt-4 p-2">
    <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
  </div>
</div>
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $('[name="is_date_time_limited"]').click(() => {
    if ($('[name="is_date_time_limited"]').is(':checked')) {
      $('[name="start_date_time"], [name="end_date_time"]').attr('required', true);
      $('#start-end-date-time-inputs').show();
    } else {
      $('[name="start_date_time"], [name="end_date_time"]').removeAttr('required');
      $('#start-end-date-time-inputs').hide();
    }
  })
</script>
<div class="card">
  <div class="card-body pt-4 p-2">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <form action="{{ route('store presence') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama presensi</label>
            <input class="form-control" type="text" name="name" placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Tanggal acara</label>
            <input class="form-control" type="date" name="event_date">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Grup presensi (opsional)</label>
            <select class="form-control" type="text" name="fkPresence_group_id">
              <option value="" selected>Tidak masuk grup manapun</option>
              @foreach($presenceGroups as $presenceGroup)
              <option value="{{ $presenceGroup->id }}">{{ $presenceGroup->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <hr class="horizontal dark">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-check">
            <label class="custom-control-label" for="customCheck1">Atur waktu buka/tutup</label>
            <input class="form-check-input" type="checkbox" name="is_date_time_limited">
          </div>
        </div>
      </div>
      <div class="row ms-4" id="start-end-date-time-inputs" style="display: none">
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Waktu buka</label>
            <input class="form-control" type="datetime-local" name="start_date_time">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Waktu tutup</label>
            <input class="form-control" type="datetime-local" name="end_date_time">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Submit">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

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
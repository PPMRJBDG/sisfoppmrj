
<div class="card shadow border">
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

    <form action="{{ route('store presence group') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama grup presensi</label>
            <input class="form-control" type="text" name="name" required placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>

      <div class="row">
        <label class="custom-control-label">Jadwal</label>
      </div>
      <div class="row ms-4">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-monday" name="days[]" type="checkbox" value="monday">
              <label class="custom-control-label" for="cc-monday">Senin</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-tuesday" name="days[]" type="checkbox" value="tuesday">
              <label class="custom-control-label" for="cc-tuesday">Selasa</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-wednesday" name="days[]" type="checkbox" value="wednesday">
              <label class="custom-control-label" for="cc-wednesday">Rabu</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-thursday" name="days[]" type="checkbox" value="thursday">
              <label class="custom-control-label" for="cc-thursday">Kamis</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-friday" name="days[]" type="checkbox" value="friday">
              <label class="custom-control-label" for="cc-friday">Jumat</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-saturday" name="days[]" type="checkbox" value="saturday">
              <label class="custom-control-label" for="cc-saturday">Sabtu</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <input class="form-check-input" id="cc-sunday" name="days[]" type="checkbox" value="sunday">
              <label class="custom-control-label" for="cc-sunday">Minggu</label>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-check">
            <input class="form-check-input" id="is_date_time_limited" type="checkbox" name="is_date_time_limited">
            <label class="custom-control-label" for="is_date_time_limited">Atur waktu buka/tutup</label>
          </div>
        </div>
      </div>
      <div class="row mt-3" id="start-end-date-time-inputs" style="display: none">
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Waktu buka</label>
            <input class="form-control" type="time" name="start_hour">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Waktu tutup</label>
            <input class="form-control" type="time" name="end_hour">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-check">
            <input class="form-check-input" id="show_summary_at_home" name="show_summary_at_home" type="checkbox">
            <label class="custom-control-label" for="show_summary_at_home">Tampilkan summary pada Dashboard</label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Status</label>
            <select data-mdb-filter="true" class="select form-control" name="status">
              <option value="active">Aktif</option>
              <option value="inactive">Non Aktif</option>
            </select>
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
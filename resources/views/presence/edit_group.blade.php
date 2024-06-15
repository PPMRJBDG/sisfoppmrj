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
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    @if(isset($presenceGroup))
    <form action="{{ route('update presence group', $presenceGroup->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama grup presensi</label>
            <input class="form-control" name="name" value="{{ $presenceGroup->name }}" type="text" placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>

      <hr class="horizontal dark">

      <div class="row">
        <label class="custom-control-label">Jadwal</label>
      </div>
      <div class="row ms-4">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Senin</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="monday" {{ in_array('monday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Selasa</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="tuesday" {{ in_array('tuesday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Rabu</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="wednesday" {{ in_array('wednesday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Kamis</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="thursday" {{ in_array('thursday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Jumat</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="friday" {{ in_array('friday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Sabtu</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="saturday" {{ in_array('saturday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Minggu</label>
              <input class="form-check-input" name="days[]" type="checkbox" value="sunday" {{ in_array('sunday', $presenceGroup->array_days()) ? 'checked' : '' }}>
            </div>
          </div>
        </div>
      </div>

      <div>
        <hr class="horizontal dark">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Atur waktu mulai/selesai KBM</label>
              <input class="form-check-input" type="checkbox" name="is_date_time_limited" {{ isset($presenceGroup->start_hour) && isset($presenceGroup->end_hour) ? 'checked' : '' }}>
            </div>
          </div>
        </div>
        <div class="row ms-4" id="start-end-date-time-inputs" style="display: {{ isset($presenceGroup->start_hour) && isset($presenceGroup->end_hour) ? '' : 'none' }}">
          <div class="col-md-6">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Mulai KBM</label>
              <input class="form-control" type="time" name="start_hour" value="{{ isset($presenceGroup->start_hour) ? $presenceGroup->start_hour : '' }}">
              <label for="example-text-input" class="form-control-label">Display Barcode (On)</label>
              <input class="form-control" type="time" name="presence_start_hour" value="{{ isset($presenceGroup->presence_start_hour) ? $presenceGroup->presence_start_hour : '' }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Selesai KBM</label>
              <input class="form-control" type="time" name="end_hour" value="{{ isset($presenceGroup->end_hour) ? $presenceGroup->end_hour : '' }}">
              <label for="example-text-input" class="form-control-label">Display Barcode (Off)</label>
              <input class="form-control" type="time" name="presence_end_hour" value="{{ isset($presenceGroup->presence_end_hour) ? $presenceGroup->presence_end_hour : '' }}">
            </div>
          </div>
        </div>
        <hr class="horizontal dark">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Tampilkan summary pada Dashboard</label>
              <input class="form-check-input" name="show_summary_at_home" type="checkbox" {{ $presenceGroup->show_summary_at_home ? 'checked' : '' }}>
            </div>
          </div>
        </div>

        <hr class="horizontal dark">

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Status</label>
              <select class="form-control" name="status">
                <option value="active" {{ $presenceGroup->status == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $presenceGroup->status == 'inactive' ? 'selected' : '' }}>Non Aktif</option>
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
      </div>
    </form>
    @else
    <div class="alert alert-danger text-white">
      Grup presensi tidak ditemukan.
    </div>
    @endif
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
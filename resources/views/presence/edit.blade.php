
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
    @if(isset($presence))
    <form action="{{ route('update presence', $presence->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama presensi</label>
            <input class="form-control" type="text" name="name" value="{{ $presence->name }}" placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Tanggal acara</label>
            <input class="form-control" type="date" name="event_date" value="{{ $presence->event_date }}">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Grup presensi (opsional)</label>
            <select data-mdb-filter="true" class="select form-control" type="text" name="fkPresence_group_id" value="{{ $presence->fkPresence_group_id }}">
              <option value="" selected>Tidak masuk grup manapun</option>
              @foreach($presenceGroups as $presenceGroup)
              <option value="{{ $presenceGroup->id }}" {{ $presence->fkPresence_group_id == $presenceGroup->id ? 'selected' : '' }}>{{ $presenceGroup->name }}</option>
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
            <input class="form-check-input" type="checkbox" name="is_date_time_limited" {{ isset($presence->start_date_time) && isset($presence->end_date_time) ? 'checked' : '' }}>
          </div>
        </div>
      </div>
      <div class="row ms-4" id="start-end-date-time-inputs" style="display: {{ isset($presence->start_date_time) && isset($presence->end_date_time) ? '' : 'none' }}">
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Mulai KBM</label>
            <input class="form-control" type="datetime-local" name="start_date_time" value="{{ isset($presence->start_date_time) ? Carbon\Carbon::parse($presence->start_date_time)->format('Y-m-d\TH:i') : '' }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Selesai KBM</label>
            <input class="form-control" type="datetime-local" name="end_date_time" value="{{ isset($presence->end_date_time) ? Carbon\Carbon::parse($presence->end_date_time)->format('Y-m-d\TH:i') : '' }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Fingerprint Display (On)</label>
            <input class="form-control" type="datetime-local" name="presence_start_date_time" value="{{ isset($presence->presence_start_date_time) ? Carbon\Carbon::parse($presence->presence_start_date_time)->format('Y-m-d\TH:i') : '' }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Fingerprint Display (Off)</label>
            <input class="form-control" type="datetime-local" name="presence_end_date_time" value="{{ isset($presence->presence_end_date_time) ? Carbon\Carbon::parse($presence->presence_end_date_time)->format('Y-m-d\TH:i') : '' }}">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Ubah">
          </div>
        </div>
      </div>
    </form>
    @else
    <div class="alert alert-danger text-white">
      Presensi tidak ditemukan.
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
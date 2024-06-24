@if(isset($lorong))
<div class="card">
  <div class="card-body pt-4 p-2 d-flex">
    <div class="d-flex flex-column">
      <h6>Lorong Tujuan: {{ $lorong->name }}</h6>
      <span class="mb-2 text-xs">Koor: <span class="text-dark ms-sm-2 font-weight-bold">{{ $lorong->leader->user->fullname }}</span></span>
      <span class="text-xs">Anggota: <span class="text-dark ms-sm-2 font-weight-bold">{{ sizeof($lorong->members) }} orang</span></span>
    </div>
  </div>
</div>
<div class="card shadow border mt-4">
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
    <form action="{{ route('store lorong member', $lorong->id) }}">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama</label>
            <select data-mdb-filter="true" name="santri_id" class="select form-control">
              @foreach($santris as $santri)
              <option value="{{ $santri->id }}">{{ $santri->user->fullname }}</option>
              @endforeach
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
@else
<div class="card">
  <div class="card-body pt-4 p-2">
    <div class="alert alert-danger text-white">Lorong tidak ditemukan.</div>
  </div>
</div>
@endif

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $('#with-schedule').click(() => {
    if ($('#with-schedule').is(':checked')) {
      $('#schedule-list').show();
      $('#start-end-hour').show();

      $('[name="start-hour"], [name="end-hour"]').prop('type', 'time');
    } else {
      $('#schedule-list').hide();
      $('#start-end-hour').show();

      $('[name="start-hour"], [name="end-hour"]').prop('type', 'datetime-local');
    }
  });

  $('[name="is-hours-scheduled"]').click(() => {
    if ($('[name="is-hours-scheduled"]').is(':checked')) {
      $('[name="start-hour"], [name="end-hour"]').attr('required', true);
      $('#start-end-hour-inputs').show();
    } else {
      $('[name="start-hour"], [name="end-hour"]').removeAttr('required');
      $('#start-end-hour-inputs').hide();
    }
  })
</script>
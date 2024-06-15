@if(isset($presence))
@if(isset($santri))
<div class="card">
  <div class="card-body pt-4 p-3 d-flex">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="d-flex flex-column">
      <div class="d-flex gap-3 justify-content-start">
        <span>
          <h6>{{ $presence->name }}</h6>
        </span>
        <div>
          <span class="badge {{ $status == 'terbuka' || $status == 'sudah presensi' ? 'bg-gradient-success' : ($status == 'tutup' ? 'bg-gradient-secondary' : 'bg-gradient-danger' ) }}">{{ $status }}</span>
        </div>
      </div>
      <span class="mb-2 text-xs">Waktu buka: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presence->start_date_time ? $presence->start_date_time : 'Tidak ada'}}</span></span>
      <span class="text-xs">Waktu tutup: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presence->end_date_time ? $presence->end_date_time : 'Tidak ada'}}</span></span>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-body pt-4 p-3">
    <form action="{{ route('store my present', $presence->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn {{ $status == 'terbuka' ? 'btn-success' : 'btn-secondary' }} form-control" type="submit" value="Presensi atas nama Bagus Seno" {{ $status == 'terbuka' ? '' : 'disabled' }}>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">User ini bukanlah santri.</div>
  </div>
</div>
@endif
@else
<div class="card">
  <div class="card-body pt-4 p-3">
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
@include('base.start', ['path' => 'presensi/list', 'title' => 'Tambah Presensi', 'breadcrumbs' => ['Daftar Presensi', 'Tambah Presensi'],
  'backRoute' => route('view presence group', isset($presenceGroup) ? $presenceGroup->id : '')
])
  @if(isset($presenceGroup))
    <div class="card">
      <div class="card-body pt-4 p-3 d-flex">
        <div class="d-flex flex-column">
          <h6>Tujun Grup Presensi: {{ $presenceGroup->name }}</h6>
          <span class="mb-2 text-xs">Jadwal: <span class="text-dark font-weight-bold ms-sm-2">{{ ucwords($presenceGroup->days_in_bahasa()) }}</span></span>
          <span class="mb-2 text-xs">Jadwal jam buka: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->start_hour }}</span></span>
          <span class="text-xs">Jadwal jam tutup: <span class="text-dark ms-sm-2 font-weight-bold">{{ $presenceGroup->end_hour  }}</span></span>
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-body pt-4 p-3">
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
    @else
      <div class="card">
        <div class="card-body pt-4 p-3">
          <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
        </div>
      </div>
    @endif
  <script>
    $('[name="is_date_time_limited"]').click(() =>
    {
      if($('[name="is_date_time_limited"]').is(':checked'))
      {
        $('[name="start_date_time"], [name="end_date_time"]').attr('required', true); 
        $('#start-end-date-time-inputs').show();
      }
      else
      {
        $('[name="start_date_time"], [name="end_date_time"]').removeAttr('required'); 
        $('#start-end-date-time-inputs').hide();
      }
    })
  </script>
@include('base.end')

@include('base.start', ['path' => 'presensi/list', 'title' => 'Tambah Grup Presensi', 'breadcrumbs' => ['Daftar Presensi', 'Tambah Grup Presensi'],
  'backRoute' => route('presence tm')
])
  <div class="card">
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
      <form action="{{ route('store presence group') }}" method="post">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nama grup presensi</label>
              <input class="form-control" type="text" name="name" placeholder="Contoh: Pengajian Maghrib">
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
                <input class="form-check-input" name="days[]" type="checkbox" value="monday">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Selasa</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="tuesday">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Rabu</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="wednesday">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Kamis</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="thursday">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Jumat</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="friday">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Sabtu</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="saturday">
              </div>
            </div>            
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Minggu</label>
                <input class="form-check-input" name="days[]" type="checkbox" value="sunday">
              </div>
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
        <hr class="horizontal dark">  
        <div class="row">
          <div class="col-md-12">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Tampilkan summary pada Dashboard</label>
              <input class="form-check-input" name="show_summary_at_home" type="checkbox">
            </div>
          </div>
        </div>

        <hr class="horizontal dark">

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Status</label>
              <select class="form-control" name="status">
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

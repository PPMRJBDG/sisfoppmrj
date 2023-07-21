@include('base.start', ['path' => 'lorong/list', 'title' => 'Ubah Lorong', 'breadcrumbs' => ['Daftar Lorong', 'Ubah Lorong']
  ,'backRoute' => url()->previous() ? (url()->previous() != url()->current() ? url()->previous() : route('lorong tm')) : route('lorong tm')
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
      @if (session('success'))
        <div class="alert alert-success text-white">
          {{ session('success') }}
        </div>
      @endif
      @if(isset($lorong))
        <form action="{{ route('update lorong', $lorong->id) }}" method="post">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="example-text-input" class="form-control-label">ID</label>
                <input class="form-control" type="text" value="{{ $lorong->id }}" required readonly>
              </div>
            </div>
          </div>  
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="example-text-input" class="form-control-label">Nama</label>
                <input class="form-control" type="text" name="name" value="{{ $lorong->name }}" placeholder="Contoh: Lorong Balkon">
              </div>
            </div>
          </div>     
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="example-text-input" class="form-control-label">Santri ketua</label>
                <select class="form-control" name="fkSantri_leaderId">
                  @foreach($users as $user)
                    <option value="{{ $user->santri->id }}" {{ $user->santri->id == $lorong->leader->id ? 'selected' : '' }}>{{ $user->santri->user->fullname }} - {{ $user->santri->angkatan }}</option>
                  @endforeach
                  @if (sizeof($users) == 0) 
                    <option>Belum ada santri.</option>
                  @endif
                </select>
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
          Lorong tidak ditemukan.
        </div>
      @endif
    </div>
  </div>
  <script>
    $('#with-schedule').click(() =>
    {
      if($('#with-schedule').is(':checked'))
      {
        $('#schedule-list').show();
        $('#start-end-hour').show();
        
        $('[name="start-hour"], [name="end-hour"]').prop('type', 'time');      
      }
      else
      {
        $('#schedule-list').hide();
        $('#start-end-hour').show();

        $('[name="start-hour"], [name="end-hour"]').prop('type', 'datetime-local');
      }
    });

    $('[name="is-hours-scheduled"]').click(() =>
    {
      if($('[name="is-hours-scheduled"]').is(':checked'))
      {
        $('[name="start-hour"], [name="end-hour"]').attr('required', true); 
        $('#start-end-hour-inputs').show();
      }
      else
      {
        $('[name="start-hour"], [name="end-hour"]').removeAttr('required'); 
        $('#start-end-hour-inputs').hide();
      }
    })
  </script>
@include('base.end')

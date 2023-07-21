@include('base.start', ['path' => 'materi/list', 'title' => 'Tambah Materi', 'breadcrumbs' => ['Daftar Materi', 'Tambah Materi']
  ,'backRoute' => route('materi tm')
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
      <form action="{{ route('store materi') }}" method="post">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nama</label>
              <input class="form-control" type="text" name="name" placeholder="Contoh: K. Nikah">
            </div>
          </div>
        </div>    
        
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Jumlah halaman</label>
              <input class="form-control" type="text" name="pageNumbers" placeholder="Contoh: 150">
            </div>
          </div>
        </div>    

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Untuk</label>
              <select class="form-control" name="for">
                  <option value="mubalegh">Mubalegh</option>
                  <option value="reguler">Reguler</option>
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
@include('base.end')

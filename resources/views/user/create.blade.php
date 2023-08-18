@include('base.start', ['path' => 'user/list', 'title' => 'Tambah User', 'breadcrumbs' => ['Daftar User', 'Tambah User']
,'backRoute' => url()->previous() ? url()->previous() : route('user tm')
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
    <form action="{{ route('store user') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama Lengkap</label>
            <input class="form-control" type="text" name="fullname" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Jenis Kelamin</label>
            <select class="form-control" name="gender" required>
              <option value="female">Perempuan</option>
              <option value="male">Laki-laki</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Tanggal lahir</label>
            <input class="form-control" type="date" name="birthdate" placeholder="Contoh: 27/03/2000" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nomor HP (WA)</label>
            <input class="form-control" type="text" name="nohp" placeholder="Contoh: 082312345678" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Email</label>
            <input class="form-control" type="email" name="email" placeholder="Contoh: user@example.com" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Password</label>
            <input class="form-control" type="password" name="password" placeholder="Contoh: bismillah354" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Foto profil</label>
            <input class="form-control" type="file" name="profileImg">
          </div>
        </div>
      </div>
      <div class="row">
        <label class="custom-control-label">Role</label>
      </div>
      <div class="row ms-4">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Santri</label>
              <input class="form-check-input" type="checkbox" id="role-santri" name="role-santri">
            </div>
          </div>
          <!-- <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Superadmin</label>
              <input class="form-check-input" type="checkbox" name="role-superadmin">
            </div>
          </div> -->
          <!-- <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Dewan Guru</label>
              <input class="form-check-input" type="checkbox" name="role-dewan-guru">
            </div>
          </div> -->
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">RJ1</label>
              <input class="form-check-input" type="checkbox" name="role-rj1">
            </div>
          </div>
          <!-- <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Pengabsen</label>
              <input class="form-check-input" type="checkbox" name="role-pengabsen">
            </div>
          </div> -->
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">WK</label>
              <input class="form-check-input" type="checkbox" name="role-wk">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Sekretaris</label>
              <input class="form-check-input" type="checkbox" name="role-sekretaris">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi IT</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-it">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Kurikulum</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-kurikulum">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Kebersihan</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-kebersihan">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Sarpras</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-sarpras">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Olahraga</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-olahraga">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Kreatif</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-kreatif">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi PT</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-pt">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Asad</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-asad">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Divisi Keamanan</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-keamanan">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Mubalegh</label>
              <input class="form-check-input" type="checkbox" name="role-mubalegh">
            </div>
          </div>
          @if(auth()->user()->hasRole('superadmin'))
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">KU</label>
              <input class="form-check-input" type="checkbox" name="role-ku">
            </div>
          </div>
          @endif
        </div>
      </div>
      <section id="santri-data-section" style="display: none">
        <hr class="horizontal dark">
        <p class="text-uppercase text-sm">Data Santri</p>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nama Ortu (Bapak/Ibu)</label>
              <input class="form-control" type="text" name="nama_ortu" placeholder="Contoh: Tamara Zayya">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nomor HP Ortu (WA)</label>
              <input class="form-control" type="text" name="nohp_ortu" placeholder="Contoh: 082312345678">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Angkatan</label>
              <input class="form-control" type="text" name="angkatan" placeholder="Contoh: 2022">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Tanggal Masuk</label>
              <input class="form-control" type="date" name="join_at" placeholder="Contoh: 24/09/2022" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">NIS</label>
              <input class="form-control" type="number" name="nis" placeholder="Contoh: 39012930123">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Lorong</label>
              <select class="form-control" name="fkLorong_id">
                <option value="" selected>Tidak masuk lorong manapun.</option>
                @foreach($lorongs as $lorong)
                <option value="{{ $lorong->id }}">{{ $lorong->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </section>

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
  $('#role-santri').click(() => {
    if ($('#role-santri').is(':checked')) {
      $('#santri-data-section').show();

      $('[name="angkatan"').attr('required', true);
      $('[name="nis"').attr('required', true);
    } else {
      $('#santri-data-section').hide();

      $('[name="angkatan"').attr('required', false);
      $('[name="nis"').attr('required', false);
    }
  });
</script>
@include('base.end')
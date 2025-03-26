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


<div class="card shadow border p-2">
  <div class="card-body p-2">
    @if(isset($user))
    <form action="{{ route('update user', $user->id) }}" method="post" id="upload-file" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">ID User</label>
            <input class="form-control" type="text" value="{{ $user->id }}" required readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama Lengkap</label>
            <input class="form-control" type="text" name="fullname" value="{{ $user->fullname }}" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Jenis Kelamin</label>
            <select data-mdb-filter="true" class="select form-control" name="gender" required>
              <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Perempuan</option>
              <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Laki-laki</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Tanggal lahir</label>
            <input class="form-control" type="date" name="birthdate" value="{{ $user->birthdate }}" placeholder="Contoh: Pengajian Maghrib" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nomor HP (WA)</label>
            <input class="form-control" type="text" name="nohp" value="{{ $user->nohp }}" placeholder="Contoh: 082312345678" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Email</label>
            <input class="form-control" type="email" name="email" value="{{ $user->email }}" placeholder="Contoh: user@example.com" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">New Password</label>
            <input class="form-control" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Foto profil (kosongkan jika tidak ingin diganti)</label>
            @if(isset($user->profileImgUrl))
            <div class="alert alert-info text-white">
              <div>
                Foto profile saat ini
              </div>
              <img style="width: 256px" src="{{ url('storage/users/' . $user->profileImgUrl) }}" alt="">
            </div>
            @endif
            <input class="form-control" type="file" name="profileImg" placeholder="Contoh: Pengajian Maghrib">
          </div>
        </div>
      </div>
      <div class="row">
        <label class="custom-control-label">Role</label>
      </div>
      <div class="row ms-4">
        <div class="row">
          @if(auth()->user()->hasRole('superadmin'))
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Superadmin</label>
                <input class="form-check-input" type="checkbox" name="role-superadmin" value="superadmin" {{ $user->hasRole(['superadmin']) ? 'checked' : '' }}>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-check">
                <label class="custom-control-label" for="customCheck1">Dewan Guru</label>
                <input class="form-check-input" type="checkbox" name="role-dewan-guru" {{ $user->hasRole(['dewan guru']) ? 'checked' : '' }}>
              </div>
            </div>
          @endif
          <!-- <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Pengabsen</label>
              <input class="form-check-input" type="checkbox" name="role-pengabsen" {{ $user->hasRole(['pengabsen']) ? 'checked' : '' }}>
            </div>
          </div> -->
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Santri</label>
              <input class="form-check-input" type="checkbox" name="role-santri" id="role-santri" value="santri" {{ $user->hasRole(['santri']) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">RJ1</label>
              <input class="form-check-input" type="checkbox" name="role-rj1" {{ $user->hasRole(['rj1']) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">WK</label>
              <input class="form-check-input" type="checkbox" name="role-wk" {{ $user->hasRole(['wk']) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Kurikulum</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-kurikulum" {{ $user->hasRole(['divisi kurikulum']) ? 'checked' : '' }}>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Keamanan</label>
              <input class="form-check-input" type="checkbox" name="role-divisi-keamanan" {{ $user->hasRole(['divisi keamanan']) ? 'checked' : '' }}>
            </div>
          </div>

          @if(auth()->user()->hasRole('superadmin'))
            <div style="display:none;">
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Sekretaris</label>
                  <input class="form-check-input" type="checkbox" name="role-sekretaris" {{ $user->hasRole(['sekretaris']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi IT</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-it" {{ $user->hasRole(['divisi it']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi Kebersihan</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-kebersihan" {{ $user->hasRole(['divisi kebersihan']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi Sarpras</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-sarpras" {{ $user->hasRole(['divisi sarpras']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi Olahraga</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-olahraga" {{ $user->hasRole(['divisi olahraga']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi Kreatif</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-kreatif" {{ $user->hasRole(['divisi kreatif']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi PT</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-pt" {{ $user->hasRole(['divisi pt']) ? 'checked' : '' }}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label" for="customCheck1">Divisi Asad</label>
                  <input class="form-check-input" type="checkbox" name="role-divisi-asad" {{ $user->hasRole(['divisi asad']) ? 'checked' : '' }}>
                </div>
              </div>
            </div>
          @endif

          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">Mubalegh</label>
              <input class="form-check-input" type="checkbox" name="role-mubalegh" {{ $user->hasRole(['mubalegh']) ? 'checked' : '' }}>
            </div>
          </div>
          @if(auth()->user()->hasRole('superadmin'))
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label" for="customCheck1">KU</label>
              <input class="form-check-input" type="checkbox" name="role-ku" {{ $user->hasRole(['ku']) ? 'checked' : '' }}>
            </div>
          </div>
          @endif
        </div>
      </div>
      <section id="santri-data-section" style="display: {{ $user->hasRole(['santri']) ? '' : 'none' }}">
        <hr class="horizontal dark">
        <p class="text-uppercase text-sm">Data Santri</p>
        @if(isset($user->santri))
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">ID Santri & Fingerprint</label>
              <input class="form-control" type="text" name="santri_id" value="{{ $user->santri->id }}" required readonly>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Template Fingerprint</label>
              <input class="form-control" type="text" name="template_fs1" value="{{ isset($user->santri) ? $user->santri->template_fs1 : ''}}" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nama Ortu (Bapak/Ibu)</label>
              <input class="form-control" type="text" name="nama_ortu" value="{{ isset($user->santri) ? $user->santri->nama_ortu : ''}}" placeholder="Contoh: Tamara Zayya">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Nomor HP Ortu (WA)</label>
              <input class="form-control" type="text" name="nohp_ortu" value="{{ isset($user->santri) ? $user->santri->nohp_ortu : ''}}" placeholder="Contoh: 082312345678">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Angkatan</label>
              <input class="form-control" type="text" name="angkatan" value="{{ isset($user->santri) ? $user->santri->angkatan : ''}}" placeholder="Contoh: 2020">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Tanggal Masuk</label>
              <input class="form-control" type="date" name="join_at" value="{{ isset($user->santri) ? $user->santri->join_at : ''}}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Tanggal Keluar</label>
              <input class="form-control" type="date" name="exit_at" id="exit_at" value="{{ isset($user->santri) ? $user->santri->exit_at : ''}}">
              <small><a style='opacity:0.6;cursor:pointer;' onclick="$('#exit_at').val(null)">Kosongkan</a></small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Alasan Keluar</label>
              <select data-mdb-filter="true" class="select form-control" name="alasan_keluar" id="alasan_keluar">
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='0' ? 'selected' : '' }} value="0"></option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Dikeluarkan Dengan Cara Hormat' ? 'selected' : '' }} value="Dikeluarkan Dengan Cara Hormat">Dikeluarkan Dengan Cara Hormat</option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Dikeluarkan Karena SP3' ? 'selected' : '' }} value="Dikeluarkan Karena SP3">Dikeluarkan Karena SP3</option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Pindah KPM' ? 'selected' : '' }} value="Pindah KPM">Pindah KPM</option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Sudah Lulus' ? 'selected' : '' }} value="Sudah Lulus">Sudah Lulus</option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Meninggal Dunia' ? 'selected' : '' }} value="Meninggal Dunia">Meninggal Dunia</option>
                <option {{ isset($user->santri) && $user->santri->alasan_keluar=='Lain-lain' ? 'selected' : '' }} value="Lain-lain">Lain-lain</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">NIS</label>
              <input class="form-control" type="text" name="nis" value="{{ isset($user->santri) ? $user->santri->nis : '' }}" placeholder="Contoh: 123">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="example-text-input" class="form-control-label">Lorong</label>
              <select data-mdb-filter="true" class="select form-control" name="fkLorong_id">
                <option value="" selected>Tidak masuk lorong manapun.</option>
                @foreach($lorongs as $lorong)
                <option value="{{ $lorong->id }}" {{ isset($user->santri) && $user->santri->fkLorong_id == $lorong->id ? 'selected' : '' }}>{{ $lorong->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </section>

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
      User tidak ditemukan.
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

  $('#role-santri').click(() => {
    if ($('#role-santri').is(':checked')) {
      $('#santri-data-section').show();

      $('[name="angkatan"').attr('required', true);
      $('[name="nis"').attr('required', true);
    } else {
      $('#santri-data-section').hide();

      $('[name="angkatan"').attr('required', false);
      $('[name="nis"').attr('required', true);
    }
  });
</script>
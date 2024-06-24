<div class="card">
  <div class="card-body p-2">
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
    @if(isset($user))
    <br>
    <form action="{{ route('update my profile') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-outline" data-mdb-input-init>
            <input class="form-control" id="fullname" type="text" name="fullname" value="{{ $user->fullname }}" required>
            <label for="fullname" class="form-label">Nama Lengkap</label>
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
            <input class="form-control" type="date" name="birthdate" value="{{ $user->birthdate }}" placeholder="Contoh: 27/03/2000" required>
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
      <!-- <div class="row">
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
            <input class="form-control" type="file" name="profileImg">
          </div>
        </div>
      </div> -->

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
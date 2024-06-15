<div class="card">
  <div class="card-header p-2 d-flex justify-content-between align-items-center">
    <a href="#" class="font-weight-bolder m-0 btn btn-secondary">PROFILE USER</a>
    <a href="{{ route('edit my profile') }}" class="btn btn-primary m-0">
      <i class="fas fa-pen" aria-hidden="true"></i> Ubah profil
    </a>
  </div>
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
    @if(isset($user))
    <!-- <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Foto profil</label>
          @if(isset($user->profileImgUrl))
          <div class="alert alert-info text-white">
            <img style="width: 256px" src="{{ url('storage/users/' . $user->profileImgUrl) }}" alt="">
          </div>
          @else
          <div>
            Belum ada foto profil
          </div>
          @endif
        </div>
      </div>
    </div> -->
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Nama Lengkap</label>
          <input class="form-control" type="text" value="{{ $user->fullname }}" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Jenis Kelamin</label>
          <input class="form-control" type="text" value="{{ $user->gender }}" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Tanggal lahir</label>
          <input class="form-control" type="text" value="{{ $user->birthdate }}" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">No HP (WA)</label>
          <input class="form-control" type="text" value="{{ $user->nohp }}" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Email</label>
          <input class="form-control" type="text" value="{{ $user->email }}" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="custom-control-label">Role</label>
          <div class="form-control">
            @foreach($user->getRoleNames() as $roleName)
            <span class="badge bg-gradient-success">{{ $roleName }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <section id="santri-data-section" style="display: {{ $user->hasRole(['santri']) ? '' : 'none' }}">
      <hr class="horizontal dark">
      <a href="#" class="text-uppercase btn btn-sm btn-secondary">Data Santri</a>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Angkatan</label>
            <input class="form-control" type="text" value="{{ isset($user->santri) ? $user->santri->angkatan : '' }}" readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">NIS</label>
            <input class="form-control" type="text" value="{{ isset($user->santri) ? $user->santri->nis : '' }}" readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Lorong</label>
            <input class="form-control" type="text" value="{{ isset($user->santri->lorong) ? $user->santri->lorong->name : '' }}" readonly>
          </div>
        </div>
      </div>
    </section>
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
</script>
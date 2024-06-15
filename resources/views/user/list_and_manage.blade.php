<div class="card">
  @if($count_dashboard!='')
  <div class="card shadow-lg mb-0">
    <div class="card-body p-3">
      <p class="mb-0 text-sm font-weight-bolder btn btn-primary" onclick="showHideCacah()">Tampilkan Cacah Jiwa</p>
      <div id="toggle-cacahjiwa" style="display:none;">
        <?php echo $count_dashboard; ?>
      </div>
    </div>
  </div>
  @endif

  <div class="card-header pb-0 p-3">
    @can('create users')
    <a href="{{ route('create user') }}" class="btn btn-primary form-control mb-2">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat User
    </a>
    @endcan
    <h6 class="mt-1 mb-2 text-center text-sm">Data Mahasiswa {{$select_angkatan}}</h6>
    <div class="row">
      <div class="col-6 col-sm-6">
        <select class="angkatan-list form-control" name="" id="angkatan-list">
          <option value="-">Filter angkatan</option>
          @foreach($list_angkatan as $angkatan)
          <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-sm-6">
        <select class="role-list form-control" name="" id="role-list">
          <option value="-">Filter role</option>
          @foreach($list_role as $vrole)
          <option {{ ($select_role == $vrole->id) ? 'selected' : '' }} value="{{$vrole->id}}">{{$vrole->name}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="card-body p-3">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="table-responsive p-0">
      <table id="table" class="table align-items-center mb-0">
        <thead class="thead-light" style="background-color:#f6f9fc;">
          <tr class="list">
            <th class="text-center text-uppercase text-xxs font-weight-bolder">Action</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder">Nama</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">No HP</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Kelamin</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Tgl Lahir</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Angkatan</th>
            <!-- <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Nama Ortu</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">No HP Ortu</th> -->
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Role</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder ps-2">Lorong</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td class="align-middle text-center text-sm">
              @can('delete users')
              <!-- <a href="{{ route('delete user', $user->id) }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus {{ $user->fullname }}?')">Hapus</a> -->
              @endcan
              @can('update users')
              <a href="{{ route('edit user', $user->id) }}" class="btn btn-primary btn-sm mb-0">Edit</a>
              @endcan
            </td>
            <td class="text-sm" data-toggle="tooltip" data-placement="top" title="Klik unutk melihat report" onclick="getReport('<?php echo base64_encode($user->santri->id); ?>')" style="cursor:pointer;">
              <div class="d-flex px-2 py-1">
                <div>
                  <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <?php
                  $is_kl = false;
                  foreach ($user->getRoleNames() as $role) {
                    if ($role == 'koor lorong') {
                      $is_kl = true;
                    }
                  }
                  $unkl = '';
                  if ($user->santri->fkLorong_id == '' && !$is_kl) {
                    $unkl = 'text-warning';
                  }
                  foreach ($lorong as $l) {
                    if ($l->fkSantri_leaderId == $user->santri->id) {
                      $unkl = 'text-primary';
                    }
                  }
                  ?>
                  <h6 class="mb-0 text-sm {{ $unkl }}">{{ $user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td class="text-sm">
              {{ $user->nohp }}
            </td>
            <td class="text-sm">
              @if($user->gender == 'male')
              L
              @endif
              @if($user->gender == 'female')
              P
              @endif
            </td>
            <td class="text-sm">
              {{ $user->birthdate }}
            </td>
            <td class="text-sm">
              {{ $user->santri ? $user->santri->angkatan : 'Bukan santri' }}
            </td>
            <!-- <td>
              {{ $user->santri->nama_ortu }}
            </td>
            <td>
              {{ $user->santri->nohp_ortu }}
            </td> -->
            <td class="text-sm">
              @foreach ($user->getRoleNames() as $role)
              @if($role!='santri' && $role!='mubalegh')
              <span class="badge {{ $role=='koor lorong' ? 'bg-gradient-primary' : 'bg-gradient-success' }}">{{ $role }}</span>
              @endif
              @endforeach
            </td>
            <td class="text-sm">
              {{ $user->santri && $user->santri->lorong ? str_replace('Lorong', '', $user->santri->lorong->name) : '' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $('#table').DataTable({
    order: [
      // [1, 'desc']
    ],
    pageLength: 25
  });
  $('.angkatan-list').change((e) => {
    var role = $('#role-list').val()
    getPage(`{{ url("/") }}/user/list/santri/${$(e.currentTarget).val()}/` + role)
  })
  $('.role-list').change((e) => {
    var angkatan = $('#angkatan-list').val()
    getPage(`{{ url("/") }}/user/list/santri/` + angkatan + `/${$(e.currentTarget).val()}`)
  })
</script>
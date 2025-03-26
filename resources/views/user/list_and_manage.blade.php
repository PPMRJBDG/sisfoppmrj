<div class="card border">
  <div class="card-header p-2">
    @can('create users')
    <a href="{{ route('create user') }}" class="btn btn-primary form-control mb-2">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat User
    </a>
    @endcan
    <h6 class="mt-1 mb-2 text-center text-sm">Data Mahasiswa {{$select_angkatan}}</h6>
    <div class="row">
      <div class="col-6 col-sm-6">
        <select data-mdb-filter="true" class="select angkatan-list form-control" name="" id="angkatan-list">
          <option value="-">Filter angkatan</option>
          @foreach($list_angkatan as $angkatan)
          <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-sm-6">
        <select data-mdb-filter="true" class="select role-list form-control" name="" id="role-list">
          <option value="-">Filter role</option>
          @foreach($list_role as $vrole)
          <option {{ ($select_role == $vrole->id) ? 'selected' : '' }} value="{{$vrole->id}}">{{$vrole->name}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="card-body p-2">
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

    <div class="datatable datatable-sm p-0" data-mdb-entries="200">
      <table id="table" class="table align-items-center mb-0">
        <thead class="thead-light" style="background-color:#f6f9fc;">
          <tr class="list">
            <th class="text-center text-uppercase text-xxs font-weight-bolder">Action</th>
            <th class="text-uppercase sort text-xxs font-weight-bolder">Sync</th>
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
            <td class="text-sm">
              <a href="#" class="btn {{ ($user->santri->template_fs1!=null) ? 'btn-warning' : 'btn-danger' }} btn-sm mb-0">
                {{ ($user->santri->template_fs1!=null) ? 'Ok' : 'Nok'; }}
              </a>
            </td>
            <td class="text-sm" data-toggle="tooltip" data-placement="top" title="Klik unutk melihat report" onclick="getReport('<?php echo base64_encode($user->santri->id); ?>')" style="cursor:pointer;">
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
                <span class="mb-0 font-weight-bolder text-sm {{ $unkl }}">{{ $user->fullname }}</span>
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
              <span class="badge {{ $role=='koor lorong' ? 'bg-primary' : 'bg-success' }}">{{ $role }}</span>
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

  $('.angkatan-list').change((e) => {
    var role = $('#role-list').val()
    getPage(`{{ url("/") }}/user/list/santri/${$(e.currentTarget).val()}/` + role)
  })

  $('.role-list').change((e) => {
    var angkatan = $('#angkatan-list').val()
    getPage(`{{ url("/") }}/user/list/santri/` + angkatan + `/${$(e.currentTarget).val()}`)
  })
</script>
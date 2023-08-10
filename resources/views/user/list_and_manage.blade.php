@include('base.start', ['path' => 'user/list/santri', 'title' => 'Daftar User', 'breadcrumbs' => ['Daftar User']])
<div class="card">
  <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
    <div class="p-2 d-flex">
      <select class="angkatan-list form-control ms-4" name="" id="angkatan-list">
        <option value="-">Filter angkatan</option>
        @foreach($list_angkatan as $angkatan)
        <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
        @endforeach
      </select>
      <select class="role-list form-control ms-4" name="" id="role-list">
        <option value="-">Filter role</option>
        @foreach($list_role as $vrole)
        <option {{ ($select_role == $vrole->id) ? 'selected' : '' }} value="{{$vrole->id}}">{{$vrole->name}}</option>
        @endforeach
      </select>
    </div>
    <h6 class="mb-0">{{ count($users) }} Data Mahasiswa {{$select_angkatan}}</h6>
    @can('create users')
    <a href="{{ route('create user') }}" class="btn btn-primary">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat user
    </a>
    @endcan
  </div>
  <div class="card-body pt-4 p-3">
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
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No HP</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kelamin</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tgl Lahir</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Angkatan</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Ortu</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No HP Ortu</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
            <th class="text-uppercase sort text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lorong</th>
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
            <td data-toggle="tooltip" data-placement="top" title="Klik unutk melihat report" onclick="getReport('<?php echo $user->santri->nohp_ortu; ?>','<?php echo $user->santri->id; ?>')" style="cursor:pointer;">
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
                  $unkl = false;
                  if ($user->santri->fkLorong_id == '' && !$is_kl) {
                    $unkl = true;
                  }
                  ?>
                  <h6 class="mb-0 text-sm {{ ($unkl) ? 'text-warning' : '' }}">{{ $user->fullname }}</h6>
                </div>
              </div>
            </td>
            <td>
              {{ $user->nohp }}
            </td>
            <td>
              @if($user->gender == 'male')
              L
              @endif
              @if($user->gender == 'female')
              P
              @endif
            </td>
            <td>
              {{ $user->birthdate }}
            </td>
            <td>
              {{ $user->santri ? $user->santri->angkatan : 'Bukan santri' }}
            </td>
            <td>
              {{ $user->santri->nama_ortu }}
            </td>
            <td>
              {{ $user->santri->nohp_ortu }}
            </td>
            <td>
              @foreach ($user->getRoleNames() as $role)
              @if($role!='santri' && $role!='mubalegh')
              <span class="badge {{ $role=='koor lorong' ? 'bg-gradient-primary' : 'bg-gradient-success' }}">{{ $role }}</span>
              @endif
              @endforeach
            </td>
            <td>
              {{ $user->santri && $user->santri->lorong ? $user->santri->lorong->name : '' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px !important;">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title" id="exampleModalLabel">Report</h6>
          <h5 class="modal-title" id="exampleModalLabel"><span id="nm"></span></h5>
        </div>
      </div>
      <div class="modal-body" id="contentReport" style="height:600px!important;">
        <tr>
          <td colspan="3">
            <span class="text-center">
              Loading...
            </span>
          </td>
        </tr>
      </div>
      <div class="modal-footer">
        <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
  function getReport(nohp, santri_id) {
    $('#exampleModal').fadeIn();
    $('#exampleModal').css('background', 'rgba(0, 0, 0, 0.7)');
    $('#exampleModal').css('z-index', '10000');
    if (nohp == '') {
      $('#contentReport').html('<h6>Nomor HP Orang Tua belum diinput</h6>');
    } else {
      $('#contentReport').html('<iframe src="{{ url("/") }}/report/' + nohp + '/' + santri_id + '"  style="height:100%;width:100%;">< /iframe>');
    }
  }

  $('#close').click(function() {
    $('#exampleModal').fadeOut();
    $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
  });
  $('#table').DataTable({
    order: [
      // [1, 'desc']
    ],
    pageLength: 25
  });
  $('.angkatan-list').change((e) => {
    var role = $('#role-list').val()
    window.location.replace(`{{ url("/") }}/user/list/santri/${$(e.currentTarget).val()}/` + role)
  })
  $('.role-list').change((e) => {
    var angkatan = $('#angkatan-list').val()
    window.location.replace(`{{ url("/") }}/user/list/santri/` + angkatan + `/${$(e.currentTarget).val()}`)
  })
</script>
@include('base.end')
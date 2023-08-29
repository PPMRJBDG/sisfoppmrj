@include('base.start', ['path' => 'user/list/muballigh', 'title' => 'Daftar Muballigh', 'breadcrumbs' => ['Daftar Muballigh']])
<div class="card">
    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
        <div class="p-2">
            <select class="angkatan-list form-control ms-4" name="" id="">
                <option value="">Filter angkatan</option>
                @foreach($list_angkatan as $angkatan)
                <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
                @endforeach
            </select>
        </div>
        <h6 class="mb-0">{{ count($users) }} Data Muballigh {{ $select_angkatan }}</h6>
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
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-xxs font-weight-bolder">Nama</th>
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">No HP</th>
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Kelamin</th>
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Tgl Lahir</th>
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Angkatan</th>
                        <!-- <th class="text-uppercase text-xxs font-weight-bolder ps-2">Nama Ortu</th>
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">No HP Ortu</th> -->
                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Tanggal Keluar</th>
                        <th class="text-center text-uppercase text-xxs font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-sm">
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{ $user->fullname }}</h6>
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
                            {{ $user->angkatan }}
                        </td>
                        <!-- <td class="text-sm">
                            {{ isset($user->santri) ? $user->santri->nama_ortu : '' }}
                        </td>
                        <td class="text-sm">
                            {{ isset($user->santri) ? $user->santri->nohp_ortu : '' }}
                        </td> -->
                        <td class="text-sm">
                            {{ $user->exit_at }}
                        </td>
                        <td class="align-middle text-center text-sm">
                            @can('delete users')
                            <!-- <a href="{{ route('delete user', $user->id) }}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus {{ $user->fullname }}?')">Hapus</a> -->
                            @endcan
                            @can('update users')
                            <a href="{{ route('edit user', $user->id) }}" class="btn btn-primary btn-sm mb-0">Ubah</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('#table').DataTable({
        order: [
            // [1, 'desc']
        ],
        pageLength: 25
    });
    $('.angkatan-list').change((e) => {
        window.location.replace(`{{ url("/") }}/user/list/muballigh/${$(e.currentTarget).val()}`)
    })
</script>
@include('base.end')
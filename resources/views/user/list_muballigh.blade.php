<div class="card shadow border">
    <div class="card-header p-2 d-flex justify-content-between align-items-center">
        <div class="p-2">
            <select data-mdb-filter="true" class="select angkatan-list form-control" name="" id="">
                <option value="">Filter angkatan</option>
                @foreach($list_angkatan as $angkatan)
                <option {{ ($select_angkatan == $angkatan->angkatan) ? 'selected' : '' }} value="{{$angkatan->angkatan}}">{{$angkatan->angkatan}}</option>
                @endforeach
            </select>
        </div>
        <h6 class="mb-0 text-sm p-1">{{ count($users) }} Muballigh {{ $select_angkatan }}</h6>
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

        <div class="p-2">
            <input class="form-control" placeholder="Search" type="text" id="search" onkeyup="searchDataSantri('santrix',this.value)">
        </div>
        <div id="santrix" class="datatable p-0" data-mdb-sm="true" data-mdb-pagination="false">
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
                        <td class="text-sm font-weight-bolder">
                            <span class="santri-name mb-0 font-weight-bolder text-sm" santri-name="{{ $user->fullname }}">{{ $user->fullname }}</span>
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
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    $('.angkatan-list').change((e) => {
        getPage(`{{ url("/") }}/user/list/muballigh/${$(e.currentTarget).val()}`)
    })
</script>
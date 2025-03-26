<div class="card shadow border">
    <div class="card-header p-2 d-flex justify-content-between align-items-center">
        @can('create users')
        <a href="{{ route('create user') }}" class="btn btn-primary mb-0">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Buat user
        </a>
        @endcan
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

        <div class="datatable datatable-sm p-0">
            <table id="table" class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm font-weight-bolder">Nama</th>
                        <th class="text-uppercase text-sm font-weight-bolder ps-2">Email</th>
                        <th class="text-uppercase text-sm font-weight-bolder ps-2">No HP</th>
                        <th class="text-uppercase text-sm font-weight-bolder ps-2">Role</th>
                        <th class="text-center text-uppercase text-sm font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-sm font-weight-bolder">
                            {{ $user->fullname }}
                        </td>
                        <td class="text-sm">
                            {{ $user->email }}
                        </td>
                        <td class="text-sm">
                            {{ $user->nohp }}
                        </td>
                        <td class="text-sm">
                            @foreach ($user->getRoleNames() as $role)
                                <span class="badge badge-success">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td class="align-middle text-center text-sm">
                            @if(auth()->user()->hasRole('superadmin'))
                                <a href="{{ route('edit user', $user->id) }}" class="btn btn-primary btn-sm mb-0">Ubah</a>
                            @endif
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
</script>
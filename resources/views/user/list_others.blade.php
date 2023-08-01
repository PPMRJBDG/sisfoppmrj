@include('base.start', ['path' => 'user/list/others', 'title' => 'Daftar Others', 'breadcrumbs' => ['Daftar Others']])
<div class="card">
    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
        <div class="p-2 d-flex">
        </div>
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
            <table class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No HP</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="{{ asset('img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{ $user->fullname }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            {{ $user->nohp }}
                        </td>
                        <td>
                            @foreach ($user->getRoleNames() as $role)
                            <span class="badge bg-gradient-success">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td class="align-middle text-center text-sm">
                            @can('update users')
                            @foreach ($user->getRoleNames() as $role)
                            @if($role!='superadmin')
                            <a href="{{ route('edit user', $user->id) }}" class="btn btn-primary btn-sm mb-0">Ubah</a>
                            @endif
                            @endforeach
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('base.end')
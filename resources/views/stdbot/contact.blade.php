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

<div class="card shadow border mb-2">
    <div class="card-body">
        <div class="d-flex">
            <a href="{{ route('stdbot generate bulk') }}" id="generate-contact" class="btn btn-secondary btn-block btn-sm mb-0">
                <i class="ni ni-curved-next" aria-hidden="true"></i>
                Generate Contact
            </a>
        </div>
    </div>
</div>

<div class="card shadow border mb-2">
    <div class="card-body">
        <div class="d-flex">
            <a href="#" onclick="createGroup()" class="btn btn-sm btn-primary mb-2">
                <i class="fas fa-plus" aria-hidden="true"></i>
                Tambah Group Whatsapp
            </a>
        </div>

        <div class="datatable" data-mdb-sm="true" data-mdb-entries="200">
            <table id="table-group" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Group</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder ps-2">Contact</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder ps-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group_user as $gu)
                    <tr class="text-sm">
                        <td>
                            {{ $gu['group_id'] }}
                        </td>
                        <td>
                            {{ $gu['group_name'] }}
                        </td>
                        <td>
                            {{ $gu['phone'] }}
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a href="#" class="btn btn-primary btn-sm mb-0" onclick="changeBulk('<?php echo $gu['group_id']; ?>','<?php echo $gu['group_name']; ?>','<?php echo $gu['phone']; ?>')">Kirim Pesan</a>
                            <a href="#" class="btn btn-danger btn-sm mb-0" onclick="deleteContact('<?php echo $gu['group_id']; ?>')">Hapus</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="datatable datatable-sm">
            <table id="table-contact" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Pribadi</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder">Nohp Pribadi</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Ortu</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder">Nohp Ortu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contact_user as $gu)
                    <tr class="text-sm">
                        <td>
                            {{ $gu['pribadi_id'] }}
                        </td>
                        <td>
                            {{ $gu['nama_pribadi'] }}
                        </td>
                        <td>
                            <a href="#" onclick="changeBulk('<?php echo $gu['pribadi_id']; ?>','<?php echo $gu['nama_pribadi']; ?>','<?php echo $gu['nohp_pribadi']; ?>')" class="btn btn-primary btn-sm mb-0">{{ $gu['nohp_pribadi'] }}</a>
                        </td>
                        <td>
                            {{ $gu['ortu_id'] }}
                        </td>
                        <td>
                            {{ $gu['nama_ortu'] }}
                        </td>
                        <td>
                            <a href="#" onclick="changeBulk('<?php echo $gu['ortu_id']; ?>','<?php echo $gu['nama_ortu']; ?>','<?php echo $gu['nohp_ortu']; ?>')" class="btn btn-primary btn-sm mb-0">{{ $gu['nohp_ortu'] }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-6">
        <h6 class="align-items-center"><b>Daftar Calon Mahasiswa Baru</b></h6>
    </div>
    <div class="col-md-6">
        <select data-mdb-filter="true" class="select select_angkatan form-control bg-white" name="select_angkatan" id="select_angkatan">
            @foreach($list_angkatan as $la)
            <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="card shadow border mb-2">
    <div class="card-body p-2">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary"></th>
                        <th class="text-uppercase text-sm text-secondary">NAMA</th>
                        <th class="text-uppercase text-sm text-secondary">KELAMIN</th>
                        <th class="text-uppercase text-sm text-secondary">TEMPAT TGL LAHIR</th>
                        <th class="text-uppercase text-sm text-secondary">GOL DARAH</th>
                        <th class="text-uppercase text-sm text-secondary">NOMOR WA</th>
                        <th class="text-uppercase text-sm text-secondary">STATUS</th>
                        <!-- <th class="text-uppercase text-sm text-secondary">RIWAYAT PENYAKIT</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($camabas as $camaba)
                        <tr class="text-sm">
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" onclick="detilMaba({{$camaba}})">DETIL</a>
                            </td>
                            <td>
                                {{ strtoupper($camaba->fullname) }}
                            </td>
                            <td>
                                {{ strtoupper($camaba->gender) }}
                            </td>
                            <td>
                                {{ strtoupper($camaba->place_of_birth).', '.date_format(date_create($camaba->birthday), 'd-m-Y') }}
                            </td>
                            <td>
                                {{ $camaba->blood_group }}
                            </td>
                            <td>
                                {{ $camaba->nomor_wa }}
                            </td>
                            <td>
                            <select class="form-control" id="status" name="status" onchange="return changeStatus({{$camaba->id}},this.value)">
                                <option {{ ($camaba->status=='in-review') ? 'selected' : '' }} value="in-review">REVIEW</option>
                                <option {{ ($camaba->status=='interview') ? 'selected' : '' }} value="interview">INTERVIEW</option>
                                <option {{ ($camaba->status=='pass') ? 'selected' : '' }} value="pass">LOLOS</option>
                            </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
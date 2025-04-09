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

<h6>Catatan Penguhubung</h6>

<div class="card border shadow-lg p-2">
    <div class="datatable datatable-sm" data-mdb-entries="50">
        <table id="table" class="table align-items-center mb-0">
            <thead style="background-color:#f6f9fc;">
                <tr>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Mahasiswa</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Kepribadian</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Sholat</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">KBM</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Asmara</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Akhlaq</th>
                    <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Umum</th>
                </tr>
            </thead>
            <tbody>
                @if(count($cat_penghubung)>0)
                @foreach($cat_penghubung as $cp)
                <tr class="text-sm">
                    <td>
                        <a href="#" onclick="openCatatan('{{$cp->id}}','{{$cp->santri_id}}','[{{$cp->angkatan}}] {{$cp->fullname}}','{{$cp->cat_kepribadian}}','{{$cp->cat_sholat}}','{{$cp->cat_kbm}}','{{$cp->cat_asmara}}','{{$cp->cat_akhlaq}}','{{$cp->cat_umum}}')" class="btn btn-primary btn-sm mb-0">INPUT</a>
                        [{{$cp->angkatan}}] {{$cp->fullname}}
                    </td>
                    <td id="kepribadian{{$cp->santri_id}}">{{substr($cp->cat_kepribadian,0,20);}}</td>
                    <td id="sholat{{$cp->santri_id}}">{{substr($cp->cat_sholat,0,20);}}</td>
                    <td id="kbm{{$cp->santri_id}}">{{substr($cp->cat_kbm,0,20);}}</td>
                    <td id="asmara{{$cp->santri_id}}">{{substr($cp->cat_asmara,0,20);}}</td>
                    <td id="akhlaq{{$cp->santri_id}}">{{substr($cp->cat_akhlaq,0,20);}}</td>
                    <td id="umum{{$cp->santri_id}}">{{substr($cp->cat_umum,0,20);}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
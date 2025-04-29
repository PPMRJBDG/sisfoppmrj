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

<div class="text-end">
    <a href="{{route('pelanggaran by mhs')}}" class="btn btn-outline-secondary btn-sm mb-1">Tampilkan Berdasarkan Mahasiswa</a>
</div>

<div class="card shadow border p-2 mb-2">
    <button type="button" class="btn btn-sm btn-outline-light text-dark" onclick="$('#s-dash').toggle();">Dashboard</button>
    <div id="s-dash" style="display:none;">
        @if($id!=null)
        <a href="/pelanggaran/s/{{$is_archive}}/param/all" class="btn btn-primary btn-sm col-12 col-md-2 mb-1">Tampilkan Semua</a>
        @endif
        <div class="row">
            @foreach($count_pelanggaran as $key=>$cp)
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow border mb-1 mt-1 shadow-sm">
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bolder">
                                        <?php
                                        $color = 'success';
                                        if ($cp['kategori'] == 'Berat') {
                                            $color = 'danger';
                                        } elseif ($cp['kategori'] == 'Sedang') {
                                            $color = 'primary';
                                        }
                                        ?>
                                        <a href="/pelanggaran/s/{{$is_archive}}/param/{{$value}}/{{$key}}">
                                            <span class="text-{{$color}} text-sm font-weight-bolder">
                                                [{{ $cp['kategori'] }}]
                                            </span>
                                            {{ $cp['pelanggaran'] }}
                                        </a>
                                    </p>
                                    <p class="font-weight-bolder mb-0">
                                        Sudah SP: {{$cp['fix']}}
                                    </p>
                                    <p class="font-weight-bolder mb-0">
                                        {{ ($is_archive) ? 'Saat Dipantau' : 'Sedang Dipantau' }}: {{$cp['pemantauan']}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 mt-1">
                <a href="{{ $id==null ? '/pelanggaran/s/'.$is_archive.'/param/sp' : '/pelanggaran/s/'.$is_archive.'/param/sp/'.$id }}" class="btn btn-primary btn-sm mb-0">
                    Sudah SP
                </a>
                <a href="{{ $id==null ? '/pelanggaran/s/'.$is_archive.'/param/pantau' : '/pelanggaran/s/'.$is_archive.'/param/pantau/'.$id }}" class="btn btn-warning btn-sm mb-0">
                    Dipantau
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow border p-2">
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
    <div class="card-header">
        <div class="row">
            <div class="col-md-6 col-sm-12 text-primary font-weight-bold mb-2">
                Daftar Pelanggaran {{ ($is_archive) ? 'Arsip' : 'Aktif' }}
            </div>
            <div class="col-md-6 col-sm-12 text-end">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <a href="{{ route('create pelanggaran') }}" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-plus" aria-hidden="true"></i>
                            Input Pelanggaran
                        </a>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        @if($is_archive)
                        <a href="/pelanggaran/s/0/param/{{$value}}/{{$id}}" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="fas fa-view" aria-hidden="true"></i>
                            Pelanggaran Aktif
                        </a>
                        @else
                        <a href="/pelanggaran/s/1/param/{{$value}}/{{$id}}" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="fas fa-view" aria-hidden="true"></i>
                            Lihat Arsip
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="card-body p-2 pt-0">
        <div class="p-2">
            <input class="form-control" placeholder="Search" type="text" id="search" onkeyup="searchDataSantri('santrix',this.value)">
        </div>
        <div id="santrix" class="datatable" data-mdb-sm="true" data-mdb-pagination="false">
            <table id="table" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        @foreach($column as $c)
                        <th class="text-uppercase text-sm font-weight-bolder ps-2">{{$c}}</th>
                        @endforeach
                        <th class="text-uppercase text-sm align-middle text-center font-weight-bolder ps-2">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($list_pelanggaran))
                    @foreach($list_pelanggaran as $data)
                    <tr class="text-sm">
                        <td>
                            <span class="santri-name text-left" santri-name="{{ $data->santri->user->fullname }}" onclick="getReport('<?php echo base64_encode($data->santri->id); ?>')" style="cursor:pointer;"> 
                                <b>[{{ $data->santri->angkatan }}] {{ $data->santri->user->fullname }}</b>
                                <br>
                                <b>Pemanggilan:</b> {{ date_format(date_create($data->tanggal_melanggar), 'd M Y') }}
                                
                            </span>
                        </td>
                        <td class="text-xs">
                            <select style="font-size:11px!important;" class="form-control" id="fkJenis_pelanggaran_id{{$data->id}}" name="fkJenis_pelanggaran_id{{$data->id}}" onchange="updatePelanggaran(event,'fkJenis_pelanggaran_id',this.value,{{$data->id}})">
                                @foreach($jenis_pelanggaran as $jp)
                                    <option <?php if (isset($data)) { echo $data->fkJenis_pelanggaran_id == $jp->id ? 'selected' : ''; } ?> value="{{$jp->id}}">
                                        [{{ $jp->kategori_pelanggaran }}] {{ $jp->jenis_pelanggaran }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-md-6 p-0">
                                    <select style="font-size:11px!important;" class="form-control" id="keringanan_sp{{$data->id}}" name="keringanan_sp{{$data->id}}" onchange="updatePelanggaran(event,'keringanan_sp',this.value,{{$data->id}})">
                                        <option value="">-</option>
                                        @for($i=1; $i<=3; $i++) 
                                            <option <?php if (isset($data)) { echo $data->keringanan_sp == $i ? 'selected' : ''; } ?> value="{{$i}}">SP {{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6 p-0">
                                    <select style="font-size:11px!important;" class="form-control" id="is_peringatan_keras{{$data->id}}" name="is_peringatan_keras{{$data->id}}" onchange="updatePelanggaran(event,'is_peringatan_keras',this.value,{{$data->id}})">
                                        <option <?php if (isset($data)) { echo $data->is_peringatan_keras == 1 ? 'selected' : ''; } ?> value="1">Peringatan Keras</option>
                                        <option <?php if (isset($data)) { echo $data->is_peringatan_keras == 0 ? 'selected' : ''; } ?> value="0">Hati-hati</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input style="font-size:11px!important;" type="text" class="form-control" value="{{ $data->keterangan }}" name="keterangan{{$data->id}}" id="keterangan{{$data->id}}" onkeypress="updatePelanggaran(event,'keterangan',this.value,{{$data->id}})">
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a href="{{ route('edit pelanggaran', [$data->id])}}" class="btn btn-primary btn-sm mb-0">Lihat</a>
                            @if(!$is_archive)
                            <a href="{{ route('archive pelanggaran', [$data->id])}}" class="btn btn-success btn-sm mb-0" onclick="confirm('Yakin data akan diarsipkan ?')">Arsip</a>
                            @endif
                            <a href="{{ route('delete pelanggaran', [$data->id])}}" class="btn btn-danger btn-sm mb-0" onclick="confirm('Yakin data akan dihapus ?')">Hapus</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
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

    function updatePelanggaran(event,field,value,id){
        if(event.which==13 || event.type=="change"){
            // console.log(event)
            var datax = {};
            datax['id'] = id;
            datax['value'] = value;
            datax['field'] = field;
            $.post("{{ route('update_pelanggaran') }}", datax,
                function(dataz, status) {
                    var return_data = JSON.parse(dataz);
                    console.log(return_data.message)
                    if (!return_data.status) {
                        alert(return_data.message)
                    }
                }
            );
        }
    }
</script>
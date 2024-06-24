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

<div class="card shadow border p-2 mb-2">
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
        <div class="datatable datatable-sm">
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
                        <td onclick="getReport('<?php echo base64_encode($data->santri->id); ?>')" style="cursor:pointer;">
                            <b>[{{ $data->santri->angkatan }}] {{ $data->santri->user->fullname }}</b>
                            <br>
                            [{{ isset($data->jenis) ? $data->jenis->kategori_pelanggaran : '-' }}] {{ isset($data->jenis) ? $data->jenis->jenis_pelanggaran : '' }}
                        </td>
                        <td class="text-xs">
                            <b>Melanggar:</b> {{ date_format(date_create($data->tanggal_melanggar), 'd M Y') }}
                            <br>
                            <b>SP:</b> {{ $data->is_surat_peringatan!=null ? date_format(date_create($data->is_surat_peringatan), 'd M Y') : '-' }}
                        </td>
                        <td>
                            <b>
                                SP {{ $data->kategori_sp_real }} -> SP {{ $data->keringanan_sp }}
                                <br>
                                Peringatan Keras: {{ ($data->is_peringatan_keras==1) ? 'Ya' : 'Tidak' }}
                            </b>
                        </td>
                        <td>
                            {{ $data->keterangan }}
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a href="{{ route('edit pelanggaran', [$data->id])}}" class="btn btn-primary btn-xs mb-0">Lihat</a>
                            @if(!$is_archive)
                            <a href="{{ route('archive pelanggaran', [$data->id])}}" class="btn btn-success btn-xs mb-0" onclick="confirm('Yakin data akan diarsipkan ?')">Arsip</a>
                            @endif
                            <a href="{{ route('delete pelanggaran', [$data->id])}}" class="btn btn-danger btn-xs mb-0" onclick="confirm('Yakin data akan dihapus ?')">Hapus</a>
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

    // $('#table').DataTable({
    //     order: [
    //         // [1, 'desc']
    //     ]
    // });
</script>
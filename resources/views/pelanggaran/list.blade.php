@include('base.start', ['path' => 'pelanggaran', 'title' => 'Daftar Pelanggaran', 'breadcrumbs' => ['Daftar Pelanggaran']])
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

<div class="card">
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1'))
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex text-primary font-weight-bold">
            Daftar Pelanggaran {{ ($is_archive) ? 'Arsip' : 'Aktif' }}
        </div>
        <div class="d-flex">
            <a href="{{ route('create pelanggaran') }}" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                Input Pelanggaran
            </a>
            @if($is_archive)
            <a href="{{ route('pelanggaran tm') }}" class="btn btn-success">
                <i class="fas fa-view" aria-hidden="true"></i>
                Pelanggaran Aktif
            </a>
            @else
            <a href="{{ route('pelanggaran archive') }}" class="btn btn-success">
                <i class="fas fa-view" aria-hidden="true"></i>
                Lihat Arsip
            </a>
            @endif
        </div>
    </div>
    @endif
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        @foreach($column as $c)
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$c}}</th>
                        @endforeach
                        <th class="text-uppercase text-sm text-secondary align-middle text-center font-weight-bolder ps-2">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($list_pelanggaran))
                    @foreach($list_pelanggaran as $data)
                    <tr class="text-sm">
                        <td>
                            {{ $data->santri->user->fullname }}
                        </td>
                        <td>
                            {{ $data->santri->angkatan }}
                        </td>
                        <td>
                            [{{ isset($data->jenis) ? $data->jenis->kategori_pelanggaran : '-' }}] {{ isset($data->jenis) ? $data->jenis->jenis_pelanggaran : '' }}
                        </td>
                        <td>
                            {{ $data->tanggal_melanggar }}
                        </td>
                        <td>
                            SP {{ $data->kategori_sp_real }} -> SP {{ $data->keringanan_sp }}
                        </td>
                        <td>
                            {{ $data->is_st }}
                        </td>
                        <td>
                            {{ ($data->is_peringatan_keras==1) ? 'Ya' : 'Tidak' }}
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
@include('base.end')
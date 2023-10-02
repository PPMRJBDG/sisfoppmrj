@include('base.start', ['path' => 'pelanggaran/create', 'title' => 'Input Pelanggaran', 'breadcrumbs' => ['Input Pelanggaran'], 'backRoute' => route('pelanggaran tm')
])
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
    <div class="card-body pt-4 p-3">
        <form action="{{ route('store pelanggaran') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12 text-warning font-weight-bold">SECTION #1</div>

                @if(isset($id))
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">
                            ID
                        </label>
                        <input class="form-control" value="{{$datax->id}}" type="text" name="id" readonly>
                    </div>
                </div>
                @endif

                @foreach($column as $c)
                @if($c!='id' && $c!='created_at' && $c!='updated_at' && $c!='is_archive' && $c!='is_wa')
                @if($c == 'penasehat_1')
                <div class="col-md-12 text-warning font-weight-bold">SECTION #2</div>
                @endif
                <?php
                $md = 4;
                if ($c == 'keterangan') {
                    $md = 12;
                }
                ?>
                <div class="col-md-{{$md}}">
                    <div class="form-group">
                        <label for="example-text-input" class="form-control-label">
                            @if($c=='fkSantri_id')
                            NAMA
                            @elseif($c=='fkJenis_pelanggaran_id')
                            JENIS PELANGGARAN
                            @elseif(str_contains($c, 'is_'))
                            {{strtoupper(str_replace('_', ' ',str_replace('is_','',$c)))}}
                            @else
                            {{strtoupper(str_replace('_',' ',$c))}}
                            @endif
                        </label>
                        @if($c=='fkSantri_id')
                        <select class="form-control" {{ (isset($datax)) ? 'readonly' : '' }} name="{{$c}}" required>
                            <option value="">Pilih Mahasiswa</option>
                            @foreach($list_santri as $mhs)
                            <option <?php if (isset($datax)) {
                                        echo (intval($datax->$c) == intval($mhs->santri_id)) ? 'selected' : '';
                                    } ?> value="{{$mhs->santri_id}}">{{$mhs->fullname}}</option>
                            @endforeach
                        </select>
                        @elseif($c=='fkJenis_pelanggaran_id')
                        <select class="form-control" name="{{$c}}" required>
                            <option value="">Pilih Pelanggaran</option>
                            @foreach($list_jenis_pelanggaran as $pln)
                            <option <?php if (isset($datax)) {
                                        echo $datax->$c == $pln->id ? 'selected' : '';
                                    } ?> value="{{$pln->id}}">[{{$pln->kategori_pelanggaran}}] {{$pln->jenis_pelanggaran}}</option>
                            @endforeach
                        </select>
                        @elseif($c == 'is_peringatan_keras' || str_contains($c, 'info_ortu'))
                        <select class="form-control" name="{{$c}}">
                            <option <?php if (isset($datax)) {
                                        echo $datax->$c == 0 ? 'selected' : '';
                                    } ?> value="0">{{ $c == 'is_peringatan_keras' ? 'Tidak' : 'Belum' }}</option>
                            <option <?php if (isset($datax)) {
                                        echo $datax->$c == 1 ? 'selected' : '';
                                    } ?> value="1">{{ $c == 'is_peringatan_keras' ? 'Ya' : 'Sudah' }}</option>
                        </select>
                        @elseif(str_contains($c, '_sp'))
                        <select class="form-control" name="{{$c}}">
                            <option value="">-</option>
                            @for($i=1; $i<=3; $i++) <option <?php if (isset($datax)) {
                                                                echo $datax->$c == $i ? 'selected' : '';
                                                            } ?> value="{{$i}}">SP {{$i}}</option>
                                @endfor
                        </select>
                        @elseif($c=='keterangan')
                        <textarea class="form-control" type="{{$type}}" name="{{$c}}"><?php if (isset($datax)) {
                                                                                            echo $datax->$c;
                                                                                        } ?></textarea>
                        @else
                        <?php $type = 'text';
                        if ($c == 'tanggal_melanggar' || $c == 'is_st' || $c == 'is_surat_peringatan' || $c == 'is_surat_perubahan' || $c == 'is_st_dikembalikan' || str_contains($c, '_tgl'))
                            $type = 'date';
                        ?>
                        <input class="form-control" value="<?php if (isset($datax)) {
                                                                echo $datax->$c;
                                                            } ?>" type="{{$type}}" name="{{$c}}">
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
                <div class="col-3">
                    <div class="form-group">
                        <label class="custom-control-label">INFO WA</label>
                        <select class="form-control" name="is_wa">
                            <option <?php if (isset($datax)) {
                                        echo $datax->is_wa == 0 ? 'selected' : '';
                                    } ?> value="0">Tidak</option>
                            <option <?php if (isset($datax)) {
                                        echo $datax->is_wa == 1 ? 'selected' : '';
                                    } ?> value="1">Ya</option>
                        </select>
                    </div>
                </div>
            </div>
            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input class="btn btn-primary form-control" type="submit" value="Submit">
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>
@include('base.end')
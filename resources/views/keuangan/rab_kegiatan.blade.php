<style>
    .new-td, .datatable.datatable-sm td {
        padding: 2px 5px !important;
    }
    .form-control {
        border: transparent;
        border-bottom: solid 1px #dee2e6;
        font-size: 0.7rem !important;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul class="m-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card border p-2 mb-2" style="border-bottom:solid 2px #93c2f2!important;">
    <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-12">
            <h6 class="m-0">RAB Kegiatan</h6>
        </div>
    </div>
</div>

@if($detail_kegiatans==null)
    <div class="card border mt-2">
        <button type="button" class="btn btn-sm btn-outline-light text-dark m-2" onclick="$('#s-dash').toggle();"><i class="fa fa-plus"></i> Input Kegiatan</button>
        <div class="card-body p-0" id="s-dash" style="display:none;">
            <form action="{{ route('store rab kegiatan') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="parent_id" id="parent_id" value="" required>
                <input type="hidden" name="is_duplicate" id="is_duplicate" value="0" required>
                <div class="row p-2">
                    <div class="col-md-2">
                        <select class="form-control mb-2" value="" id="fkRab_id" name="fkRab_id" required>
                            <option value="">--pilih rab--</option>
                                @foreach($rabs as $rab)
                                <option value="{{$rab->id}}">{{strtoupper($rab->keperluan)}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input class="form-control mb-2" type="text" placeholder="Nama / Tema Kegiatan" id="name" name="name" required>
                        <select class="form-control mb-2" value="" id="fkSantri_id_ketua" name="fkSantri_id_ketua" required>
                            <option value="">--ketua panitia atau koor divisi--</option>
                                @foreach($santris as $santri)
                                <option value="{{$santri->santri_id}}">{{strtoupper($santri->fullname)}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control mb-2" type="date" value="{{date('Y-m')}}" id="date" name="date" required>
                        <select class="form-control mb-2" value="" id="fkSantri_id_bendahara" name="fkSantri_id_bendahara">
                            <option value="">--bendahara--</option>
                                @foreach($bendaharas as $bendahara)
                                <option value="{{$bendahara->santri_id}}">{{strtoupper($bendahara->fullname)}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <textarea rows="3" class="form-control mb-2" type="textarea" placeholder="Deskripsi" id="deskripsi" name="deskripsi"></textarea>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm mb-0">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            SIMPAN
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border mt-2">
        <div class="card-body p-0">
            <div class="px-2 py-4">
                <div>
                    <button type="button" class="btn btn-sm btn-secondary btn-floating" data-mdb-ripple-init>
                        <i class="fa fa-copy text-white"></i>
                    </button>
                    Duplikat Kegiatan Untuk Bulan Selanjutnya Akan Sekaligus Menduplikasi Rincian / Detail RAB
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-primary btn-floating" data-mdb-ripple-init>
                        <i class="fa fa-link text-white"></i>
                    </button>
                    Copy Link URL RAB dan Bagikan ke Masing-masing Divisi
                </div>
            </div>
            <div data-mdb-bordered="true" class="datatable datatable-sm text-uppercase text-center">
                <table class="table align-items-center justify-content-center mb-0 text-sm">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase font-weight-bolder">DUPLIKAT</th>
                            <th class="text-uppercase font-weight-bolder">LINK</th>
                            <th class="text-uppercase font-weight-bolder">AGENDA</th>
                            <th class="text-uppercase font-weight-bolder">NAMA KEGIATAN</th>
                            <th class="text-uppercase font-weight-bolder">PERIODE</th>
                            <th class="text-uppercase font-weight-bolder">RAB</th>
                            <th class="text-uppercase font-weight-bolder">PENGAJUAN</th>
                            <th class="text-uppercase font-weight-bolder">REALISASI</th>
                            <th class="text-uppercase font-weight-bolder">DESKRIPSI</th>
                            <th class="text-uppercase font-weight-bolder">DETIL</th>
                            <th class="text-uppercase font-weight-bolder">STATUS</th>
                            <th class="text-uppercase font-weight-bolder"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($kegiatans)
                            @foreach($kegiatans as $mb)
                                <tr>
                                    <td class="new-td">
                                        <center>
                                        <button type="button" class="btn btn-sm btn-secondary btn-floating" data-mdb-ripple-init>
                                            <i onclick="ubah('parent',{{$mb}},null,true)" class="fa fa-copy text-white"></i>
                                        </button>
                                    </td>
                                    <td class="new-td">
                                        <center>
                                        <button type="button" class="btn btn-sm btn-primary btn-floating" data-mdb-ripple-init>
                                            <i onclick="copyLink('{{$mb->ids}}')" class="fa fa-link text-white"></i>
                                        </button>
                                    </td>
                                    <td class="new-td"><div class="text-start">{{$mb->rab->keperluan}}</div></td>
                                    <td class="new-td"><div class="text-start">{{$mb->nama}}</div></td>
                                    <td class="new-td">{{date_format(date_create($mb->periode_bulan), 'd-m-Y')}}</td>
                                    <td class="new-td"><div class="text-end">{{number_format($mb->rab->biaya,0, ',', '.')}}</div></td>
                                    <td class="new-td">{{number_format($mb->total_biaya(),0, ',', '.')}}</td>
                                    <td class="new-td">{{number_format($mb->total_realisasi(),0, ',', '.')}}</td>
                                    <td class="new-td">{{$mb->deskripsi}}</td>
                                    <td class="new-td"><a href="{{route('rab kegiatan id',$mb->id)}}" class="btn btn-sm btn-outline-secondary">Lihat Detil</a></td>
                                    <td class="new-td">
                                        <?php
                                            $badge = 'warning';
                                            if($mb->status=='submit'){
                                                $badge = 'info';
                                            }elseif($mb->status=='approved'){
                                                $badge = 'primary';
                                            }elseif($mb->status=='rejected'){
                                                $badge = 'danger';
                                            }elseif($mb->status=='posted'){
                                                $badge = 'success';
                                            }
                                        ?>
                                        <span class="badge badge-{{$badge}}">{{$mb->status}}</span>
                                    </td>
                                    <td class="new-td">
                                        @if($mb->status=='draft')
                                        <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Edit" onclick="ubah('parent',{{$mb}})">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Hapus" onclick="hapus('parent',{{$mb->id}})">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@include('keuangan.rab_kegiatan_form_table',[
    'ids' => null,
    'detail_of' => $detail_of,
    'kegiatans' => $kegiatans,
    'detail_kegiatans' => $detail_kegiatans,
    'rabs' => $rabs,
    'santris' => $santris,
    'bendaharas' => $bendaharas,
    'ketuapanitia' => $ketuapanitia,
    'form_url' => ''
])
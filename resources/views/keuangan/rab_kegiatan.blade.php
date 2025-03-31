<style>
    .new-td {
        padding: 2px 5px !important;
    }
</style>

<div class="card border p-2 mb-2">
    <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-12">
            <h6 class="m-0">RAB Kegiatan</h6>
        </div>
    </div>
</div>

<div class="card border mt-2">
    <div class="card-body p-0">
        <form action="{{ route('store rab kegiatan') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="parent_id" id="parent_id" value="" required>
            <div class="row p-2">
                <div class="col-md-2">
                    <select class="form-control" value="" id="fkRab_id" name="fkRab_id" required>
                        <option value="">--pilih rab--</option>
                            @foreach($rabs as $rab)
                            <option value="{{$rab->id}}">{{strtoupper($rab->keperluan)}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="text" placeholder="Nama Kegiatan" id="name" name="name" required>
                    <select class="form-control mt-2" value="" id="fkSantri_id_ketua" name="fkSantri_id_ketua" required>
                        <option value="">--ketua panitia--</option>
                            @foreach($santris as $santri)
                            <option value="{{$santri->santri_id}}">{{strtoupper($santri->fullname)}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input class="form-control" type="date" value="{{date('Y-m')}}" id="date" name="date" required>
                    <select class="form-control mt-2" value="" id="fkSantri_id_bendahara" name="fkSantri_id_bendahara" required>
                        <option value="">--bendahara panitia--</option>
                            @foreach($santris as $santri)
                            <option value="{{$santri->santri_id}}">{{strtoupper($santri->fullname)}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <textarea rows="3" class="form-control" type="textarea" placeholder="Deskripsi" id="deskripsi" name="deskripsi"></textarea>
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
        <div class="datatable datatable-sm text-uppercase">
            <table class="table align-items-center justify-content-center mb-0 table-striped table-bordered text-sm text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2">KEGIATAN</th>
                        <th class="text-uppercase font-weight-bolder ps-2">NAMA</th>
                        <th class="text-uppercase font-weight-bolder ps-2">PERIODE</th>
                        <th class="text-uppercase font-weight-bolder ps-2">TOTAL BIAYA</th>
                        <th class="text-uppercase font-weight-bolder ps-2">DESKRIPSI</th>
                        <th class="text-uppercase font-weight-bolder ps-2">DETIL</th>
                        <th class="text-uppercase font-weight-bolder ps-2">STATUS</th>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if($kegiatans)
                        @foreach($kegiatans as $mb)
                            <tr>
                                <td>{{$mb->rab->keperluan}}</td>
                                <td>{{$mb->nama}}</td>
                                <td>{{date_format(date_create($mb->periode_bulan), 'd-m-Y')}}</td>
                                <td>{{number_format($mb->total_biaya(),0, ',', '.')}}</td>
                                <td >{{$mb->deskripsi}}</td>
                                <td><a href="{{route('rab kegiatan id',$mb->id)}}" class="btn btn-sm btn-outline-secondary">Lihat Detil</a></td>
                                <td>
                                    <?php
                                        $badge = 'warning';
                                        if($mb->status=='approved'){
                                            $badge = 'primary';
                                        }elseif($mb->status=='rejected'){
                                            $badge = 'danger';
                                        }elseif($mb->status=='posted'){
                                            $badge = 'success';
                                        }
                                    ?>
                                    <span class="badge badge-{{$badge}}">{{$mb->status}}</span>
                                </td>
                                <td>
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

@if($detail_kegiatans!=null)
<div class="card border p-2 mt-2" style="border-top:solid 2px #f29393!important;">
    <div class="row align-items-center justify-content-center">
        <div class="col-md-12 text-center mb-2">
            <h6 class="m-0">
                Detail Kegiatan: <b class="badge badge-primary">{{strtoupper($detail_of->nama)}}</b> 
                <?php
                    $badge = 'warning';
                    if($detail_of->status=='approved'){
                        $badge = 'primary';
                    }elseif($detail_of->status=='rejected'){
                        $badge = 'danger';
                    }elseif($detail_of->status=='posted'){
                        $badge = 'success';
                    }
                ?>
                <span class="badge badge-{{$badge}}">{{$detail_of->status}} {{ ($badge=='submit') ? ': menunggu persetujuan pusat' : '' }}</span>
            </h6>
        </div>
        <div class="p-3 pt-0">
            <h6 class="mb-0">Deskripsi:</h6>
            {{ucwords($detail_of->deskripsi)}}
            <hr>
        </div>
        <form action="{{ route('store detail rab kegiatan') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" value="" required>
            <input type="hidden" name="parent_id_detail" id="parent_id_detail" value="{{$detail_of->id}}" required>
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" value="" id="divisi" name="divisi" required>
                        <option value="">--pilih divisi--</option>
                        <option value="PERKAB">PERKAB</option>
                        <option value="KONSUMSI">KONSUMSI</option>
                        <option value="ACARA">ACARA</option>
                        <option value="PUBDOK">PUBDOK</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="text" id="uraian" name="uraian" placeholder="Uraian" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-1">
                    <input class="form-control" type="number" id="qty" name="qty" placeholder="Qty" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-1">
                    <input class="form-control" type="text" id="satuan" name="satuan" placeholder="Satuan" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="number" id="biaya" name="biaya" placeholder="Biaya" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-1">
                    @if($detail_of->status=="draft" || $detail_of->status=="submit")
                    <button type="submit" class="btn btn-primary btn-sm mb-0">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        SIMPAN
                    </button>
                    @endif
                </div>
            </div>
            @if($detail_of->status=="approved")
            <div class="row mt-2 align-items-center justify-content-center">
                <div class="col-md-3"></div>
                <div class="col-md-3 text-end font-weight-bolder">
                    Realisasi
                </div>
                <div class="col-md-1">
                    <input class="form-control" type="number" id="qty_realisasi" name="qty_realisasi" placeholder="Qty" required>
                </div>
                <div class="col-md-1">
                    <input class="form-control" type="text" id="satuan_realisasi" name="satuan_realisasi" placeholder="Satuan" required>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="number" id="biaya_realisasi" name="biaya_realisasi" placeholder="Biaya" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        SIMPAN
                    </button>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border mt-2">
    <div class="card-body p-0">
        <div class="datatablex table-responsive datatable-sm">
            <table class="table align-items-center justify-content-center mb-0 table-stripe table-bordered text-sm text-uppercase">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #e7a2a2;" colspan="4">RAB</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center" style="border-bottom:solid 2px #42c19c;" colspan="4">REALISASI</th>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                    </tr>
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2">URAIAN</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center">SATUAN</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center">QTY</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center">SATUAN</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">BIAYA</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">TOTAL</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">SELISIH</th>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; $total_realisasi = 0; $divisi = "";?>
                    @foreach($detail_kegiatans as $mb)
                        @if($mb->divisi!=$divisi)
                            <tr style="background:#f7f9fd;">
                                <td colspan="11" class="font-weight-bolder text-italic"><span class="badge badge-secondary">{{$mb->divisi}}</span></td>
                            </tr>
                        @endif
                        <?php $divisi = $mb->divisi; ?>
                        <tr>
                            <td class="new-td">{{$mb->uraian}}</td>
                            <td class="new-td text-center">{{$mb->qty}}</td>
                            <td class="new-td text-center">{{$mb->satuan}}</td>
                            <td class="new-td text-end">{{number_format($mb->biaya,0, ',', '.')}}</td>
                            <?php $total = $total + ($mb->qty*$mb->biaya); ?>
                            <td class="new-td text-end">{{number_format(($mb->qty*$mb->biaya),0, ',', '.')}}</td>
                            <td class="new-td text-center">{{$mb->qty_realisasi}}</td>
                            <td class="new-td text-center">{{$mb->satuan_realisasi}}</td>
                            <td class="new-td text-end">{{number_format($mb->biaya_realisasi,0, ',', '.')}}</td>
                            <?php $total_realisasi = $total_realisasi + ($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                            <td class="new-td text-end">{{number_format(($mb->qty_realisasi*$mb->biaya_realisasi),0, ',', '.')}}</td>
                            <td class="new-td text-end">
                                <?php
                                $selisih = ($mb->qty*$mb->biaya)-($mb->qty_realisasi*$mb->biaya_realisasi);
                                ?>
                                {{number_format($selisih,0, ',', '.')}}
                            </td>
                            <td class="new-td text-center">
                                @if($detail_of->status=='draft' || $detail_of->status=='approved')
                                <a block-id="return-false" href="#" class="btn btn-success btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Edit" onclick="ubah('detil',{{$mb}},'{{$detail_of->status}}')">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </a>
                                @endif
                                @if($detail_of->status=='draft')
                                <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Hapus" onclick="hapus('detil',{{$mb->id}})">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfooter style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total,0, ',', '.')}}</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end">{{number_format($total_realisasi,0, ',', '.')}}</th>
                        <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                        <th class="text-uppercase font-weight-bolder ps-2"></th>
                    </tr>
                    <tr>
                        <th colspan="11" class="text-uppercase font-weight-bolder ps-2 text-center">
                            @if($detail_of->status=='approved')
                                <p class="m-1">Catatan: Sebelum Posting ke Jurnal, pastikan semua realisasi sudah tercatat di setiap Item.</p>
                            @endif
                            @if($detail_of->status=='draft')
                            <button type="submit" id="submit" class="btn btn-warning btn-sm mb-0" onclick="submitKegiatan('submit',{{$detail_of}})">
                                <i class="fas fa-file-arrow-up" aria-hidden="true"></i>
                                SUBMIT
                            </button>
                            @endif
                            @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku'))
                                <button type="submit" id="posted" class="btn btn-success btn-sm mb-0" onclick="submitKegiatan('draft',{{$detail_of}})">
                                    <i class="fab fa-firstdraft" aria-hidden="true"></i>
                                    DRAFT
                                </button>
                                @if($detail_of->status=='submit' && auth()->user()->hasRole('superadmin'))
                                    <button type="submit" id="rejected" class="btn btn-danger btn-sm mb-0" onclick="submitKegiatan('rejected',{{$detail_of}})">
                                        <i class="fas fa-xmark" aria-hidden="true"></i>
                                        REJECTED
                                    </button>
                                    <button type="submit" id="approved" class="btn btn-primary btn-sm mb-0" onclick="submitKegiatan('approved',{{$detail_of}})">
                                        <i class="fas fa-check-double" aria-hidden="true"></i>
                                        APPROVED
                                    </button>
                                @endif
                                @if($detail_of->status=='approved')
                                    <button type="submit" id="posted" class="btn btn-secondary btn-sm mb-0" onclick="submitKegiatan('posted',{{$detail_of}})">
                                        <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
                                        POSTING JURNAL
                                    </button>
                                @endif
                            @endif
                        </th>
                    </tr>
                </tfooter>
            </table>
        </div>
    </div>
</div>
@endif

<script>
function ubah(x,data,status){
    if(x=="parent"){
        $("#fkRab_id").val(data.fkRab_id);
        $("#parent_id").val(data.id);
        $("#name").val(data.nama);
        $("#date").val(data.periode_bulan);
        $("#deskripsi").val(data.deskripsi);
        $("#fkSantri_id_ketua").val(data.fkSantri_id_ketua);
        $("#fkSantri_id_bendahara").val(data.fkSantri_id_bendahara);
    }else if(x=="detil"){
        $("#id").val(data.id);
        $("#divisi").val(data.divisi);
        $("#uraian").val(data.uraian);
        $("#qty").val(data.qty);
        $("#satuan").val(data.satuan);
        $("#biaya").val(data.biaya);
        if(status=="approved"){
            $("#qty_realisasi").val((data.qty_realisasi==null) ? data.qty : data.qty_realisasi);
            $("#satuan_realisasi").val((data.satuan_realisasi==null) ? data.satuan : data.satuan_realisasi);
            $("#biaya_realisasi").val((data.biaya_realisasi==null) ? data.biaya : data.biaya_realisasi);
        }
    }
}

function hapus(x,id){
    if (confirm('Apakah RAB ini yakin akan dihapus ?')) {
        $("#loadingSubmit").show();
        route = `rab-kegiatan/delete-detail`;
        if(x=="parent"){
            route = `rab-kegiatan/delete`;
        }

        $.get(`{{ url("/") }}/keuangan/`+route+`/` + id,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    window.location.reload();
                }else{
                    $("#loadingSubmit").hide();
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html(return_data.message);
                }
            }
        );
    }
}

function submitKegiatan(tipe,detail_of){
    if (confirm('Apakah RAB ini yakin akan di '+tipe+' ?')) {
        $("#loadingSubmit").show();
        var datax = {};
        datax['parent_id'] = detail_of.id;
        datax['status'] = tipe;
        $.post("{{ route('store rab kegiatan') }}", datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    window.location.reload();
                }else{
                    $("#loadingSubmit").hide();
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html(return_data.message);
                }
            }
        )
    }
}
</script>
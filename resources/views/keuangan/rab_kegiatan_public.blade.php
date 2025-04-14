@include('base.start_without_bars',['title' => "RAB ".strtoupper($detail_of->nama)])

<style>
    .new-td {
        padding: 2px 5px !important;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($detail_kegiatans!=null)
<div class="card border p-2 mt-2" style="border-top:solid 2px #f29393!important;">
    <div class="row align-items-center justify-content-center">
        <div class="col-md-12 text-center mb-2">
            <h6 class="m-0">
                Detail RAB Kegiatan: <b class="badge badge-primary">{{strtoupper($detail_of->nama)}}</b> 
                <?php
                    $badge = 'secondary';
                    if($detail_of->status=='approved'){
                        $badge = 'primary';
                    }elseif($detail_of->status=='rejected'){
                        $badge = 'danger';
                    }elseif($detail_of->status=='posted'){
                        $badge = 'success';
                    }
                ?>
                <span class="badge text-uppercase badge-{{$badge}}">{{$detail_of->status}} {{ ($badge=='submit') ? ': menunggu persetujuan pusat' : '' }}</span>
            </h6>
        </div>
        <div class="p-3 pt-0 pb-0">
            <p class="mb-0">Rencana Pelaksanaan: {{date_format(date_create($detail_of->periode_bulan), 'd M Y')}}</p>
            <p class="mb-0">Budget: <b>Rp {{number_format($detail_of->rab->biaya,0, ',', '.')}}</b></p>
            <p class="mb-0">Deskripsi:</p>
            <div class="p-2 mb-2 border" style="background-color:#f6f9fc;">{{ucwords($detail_of->deskripsi)}}</div>
        </div>
        <form action="{{ route('store detail rab kegiatan public') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="status" id="status" value="{{$detail_of->status}}" required>
            <input type="hidden" name="ids" id="ids" value="{{$ids}}" required>
            <input type="hidden" name="id" id="id" value="" required>
            <input type="hidden" name="parent_id_detail" id="parent_id_detail" value="{{$detail_of->id}}" required>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select class="form-control" value="" id="divisi" name="divisi" required>
                        <option value="">--pilih divisi--</option>
                        <option value="PERKAB">PERKAB</option>
                        <option value="KONSUMSI">KONSUMSI</option>
                        <option value="ACARA">ACARA</option>
                        <option value="PUBDOK">PUBDOK</option>
                        <option value="KEBERSIHAN">KEBERSIHAN</option>
                        <option value="HUMAS">HUMAS</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <input class="form-control" type="text" id="uraian" name="uraian" placeholder="Uraian / Item" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-1 mb-2">
                    <input class="form-control" type="number" id="qty" name="qty" placeholder="Qty" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-1 mb-2">
                    <input class="form-control" type="text" id="satuan" name="satuan" placeholder="Satuan" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                </div>
                <div class="col-md-3 mb-2">
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
                                <td colspan="11" class="new-td font-weight-bolder text-italic"><span class="badge badge-secondary">{{$mb->divisi}}</span></td>
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
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<script>
function ubah(x,data,status){
    if(x=="detil"){
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
        route = `rab/delete-detail`;

        $.get(`{{ url("/") }}/`+route+`/` + id,
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
</script>
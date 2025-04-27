<?php $total = 0; $total_realisasi = 0; $divisi = "";?>
@if($detail_kegiatans!=null)
    @if($form_url=='')
    <div class="text-end">
        <a href="{{route('rab kegiatan')}}" class="btn btn-sm btn-outline-secondary text-end">Kembali</a>
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku'))
            <a href="{{ url('keuangan/jurnal') }}" class="btn btn-sm btn-outline-primary text-end">Ke Jurnal</a>
        @endif
    </div>
    @endif
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
            <div class="p-3 pt-0 pb-0">
                <p class="mb-0">Rencana Pelaksanaan: {{date_format(date_create($detail_of->periode_bulan), 'd M Y')}}</p>
                <p class="mb-0">Budget: <b>Rp {{number_format($detail_of->rab->biaya,0, ',', '.')}}</b></p>
                <p class="mb-0">Deskripsi:</p>
                <div class="p-2 mb-2 border" style="background-color:#f6f9fc;">{{ucwords($detail_of->deskripsi)}}</div>
            </div>
            @if($detail_of->status=='draft')
                <form action="{{ route('store detail rab kegiatan'.$form_url) }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="status" id="status" value="{{$detail_of->status}}" required>
                    <input type="hidden" name="ids" id="ids" value="{{$ids}}" required>
                    <input type="hidden" name="id" id="id" value="" required>
                    <input type="hidden" name="parent_id_detail" id="parent_id_detail" value="{{$detail_of->id}}" required>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select data-mdb-filter="true" class="select form-control" value="" id="divisi" name="divisi" required>
                                <option value="">--pilih divisi--</option>
                                <option value="PERKAB">PERKAB</option>
                                <option value="KONSUMSI">KONSUMSI</option>
                                <option value="ACARA">ACARA</option>
                                <option value="PUBDOK">PUBDOK</option>
                                <option value="KEBERSIHAN">KEBERSIHAN</option>
                                <option value="HUMAS">HUMAS</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input class="form-control" type="text" id="uraian" name="uraian" placeholder="Uraian" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                        </div>
                        <div class="col-md-1 mb-2">
                            <input class="form-control" type="number" id="qty" step="0.01" name="qty" placeholder="Qty" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                        </div>
                        <div class="col-md-1 mb-2">
                            <input class="form-control" type="text" id="satuan" name="satuan" placeholder="Satuan" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                        </div>
                        <div class="col-md-3 mb-2">
                            <input class="form-control" onkeyup="formatRupiah(this.value,'biaya')" type="text" value="RP " id="biaya" name="biaya" placeholder="Biaya" required {{($detail_of->status=='approved') ? 'readonly' : ''}}>
                        </div>
                        <div class="col-md-1 mb-2" style="display:none;">
                            @if($detail_of->status=="draft" || $detail_of->status=="submit")
                            <button type="submit" class="btn btn-primary btn-sm mb-0">
                                <i class="fas fa-save" aria-hidden="true"></i>
                                SIMPAN
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="card border mt-2">
        <div class="card-body p-0">
            <div class="datatablex table-responsive" data-mdb-borderless="true" data-mdb-sm="true" data-mdb-bordered="false">
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
                                <td class="new-td">
                                    <input style="width:200px;" onchange="updateValue('uraian',this.value,{{$mb->id}},1)" class="form-control" type="text" id="uraian{{$mb->id}}" value="{{strtoupper($mb->uraian)}}" name="uraian{{$mb->id}}" placeholder="Uraian" required {{($detail_of->status=='draft') ? '' : 'readonly'}}>
                                </td>

                                <td class="new-td text-center">
                                    <input style="width:70px;" onchange="updateValue('qty',this.value,{{$mb->id}},1)" class="form-control" type="number" id="qty{{$mb->id}}" value="{{$mb->qty}}" step="0.01" name="qty{{$mb->id}}" placeholder="Qty" required {{($detail_of->status=='draft') ? '' : 'readonly'}}>
                                </td>
                                <td class="new-td text-center">
                                    <input style="width:70px;" class="form-control" onchange="updateValue('satuan',this.value,{{$mb->id}},1)" type="text" id="satuan{{$mb->id}}" value="{{strtoupper($mb->satuan)}}" name="satuan{{$mb->id}}" placeholder="Satuan" required {{($detail_of->status=='draft') ? '' : 'readonly'}}>
                                </td>
                                <td class="new-td text-end">
                                    <input class="form-control text-end" onchange="updateValue('biaya',this.value,{{$mb->id}},1)" onkeyup="formatRupiah(this.value,'biaya{{$mb->id}}')" type="text" id="biaya{{$mb->id}}" value="RP {{number_format($mb->biaya,0, ',', '.')}}" name="biaya{{$mb->id}}" placeholder="Biaya" required {{($detail_of->status=='draft') ? '' : 'readonly'}}>
                                </td>
                                <?php $total = $total + ($mb->qty*$mb->biaya); ?>
                                <td class="new-td text-end">
                                    <input class="form-control text-end t-total" onchange="setTotal('qty','biaya','total',{{$mb->id}})" type="text" id="total{{$mb->id}}" value="RP {{number_format(($mb->qty*$mb->biaya),0, ',', '.')}}" required readonly>
                                </td>

                                <td class="new-td text-center">
                                    <input style="width:70px;" class="form-control" onchange="updateValue('qty_realisasi',this.value,{{$mb->id}},2)" type="number" id="qty_realisasi{{$mb->id}}" value="{{$mb->qty_realisasi}}" step="0.01" name="qty_realisasi{{$mb->id}}" placeholder="Qty" required {{($detail_of->status=='approved') ? '' : 'readonly'}}>
                                </td>
                                <td class="new-td text-center">
                                    <input class="form-control" onchange="updateValue('satuan_realisasi',this.value,{{$mb->id}},2)" type="text" id="satuan_realisasi{{$mb->id}}" value="{{strtoupper($mb->satuan_realisasi)}}" name="satuan_realisasi{{$mb->id}}" placeholder="Satuan" required {{($detail_of->status=='approved') ? '' : 'readonly'}}>
                                </td>
                                <td class="new-td text-end">
                                    <input class="form-control text-end" onchange="updateValue('biaya_realisasi',this.value,{{$mb->id}},2)" onkeyup="formatRupiah(this.value,'biaya_realisasi{{$mb->id}}')" type="text" id="biaya_realisasi{{$mb->id}}" value="RP {{number_format($mb->biaya_realisasi,0, ',', '.')}}" name="biaya_realisasi{{$mb->id}}" placeholder="Biaya Realisasi" required {{($detail_of->status=='approved') ? '' : 'readonly'}}>
                                </td>
                                <?php $total_realisasi = $total_realisasi + ($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                                <td class="new-td text-end">
                                    <input class="form-control text-end t-total-realisasi" onchange="setTotal('qty_realisasi','biaya_realisasi','total_realisasi',{{$mb->id}})" type="text" id="total_realisasi{{$mb->id}}" value="RP {{number_format(($mb->qty_realisasi*$mb->biaya_realisasi),0, ',', '.')}}" required readonly>
                                </td>
                                <?php $selisih = ($mb->qty*$mb->biaya)-($mb->qty_realisasi*$mb->biaya_realisasi); ?>
                                <td class="new-td text-end">
                                    <input class="form-control text-end" type="text" id="selisih{{$mb->id}}" value="RP {{number_format($selisih,0, ',', '.')}}" name="selisih{{$mb->id}}" readonly>
                                </td>
                                <td class="new-td text-center">
                                    @if($detail_of->status=='draft')
                                    <a block-id="return-false" href="#" class="btn btn-danger btn-sm mb-0" style="padding:3px 7px;border-radius:0px;" type="submit" value="Hapus" onclick="hapus('detil',{{$mb->id}})">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase font-weight-bolder ps-2"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-end">
                                <span id="all-total">RP {{number_format($total,0, ',', '.')}}</span>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-center"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-end">
                                <span id="all-total-realisasi">RP {{number_format($total_realisasi,0, ',', '.')}}</span>
                            </th>
                            <th class="text-uppercase font-weight-bolder ps-2 text-end"></th>
                            <th class="text-uppercase font-weight-bolder ps-2"></th>
                        </tr>

                        @if($form_url=='')
                            <?php
                            $display = "none";
                            $display_t = "none";
                            $display_tr = "none";
                            if($detail_of->rab->biaya < $total || $total < $total_realisasi){
                                $display = "content";
                                if($detail_of->rab->biaya < $total){
                                    $display_t = "block";
                                }
                                if($total < $total_realisasi){
                                    $display_tr = "block";
                                }
                            }
                            ?>
                            <tr id="tr-justifikasi" style="display:{{$display}};">
                                <th class="text-uppercase font-weight-bolder ps-2">Justifikasi</th>
                                <th colspan="4" class="text-uppercase font-weight-bolder ps-2 text-center">
                                    <textarea style="display:{{$display_t}};" placeholder="Alasan kenapa Pengajuan lebih banyak dari RAB" {{($detail_of->status=="posted") ? 'readonly' : ''}} rows="3" class="form-control" name="justifikasi-rab" id="justifikasi-rab">{{$detail_of->justifikasi_rab}}</textarea>
                                </th>
                                <th colspan="4" class="text-uppercase font-weight-bolder ps-2 text-center">
                                    <textarea style="display:{{$display_tr}};" placeholder="Alasan kenapa Realisasi lebih banyak dari Pengajuan" {{($detail_of->status=="posted") ? 'readonly' : ''}} rows="3" class="form-control" name="justifikasi-realisasi" id="justifikasi-realisasi">{{$detail_of->justifikasi_realisasi}}</textarea>
                                </th>
                                <th colspan="2" class="text-uppercase font-weight-bolder ps-2"></th>
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
                                    @if($form_url=='')
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
                                                <div class="card p-2 border mt-2">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <div class="col-8">
                                                                    <input type="date" value="{{date('Y-m-d')}}" class="form-control" name="tanggal-posting" id="tanggal-posting">
                                                                </div>
                                                                <div class="col-4 text-start ps-2">
                                                                    <button type="submit" id="posted" class="btn btn-secondary btn-sm mb-0" onclick="submitKegiatan('posted',{{$detail_of}})">
                                                                        <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
                                                                        POSTING JURNAL
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                </th>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif

<script>
let form_url = '<?php echo $form_url;?>';

function copyLink(ids){
    var route = `{{ url("/") }}/rab/`+ids;
    navigator.clipboard.writeText(route);
}

function formatRupiah(val,field){
    var format = toFormatRupiah(val);
    $("#"+field).val("RP "+format);
}

function setTotal(field1,field2,field3,id){
    var format = toFormatRupiah($("#"+field2+id).val());
    var number_string = format.replace(/[^,\d]/g, '').toString();
    var total = $("#"+field1+id).val() * parseFloat(number_string);
    $("#"+field3+id).val(toNumber(total).toUpperCase());

    // SELISIH
    var biaya = toFormatRupiah($("#total"+id).val());
    biaya = biaya.replace(/[^,\d]/g, '').toString();
    var biaya_realisasi = toFormatRupiah($("#total_realisasi"+id).val());
    biaya_realisasi = biaya_realisasi.replace(/[^,\d]/g, '').toString();
    var selisih = parseFloat(biaya) - parseFloat(biaya_realisasi);
    $("#selisih"+id).val(toNumber(selisih).toUpperCase());

    // TOTAL ALL
    var class_t_total = document.querySelectorAll(".t-total");
    var class_t_total_realisasi = document.querySelectorAll(".t-total-realisasi");
    var t_total = 0, t_total_realisasi = 0;
    for (var i = 0; i < class_t_total.length; i++) {
        t_total += parseFloat(class_t_total[i].value.replace(/[^,\d]/g, '').toString());
    }
    for (var i = 0; i < class_t_total_realisasi.length; i++) {
        t_total_realisasi += parseFloat(class_t_total_realisasi[i].value.replace(/[^,\d]/g, '').toString());
    }
    $("#all-total").html(toNumber(t_total).toUpperCase());
    $("#all-total-realisasi").html(toNumber(t_total_realisasi).toUpperCase());

    // JUSTIFIKASI
    var rab = <?php echo ($detail_of) ? $detail_of->rab->biaya : 0; ?>;
    if(rab < t_total || t_total < t_total_realisasi){
        $("#tr-justifikasi").show();
        if(rab < t_total){
            $("#justifikasi-rab").attr('style','display:content;');
        }else{
            $("#justifikasi-rab").hide();
        }
        if(t_total < t_total_realisasi){
            $("#justifikasi-realisasi").attr('style','display:content;');
        }else{
            $("#justifikasi-realisasi").hide();
        }
    }else{
        $("#tr-justifikasi").hide();
    }
}

function updateValue(field,value,id,t){
    var datax = {};
    datax['id'] = id;
    datax['t'] = t;
    datax['field'] = field;
    if(field=='biaya' || field=='biaya_realisasi'){
        datax['value'] = value.replace(/[^,\d]/g, '').toString();
    }else{
        datax['value'] = value;
    }
    $.post("{{ route('store detail by field'.$form_url) }}", datax,
        function(data, status) {
            if(t==1){
                setTotal('qty','biaya','total',id);
            }else if(t==2){
                setTotal('qty_realisasi','biaya_realisasi','total_realisasi',id);
            }
        }
    )
}

function ubah(x,data,status=null,duplicate=false){
    $('#s-dash').toggle('show');
    if(x=="parent"){
        if(!duplicate){
            $("#is_duplicate").val(0);
            $("#date").val(data.periode_bulan);
            $("#name").val(data.nama.toUpperCase());
        }else{
            $("#is_duplicate").val(1);
            $("#name").val(data.nama.toUpperCase()+" (Copy)");
        }
        $("#parent_id").val(data.id);
        $("#fkRab_id").val(data.fkRab_id);
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
    }
}

function hapus(x,id){
    if (confirm('Apakah RAB ini yakin akan dihapus ?')) {
        $("#loadingSubmit").show();
        if(form_url==''){
            var route = `keuangan/rab-kegiatan/delete-detail`;
            if(x=="parent"){
                route = `keuangan/rab-kegiatan/delete`;
            }
        }else{
            var route = `rab/delete-detail`;
        }

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

function submitKegiatan(tipe,detail_of){
    if(tipe=="submit" || tipe=="posted"){
        var budget = detail_of.rab.biaya;
        var total_rab = <?php echo $total; ?>;
        var total_realisasi = <?php echo$total_realisasi; ?>;
        if(tipe=="submit"){
            if(budget<total_rab && $("#justifikasi-rab").val()==""){
                alert("Berikan Justifikasi pada RAB");
                return false;
            }
            postData(tipe,detail_of)
        }
        if(tipe=="posted"){
            if(total_rab<total_realisasi && $("#justifikasi-realisasi").val()==""){
                alert("Berikan Justifikasi pada Realisasi");
                return false;
            }
            if(confirm("Apakah Anda yakin akan di Posting ke Jurnal ?")){
                postData(tipe,detail_of)
            }
        }
    }else if(tipe=="draft" && detail_of.status=="posted"){
        if(confirm("Status POSTED, jika akan diubah menjadi DRAFT maka catatan di Jurnal Keuangan akan terhapus, apakah Anda yakin ?")){
            postData(tipe,detail_of)
        }
    }else{
        if (confirm('Apakah RAB ini yakin akan di '+tipe+' ?')) {
            postData(tipe,detail_of)
        }
    }
}

function postData(tipe,detail_of){
    $("#loadingSubmit").show();
    var datax = {};
    datax['parent_id'] = detail_of.id;
    datax['status'] = tipe;
    datax['tanggal_posting'] = $("#tanggal-posting").val();
    datax['justifikasi_rab'] = $("#justifikasi-rab").val();
    datax['justifikasi_realisasi'] = $("#justifikasi-realisasi").val();
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
</script>
<div class="card border mb-2">
    <div class="card-body">
        <table class="table font-weight-bolder">
        @foreach($counts as $c)
            <tr>
                <td width="20%">{{$c->name}}</td>
                <td>: {{$c->total}}</td>
            </tr>
        @endforeach
        </table>
    </div>
</div>

<?php 
$lock_calendar = App\Helpers\CommonHelpers::settings()->lock_calendar;
$agenda_khusus = App\Helpers\CommonHelpers::agendaKhusus();
$hari = ['Sabtu','Minggu','Senin','Selasa','Rabu','Kamis','Jumat'];
$seq = 1;
$kelas = ['mt','reguler','pemb'];
$ke = 0;
?>

<h6>Status Kalender:  {{($lock_calendar) ? 'Dikunci' : 'Dibuka'}}</h6>

<?php
for($i=1; $i<=8; $i++){
?>
<div class="card border mb-2">
    <div class="card-body">
        <div class="row">
            @for($j=1; $j<=4; $j++)
                @if($seq<=31)
                    <div class="col-md-3">
                        <table class="table table-sm table-bordered align-items-center justify-content-center">
                            <tr colspan="3" class="font-weight-bolder"><b>Urutan <span class="badge badge-danger h6">{{$seq}}</span> | {{$hari[$ke]}}</b></tr>
                            <?php
                            $ke++;
                            if($ke==7){
                                $ke = 0;
                            }
                            $data_temp_shubuh = $template->where('sequence',$seq)->where('waktu','shubuh')->first();
                            $data_shubuh_id = 0;
                            $checkbox_shubuh = false;
                            $checkbox_shubuh_val = "";
                            if($data_temp_shubuh){
                                if($data_temp_shubuh->is_agenda_khusus){
                                    $data_shubuh_id = $data_temp_shubuh->id;
                                    $checkbox_shubuh = true;
                                    $checkbox_shubuh_val = $data_temp_shubuh->nama_agenda_khusus;
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <b>Shubuh</b>
                                    <br>
                                    <div class="form-group">
                                        <label>Khusus</label>
                                        <input {{($lock_calendar) ? 'disabled readonly' : ''}} {{($checkbox_shubuh) ? 'checked' : ''}} onclick="onCheck('shubuh',{{$seq}})" class="form-check-input m-0" type="checkbox" id="check-{{$seq}}-shubuh" name="check-{{$seq}}-shubuh">
                                    </div>
                                </td>
                                <td>
                                    <div id="khusus-{{$seq}}-shubuh" style="display:{{($checkbox_shubuh) ? 'block' : 'none'}};">
                                        <select style="font-size: 13px !important;" {{($lock_calendar) ? 'disabled readonly' : ''}} onchange="onChange({{$data_shubuh_id}},'khusus','shubuh',{{$seq}})" data-mdb-filter="true" name="khusus-shubuh-{{$seq}}" id="khusus-shubuh-{{$seq}}" class="form-control cursor-pointer">
                                            <option value="">-</option>
                                            @foreach($agenda_khusus as $ak)
                                            <option {{($checkbox_shubuh_val==$ak) ? 'selected' : ''}} value="{{$ak}}">{{$ak}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="non-khusus-{{$seq}}-shubuh" style="display:{{(!$checkbox_shubuh) ? 'block' : 'none'}};">
                                        @foreach($kelas as $k)
                                            <?php
                                            $val_select_shubuh = "";
                                            $data_shubuh_id = 0;
                                            if(!$checkbox_shubuh){
                                                $data_temp_shubuh = $template->where('kelas',$k)->where('sequence',$seq)->where('waktu','shubuh')->first();
                                                if($data_temp_shubuh){
                                                    $data_shubuh_id = $data_temp_shubuh->id;
                                                    $val_select_shubuh = $data_temp_shubuh->fkDewanPengajar_id;
                                                }
                                            }
                                            ?>
                                            <select style="font-size: 13px !important;" {{($lock_calendar) ? 'disabled readonly' : ''}} onchange="onChange({{$data_shubuh_id}},'nonkhusus','shubuh',{{$seq}},'{{$k}}')" data-mdb-filter="true" name="{{$k}}-shubuh-{{$seq}}" id="{{$k}}-shubuh-{{$seq}}" class="form-control cursor-pointer">
                                                <option value="">{{strtoupper($k)}} -</option>
                                                @foreach($pengajars as $p)
                                                    <option {{($val_select_shubuh==$p->id) ? 'selected' : ''}} value="{{$p->id}}">{{strtoupper($k)}} | {{$p->name}}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $data_temp_malam = $template->where('sequence',$seq)->where('waktu','malam')->first();
                            $data_malam_id = 0;
                            $checkbox_malam = false;
                            $checkbox_malam_val = "";
                            if($data_temp_malam){
                                if($data_temp_malam->is_agenda_khusus){
                                    $data_malam_id = $data_temp_malam->id;
                                    $checkbox_malam = true;
                                    $checkbox_malam_val = $data_temp_malam->nama_agenda_khusus;
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <b>Malam</b>
                                    <br>
                                    <div class="form-group">
                                        <label>Khusus</label>
                                        <input {{($lock_calendar) ? 'disabled readonly' : ''}} {{($checkbox_malam) ? 'checked' : ''}} onclick="onCheck('malam',{{$seq}})" class="form-check-input m-0" type="checkbox" id="check-{{$seq}}-malam" name="check-{{$seq}}-malam">
                                    </div>
                                </td>
                                <td>
                                    <div id="khusus-{{$seq}}-malam" style="display:{{($checkbox_malam) ? 'block' : 'none'}};">
                                        <select style="font-size: 13px !important;" {{($lock_calendar) ? 'disabled readonly' : ''}} onchange="onChange({{$data_malam_id}},'khusus','malam',{{$seq}})" data-mdb-filter="true" name="khusus-malam-{{$seq}}" id="khusus-malam-{{$seq}}" class="form-control cursor-pointer">
                                            <option value="">-</option>
                                            @foreach($agenda_khusus as $ak)
                                            <option {{($checkbox_malam_val==$ak) ? 'selected' : ''}} value="{{$ak}}">{{$ak}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="non-khusus-{{$seq}}-malam" style="display:{{(!$checkbox_malam) ? 'block' : 'none'}};">
                                        @foreach($kelas as $k)
                                            <?php
                                            $val_select_malam = ""; 
                                            $data_malam_id = 0;
                                            if(!$checkbox_malam){
                                                $data_temp_malam = $template->where('kelas',$k)->where('sequence',$seq)->where('waktu','malam')->first();
                                                if($data_temp_malam){
                                                    $data_malam_id = $data_temp_malam->id;
                                                    $val_select_malam = $data_temp_malam->fkDewanPengajar_id;
                                                }
                                            }
                                            ?>
                                            <select style="font-size: 13px !important;" {{($lock_calendar) ? 'disabled readonly' : ''}} onchange="onChange({{$data_malam_id}},'nonkhusus','malam',{{$seq}},'{{$k}}')" data-mdb-filter="true" name="{{$k}}-malam-{{$seq}}" id="{{$k}}-malam-{{$seq}}" class="form-control cursor-pointer">
                                                <option value="">{{strtoupper($k)}} -</option>
                                                @foreach($pengajars as $p)
                                                    <option {{($val_select_malam==$p->id) ? 'selected' : ''}} value="{{$p->id}}">{{strtoupper($k)}} | {{$p->name}}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        </table>    
                    </div>
                    <?php $seq++; ?>
                @endif
            @endfor
        </div>
    </div>
</div>
<?php
}
?>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    function onCheck(kbm,seq){
        var checkBox = document.getElementById("check-"+seq+"-"+kbm);
        if (checkBox.checked == true) {
            $("#khusus-"+seq+"-"+kbm).show();
            $("#non-khusus-"+seq+"-"+kbm).hide();

            $("#reguler-"+kbm+"-"+seq).val("");
            $("#mt-"+kbm+"-"+seq).val("");
            $("#pemb-"+kbm+"-"+seq).val("");
        }else{
            $("#khusus-"+seq+"-"+kbm).hide();
            $("#non-khusus-"+seq+"-"+kbm).show();

            $("#khusus-"+kbm+"-"+seq).val("");
        }
    }

    function onChange(id,tipe,kbm,seq,kelas){
        // $("#loadingSubmit").show();
        var datax = {};
        datax['id'] = id;
        var checkBox = document.getElementById("check-"+seq+"-"+kbm);
        if (checkBox.checked == true) {
            datax['is_agenda_khusus'] = 1;
            datax['nama_agenda_khusus'] = $("#khusus-"+kbm+"-"+seq).val();
        }else{
            datax['is_agenda_khusus'] = 0;
            datax['nama_agenda_khusus'] = null;
        }
        datax['sequence'] = seq;
        datax['waktu'] = kbm;
        datax['kelas'] = kelas;
        datax['fkDewanPengajar_id'] = $("#"+kelas+"-"+kbm+"-"+seq).val();

        $.post("{{ route('store_template_kalender_ppm') }}", datax,
            function(data, status) {
                // window.location.reload();
            }
        )
    }
</script>
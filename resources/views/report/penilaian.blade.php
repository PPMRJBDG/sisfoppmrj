<style>
.datatable.datatable-sm th {
    background: #f6f9fc !important;
}
.datatable.datatable-sm td {
    padding: 2px 5px !important;
}
.form-control {
    border: transparent;
    border-bottom: solid 1px #dee2e6;
    font-size: 0.7rem !important;
}
</style>
<?php
$score_sangat_aman = 0;
$score_aman = 0;
$score_hati_hati = 0;
$score_tidak_aman = 0;
?>
<h6 class="font-weight-bold">Penilaian Mahasiswa</h6>

<div class="card border mb-2 p-2">
  <section class="text-center">
      <div class="row">
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
              <i class="fas fa-user fa-2x text-black mb-2"></i>
              <h5 class="text-black fw-bold mb-2" id="score-sangat-aman"></h5>
              <h6 class="fw-normal mb-0">Sangat Aman</h6>
              <div class="vr vr-blurry position-absolute my-0 h-100 d-none d-md-block top-0 end-0"></div>
          </div>
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
              <i class="fas fa-user fa-2x text-info mb-2"></i>
              <h5 class="text-info fw-bold mb-2" id="score-aman"></h5>
              <h6 class="fw-normal mb-0">Aman</h6>
              <div class="vr vr-blurry position-absolute my-0 h-100 d-none d-md-block top-0 end-0"></div>
          </div>
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
              <i class="fas fa-user fa-2x text-warning mb-2"></i>
              <h5 class="text-warning fw-bold mb-2" id="score-hati-hati"></h5>
              <h6 class="fw-normal mb-0">Hati-Hati</h6>
              <div class="vr vr-blurry position-absolute my-0 h-100 d-none d-md-block top-0 end-0"></div>
          </div>
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5 mb-lg-0 position-relative">
              <i class="fas fa-user fa-2x text-danger mb-2"></i>
              <h5 class="text-danger fw-bold mb-2" id="score-tidak-aman"></h5>
              <h6 class="fw-normal mb-0">Tidak Aman</h6>
          </div>
      </div>
  </section>
</div>

<div class="card">
  <div class="p-2">
      <input class="form-control" placeholder="Search" type="text" id="search" onkeyup="searchDataSantri('santrix',this.value)">
  </div>
  <div class="card-body p-2">
    <div id="santrix" class="datatable" data-mdb-sm="true" data-mdb-entries="200" data-mdb-hover="true" data-mdb-max-height="650" data-mdb-fixed-header="true">
      <table id="table" class="table justify-content-center align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th data-mdb-width="200" data-mdb-fixed="true">Nama</th>
            <th data-mdb-width="70" data-mdb-fixed="true">SKOR</th>
            <th>KEFAHAMAN</th>
            <th data-mdb-width="80">PELANGGARAN</th>
            <th>KBM-TOTAL</th>
            <th>KBM-HADIR</th>
            <th>KBM-IJIN</th>
            <th>KBM-ALPHA</th>
            <th data-mdb-width="50">IJIN MALAM</th>
            <th>AKHLAQ</th>
            <th>IBADAH</th>
            <th>TA'DZIM</th>
            <th>AMALSHOLIH</th>
            <th>PENAMPILAN</th>
            <th>KULIAH</th>
            <th>ORTU</th>
            <th>EKONOMI</th>
            <th>PERSUS</th>
          </tr>
        </thead>
        <tbody>
          @if(sizeof($mahasiswa) > 0)
            @foreach($mahasiswa as $mhs)
              <tr>
                <?php 
                  $pelanggaran = App\Models\Pelanggaran::where('fkSantri_id', $mhs->santri_id)->get();
                  $jam_malam = App\Models\TelatPulangMalams::where('fkSantri_id', $mhs->santri_id)->get();
                  $kehadiran = $mhs->hadir / $mhs->kbm * 100;
                  $score = App\Helpers\CountDashboard::score($mhs);
                  if($score['score']>=80){
                    $score_sangat_aman ++;
                  }elseif($score['score']<80 && $score['score']>=50){
                    $score_aman ++;
                  }elseif($score['score']<50 && $score['score']>=20){
                    $score_hati_hati ++;
                  }elseif($score['score']<20){
                    $score_tidak_aman ++;
                  }
                ?>
                <td>
                  <span class="santri-name text-left {{$score['score_text']}}" santri-name="{{ $mhs->fullname }}" onclick="getReport('<?php echo base64_encode($mhs->santri_id); ?>')" style="cursor:pointer;">
                      <b>[{{ $mhs->angkatan }}] {{ $mhs->fullname }}</b>
                  </span>
                </td>
                <td>
                  <center>
                    <span class="{{$score['score_text']}}" id="update-score{{$mhs->santri_id}}">
                      <?php
                        echo number_format($score['score'], 2);
                      ?>
                    </span>
                  </center>
                </td>
                <td>
                  <select class="form-control" name="kefahaman" id="kefahaman" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                      <option value="-"></option>
                      <option {{ ($mhs->kefahaman==0) ? 'selected' : '' }} value="0">Dzolimu Linafsih</option>
                      <option {{ ($mhs->kefahaman==1) ? 'selected' : '' }} value="1">Muqtashidun</option>
                      <option {{ ($mhs->kefahaman==2) ? 'selected' : '' }} value="2">Saabiqun Bil Khoirot</option>
                  </select>
                </td>
                <td>
                  <select class="form-control text-center">
                      <option>{{count($pelanggaran)}}</option>
                      @if(count($pelanggaran)>0)
                          @foreach($pelanggaran as $p)
                              <option>[{{$p->jenis->kategori_pelanggaran}}] {{$p->jenis->jenis_pelanggaran}}</option>
                          @endforeach
                      @endif
                  </select>
                </td>
                <td><center>{{ $mhs->kbm }}</center></td>
                <td>
                  <center>
                  <?php
                    $text_color = '';
                    if($kehadiran<80 && $kehadiran>50){
                      $text_color = 'text-warning';
                    }elseif($kehadiran<50){
                      $text_color = 'text-danger';
                    }
                  ?>
                  {{ $mhs->hadir }} | <b class="{{$text_color}}">{{ number_format($kehadiran, 2) }}%</b>
                  </center>
                </td>
                <td><center>{{ $mhs->ijin }} | <b>{{ number_format($mhs->ijin / $mhs->kbm * 100, 2) }}%</b></center></td>
                <td>
                  <center>
                  {{ $mhs->kbm-($mhs->hadir+$mhs->ijin) }} | <b>{{ number_format(($mhs->kbm-($mhs->hadir+$mhs->ijin)) / $mhs->kbm * 100, 2) }}%</b>
                  </center>
                </td>
                <td>
                  <select class="form-control text-center">
                      <option>{{count($jam_malam)}}</option>
                      @if(count($jam_malam)>0)
                          @foreach($jam_malam as $p)
                              <option>{{$p->alasan}}</option>
                          @endforeach
                      @endif
                  </select>
                </td>
                <td>
                    <select class="form-control" name="akhlaq" id="akhlaq" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->akhlaq==0) ? 'selected' : '' }} value="0">Tidak Baik</option>
                        <option {{ ($mhs->akhlaq==1) ? 'selected' : '' }} value="1">Cukup Baik</option>
                        <option {{ ($mhs->akhlaq==2) ? 'selected' : '' }} value="2">Baik</option>
                        <option {{ ($mhs->akhlaq==3) ? 'selected' : '' }} value="3">Sangat Baik</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="ibadah" id="ibadah" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->ibadah==0) ? 'selected' : '' }} value="0">Tidak Semangat</option>
                        <option {{ ($mhs->ibadah==1) ? 'selected' : '' }} value="1">Kurang Semangat</option>
                        <option {{ ($mhs->ibadah==2) ? 'selected' : '' }} value="2">Cukup Semangat</option>
                        <option {{ ($mhs->ibadah==3) ? 'selected' : '' }} value="3">Sangat Semangat</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="takdzim" id="takdzim" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->takdzim==0) ? 'selected' : '' }} value="0">Tidak Baik</option>
                        <option {{ ($mhs->takdzim==1) ? 'selected' : '' }} value="1">Cukup Baik</option>
                        <option {{ ($mhs->takdzim==2) ? 'selected' : '' }} value="2">Baik</option>
                        <option {{ ($mhs->takdzim==3) ? 'selected' : '' }} value="3">Sangat Baik</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="amalsholih" id="amalsholih" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->amalsholih==0) ? 'selected' : '' }} value="0">Tidak Pernah</option>
                        <option {{ ($mhs->amalsholih==1) ? 'selected' : '' }} value="1">Sangat Jarang</option>
                        <option {{ ($mhs->amalsholih==2) ? 'selected' : '' }} value="2">Cukup Sering</option>
                        <option {{ ($mhs->amalsholih==3) ? 'selected' : '' }} value="3">Sangat Sering</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="penampilan" id="penampilan" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->penampilan==0) ? 'selected' : '' }} value="0">Tidak Baik</option>
                        <option {{ ($mhs->penampilan==1) ? 'selected' : '' }} value="1">Kurang Baik</option>
                        <option {{ ($mhs->penampilan==2) ? 'selected' : '' }} value="2">Biasa Saja</option>
                        <option {{ ($mhs->penampilan==3) ? 'selected' : '' }} value="3">Berwibawa</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="kuliah" id="kuliah" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->kuliah==0) ? 'selected' : '' }} value="0">Bermasalah</option>
                        <option {{ ($mhs->kuliah==1) ? 'selected' : '' }} value="1">Lumayan Lancar</option>
                        <option {{ ($mhs->kuliah==2) ? 'selected' : '' }} value="2">Lancar</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="ortu" id="ortu" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->ortu==0) ? 'selected' : '' }} value="0">Semuanya Hidup</option>
                        <option {{ ($mhs->ortu==1) ? 'selected' : '' }} value="1">Perceraian</option>
                        <option {{ ($mhs->ortu==2) ? 'selected' : '' }} value="2">Ibu Meninggal</option>
                        <option {{ ($mhs->ortu==3) ? 'selected' : '' }} value="3">Bapak Meninggal</option>
                        <option {{ ($mhs->ortu==4) ? 'selected' : '' }} value="4">Keduanya Meninggal</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="ekonomi" id="ekonomi" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->ekonomi==0) ? 'selected' : '' }} value="0">Dhuafa</option>
                        <option {{ ($mhs->ekonomi==1) ? 'selected' : '' }} value="1">Kecukupan</option>
                        <option {{ ($mhs->ekonomi==2) ? 'selected' : '' }} value="2">Aghnia</option>
                        <option {{ ($mhs->ekonomi==3) ? 'selected' : '' }} value="3">Super Aghnia</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="persus" id="persus" onchange="updateEvaluasi(this,{{$mhs->santri_id}})">
                        <option value="-"></option>
                        <option {{ ($mhs->persus==0) ? 'selected' : '' }} value="0">Sangat Butuh</option>
                        <option {{ ($mhs->persus==1) ? 'selected' : '' }} value="1">Cukup Butuh</option>
                        <option {{ ($mhs->persus==2) ? 'selected' : '' }} value="2">Aman</option>
                    </select>
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

$("#score-sangat-aman").html("<?php echo $score_sangat_aman; ?>");
$("#score-aman").html("<?php echo $score_aman; ?>");
$("#score-hati-hati").html("<?php echo $score_hati_hati; ?>");
$("#score-tidak-aman").html("<?php echo $score_tidak_aman; ?>");

function updateEvaluasi(thisx,id){
  var datax = {};
  datax['santri_id'] = id
  datax['field'] = thisx.name
  datax['value'] = thisx.value
  $.post("{{ route('store evaluation') }}", datax,
      function(dataz, status) {
        var return_data = JSON.parse(dataz);
            if (return_data.status) {
              $("#update-score"+id).html(return_data.score.score)
              $("#update-score"+id).attr('class',return_data.score.score_text)
                // refreshCurrentUrl();
            }
      }
  );
}
</script>
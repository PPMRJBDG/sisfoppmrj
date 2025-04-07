
@if(!auth()->user())
  @include('base.start_without_bars', ['title' => "Kalender PPM"])
  <h5 class="text-center font-weight-bolder p-4">KALENDER PPM PERIODE {{App\Helpers\CommonHelpers::periode()}}</h5>
@endif

<?php
$GLOBALS['lock_calendar'] = App\Helpers\CommonHelpers::settings()->lock_calendar;
$GLOBALS['total_ngajar_all'] = [];
function build_calendar($month, $year, $today, $templates, $template, $start_seq, $start_tgl, $id_kalender_seq, $id_kalender_tgl, $kalender_conditions){
  $lock_calendar = $GLOBALS['lock_calendar'];
  $daysOfWeek = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
  $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
  $numberDays = date('t', $firstDayOfMonth);
  $dateComponents = getdate($firstDayOfMonth);
  $monthName = $dateComponents['month'];
  $dayOfWeek = $dateComponents['wday'];

  $total_ngajar = [];

  $selectoption = "";
  if(auth()->user()){
    if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('divisi kurikulum')){
      // for($x=2; $x>=1; $x--){
      //   if($x==1){
      //     $display = 'none';
      //     $option = 'Sequence';
      //     $label_select = '<label>Tanggal 1 Start Sequence:</label>';
      //     $id_kalender = $id_kalender_seq;
      //   }else{
      //     $display = 'block';
      //     $option = 'Tanggal';
      //     $label_select = '<label>Sequence 1 Start Tanggal:</label>';
      //     $id_kalender = $id_kalender_tgl;
      //   }
      //   $disabled = "";
      //   if($lock_calendar){
      //     $disabled = "disabled";
      //   }
      //   $selectoption .= "<div style='display:$display;' class='col-md'>$label_select<select $disabled data-mdb-filter='true' onchange='changeStart($x, this.value, $month, $id_kalender)' class='select form-control'>";
      //   $selectoption .= "<option value=''>--Pilih $option--</option>";
      //   foreach($template as $t){
      //     $selected = "";
      //     if($x==1 && $start_seq==$t->sequence){
      //       $selected = "selected";
      //     }elseif($x==2 && $start_tgl==$t->sequence){
      //       $selected = "selected";
      //     }
      //     $selectoption .= "<option $selected value='$t->sequence'>$option $t->sequence</option>";
      //   }
      //   $selectoption .= "</select></div>";
      // }

      $disabled = "";
      if($lock_calendar){
        $disabled = "disabled";
      }
      $selectoption .= "<div class='col-md'><label>Sequence 1 Start Tanggal:</label><select $disabled data-mdb-filter='true' onchange='changeStart(2, this.value, $month, $id_kalender_tgl)' class='select form-control'>";
      $selectoption .= "<option value=''>--Pilih Tanggal--</option>";
      $jumlah_tanggal = cal_days_in_month(CAL_GREGORIAN, $month, $year);
      for($i=1; $i<=$jumlah_tanggal; $i++){
        $currentDate = $year."-".$month."-".$i;
        $currentDay = strtolower(date_format(date_create($currentDate), 'l'));
        if($currentDay=='saturday'){
          $selected = "";
          if($start_tgl==$i){
            $selected = "selected";
          }
          $selectoption .= "<option $selected value='$i'>Tanggal $i</option>";
        }
      }
      $selectoption .= "</select></div>";
    }
  }

  $calendar = "<div class='row mb-2'><div class='col-md'><h6>$monthName $year</h6></div>";
  $calendar .= $selectoption."</div>";

  $calendar .= "<table class='table align-items-center table-responsive bg-white'>";
  $calendar .= "<tr>";
  foreach ($daysOfWeek as $day) {
    $calendar .= "<th class='header'>$day</th>";
  }
  $currentDay = 1;
  $calendar .= "</tr><tr>";
  if ($dayOfWeek > 0) {
    $calendar .= "<td colspan='$dayOfWeek' class='not-month'>&nbsp;</td>";
  }
  $month = str_pad($month, 2, "0", STR_PAD_LEFT);
  while ($currentDay <= $numberDays) {
    if ($dayOfWeek == 7) {
      $dayOfWeek = 0;
      $calendar .= "</tr><tr>";
    }
    $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
    $date = "$year-$month-$currentDayRel";

    $css1 = 'today-date';
    $css2 = '';
    if ($date == date("Y-m-d")) {
      $css1 = 'day-date';
      $css2 = 'today';
    }
    if($start_tgl == $currentDay){
      $start_seq = 1;
    }
    $label_seq = "";
    if(auth()->user()){
      if(!auth()->user()->hasRole('santri') || auth()->user()->hasRole('divisi kurikulum')){
        if(!$lock_calendar){
          $label_seq = "| <a href='#' onclick='showFormChange($month,$start_seq,`$monthName`,$currentDay)' class='text-white'>Seq $start_seq <i class='fa fa-edit text-white'></i></a>";
        }
      }
    }

    $calendar .= "<td class='day $css2' rel='$date'><span class='$css1 badge badge-secondary mb-1'>Tanggal $currentDay $label_seq<div class='calendar-presences-list'></div></span>";

    $check_liburan = App\Models\Liburan::where('liburan_from', '<=', $date)->where('liburan_to', '>=', $date)->get();
    if(count($check_liburan) == 0){
      $get_data = $templates->where('sequence',$start_seq);
      $certain_condition_waktu = "";
      foreach($get_data as $dt){
        $waktu = 'success';
        if($dt->waktu=='malam'){
          $waktu = 'warning';
        }

        $certain_condition = $kalender_conditions->where('waktu_certain_conditions',$dt->waktu)->where('start',$currentDay)->first();
        if($certain_condition){
          if($certain_condition_waktu != $dt->waktu){
            $libur = 'primary';
            if($certain_condition->nama_certain_conditions=="LIBUR"){
              $libur = 'danger';
            }
            $calendar .= '<br><small style="font-size:0.8em;"><span class="badge badge-'.$waktu.'">'.strtoupper($certain_condition->waktu_certain_conditions).'</span>: <span class="badge badge-'.$libur.'" style="font-size:1.0em!important;">'.strtoupper($certain_condition->nama_certain_conditions).'</span></small>';
            $certain_condition_waktu = $dt->waktu;
          }
        }else{
          $name_degur = "";
          if($dt->is_agenda_khusus){
            $libur = 'primary';
            if($dt->nama_agenda_khusus=="LIBUR"){
              $libur = 'danger';
            }
            $calendar .= '<br><small style="font-size:0.8em;"><span class="badge badge-'.$waktu.'">'.strtoupper($dt->waktu).'</span>: <span class="badge badge-'.$libur.'" style="font-size:1.0em!important;">'.strtoupper($dt->nama_agenda_khusus).'</span></small>';
          }elseif($dt->pengajar){
            array_push($total_ngajar, $dt->pengajar->name);
            array_push($GLOBALS['total_ngajar_all'], $dt->pengajar->name);
            
            $name_degur = $dt->pengajar->name;
            $calendar .= '<br><small style="font-size:0.8em;"><span class="badge badge-'.$waktu.'">'.strtoupper($dt->waktu).'</span> '.strtoupper($dt->kelas).': <b>'.strtoupper($name_degur).'</b></small>';
          }
        }
      }
    }else{
      $calendar .= "<div class=''><span class='badge badge-danger'>LIBURAN</span></div>";
    }

    $calendar .= "</td>";

    $currentDay++;
    $dayOfWeek++;
    $start_seq++;
  }
  if ($dayOfWeek != 7) {
    $remainingDays = 7 - $dayOfWeek;
    $calendar .= "<td colspan='$remainingDays' class='not-month'>&nbsp;</td>";
  }
  $calendar .= "</tr>";
  $calendar .= "</table>";

  // if(auth()->user()){
    $calendar .= "<table>";
    $dump_total_ngajar = array_count_values($total_ngajar);
    foreach($dump_total_ngajar as $keydtn => $val){
      if (str_contains($keydtn, 'Ust.')) {
        $calendar .= "<tr class='font-weight-bolder'><td>$keydtn</td><td>: $val</td>";
      }
    }
    $calendar .= "</table>";
  // }

  return $calendar;
}
?>

<style>
  caption {
    font-size: 22pt;
    margin: 10px 0 20px 0;
    font-weight: 700;
  }

  table.calendar {
    width: 100%;
    border: 1px solid #000;
    overflow-x: auto;
  }

  td.day {
    border: 1px solid #000;
    vertical-align: top;
    min-width: 128px;
    width: 14%;
    height: 160px;
    padding: 2px;
  }

  td.day span.day-date {
    font-size: 12px;
    font-weight: 700;
  }

  th.header {
    background-color: #003972;
    color: #fff;
    font-size: 12px;
    padding: 5px;
  }

  .not-month {
    background-color: #a6c3df;
  }

  td.today {
    border-width: 8px !important;
    border-color: lightgreen;
  }

  td.day span.today-date {
    font-size: 12px;
  }

  .calendar-presences-list {
    font-size: 14px;
  }

  .presence-label {
    padding: 2px
  }

  .btn-close {
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat !important;
  }

  .calendar-container {
    overflow: scroll;
  }
</style>

@if(auth()->user())
  @if(auth()->user()->hasRole('superadmin') && !$GLOBALS['lock_calendar'])
    <a onclick="return resetKalender()" type="button" href="#" class="btn btn-sm btn-danger mb-2">RESET</a>
    <script>
      function resetKalender(){
        if(confirm('Apakah yakin kalender akan di Reset ?')){
          $.get(`{{ route('reset_kalender_ppm') }}`,
            function(data, status) {
              window.location.reload();
            }
          );
        }
      }
    </script>
  @endif
@endif

<?php
$periode = App\Helpers\CommonHelpers::periode();
$periode = explode("-",$periode);
$month = ['09','10','11','12','01','02','03','04','05','06','07','08'];
$year = [$periode[0],$periode[0],$periode[0],$periode[0],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1]];

$height = "0";
if(auth()->user()){
  $height = "50px";
}

for($i=0; $i<12; $i++){
?>
<div class="card border mb-2" id="month-{{$i}}">
  <div class="" style="padding-top:{{$height}};"></div>
  <div class="card-body">
    <?php 
      $start_seq = 1;
      $start_tgl = 1;
      $id_kalender_seq = 0;
      $id_kalender_tgl = 0;
      if($kalenders){
        $kalenders1 = $kalenders->where('x',1)->where('bulan',$month[$i])->first();
        if($kalenders1){
          $start_seq = $kalenders1->start;
          $id_kalender_seq = $kalenders1->id;
        }
        $kalenders2 = $kalenders->where('x',2)->where('bulan',$month[$i])->first();
        if($kalenders2){
          $start_tgl = $kalenders2->start;
          $id_kalender_tgl = $kalenders2->id;
        }
        $kalender_conditions = $kalenders->where('is_certain_conditions',1)->where('bulan',$month[$i]);
      }
      
      echo build_calendar($month[$i], $year[$i], $today, $templates, $template, $start_seq, $start_tgl, $id_kalender_seq, $id_kalender_tgl, $kalender_conditions);
    ?>
  </div>
</div>
<?php
}
?>

<div class="card border mb-5 p-3">
<?php
$table = "<table>";
$dump_total_ngajar_all = array_count_values($GLOBALS['total_ngajar_all']);
foreach($dump_total_ngajar_all as $keydtn => $val){
  if (str_contains($keydtn, 'Ust.')) {
    $table .= "<tr class='font-weight-bolder border-bottom'><td width='50%'>$keydtn</td><td>: $val</td>";
  }
}
$table .= "</table>";
echo $table;
?>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
    $("#footer-calendar").show();
    document.getElementById('month-'+<?php echo array_search(date('m'), $month); ?>).scrollIntoView()
</script>

@if(auth()->user())
<script>
    function changeStart(x,val,month,id_kalender){
      $("#loadingSubmit").show();

      var datax = {};
      datax['ak'] = false;
      datax['id'] = id_kalender;
      datax['x'] = x;
      datax['bulan'] = month;
      datax['start'] = val;
      $.post("{{ route('store_kalender_ppm') }}", datax,
          function(data, status) {
              window.location.reload();
          }
      )
    }

    function showFormChange(bulan,sequence,nama_bulan,tanggal){
      $("#ak-bulan").val(bulan);
      $("#ak-tanggal").val(tanggal);
      $("#title-change-kalender-ppm").html("Buat Agenda Khusus Pada: "+tanggal+" "+nama_bulan);
      $("#showFormChange").show();
    }

    function changeFormKalenderAk(val){
      $("#loadingSubmit").show();

      var datax = {};
      datax['ak'] = true;
      datax['bulan'] = $("#ak-bulan").val();
      datax['start'] = $("#ak-tanggal").val();
      datax['waktu'] = $("#ak-waktu").val();
      datax['nama'] = val;
      $.post("{{ route('store_kalender_ppm') }}", datax,
          function(data, status) {
              window.location.reload();
          }
      )
    }
</script>
@endif
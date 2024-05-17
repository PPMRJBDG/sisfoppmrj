@include('base.start', ['path' => 'presensi/izin/saya', 'title' => 'Pengajuan Izin', 'breadcrumbs' => ['Daftar Izin Saya', 'Pegajuan Izin'],
'backRoute' => url()->previous() ? url()->previous() : route('my presence permits')
])
<?php
function build_calendar($month, $year)
{
  // Create array containing abbreviations of days of week.
  $daysOfWeek = array('Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat');
  // What is the first day of the month in question?
  $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
  // How many days does this month contain?
  $numberDays = date('t', $firstDayOfMonth);
  // Retrieve some information about the first day of the
  // month in question.
  $dateComponents = getdate($firstDayOfMonth);
  // What is the name of the month in question?
  $monthName = $dateComponents['month'];
  // What is the index value (0-6) of the first day of the
  // month in question.
  $dayOfWeek = $dateComponents['wday'];
  // Create the table tag opener and day headers
  $calendar = "<table class='calendar'>";
  $calendar .= "<caption>$monthName $year</caption>";
  $calendar .= "<tr>";
  // Create the calendar headers
  foreach ($daysOfWeek as $day) {
    $calendar .= "<th class='header'>$day</th>";
  }
  // Create the rest of the calendar
  // Initiate the day counter, starting with the 1st.
  $currentDay = 1;
  $calendar .= "</tr><tr>";
  // The variable $dayOfWeek is used to
  // ensure that the calendar
  // display consists of exactly 7 columns.
  if ($dayOfWeek > 0) {
    $calendar .= "<td colspan='$dayOfWeek' class='not-month'>&nbsp;</td>";
  }
  $month = str_pad($month, 2, "0", STR_PAD_LEFT);
  while ($currentDay <= $numberDays) {
    // Seventh column (Saturday) reached. Start a new row.
    if ($dayOfWeek == 7) {
      $dayOfWeek = 0;
      $calendar .= "</tr><tr>";
    }
    $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
    $date = "$year-$month-$currentDayRel";
    if ($date == date("Y-m-d")) {
      $calendar .= "<td class='day today' rel='$date'><span class='today-date'>$currentDay</span></td>";
    } else {
      $calendar .= "<td class='day' rel='$date'><span class='day-date'>$currentDay</span></td>";
    }
    // Increment counters
    $currentDay++;
    $dayOfWeek++;
  }
  // Complete the row of the last week in month, if necessary
  if ($dayOfWeek != 7) {
    $remainingDays = 7 - $dayOfWeek;
    $calendar .= "<td colspan='$remainingDays' class='not-month'>&nbsp;</td>";
  }
  $calendar .= "</tr>";
  $calendar .= "</table>";
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
  }

  td.day {
    width: 14%;
    height: 140px;
    border: 1px solid #000;
    vertical-align: top;
  }

  td.day span.day-date {
    font-size: 14pt;
    font-weight: 700;
  }

  th.header {
    background-color: #003972;
    color: #fff;
    font-size: 14pt;
    padding: 5px;
  }

  .not-month {
    background-color: #a6c3df;
  }

  td.today {
    background-color: #efefef;
  }

  td.day span.today-date {
    font-size: 16pt;
  }
</style>

<div class="card">
  <div class="card-body pt-4 p-3">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <form action="{{ route('store my presence permit') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-control-label">
            NB:<br>
            1. Silahkan izin dengan sebenar-benarnya dan penuh tanggung jawab<br>
            2. Bagi yang izin, mudah-mudahan lancar barokah, dan diampuni dosanya<br>
            3. Bagi yang izin karena sakit, mudah-mudahan Allah paring sembuh dan sehat barokah<br>
            4. Bagi yang <b>izin pulang, jangan lupa meminta SS</b><br>
            5. Bagi yang mengajar prasaringan/musyawarah tidak perlu izin<br>
            6. Jika memungkinkan, mengikuti KBM melalui SDC<br>
            7. Ijin ini akan dikirim otomatis via WA ke Koor Lorong dan Orang Tua
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group" style="border:solid 1px #ddd;padding:5px 8px;border-radius:4px;background:#f9f9f9;">
            Estimasi Jumlah KBM bulan ini: <b>{{ $data_kbm_ijin['kbm'] }}</b>
            <br>
            Jumlah kuota ijin: <b>{{ $data_kbm_ijin['kuota'] }} (30% dari total KBM)</b>
            <br>
            Jumlah ijin saat ini: <b>{{ $data_kbm_ijin['ijin'] }}</b>
            <br>
            Sisa kuota ijin: <b>{{ number_format(($data_kbm_ijin['kbm'] * 30 / 100) - $data_kbm_ijin['ijin'],0) }}</b>
          </div>
        </div>
      </div>
      @if($data_kbm_ijin['status'])
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Presensi untuk diajukan izin</label>
            <select name="fkPresence_id" class="form-control" required>
              <option selected disabled>Pilih presensi</option>
              @foreach($openPresences as $openPresence)
              <option value="{{ $openPresence->id }}" {{ isset($presenceId) ? ($presenceId == $openPresence->id ? 'selected' : '') : '' }}>{{ $openPresence->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Kategori alasan</label>
            <select name="reason_category" class="form-control" required onchange="checkSS(this)">
              <option value="">Pilih kategori alasan</option>
              @foreach(App\Models\JenisAlasanIjins::get() as $alasan)
              <option value="{{ $alasan->jenis_alasan }}">{{ $alasan->jenis_alasan }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="row" id="show-ss" style="display:none;">
        <div class="col-md-12">
          <div class="form-group">
            <label class="form-control-label">Apakah sudah meminta SS ?</label>
            <select name="status_ss" id="status_ss" disabled class="form-control" onchange="infoSS(this)">
              <option value="Setelah ini mau meminta">Setelah ini mau meminta</option>
              <option value="Belum, maaf mendadak tidak sempat">Belum, maaf mendadak tidak sempat</option>
              <option value="Belum, maaf posisi sudah di tempat tujuan">Belum, maaf posisi sudah di tempat tujuan</option>
              <option value="Belum, maaf dewan guru tidak ada di rumah">Belum, maaf dewan guru tidak ada di rumah</option>
              <option value="Tidak perlu membawa SS karena tujuan Bandung Raya">Tidak perlu membawa SS karena tujuan Bandung Raya</option>
              <option value="Alhamdulillah Sudah">Alhamdulillah Sudah</option>
            </select>
          </div>
          <label id="show-info-ss" style="display:none;" class="alert alert-danger text-white m-0 mb-2">Silahkan menghubungi dewan guru untuk mengirimkan foto SS</label>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Berikan Alasan yg Jelas <span style="color:#4d4d4d;">(minimal 10 karakter)</span></label>
            <textarea class="form-control" name="reason" minlength="10" placeholder="Cth: Sakit" required onkeyup="return checkTextLength(this)"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input style="display:none;" id="btn-prsc" class="btn btn-primary form-control" type="submit" value="Ajukan atas {{ auth()->user()->fullname }}">
          </div>
        </div>
      </div>
      @else
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <b class="text-danger">Mohon maaf, Kuota ijin Anda sudah habis :(</b>
          </div>
        </div>
      </div>
      @endif
    </form>
  </div>
</div>
@include('base.end')
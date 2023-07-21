@include('base.start', ['path' => '', 'title' => 'Dashboard', 'breadcrumbs' => ['Dashboard']])

<?php
    function build_presence_label_button($presence)
    {
      return "<div data-has-permit=" . ($presence->myPermit() ? '1' : '0') . " class='presence-label btn " . ($presence->myPresent() ? 'btn-success' : ($presence->myPermit() ? 'btn-warning' : ($presence->event_date < date('Y-m-d') ? 'btn-danger' : 'btn-primary'))) . "' data-presence-title='$presence->name' data-present='" . ($presence->myPresent() ? 1 : 0) . "' data-presence-id='$presence->id' data-bs-toggle='modal' data-bs-target='#presence-modal'>$presence->name</div>";
    }

    function build_calendar($month, $year, $presences) 
    {

      // Create array containing abbreviations of days of week.
      $daysOfWeek = array('Sun','Mon','Tues','Wed','Thurs','Fri','Sat');

      // What is the first day of the month in question?
      $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

      // How many days does this month contain?
      $numberDays = date('t',$firstDayOfMonth);

      // Retrieve some information about the first day of the
      // month in question.
      $dateComponents = getdate($firstDayOfMonth);

      // What is the name of the month in question?
      $monthName = $dateComponents['month'];

      // What is the index value (0-6) of the first day of the
      // month in question.
      $dayOfWeek = $dateComponents['wday'];

      // Create the table tag opener and day headers

      $calendar = "<caption>$monthName $year</caption>";
      $calendar .= "<table class='calendar'>";
      $calendar .= "<tr>";

      // Create the calendar headers

      foreach($daysOfWeek as $day) {
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

          if ($date == date("Y-m-d")){
            $calendar .= "<td class='day today' rel='$date'><span class='today-date'>$currentDay<div class='calendar-presences-list'>";

            foreach($presences as $presence)
            {
              if($presence->event_date == $date)
                $calendar .= build_presence_label_button($presence);
            }

            $calendar .= "</div></span></td>";
          }
          else{
            $calendar .= "<td class='day' rel='$date'><span class='day-date'>$currentDay<div class='calendar-presences-list'>";

            foreach($presences as $presence)
            {
              if($presence->event_date == $date)
                $calendar .= build_presence_label_button($presence);
            }

            $calendar .= "</div></span></td>";
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
    caption{font-size: 22pt; margin: 10px 0 20px 0; font-weight: 700;}
    table.calendar{width:100%; border:1px solid #000;}
    td.day{border: 1px solid #000; vertical-align: top;min-width: 128px; width: 14%; height: 128px}
    td.day span.day-date{font-size: 12px; font-weight: 700;}
    th.header{background-color: #003972; color: #fff; font-size: 12px; padding: 5px;}
    .not-month{background-color: #a6c3df;}
    td.today {border-width: 8px!important;border-color:lightgreen;}
    td.day span.today-date{font-size: 12px;font-weight: 700;}
    .calendar-presences-list {
      font-size:14px;
    }
    .presence-label {padding:2px}
    .btn-close {
      background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat!important;     
    }
    .calendar-container {
      overflow: scroll;
    }
  </style>

  <div class="card">
    <div class="card-body">
      <h6 class="mb-0">Selamat datang, {{ auth()->user()->fullname }}!</h6>
    </div>
  </div>
  @if(auth()->user()->hasRole('santri'))
    <!-- Modal -->
    <div class="modal fade" id="presence-modal" tabindex="-1" aria-labelledby="presence-modal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="presence-modal-title">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          @can('create presents')
            <div class="mb-3">
              <a id="presence-modal-presents-list" href="" class="btn btn-primary">Lihat daftar presensi</a>
            </div>
          @endcan
          @can('create presents')
            <div class="mb-3">
              <a id="presence-modal-input" href="" class="btn btn-primary">Input presensi</a>
            </div>
          @endcan
          <a id="presence-modal-permit" href="" class="btn btn-outline-primary mt-3">Izin</a>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Presensi Bulan Ini</h6>
      </div>
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
        @if (session('success'))
          <div class="alert alert-success text-white">
            {{ session('success') }}
          </div>
        @endif
        <ul class="list-group">      
          @if(sizeof($presences) <= 0)
            Tidak ada hasil.
          @endif
          <div class="calendar-container">
            <?php echo build_calendar(\Carbon\Carbon::now()->format('m'), \Carbon\Carbon::now()->format('Y'), $presences); ?>
          </div>
        </ul>
      </div>
    </div>
  @endif

  <script>
    $('.presence-label').click(e =>
    {
      let presenceTitle = $(e.currentTarget).attr('data-presence-title');
      let presenceId = $(e.currentTarget).attr('data-presence-id');
      let isPresent = $(e.currentTarget).attr('data-present') == 1 ? true : false;
      let hasPermit = $(e.currentTarget).attr('data-has-permit') == 1 ? true : false;

      $('#presence-modal-title').html(presenceTitle);
      $('#presence-modal-presents-list').attr('href', `{{ url('/') }}/presensi/list/${presenceId}`);
      $('#presence-modal-input').attr('href', `{{ url('/') }}/presensi/list/${presenceId}/present/create`);

      if(isPresent)
      {
        $('#presence-modal-permit').addClass('btn-success');
        $('#presence-modal-permit').removeClass('btn-outline-primary');
        $('#presence-modal-permit').html('Sudah presensi');
        $('#presence-modal-permit').attr('href', '#');
      }
      else if(hasPermit)
      {
        $('#presence-modal-permit').addClass('btn-outline-primary');
        $('#presence-modal-permit').removeClass('btn-success');
        $('#presence-modal-permit').css('opacity', 0.5);
        $('#presence-modal-permit').attr('disabled', 'true');
        $('#presence-modal-permit').html('Sudah diajukan izin');
        $('#presence-modal-permit').attr('href', '#');
      }
      else
      {
        $('#presence-modal-permit').addClass('btn-outline-primary');
        $('#presence-modal-permit').removeClass('btn-success');
        $('#presence-modal-permit').css('opacity', 1);
        $('#presence-modal-permit').removeAttr('disabled');
        $('#presence-modal-permit').html('Izin');
        $('#presence-modal-permit').attr('href', `{{ url('/') }}/presensi/izin/pengajuan?presenceId=${presenceId}`);
      }
      
    })
  </script>
@include('base.end')

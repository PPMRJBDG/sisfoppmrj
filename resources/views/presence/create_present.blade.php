@include('base.start', ['path' => 'presensi/list', 'title' => 'Presensi ' . (isset($presence) ? $presence->name : ''), 'breadcrumbs' => ['Daftar Presensi', 'Presensi ' . (isset($presence) ? $presence->name : '')],
  'backRoute' => url()->previous() ? url()->previous() : route('view presence', $presence->id)
])
  <style>
    .list-group-item-warning {
      background: lightyellow !important;
    }
  </style>
  @if(isset($presence))
    <div class="card">
      <div class="card-body pt-4 p-3 d-flex">        
        <div class="d-flex flex-column">
          <h6>Presensi {{ $presence->name }}</h6>
          @include('components.presence_summary', ['presence' => $presence])
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-body pt-4 p-3">
        @if ($errors != null)
          <div class="alert alert-danger text-white">
            <ul>
              @foreach ($errors as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form action="{{ route('store present', $presence->id) }}" method="POST">
          @csrf  
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="example-text-input" class="form-control-label">Pilih santri</label>
                <input type="text" id="search-fullname" class="form-control" placeholder="Cari nama...">
                <input type="hidden" id="santri-ids" name="santri-ids" class="form-control">
                <input type="hidden" id="late-santri-ids" name="late-santri-ids" class="form-control">
                <div id="santris-list" class="list-group mt-2">
                  @if(auth()->user()->santri->lorongUnderLead)
                    @foreach(auth()->user()->santri->lorongUnderLead->members as $santri)
                      <?php $exist = false; ?>
                      @foreach($presents as $present)
                        @if($present->santri->id == $santri->id)
                          <?php 
                            $exist = true; 
                            $isLate = $present->is_late == 1 ? true : false;
                          ?>                        
                        @endif
                      @endforeach
                      <a current-user-lorong-member="true" santri-id="{{ $santri->id }}" class="unselected santris-list-item list-group-item list-group-item-action {{ $exist ? ($isLate ? 'list-group-item-warning' : 'list-group-item-success') : 'list-group-item-primary' }}" style="{{ $exist ? 'pointer-events: none' : '' }}">{{ $santri->user->fullname }} {{ $exist ? '(Sudah presensi)' : '' }}</a>
                    @endforeach
                  @endif                  
                  @foreach($usersWithSantri as $user)
                    <?php 
                      $exist = false;
                      $printed = false;
                      $late = false;
                    ?>
                    @foreach($presents as $present)
                      @if($present->santri->id == $user->santri->id)
                        <?php 
                          $exist = true; 
                          $isLate = $present->is_late == 1 ? true : false;
                        ?>
                      @endif
                    @endforeach
                    @foreach(auth()->user()->santri->lorongUnderLead->members as $santri)
                      @if($santri->id == $user->santri->id)
                        <?php $printed = true; ?>
                      @endif
                    @endforeach
                    @if(!$printed)
                      <a santri-id="{{ $user->santri->id }}" class="unselected santris-list-item list-group-item list-group-item-action {{ $exist ? ($isLate ? 'list-group-item-warning' : 'list-group-item-success') : 'list-group-item-primary' }}" style="display:none;{{ $exist ? 'pointer-events: none' : '' }}">{{ $user->fullname }} {{ $exist ? '(Sudah presensi)' : '' }}</a>
                    @endif
                  @endforeach
                </div>
              </div>
            </div>
          </div>   
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input class="btn btn-primary form-control" type="submit" value="Presensi">
              </div>
            </div>
          </div>  
          <!-- <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="example-text-input" class="form-control-label">Atas nama</label>
                <select class="form-control" name="fkSantri_id">
                  @foreach($usersWithSantri as $user)
                    <option value="{{ $user->santri->id }}">{{ $user->fullname }} - {{ $user->santri->angkatan }}</option>
                  @endforeach
                  @if (sizeof($usersWithSantri) == 0) 
                    <option>Belum ada santri.</option>
                  @endif
                </select>
              </div>
            </div>
          </div>        -->             
        </form>
      </div>
    </div>
  @else
    <div class="card">
      <div class="card-body pt-4 p-3">
        <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
      </div>
    </div>
  @endif
  <script>
    $('#with-schedule').click(() =>
    {
      if($('#with-schedule').is(':checked'))
      {
        $('#schedule-list').show();
        $('#start-end-hour').show();
        
        $('[name="start-hour"], [name="end-hour"]').prop('type', 'time');      
      }
      else
      {
        $('#schedule-list').hide();
        $('#start-end-hour').show();

        $('[name="start-hour"], [name="end-hour"]').prop('type', 'datetime-local');
      }
    });

    $('[name="is-hours-scheduled"]').click(() =>
    {
      if($('[name="is-hours-scheduled"]').is(':checked'))
      {
        $('[name="start-hour"], [name="end-hour"]').attr('required', true); 
        $('#start-end-hour-inputs').show();
      }
      else
      {
        $('[name="start-hour"], [name="end-hour"]').removeAttr('required'); 
        $('#start-end-hour-inputs').hide();
      }
    })

    $('#search-fullname').keyup((e) => 
    {
      $('#santris-list').children().each((k, r) =>
      {
        let fullname = $(r).text();
        let keyword = e.currentTarget.value.toLowerCase();

        if(!fullname.toLowerCase().includes(keyword))
        {
          if(!$(r).hasClass('selected'))
            $(r).hide();          
        }
        else
        {
          if(keyword == '') 
          {
            if($(r).attr('current-user-lorong-member'))
              $(r).show();
            else
              $(r).hide();
          }
          else
            $(r).show();
        }
      })
    });

    let selectedSantris = [];
    let lateSelectedSantris = [];

    $('.santris-list-item').click(e =>
    {      
      if($(e.currentTarget).hasClass('unselected'))
      {               
        selectedSantris.push($(e.currentTarget));

        $(e.currentTarget).removeClass('unselected');
        $(e.currentTarget).addClass('selected');

        $(e.currentTarget).css('background', 'lightgreen');

        console.log(selectedSantris);
      }
      else if($(e.currentTarget).hasClass('selected'))
      {
        $(e.currentTarget).removeClass('selected');

        const santriId = $(e.currentTarget).attr('santri-id');
        const indexToRemove = selectedSantris.findIndex(e => $(e).attr('santri-id') == santriId);

        selectedSantris.splice(indexToRemove, 1);     

        lateSelectedSantris.push($(e.currentTarget));

        $(e.currentTarget).addClass('late-selected');
        $(e.currentTarget).css('background', 'yellow');

        console.log(lateSelectedSantris);
      }
      else
      {        
        $(e.currentTarget).addClass('unselected');
        $(e.currentTarget).removeClass('late-selected');
        $(e.currentTarget).css('background', '');

        const santriId = $(e.currentTarget).attr('santri-id');

        const indexToRemove = lateSelectedSantris.findIndex(e => $(e).attr('santri-id') == santriId);

        lateSelectedSantris.splice(indexToRemove, 1);           
      }

      $('#santri-ids').val(selectedSantris.map(e => $(e).attr('santri-id')).join(','));
      $('#late-santri-ids').val(lateSelectedSantris.map(e => $(e).attr('santri-id')).join(','));
    });
  </script>
@include('base.end')

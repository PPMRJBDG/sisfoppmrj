<style>
  @media only screen and (max-width: 600px) {

    body,
    h6 {
      font-size: 0.8rem !important;
    }
  }

  .py-4 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }
</style>

<div class="p-2">
  @if($presence)
  <div class="card">
    <div class="card-body p-2">
      <center>
        <h6>Daftar Hadir Angkatan {{$angkatan}} | {{$date .'/'. $month .'/'. $year}}</h6>
      </center>
      <div class="row text-center p-2">
        <div class="col-4">
          <i class="ni ni-check-bold text-info text-sm opacity-10"></i> Hadir
        </div>
        <div class="col-4">
          <i class="ni ni-user-run text-warning text-sm opacity-10"></i> Ijin
        </div>
        <div class="col-4">
          <i class="ni ni-fat-remove text-danger text-sm opacity-10"></i> Alpha
        </div>
      </div>
      <div class="table-responsive p-0">
        <table id="recap-table" class="table align-items-center mb-0">
          <thead style="background-color:#f6f9fc;">
            <tr>
              <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nama</th>
              @if(sizeof($presencesInDate) > 0)
              @foreach($presencesInDate as $presenceInDate)
              <th class="text-uppercase text-secondary text-xs font-weight-bolder">
                {{ $presenceInDate->presenceGroup ? $presenceInDate->presenceGroup->name : $presenceInDate->name}}
              </th>
              @endforeach
              @endif
            </tr>
          </thead>
          <tbody>
            @if(sizeof($mahasiswa) > 0)
            @foreach($mahasiswa as $mhs)
            <tr>
              <td>
                <h6 class="mb-0 text-xs">{{ $mhs->fullname }}</h6>
              </td>

              @if(sizeof($presencesInDate) > 0)
              @foreach($presencesInDate as $pid)
              <td>
                <?php
                $icon = 'fat-remove text-danger';
                foreach ($presents[$pid->id] as $present) {
                  if ($present->fkSantri_id == $mhs->santri->id) {
                    $icon = 'check-bold text-info';
                  }
                }
                if (sizeof($permits) > 0) {
                  foreach ($permits[$pid->id] as $permit) {
                    if ($permit->fkSantri_id == $mhs->santri->id) {
                      $icon = 'user-run text-warning';
                    }
                  }
                }
                ?>
                <i style="font-size:18px !important;" class="ni ni-{{$icon}} opacity-10"></i>
              </td>
              @endforeach
              @endif
            </tr>
            @endforeach
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  @if(sizeof($presencesInDate) > 0)
  <div class="card m-1">
    <div class="card-body p-2">
      <div class="alert alert-info text-white">Pilih presensi untuk mulai melihat.</div>
    </div>
  </div>
  @endif
  @endif
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>

@if($presence)
<script>
  $('#recap-table').DataTable({
    order: [
      // [1, 'desc']
    ],
    paging: false,
    searching: false
  });
</script>
@endif
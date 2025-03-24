<style>
.datatable.datatable-sm th {
    background: #f6f9fc !important;
}  
</style>

<h6 class="font-weight-bold">Daftar Kehadiran</h6>
<div class="card border p-2 mb-2">
    <div class="row">
        <div class="col-md-4 mb-2">
            <select data-mdb-filter="true" class="select select_kbm form-control mb-0" name="select_kbm" id="select_kbm">
                <option value="-">-- Pilih KBM --</option>
                @foreach($list_presence_group as $lpg)
                <option {{ ($select_kbm == $lpg->id) ? 'selected' : '' }} value="{{$lpg->id}}">{{$lpg->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select data-mdb-filter="true" class="select select_tb form-control mb-0" name="select_tb" id="select_tb">
                <option value="-">Keseluruhan</option>
                @foreach($tahun_bulan as $tbx)
                <option {{ ($select_tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="card">
  <div class="card-body p-2">
    <div class="datatable datatable-sm p-0" data-mdb-entries="200" data-mdb-striped="true" data-mdb-hover="true" data-mdb-max-height="650" data-mdb-fixed-header="true">
      <table id="table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder" data-mdb-width="300" data-mdb-fixed="true">Nama</th>
            @for($i=1; $i<=$days_in_month; $i++)
              <th class="text-uppercase text-secondary text-xs font-weight-bolder">
              {{$i}}
              </th>
            @endfor
          </tr>
        </thead>
        <tbody>
          @if(sizeof($mahasiswa) > 0)
            @foreach($mahasiswa as $mhs)
              <tr>
                <td>
                  {{ $mhs->fullname }}
                </td>

                @for($i=1; $i<=$days_in_month; $i++)
                  <td>
                    <?php
                    foreach($list_presence as $lp){
                      $dt = ($i<=9) ? '0'.$i : $i;
                      if($lp->event_date==$select_tb.'-'.$dt){
                        $present = App\Models\Present::where('fkPresence_id',$lp->id)->where('fkSantri_id',$mhs->santri_id)->first();
                        if($present){
                          echo '<i class="fa fa-check-square text-success"></i>';
                        }else{
                          $permit = App\Models\Permit::where('fkPresence_id',$lp->id)->where('fkSantri_id',$mhs->santri_id)->first();
                          if($permit){
                            echo '<i class="fa fa-exclamation text-warning"></i>';
                          }else{
                            echo '<i class="fa fa-xmark text-danger"></i>';
                          }
                        }
                      }
                    }
                    ?>
                  </td>
                @endfor
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

$('.select_kbm').change((e) => {
    var select_tb = $('#select_tb').val();
    getPage(`{{ url("/") }}/presensi/daily/` + select_tb + `/${$(e.currentTarget).val()}/`)
});

$('.select_tb').change((e) => {
    var select_kbm = $('#select_kbm').val();
    getPage(`{{ url("/") }}/presensi/daily/${$(e.currentTarget).val()}/` + select_kbm)
});
</script>
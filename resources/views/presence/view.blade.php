@include('base.start', [
'path' => 'presensi/list',
'title' => 'Presensi ' . (isset($presence) ? $presence->name : ''),
'breadcrumbs' => ['Daftar Presensi', 'Presensi ' . (isset($presence) ? $presence->name : '')],
'backRoute' => route('presence tm')
])

@if(isset($presence))
<div class="card">
  <div class="card-body p-2">
    <script>
      function togglePrsc() {
        $("#toggle-prsc").toggle();

        $("#info-update-presence").hide();
        $("#info-update").html('')
      }

      function isHasda(x) {
        if (x) {
          $("#p1").html('Penyampai Dalil / PPG');
          $("#p2").html('Penyampai Teks / Naslis');
        } else {
          $("#p1").html('Pengajar PPM 1');
          $("#p2").html('Pengajar PPM 2');
        }
      }
    </script>
    <center><button class="btn btn-primary btn-block mb-0" onclick="togglePrsc()">Presensi {{ $presence->name }}</button></center>
    <div id="toggle-prsc" style="display:none;">
      @include('components.presence_summary', ['presence' => $presence])
      @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('kurikulum'))
      <div class="row p-2 ">
        <div class="card-body p-2" style="background:#f9f9f9;border:#ddd 1px solid;">
          <div class="col-12 pb-2">
            <small>Nama KBM</small>
            <input class="form-control" value="{{$presence->name}}" id="presence_name" name="presence_name" type="text">
          </div>
          <div class="col-12 pb-2">
            <small id="p1">{{ ($presence->is_hasda) ? 'Penyampai Dalil / PPG' : 'Pengajar PPM 1' }}</small>
            <select name="dewan_pengajar1" id="dewan_pengajar1" class="form-control">
              <option value="">Pilih Dewan Pengajar PPM 1</option>
              @foreach($dewan_pengajar as $dp)
              <option {{($presence->fkDewan_pengajar_1==$dp->id) ? 'selected' : '' }} value="{{$dp->id}}">{{$dp->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 pb-2">
            <small id="p2">{{ ($presence->is_hasda) ? 'Penyampai Teks / Naslis' : 'Pengajar PPM 2' }}</small>
            <select name="dewan_pengajar2" id="dewan_pengajar2" class="form-control">
              <option value="">Pilih Dewan Pengajar PPM 2</option>
              @foreach($dewan_pengajar as $dp)
              <option {{($presence->fkDewan_pengajar_2==$dp->id) ? 'selected' : '' }} value="{{$dp->id}}">{{$dp->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 p-2 pl-0">
            <div class="form-group form-check mb-0">
              <label>Hasda</label>
              <input class="form-check-input" onchange="isHasda(this.checked)" type="checkbox" {{ ($presence->is_hasda) ? 'checked' : '' }} id="is_hasda" name="is_hasda">
            </div>
            <div class="form-group form-check mb-0">
              <label>Disatukan</label>
              <input class="form-check-input" type="checkbox" {{ ($presence->is_put_together) ? 'checked' : '' }} id="is_put_together" name="is_put_together">
            </div>
          </div>
          <div class="col-12">
            <a style="width:100%;" class="btn btn-primary text-white px-3 mb-0" href="#" onclick="savePresenceName(<?php echo $presence->id; ?>)">Simpan</a>
          </div>
          <div class="card-header p-0 pt-2" id="info-update-presence" style="display:none;">
            <h6 class="mb-0 bg-warning p-1 text-white">
              <span id="info-update"></span>
            </h6>
          </div>
        </div>
      </div>
      @endif
      <div class="ms-auto text-end">
        @can('delete presences')
        <a class="btn btn-danger text-danger text-gradient px-3 mb-0" href="{{ route('delete presence', $presence->id) }}" onclick="return confirm('Yakin menghapus? Seluruh data terkait presensi ini akan ikut terhapus.')"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Hapus</a>
        @endcan
        @can('update presences')
        <!-- <a class="btn btn-primary text-white px-3 mb-0" href="{{ route('edit presence', $presence->id) }}"><i class="fas fa-pencil-alt text-white me-2" aria-hidden="true"></i>Ubah Lainnya</a> -->
        @endcan
      </div>
    </div>
  </div>
</div>

<div class="text-white text-sm font-weight-bolder text-center mt-2">
  <span>Jumlah {{ (auth()->user()->hasRole('koor lorong')) ? 'Anggota' : 'Mahasiswa' }} {{ $jumlah_mhs }}</span>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    @if (session('santri_is_present'))
    <div class="p-0 mt-2">
      <div class="alert alert-warning text-white mb-0">
        {{ session('santri_is_present') }}
      </div>
    </div>
    @endif
    @if (session('success'))
    <div class="p-0 mt-2">
      <div class="alert alert-success text-white mb-0">
        {{ session('success') }}
      </div>
    </div>
    @endif
  </div>
</div>

@if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
<div class="row">
  <div class="col-sm-12 col-md-12 mt-2">
    <select class="select_lorong form-control" name="select_lorong" id="select_lorong">
      <option value="-">Semua Lorong</option>
      @foreach($data_lorong as $l)
      <option {{ ($lorong==$l->id) ? 'selected' : '' }} value="{{$l->id}}">{{$l->name}}</option>
      @endforeach
    </select>
  </div>
</div>
@endif

<div class="tab mt-2">
  <button class="tablinks active" onclick="openTab(event, 'hadir')">Hadir <span id="c-hdr">{{count($presents)}}</span></button>
  <button class="tablinks" onclick="openTab(event, 'ijin')">Ijin {{count($permits)}}</button>
  <button class="tablinks" onclick="openTab(event, 'alpha')">Alpha <span id="c-alp">{{count($mhs_alpha)}}</span></button>
</div>

<div class="card tabcontent" id="hadir" style="display:block;">
  <div class="card-body px-0 pt-0 pb-2">
    <div style="font-size:11px;padding:10px;">Sudah melakukan presensi: <span id="nact" class="text-bold"></span></div>
    @if($update)
    <div class="card-body p-2" style="background:#f9f9f9;">
      <a id="btn-select-all" class="btn btn-danger btn-block btn-xs mb-0" href="#" onclick="alphaAll()">Alphakan Semua</a>
    </div>
    @endif
    <div class="table-responsive">
      <table id="table-hadir" class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($presents as $present)
          @if($present->santri->fkLorong_id==$lorong || $lorong=='-')
          <tr title="{{$present->metadata}}" id="trh{{$present->fkSantri_id}}" class="dtmhsh" val-id="{{$present->fkSantri_id}}" val-name="{{$present->santri->user->fullname}}" updated-by="{{$present->updated_by}}">
            <td class=" text-sm">
              <b>{{ $present->santri->user->fullname }}</b>
              <br>
              <small>{{ $present->created_at }}</small>
            </td>
            <td class="align-middle text-center text-sm" id="slbtnh-{{$present->fkSantri_id}}">
              @if($update)
              <small style="font-size: 9px;">{{ ($present->updated_by=='') ? '' : 'Updated by '.$present->updated_by}}</small><br>
              <a class="btn btn-danger btn-block btn-xs mb-0" href="#" onclick="selectForAlpha(<?php echo $present->fkSantri_id; ?>)">Alpha</a>
              @endif
            </td>
          </tr>
          @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card tabcontent" id="ijin" style="display:none;">
  @if(count($permits)>0 || count($need_approval)>0)
  <div class="card-header p-2 pb-0">
    <h6 class="mb-0 bg-warning p-1 text-white">
      Perlu persetujuan/ditolak: {{count($need_approval)}}
    </h6>
  </div>

  <div class="table-responsive">
    <table class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder"></th>
        </tr>
      </thead>
      <tbody>
        <!-- need approval -->
        @foreach($need_approval as $na)
        @if($na->santri->fkLorong_id==$lorong || $lorong=='-')
        <tr title="{{ ($na->approved_by=='') ? '' : 'Approved by '.$na->approved_by}} {{ ($na->rejected_by=='') ? '' : ' | Rejected by '.$na->rejected_by }}">
          <td class="text-sm">
            <b>{{ $na->santri->user->fullname }}</b>
            <br>
            <small>[{{ $na->reason_category }}] - {{ $na->reason }}</small>
          </td>
          <td class="align-middle text-center text-sm">
            <span class="text-danger font-weight-bolder">{{ $na->status }}</span>
            @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
            <br>

            <?php
            $url_promptApprovePermit = route('approve presence permit', ['presenceId' => $na->fkPresence_id, 'santriId' => $na->fkSantri_id, 'lorong' => $lorong]);
            ?>

            <a class="btn btn-success btn-xs mb-0" onclick="promptApprovePermit('{{$url_promptApprovePermit}}','{{$na->ids}}','{{$na->fkPresence_id}}','{{$na->fkSantri_id}}')">Approve</a>
            <a class="btn btn-primary btn-xs mb-0" onclick="promptDeleteAndPresent('{{$na->ids}}','{{$na->fkPresence_id}}','{{$na->fkSantri_id}}')">Hadir</a>
            @endif
          </td>
          <td class="align-middle text-xs">
            {{$na->alasan_rejected}}
          </td>
        </tr>
        @endif
        @endforeach

        @foreach($permits as $permit)
        @if($permit->santri->fkLorong_id==$lorong || $lorong=='-')
        <tr title="{{ ($permit->approved_by=='') ? '' : 'Approved by '.$permit->approved_by}} {{ ($permit->rejected_by=='') ? '' : ' | Rejected by '.$permit->rejected_by }}">
          <td class="text-sm">
            <b>{{ $permit->santri->user->fullname }}</b>
            <br>
            <small>[{{ $permit->reason_category }}] - {{ $permit->reason }}</small>
          </td>
          <td class="align-middle text-center text-sm">
            <span class="text-primary font-weight-bolder">{{ $permit->status }}</span>
            <br>

            <?php
            $url_promptRejectPermit = route('reject presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id, 'lorong' => $lorong, 'json' => true]);
            ?>

            @if($update)
            <a class="btn btn-warning btn-xs mb-0" onclick="promptRejectPermit('{{$url_promptRejectPermit}}','{{$permit->ids}}','{{$permit->fkPresence_id}}','{{$permit->fkSantri_id}}')">Reject</a>
            <a class="btn btn-primary btn-xs mb-0" onclick="promptDeleteAndPresent('{{$permit->ids}}','{{$permit->fkPresence_id}}','{{$permit->fkSantri_id}}')">Hadir</a>
            @endif
          </td>
          <td></td>
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="p-2 text-center text-sm">Tidak ada yang ijin</div>
  @endif
</div>

<div class="card tabcontent" id="alpha" style="display:none;">
  @if(count($mhs_alpha)>0)
  @if($update)
  <div class="card-body p-2 pb-0">
    <a id="btn-select-all" class="btn btn-primary btn-block btn-xs mb-0" href="#" onclick="hadirAll()">Hadirkan Semua</a>
  </div>
  @endif
  <div class="table-responsive">
    <table id="table-alpha" class="table align-items-center mb-0">
      <thead>
        <tr>
          <th class="text-uppercase text-xxs font-weight-bolder">Nama</th>
          <th class="text-uppercase text-xxs font-weight-bolder">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mhs_alpha as $mhs)
        @if($mhs['fkLorong_id']==$lorong || $lorong=='-')
        <tr id="tra{{$mhs['santri_id']}}" class="dtmhsa" val-id="{{$mhs['santri_id']}}" val-name="{{$mhs['name']}}">
          <td class="text-sm">
            <b>{{ $mhs['name'] }}</b>
          </td>
          <td class="text-sm" id="slbtna-{{$mhs['santri_id']}}">
            @if($update)
            <a class="btn btn-primary btn-block btn-xs mb-0" href="#" onclick="selectForHadir(<?php echo $mhs['santri_id']; ?>)">Hadir</a>
            @endif
          </td>
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="p-2 text-center text-sm">Tidak ada yang alpha</div>
  @endif
</div>

@else
<div class="card">
  <div class="card-body pt-4 p-3">
    <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
  </div>
</div>
@endif

<style>
  #table-hadir_filter,
  #table-alpha_filter {
    margin-top: 0px !important;
  }

  /* #table-hadir_filter label,
  #table-alpha_filter label,
  #table-hadir_filter label input,
  #table-alpha_filter label input {
    width: 100% !important;
  } */
</style>
<script>
  $('#table-hadir').DataTable({
    "paging": false,
    "ordering": false,
    "info": false
  });
  $('#table-alpha').DataTable({
    "paging": false,
    "ordering": false,
    "info": false
  });

  cekKoorLorong();

  function selectForAlpha(id) {
    const el = document.querySelector("#trh" + id);
    var name = el.getAttribute('val-name')
    $("#trh" + id).addClass('bg-primary text-white');
    $("#slbtnh-" + id).html('<span class="text-white"><small>loading</small></span>');
    savePresensi(id, name, 'delete');
  }

  function selectForHadir(id) {
    const el = document.querySelector("#tra" + id);
    var name = el.getAttribute('val-name')
    $("#tra" + id).addClass('bg-primary text-white');
    $("#slbtna-" + id).html('<span class="text-white"><small>loading</small></span>');
    savePresensi(id, name, 'present');
  }

  function alphaAll() {
    if (confirm('Apakah yakin dialphakan semua ?')) {
      $(".dtmhsh").addClass('bg-primary text-white');
      const el = document.querySelectorAll(".dtmhsh");
      var id = 0;
      for (var i = 0; i < el.length; i++) {
        id = el[i].getAttribute('val-id')
        var name = el[i].getAttribute('val-name')
        $("#slbtnh-" + id).html('<span class="text-white"><small>loading</small></span>');
        savePresensi(id, name, 'delete');
      }
    }
  }

  function hadirAll() {
    if (confirm('Apakah yakin dihadirkan semua ?')) {
      $(".dtmhsa").addClass('bg-primary text-white');
      const el = document.querySelectorAll(".dtmhsa");
      var id = 0;
      for (var i = 0; i < el.length; i++) {
        id = el[i].getAttribute('val-id')
        var name = el[i].getAttribute('val-name')
        $("#slbtna-" + id).html('<span class="text-white"><small>loading</small></span>');
        savePresensi(id, name, 'present');
      }
    }
  }

  async function savePresensi(santriId, name, st) {
    var lorong = '<?php echo $lorong; ?>';
    var user = '<?php echo auth()->user()->fullname; ?>';
    // console.log(lorong)
    $.get(`{{ url("/") }}/presensi/` + <?php echo $id; ?> + `/` + st + `/` + santriId + `?lorong=` + lorong + `&json=true`, null,
      function(data, status) {
        var return_data = JSON.parse(data);
        if (return_data.status) {
          var zxca = parseInt($("#c-hdr").html());
          var zxcb = parseInt($("#c-alp").html());
          var x = 'a'; // berarti proses delete
          var tedf = 'Hadir';
          var ghdf = 'alpha';
          var btn = 'primary';
          var datetime = '';
          if (st == 'present') {
            x = 'h'; // berarti proses present
            tedf = 'Alpha';
            ghdf = 'hadir';
            btn = 'danger';
            zxca++;
            zxcb--;
            $("#c-hdr").html(zxca)
            $("#c-alp").html(zxcb)
            datetime = '<br><small>' + dateTime() + '</small>';
          } else {
            zxca--;
            zxcb++;
            $("#c-hdr").html(zxca)
            $("#c-alp").html(zxcb)
          }

          var text = '<tr id="tr' + x + santriId + '" class="dtmhs' + x + '" val-id="' + santriId + '" val-name="' + name + '" updated-by="' + user + '">' +
            '<td class="text-sm">' +
            '<b>' + name + '</b>' + datetime +
            '</td>' +
            '<td class="align-middle text-center text-sm" id="slbtn' + x + '-' + santriId + '">' +
            '<small style="font-size: 9px;">Updated by ' + user + '</small><br>' +
            '<a class = "btn btn-' + btn + ' btn-block btn-xs mb-0" href = "#" onclick = "selectFor' + tedf + '(' + santriId + ')">' + tedf + '</a>' +
            '</td>' +
            '</tr>';

          if (x == 'a') {
            x = 'h';
          } else {
            x = 'a';
          }
          const element = document.getElementById("tr" + x + "" + santriId);
          element.remove();

          text = text + $("#table-" + ghdf + " tbody").html();
          $("#table-" + ghdf + " tbody").html(text);
          cekKoorLorong();
        }
      }
    );
  }

  async function cekKoorLorong() {
    var arr_name = [];
    const el = document.querySelectorAll(".dtmhsh");
    for (var i = 0; i < el.length; i++) {
      arr_name.push(el[i].getAttribute('updated-by'));

    }
    var uniqueNames = [];
    $.each(arr_name, function(i, elx) {
      if ($.inArray(elx, uniqueNames) === -1) uniqueNames.push(elx);
    });

    var dz = '';
    $.each(uniqueNames, function(i, x) {
      if (dz == '') {
        dz = x
      } else {
        dz = dz + ', ' + x
      }
    })
    $("#nact").html(dz)
  }

  function savePresenceName(id) {
    var datax = {};
    datax['json'] = true;
    datax['name'] = $("#presence_name").val();
    datax['dp1'] = $("#dewan_pengajar1").val();
    datax['dp2'] = $("#dewan_pengajar2").val();
    var checkBoxHasda = document.getElementById("is_hasda");
    datax['is_hasda'] = (checkBoxHasda.checked) ? 1 : 0;
    var checkBoxPutGe = document.getElementById("is_put_together");
    datax['is_put_together'] = (checkBoxPutGe.checked) ? 1 : 0;
    $("#info-update-presence").hide();
    $("#info-update").html('');

    $.post(`{{ url("/") }}/presensi/list/update/` + id, datax,
      function(data, status) {
        var return_data = JSON.parse(data);
        $("#info-update-presence").show();
        $("#info-update").html(return_data.message);
      }
    )
  }

  function dateTime() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var h = String(today.getHours()).padStart(2, '0');
    var i = String(today.getMinutes()).padStart(2, '0');
    var s = String(today.getSeconds()).padStart(2, '0');

    today = yyyy + '-' + mm + '-' + dd + ' ' + h + ':' + i + ':' + s;
    return today;
  }

  $('.select_lorong').change((e) => {
    window.location.replace(`{{ url("/") }}/presensi/list/<?php echo $id; ?>?lorong=${$(e.currentTarget).val()}`)
  })
</script>
@include('base.end')
@include('base.start', ['path' => 'presensi/izin/persetujuan', 'title' => 'Tolak / Terima Daftar Izin ' . (isset($lorong) ? $lorong->name : ''), 'breadcrumbs' => ['Daftar Izin', 'Daftar Izin ' . (isset($lorong) ? $lorong->name : '')]])
@if($santri && $lorong || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('superadmin'))
<div class="card">
  <div class="card-header p-2 align-items-center" style="background-color:#f6f9fc;">
    @role('superadmin|rj1|wk|koor lorong')
    <div class="row">
      <div class="col-md-12 col-sm-6">
        <a href="{{ route('create presence permit') }}" class="btn btn-primary btn-xs m-2 mb-0" style="float:right;">
          <i class="fas fa-plus" aria-hidden="true"></i> Buatkan Izin Mahasaiswa
        </a>
      </div>
    </div>
    @endrole
    <div class="row">
      <div class="col-6">
        <small>Pilih Tahun-Bulan</small>
        <select class="select_tb form-control" name="select_tb" id="select_tb">
          <option value="-">Silahkan Pilih</option>
          @foreach($tahun_bulan as $tbx)
          <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6">
        <small>Tampilkan</small>
        <select class="select_show form-control" name="select_show" id="select_show">
          <option {{ ($status=='pending') ? 'selected': '' }} value="pending">Pending</option>
          <option {{ ($status=='rejected') ? 'selected': '' }} value="rejected">Rejected</option>
          <option {{ ($status=='approved') ? 'selected': '' }} value="approved">Approved</option>
          <option {{ ($status=='all') ? 'selected': '' }} value="all">All</option>
        </select>
      </div>
    </div>
  </div>

  @if(auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('superadmin'))
  <div class="p-2 bg-gray-100">
    <div class="d-flex">
      <div class="col-4 p-0">
        <a style="width:100%;" href="#" class="btn btn-danger btn-xs m-0" onclick="return actionSave('delete');">
          Delete
        </a>
      </div>
      @if($status=='pending' || $status=='approved')
      <div class="col-4 p-0">
        <a style="width:100%;" href="#" class="btn btn-warning btn-xs m-0" onclick="return actionSave('reject');">
          Reject
        </a>
      </div>
      @endif
      @if($status=='pending' || $status=='rejected')
      <div class="col-4 p-0">
        <a style="width:100%;" href="#" class="btn btn-primary btn-xs m-0" onclick="return actionSave('approve');">
          Approve
        </a>
      </div>
      @endif
    </div>
  </div>
  @endif

  <div class="card-body px-0 pt-0 pb-2">
    @if ($errors->any())
    <div class="p-2">
      <div class="alert alert-danger text-white">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif
    @if (session('success'))
    <div class="p-2">
      <div class="alert alert-success text-white">
        {{ session('success') }}
      </div>
    </div>
    @endif

    <div class="table-responsive p-0">
      <table id="table-harian" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder">
              <input type="checkbox" class="custom-control-input" id="all-ids" onclick="selectAllCheckbox(this)">
            </th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($permits))
          @foreach($permits as $permit)
          <tr class="text-sm" id="prmt-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}">
            <td class=" text-center">
              <input type="checkbox" onclick="showInputAlasan(this, '{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}')" class="cls-ckb" santri-id="{{$permit->fkSantri_id}}" presence-id="{{$permit->fkPresence_id}}" id="ids{{$permit->fkSantri_id}}">
            </td>
            <td>
              <b>{{ $permit->santri->user->fullname }}</b>
              <br>
              <span class="text-xxs">{{ $permit->updated_at }}</span>
              <br>
              <span class="badge {{ $permit->status == 'pending' ? 'bg-gradient-secondary' : ($permit->status == 'approved' ? 'bg-gradient-success' : ($permit->status == 'rejected' ? 'bg-gradient-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
            </td>
            <td class="text-xs">
              <div>
                <i><b>{{ $permit->presence->name }}</b></i>
                <br>
                <div class="mt-1 mb-1">
                  <span class="text-primary">[{{ ucfirst($permit->reason_category) }}]</span>
                  <br>{{ $permit->reason }}
                </div>
              </div>
              <div id="asd-b-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}" style="display:none;">
                @if($permit->status!='rejected')
                <span class="text-danger">Jika ditolak, berikan alasannya</span>
                <input type="text" class="form-control" id="alasan-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}">
                @endif
              </div>

              <!-- @if($permit->status=='rejected' || $permit->status=='pending')
              @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
              <a href="{{ route('approve presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-success btn-xs mb-0">Terima</a>
              @endif
              @endif
              @if($permit->status=='approved' || $permit->status=='pending')
              <a href="{{ route('reject presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id]) }}" class="btn btn-warning btn-xs mb-0">Tolak</a>
              @endif
              <a href="{{ route('delete presence permit', ['presenceId' => $permit->fkPresence_id, 'santriId' => $permit->fkSantri_id, 'tb' => $tb]) }}" class="btn btn-danger btn-xs mb-0" onclick="return confirm('Yakin menghapus?')">Delete</a> -->
            </td>
            @endforeach
            @endif
          </tr>
        </tbody>
      </table>
    </div>

    @if(isset($rangedPermitGenerator))
    @if(count($rangedPermitGenerator)>0)
    <div class="table-responsive p-0">
      <div class="p-2 text-center text-bold bg-gray-100" style="border-bottom:#eee solid 2px;border-top:#ddd solid 2px;">
        Berjangka <span class="badge bg-gradient-secondary">pending</span>
      </div>
      <table id="table-berjangka" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Durasi</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Alasan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($rangedPermitGenerator as $rpg)
          <tr class="text-sm" id="rpg-{{$rpg->id}}">
            <td class="ps-4">
              <b>{{ $rpg->santri->user->fullname }}</b>
              <br>
              <span class="text-xxs">{{ $rpg->updated_at }}</span>
            </td>
            <td class="text-xs">
              <i><b>{{ $rpg->presenceGroup->name }}</b></i>
              <br>
              {{$rpg->from_date}} s.d {{$rpg->to_date}}
            </td>
            <td class="text-xs">
              <div>
                <div class="mt-1 mb-1">
                  <span class="text-primary">[{{ ucfirst($rpg->reason_category) }}]</span>
                  <br>{{ $rpg->reason }}
                </div>
              </div>
            </td>
            <td>
              @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
              <a onclick="return actionSaveRangePermit('{{$rpg->id}}','{{$rpg->fkSantri_id}}');" class="btn btn-success btn-xs mb-0">Terima</a>
              @endif
            </td>
            @endforeach
          </tr>
        </tbody>
      </table>
    </div>
    @endif
    @endif
  </div>
</div>
@else
<div class="card">
  <div class="card-body p-2">
    <div class="alert alert-danger text-white m-2">
      User ini bukanlah santri atau bukan seorang koor lorong.
    </div>
  </div>
</div>
@endif
<script>
  $('.select_tb').change((e) => {
    var status = $('#select_show').val();
    window.location.replace(`{{ url("/") }}/presensi/izin/persetujuan/${$(e.currentTarget).val()}/` + status)
  })

  $('.select_show').change((e) => {
    var tb = $('#select_tb').val();
    window.location.replace(`{{ url("/") }}/presensi/izin/persetujuan/` + tb + `/${$(e.currentTarget).val()}`)
  })

  $('#table').DataTable({
    order: [],
    paging: false,
    pageLength: 25
  });

  function showInputAlasan(thisx, sid) {
    if (thisx.checked) {
      $("#asd-b-" + sid).show();
    } else {
      $("#asd-b-" + sid).hide();
    }
  }

  function actionSave(action) {
    var datax = {};
    datax['json'] = true;

    const ival = []
    const el = document.querySelectorAll(".cls-ckb");
    for (var i = 0; i < el.length; i++) {
      if (el[i].checked) {
        if (action == 'reject') {
          var alasan = $("#alasan-" + el[i].getAttribute('presence-id') + '-' + el[i].getAttribute('santri-id')).val();
          if (alasan == '') {
            alert('Berikan alasan pada form yang masih kosong')
            return false;
          }
        }
        ival.push([el[i].getAttribute('presence-id'), el[i].getAttribute('santri-id'), alasan])
      }
    }
    if (ival.length == 0) {
      alert('Silahkan pilih minimal satu mahasiswa');
      return false
    } else {
      var pesan_action = '';
      var url = '/presensi/izin/saya/'
      if (action == 'delete') {
        pesan_action = 'menghapus';
        url = '/presensi/izin/persetujuan/'
      } else if (action == 'approve') {
        pesan_action = 'menyetujui';
      } else if (action == 'reject') {
        pesan_action = 'menolak';
      }
      if (confirm('Apakah anda yakin untuk ' + pesan_action + ' perijinan ini ?')) {
        datax['data_json'] = JSON.stringify(ival);
        $.get(`{{ url("/") }}` + url + action, datax,
          function(data, status) {
            var return_data = JSON.parse(data);
            if (return_data.status) {
              if (return_data.is_present != '') {
                alert(return_data.is_present)
              }
              window.location.reload();
            }
          }
        )
      }
    }
  }

  function actionSaveRangePermit(rpgId, santriId) {
    var datax = {};
    if (confirm('Apakah anda yakin untuk menyetujui perijinan berjangka ini ?')) {
      datax['rpgId'] = rpgId;
      datax['santriId'] = santriId;
      $.get(`{{ url("/") }}/presensi/izin/pengajuan/berjangka/approve`, datax,
        function(data, status) {
          var return_data = JSON.parse(data);
          if (return_data.status) {
            const element = document.getElementById("rpg-" + rpgId);
            element.remove();
          } else {
            alert(return_data.message)
          }
        }
      )
    }
  }
</script>
@include('base.end')
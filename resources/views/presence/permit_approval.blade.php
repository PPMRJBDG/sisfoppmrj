@if ($errors->any())
<div class="">
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
<div class="">
  <div class="alert alert-success text-white">
    {{ session('success') }}
  </div>
</div>
@endif

@if($santri && $lorong || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('superadmin'))
<div class="card shadow border p-2">
  <div class="align-items-center">
    @role('superadmin|rj1|wk|koor lorong')
    <div class="row mb-2">
      <div class="col-md">
        <a href="{{ route('create presence permit') }}" class="btn btn-primary btn-block">
          <i class="fas fa-plus" aria-hidden="true"></i> Buatkan Izin Mahasiswa
        </a>
      </div>
    </div>
    @endrole
    <div class="row mb-2">
      <div class="col-6">
        <small>Pilih Tahun-Bulan</small>
        <select data-mdb-filter="true" class="select select_tb select form-control" name="select_tb" id="select_tb">
          <option value="-">Silahkan Pilih</option>
          @foreach($tahun_bulan as $tbx)
          <option {{ ($tb == $tbx->ym) ? 'selected' : '' }} value="{{$tbx->ym}}">{{$tbx->ym}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6">
        <small>Tampilkan</small>
        <select data-mdb-filter="true" class="select select_show form-control" name="select_show" id="select_show">
          <option {{ ($status=='pending') ? 'selected': '' }} value="pending">Pending</option>
          <option {{ ($status=='rejected') ? 'selected': '' }} value="rejected">Rejected</option>
          <option {{ ($status=='approved') ? 'selected': '' }} value="approved">Approved</option>
          <option {{ ($status=='all') ? 'selected': '' }} value="all">All</option>
        </select>
      </div>
    </div>

    @if(auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk') || auth()->user()->hasRole('superadmin'))
    <div class="mt-2">
      <div class="d-flex">
        <div class="col-4 p-0">
          <a style="width:100%;" href="#" class="btn btn-outline-danger btn-sm btn-xs m-0" onclick="return actionSavePermit('delete');">
            Delete
          </a>
        </div>
        @if($status=='pending' || $status=='approved')
        <div class="col-4 p-0">
          <a style="width:100%;" href="#" class="btn btn-outline-warning btn-sm btn-xs m-0" onclick="return actionSavePermit('reject');">
            Reject
          </a>
        </div>
        @endif
        @if($status=='pending' || $status=='rejected')
        <div class="col-4 p-0">
          <a style="width:100%;" href="#" class="btn btn-outline-primary btn-sm btn-xs m-0" onclick="return actionSavePermit('approve');">
            Approve
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif

<div class="p-2 text-center font-weight-bolder">
  Harian <span class="badge badge-secondary">{{$status}}</span>
</div>
<div class="card shadow border p-2">
  <div class="datatable datatable-sm" data-mdb-entries="20">
    <table id="table-harian" class="table align-items-center mb-0">
      <thead>
        <tr>
          <th data-mdb-sort="false" class="text-uppercase text-center text-secondary text-xxs font-weight-bolder">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="all-ids" onclick="selectAllCheckbox(this)">
              <label class="form-check-label" for="all-ids"></label>
            </div>
          </th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Keterangan</th>
          @if($status=='rejected')
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Alasan di Reject</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @if(isset($permits))
        @foreach($permits as $permit)
        <tr class="text-sm" id="prmt-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}">
          <td class="p-2 text-center">
            <div class="form-check">
              <input type="checkbox" onclick="showInputAlasan(this, '{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}')" class="form-check-input cls-ckb" santri-id="{{$permit->fkSantri_id}}" presence-id="{{$permit->fkPresence_id}}" id="ids-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}">
              <label class="form-check-label" for="ids-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}"></label>
            </div>
          </td>
          <td class="p-2">
            <b>{{ $permit->santri->user->fullname }}</b>
            <br>
            <span class="text-xxs">{{ $permit->updated_at }}</span>
            <br>
            <span class="badge {{ $permit->status == 'pending' ? 'bg-secondary' : ($permit->status == 'approved' ? 'bg-success' : ($permit->status == 'rejected' ? 'bg-danger' : '')) }}">{{ ucwords($permit->status) }}</span>
            Perijinan ke: {{ $permit->ijin_kuota }}
          </td>
          <td class="p-2 text-xs">
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
              <div class="form-outline">
                <input type="text" class="form-control" id="alasan-{{$permit->fkPresence_id}}-{{$permit->fkSantri_id}}">
                <label class="form-label">Jika ditolak, berikan alasannya</label>
              </div>
              @endif
            </div>
          </td>
          @if($status=='rejected')
          <td class="p-2 text-xs">{{$permit->alasan_rejected}}</td>
          @endif
          @endforeach
          @endif
        </tr>
      </tbody>
    </table>
  </div>
</div>

@if(auth()->user()->hasRole('rj1') || auth()->user()->hasRole('superadmin'))
<div class="p-2 text-center font-weight-bolder">
  Berjangka <span class="badge badge-secondary">{{$status}}</span>
</div>
<div class="card shadow border p-2">
  <div class="datatable datatable-sm" data-mdb-entries="20">
    <table id="table-berjangka" class="table align-items-center mb-0">
      <thead>
        <tr>
          <th data-mdb-sort="false" class="text-uppercase text-center text-secondary text-xxs font-weight-bolder">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="all-ids-berjangka" onclick="selectAllCheckboxBerjangka(this)">
              <label class="form-check-label" for="all-ids-berjangka"></label>
            </div>
          </th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Nama</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Durasi</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Alasan</th>
          <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2"></th> -->
        </tr>
      </thead>
      <tbody>
        @if(count($rangedPermitGenerator)>0)
        @foreach($rangedPermitGenerator as $rpg)
        <tr class="text-sm" id="rpg-{{$rpg->id}}">
          <td class="p-2 text-center">
            <div class="form-check">
              <input type="checkbox" class="form-check-input cls-ckb-berjangka" santri-id="{{$rpg->fkSantri_id}}" presence-id="{{$rpg->id}}" id="ids-berjangka{{$rpg->id}}">
              <label class="form-check-label" for="ids-berjangka{{$rpg->id}}"></label>
            </div>
          </td>
          <td class="ps-4">
            <b>{{ ($rpg->santri) ? $rpg->santri->user->fullname : 'Terhapus' }}</b>
            <br>
            <span class="text-xxs">{{ $rpg->updated_at }}</span>
          </td>
          <td class="text-xs">
            <i><b>{{ isset($rpg->presenceGroup) ? $rpg->presenceGroup->name : '' }}</b></i>
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
          <!-- <td>
              @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1'))
              <a id="return-false" onclick="return actionSaveRangePermit('{{$rpg->id}}','{{$rpg->fkSantri_id}}');" class="btn btn-success btn-xs mb-0">Approve</a>
              @endif
            </td> -->
          @endforeach
          @endif
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endif
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
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $('.select_tb').change((e) => {
    var status = $('#select_show').val();
    getPage(`{{ url("/") }}/presensi/izin/persetujuan/${$(e.currentTarget).val()}/` + status)
  })

  $('.select_show').change((e) => {
    var tb = $('#select_tb').val();
    getPage(`{{ url("/") }}/presensi/izin/persetujuan/` + tb + `/${$(e.currentTarget).val()}`)
  })

  // $('#table-harian').DataTable({
  //   order: [],
  //   paging: false
  // });

  // $('#table-berjangka').DataTable({
  //   order: [],
  //   paging: false,
  // });
</script>
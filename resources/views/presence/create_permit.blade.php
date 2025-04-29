
<div class="card shadow border">
  @if ($errors->any())
  <div class="alert alert-danger text-white">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <nav>
    <div class="nav nav-tabs nav-fill nav-justified" id="nav-tab" role="tablist">
      <a data-mdb-ripple-init class="nav-link active" id="nav-harian-tab" data-bs-toggle="tab" href="#nav-harian" role="tab" aria-controls="nav-harian" aria-selected="true">Harian</a>
      <a data-mdb-ripple-init class="nav-link" id="nav-berjangka-tab" data-bs-toggle="tab" href="#nav-berjangka" role="tab" aria-controls="nav-berjangka" aria-selected="false">Berjangka</a>
    </div>

    <div class="tab-content p-2" id="nav-tabContent">
      <div class="tab-pane fade show active" id="nav-harian" role="tabpanel" aria-labelledby="nav-harian-tab">
        <div class="card-body p-2">
          <form action="{{ route('store presence permit') }}" method="post">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkSantri_id" class="form-control-label">Santri</label>
                  <select data-mdb-filter="true" id="fkSantri_id" name="fkSantri_id" required class="select form-control">
                    <option value="">Pilih santri</option>
                    @foreach($usersWithSantri as $userWithSantri)
                    <option value="{{ $userWithSantri->santri->id }}">{{ $userWithSantri->fullname }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Presensi untuk diajukan izin</label>
                  <select data-mdb-filter="true" name="fkPresence_id" required class="select form-control">
                    <option value="">Pilih pengajian</option>
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
                  <label for="reason_category" class="form-control-label">Kategori alasan</label>
                  <select data-mdb-filter="true" name="reason_category" id="reason_category" class="select form-control" required onchange="checkSS(this)">
                    <option value="">Pilih kategori alasan</option>
                    @foreach(App\Models\JenisAlasanIjins::orderBy('kategori_alasan','ASC')->get() as $alasan)
                    <option value="{{ $alasan->jenis_alasan }}">[{{ $alasan->kategori_alasan }}] {{ $alasan->jenis_alasan }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row" id="show-ss" style="display:none;">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-control-label">Apakah sudah meminta SS ?</label>
                  <select data-mdb-filter="true" name="status_ss" id="status_ss" disabled class="select form-control" onchange="infoSS(this)">
                    <option value="Setelah ini mau meminta">Setelah ini mau meminta</option>
                    <option value="Belum, maaf mendadak tidak sempat">Belum, maaf mendadak tidak sempat</option>
                    <option value="Belum, maaf posisi sudah di tempat tujuan">Belum, maaf posisi sudah di tempat tujuan</option>
                    <option value="Belum, maaf dewan guru tidak ada di rumah">Belum, maaf dewan guru tidak ada di rumah</option>
                    <option value="Tidak perlu membawa SS karena tujuan Bandung Raya">Tidak perlu membawa SS karena tujuan Bandung Raya</option>
                    <option value="Alhamdulillah Sudah">Alhamdulillah Sudah</option>
                  </select>
                </div>
                <label id="show-info-ss" style="display:none;" class="alert alert-danger text-white m-0 mb-2">Silahkan informasikan ke mahasiswa yang bersangkutan untuk menghubungi dewan guru dan meminta diirimkan foto SS</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline form-group">
                  <textarea class="form-control" id="reason1" name="reason" rows="4" minlength="10" required></textarea>
                  <label for="reason1" class="form-label">Berikan Alasan yg Jelas (Keperluannya apa dan dimana)</label>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="custom-control-label">Status</label>
            </div>
            <div class="row ms-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" checked name="status" id="customCheck1" value="pending">
                    <label class="form-check-label fs-6" for="customCheck1"><span class="badge badge-secondary">Pending</span></label>
                  </div>
                </div>
                @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="customCheck2" value="approved">
                    <label class="form-check-label fs-6" for="customCheck2"><span class="badge badge-success">Approved</span></label>
                  </div>
                </div>
                @endif
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <input class="btn btn-primary btn-block" type="submit" value="Ajukan">
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="tab-pane fade show" id="nav-berjangka" role="tabpanel" aria-labelledby="nav-berjangka-tab">
        <div class="card-body p-2">
          <form action="{{ route('store presence permit ranged') }}" method="post">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Santri</label>
                  <select data-mdb-filter="true" name="fkSantri_id" required class="select form-control">
                    <option value="">Pilih santri</option>
                    @foreach($usersWithSantri as $userWithSantri)
                    <option value="{{ $userWithSantri->santri->id }}">{{ $userWithSantri->fullname }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Presensi untuk diajukan izin</label>
                  <select data-mdb-filter="true" name="fkPresenceGroup_id" required class="select form-control" required>
                    <option value="">Pilih presensi</option>
                    <option value="all-kbm">Semua KBM (KBM Shubuh, KBM Malam, Apel Malam, Agenda Bulanan)</option>
                    @foreach($presenceGroups as $presenceGroup)
                    <option value="{{ $presenceGroup->id }}">Hanya {{ $presenceGroup->name }} Saja</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Dari tanggal</label>
                  <input name="from_date" type="date" required class="form-control" id="from-date" value="{{ isset($fromDate) ? $fromDate : '' }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Sampai tanggal</label>
                  <input name="to_date" type="date" required class="form-control" id="to-date" value="{{ isset($toDate) ? $toDate : '' }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Kategori alasan</label>
                  <select data-mdb-filter="true" name="reason_category" id="reason_category_jangka" class="select form-control" required onchange="checkSS(this)">>
                    <option value="">Pilih kategori alasan</option>
                    @foreach(App\Models\JenisAlasanIjins::get() as $alasan)
                    <option value="{{ $alasan->jenis_alasan }}">{{ $alasan->jenis_alasan }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row" id="show-ss-berjangka" style="display:none;">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-control-label">Apakah sudah meminta SS ?</label>
                  <select data-mdb-filter="true" name="status_ss" id="status_ss_berjangka" disabled class="select form-control" onchange="infoSS(this)">
                    <option value="Setelah ini mau meminta">Setelah ini mau meminta</option>
                    <option value="Belum, maaf mendadak tidak sempat">Belum, maaf mendadak tidak sempat</option>
                    <option value="Belum, maaf posisi sudah di tempat tujuan">Belum, maaf posisi sudah di tempat tujuan</option>
                    <option value="Belum, maaf dewan guru tidak ada di rumah">Belum, maaf dewan guru tidak ada di rumah</option>
                    <option value="Tidak perlu membawa SS karena tujuan Bandung Raya">Tidak perlu membawa SS karena tujuan Bandung Raya</option>
                    <option value="Alhamdulillah Sudah">Alhamdulillah Sudah</option>
                  </select>
                </div>
                <label id="show-info-ss-berjangka" style="display:none;" class="alert alert-danger text-white m-0 mb-2">Silahkan informasikan ke mahasiswa yang bersangkutan untuk menghubungi dewan guru dan meminta diirimkan foto SS</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Berikan Alasan yg Jelas (Keperluannya apa dan dimana)</label>
                  <textarea class="form-control" name="reason" minlength="10" required placeholder="Cth: Sakit"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="custom-control-label">Status</label>
            </div>
            <div class="row ms-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" checked name="status" id="customCheck3" value="pending">
                    <label class="form-check-label fs-6" for="customCheck3"><span class="badge badge-secondary">Pending</span></label>
                  </div>
                </div>
                @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="customCheck4" value="approved">
                    <label class="form-check-label fs-6" for="customCheck4"><span class="badge badge-success">Approved</span></label>
                  </div>
                </div>
                @endif
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <input class="btn btn-primary btn-block" type="submit" value="Ajukan">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </nav>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }

  $("#tab-harian").click(function() {
    tabAction();
  })
  $("#tab-berjangka").click(function() {
    tabAction();
  })

  function tabAction() {
    $("#show-ss").hide();
    $("#show-ss-berjangka").hide();
    $("#show-info-ss").hide();
    $("#show-info-ss-berjangka").hide();
    $("#reason_category").val("");
    $("#reason_category_jangka").val("");
    $("#status_ss").val("Setelah ini mau meminta");
    $("#status_ss_berjangka").val("Setelah ini mau meminta");
    const el = document.querySelector("#status_ss");
    const elb = document.querySelector("#status_ss_berjangka");
    el.disabled = true
    if (elb != null) {
      elb.disabled = true
    }
  }
</script>
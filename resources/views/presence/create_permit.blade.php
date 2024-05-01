@include('base.start', ['path' => 'presensi/izin/persetujuan', 'title' => 'Pengajuan Izin', 'breadcrumbs' => ['Daftar Izin Saya', 'Pegajuan Izin'],
'backRoute' => url()->previous() ? url()->previous() : route('my presence permits')
])
<div class="card">
  <div class="card-body p-3">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <label class="custom-control-label">Perijinan akan di Approve oleh Ketua / RJ / Wk</label>
    <div class="tab mt-2">
      <button class="tablinks active" onclick="openTab(event, 'harian')">Harian</button>
      <button class="tablinks" onclick="openTab(event, 'berjangka')">Berjangka</button>
    </div>

    <div class="card tabcontent" id="harian" style="display:block;">
      <div class="card-body p-2">
        <form action="{{ route('store presence permit') }}" method="post">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="fkPresence_id" class="form-control-label">Santri</label>
                <select name="fkSantri_id" class="form-control">
                  <option selected disabled>Pilih santri</option>
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
                <select name="fkPresence_id" class="form-control">
                  <option selected disabled>Pilih pengajian</option>
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
                <select name="reason_category" class="form-control" required>
                  <option value="">Pilih kategori alasan</option>
                  @foreach(App\Models\JenisAlasanIjins::get() as $alasan)
                  <option value="{{ $alasan->jenis_alasan }}">{{ $alasan->jenis_alasan }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="fkPresence_id" class="form-control-label">Alasan</label>
                <textarea class="form-control" name="reason" placeholder="Cth: Sakit"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="custom-control-label">Status</label>
          </div>
          <div class="row ms-4">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-secondary">Pending</span></label>
                  <input class="form-check-input" type="radio" checked name="status" value="pending">
                </div>
              </div>
              @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
              <div class="col-md-4">
                <div class="form-group form-check">
                  <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-success">Approved</span></label>
                  <input class="form-check-input" type="radio" name="status" value="approved">
                </div>
              </div>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input class="btn btn-primary form-control" type="submit" value="Ajukan">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card tabcontent" id="berjangka" style="display:none;">
      <div class="card-body p-2">
        <form action="{{ route('store presence permit ranged') }}" method="post">
          @csrf
          <div class="row">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Santri</label>
                  <select name="fkSantri_id" class="form-control">
                    <option selected disabled>Pilih santri</option>
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
                  <select name="fkPresenceGroup_id" class="form-control" required>
                    <option selected disabled>Pilih presensi</option>
                    <option value="all-kbm">Semua KBM (KBM Shubuh, KBM Malam, Apel Malam, MM Drh)</option>
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
                  <input name="from_date" type="date" class="form-control" id="from-date" value="{{ isset($fromDate) ? $fromDate : '' }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-text-input" class="form-control-label">Sampai tanggal</label>
                  <input name="to_date" type="date" class="form-control" id="to-date" value="{{ isset($toDate) ? $toDate : '' }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Kategori alasan</label>
                  <select name="reason_category" class="form-control" required>
                    <option value="">Pilih kategori alasan</option>
                    @foreach(App\Models\JenisAlasanIjins::get() as $alasan)
                    <option value="{{ $alasan->jenis_alasan }}">{{ $alasan->jenis_alasan }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fkPresence_id" class="form-control-label">Alasan</label>
                  <textarea class="form-control" name="reason" placeholder="Cth: Sakit" required></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="custom-control-label">Status</label>
            </div>
            <div class="row ms-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group form-check">
                    <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-secondary">Pending</span></label>
                    <input class="form-check-input" type="radio" checked name="status" value="pending">
                  </div>
                </div>
                @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('rj1') || auth()->user()->hasRole('wk'))
                <div class="col-md-4">
                  <div class="form-group form-check">
                    <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-success">Approved</span></label>
                    <input class="form-check-input" type="radio" name="status" value="approved">
                  </div>
                </div>
                @endif
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <input class="btn btn-primary form-control" type="submit" value="Ajukan">
                </div>
              </div>
            </div>
        </form>
      </div>
    </div>
    @include('base.end')
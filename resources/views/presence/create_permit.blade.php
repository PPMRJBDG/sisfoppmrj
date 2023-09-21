@include('base.start', ['path' => 'presensi/izin/persetujuan', 'title' => 'Pengajuan Izin', 'breadcrumbs' => ['Daftar Izin Saya', 'Pegajuan Izin'],
'backRoute' => url()->previous() ? url()->previous() : route('my presence permits')
])
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
              <option value="Sakit (di PPM)">Sakit (di PPM)</option>
              <option value="Kontrol Sakit">Kontrol Sakit</option>
              <option value="Opname">Opname</option>
              <option value="Musyawarah">Musyawarah</option>
              <option value="Tugas">Tugas</option>
              <option value="Kuliah Malam">Kuliah Malam</option>
              <option value="Praktikum Malam">Praktikum Malam</option>
              <option value="Jaga Malam">Jaga Malam</option>
              <option value="Pulang - Keluarga Meninggal">Pulang - Keluarga Meninggal</option>
              <option value="Pulang - Undangan Pernikahan Keluarga">Pulang - Undangan Pernikahan Keluarga</option>
              <option value="Pulang - Kontrol Sakit">Pulang - Kontrol Sakit</option>
              <option value="Pulang - Permintaan Ortu">Pulang - Permintaan Ortu</option>
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
              <input class="form-check-input" type="radio" name="status" value="pending">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-success">Approved</span></label>
              <input class="form-check-input" type="radio" name="status" value="approved">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-check">
              <label class="custom-control-label fs-6" for="customCheck1"><span class="badge bg-gradient-danger">Rejected</span></label>
              <input class="form-check-input" type="radio" name="status" value="rejected">
            </div>
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
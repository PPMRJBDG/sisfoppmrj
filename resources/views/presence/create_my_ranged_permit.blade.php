@include('base.start', ['path' => 'presensi/izin/saya', 'title' => 'Pengajuan Izin Berjangka', 'breadcrumbs' => ['Daftar Izin Saya', 'Pegajuan Izin'],
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
    <form action="{{ route('store my ranged presence permit') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-control-label">
            NB:<br>
            1. Silahkan izin dengan sebenar-benarnya dan penuh tanggung jawab<br>
            2. Bagi yang izin, mudah-mudahan lancar barokah, dan diampuni dosanya<br>
            3. Bagi yang izin karena sakit, mudah-mudahan Allah paring sembuh dan sehat barokah<br>
            4. Bagi yang mengajar prasaringan/musyawarah tidak perlu izin<br>
            5. Jika memungkinkan, mengikuti KBM melalui SDC
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Presensi untuk diajukan izin</label>
            <select name="fkPresenceGroup_id" class="form-control" required>
              <option selected disabled>Pilih presensi</option>
              @foreach($presenceGroups as $presenceGroup)
              <option value="{{ $presenceGroup->id }}">{{ $presenceGroup->name }}</option>
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
              <option selected disabled>Pilih kategori alasan</option>
              <option value="sakit">Sakit (di PPM)</option>
              <option value="kontrol_sakit">Kontrol Sakit</option>
              <option value="opname">Opname</option>
              <option value="tugas">Tugas</option>
              <option value="kuliah">Kuliah Malam</option>
              <option value="praktikum">Praktikum Malam</option>
              <option value="pulang_kel_meninggal">Pulang - Keluarga Meninggal</option>
              <option value="pulang_undangan_nikah">Pulang - Undangan Pernikahan Keluarga</option>
              <option value="pulang_kontrol">Pulang - Kontrol Sakit</option>
              <option value="pulang_ortu">Pulang - Permintaan Ortu (Ortu wajib minta ijin ke Ketua terlebih dahulu)</option>
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
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Ajukan atas {{ auth()->user()->fullname }}">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@include('base.end')
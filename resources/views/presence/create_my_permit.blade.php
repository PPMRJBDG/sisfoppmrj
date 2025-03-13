<div class="card">
  <div class="card-body p-2">
    @if ($errors->any())
    <div class="alert alert-danger text-white">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <form action="{{ route('store my presence permit') }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-control-label">
            NB:<br>
            1. Silahkan izin dengan sebenar-benarnya dan penuh tanggung jawab<br>
            2. Bagi yang izin, mudah-mudahan lancar barokah, dan diampuni dosanya<br>
            3. Bagi yang izin karena sakit, mudah-mudahan Allah paring sembuh dan sehat barokah<br>
            4. Bagi yang <b>izin pulang, jangan lupa meminta SS</b><br>
            5. Bagi yang mengajar prasaringan/musyawarah tidak perlu izin<br>
            6. Jika memungkinkan, mengikuti KBM melalui SDC<br>
            7. Perizinan ini akan dikirim otomatis via WA ke Ketertiban dan Orang Tua
          </div>
        </div>
      </div>
      <div class="row mt-2 mb-2">
        <div class="col-md-12">
          <div class="card shadow border p-2 bg-secondary text-white">
            <div class="col-md-12">
              Estimasi Jumlah KBM bulan ini: <b>{{ $data_kbm_ijin['kbm'] }}</b><br>
              Jumlah kuota ijin: <b>{{ $data_kbm_ijin['kuota'] }} (30% dari total KBM)</b><br>
              Jumlah ijin saat ini: <b>{{ $data_kbm_ijin['ijin'] }}</b><br>
              Sisa kuota ijin: <b>{{ number_format(($data_kbm_ijin['kbm'] * 30 / 100) - $data_kbm_ijin['ijin'],0) }}</b>
            </div>
          </div>
        </div>
      </div>
      @if($data_kbm_ijin['status'])
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Presensi untuk diajukan izin</label>
            <select data-mdb-filter="true" name="fkPresence_id" class="select form-control" required>
              <option value="">Pilih presensi</option>
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
            <select data-mdb-filter="true" name="reason_category" class="select form-control" required onchange="checkSS(this)">
              <option value="">Pilih kategori alasan</option>
              @foreach(App\Models\JenisAlasanIjins::get() as $alasan)
              <option value="{{ $alasan->jenis_alasan }}">{{ $alasan->jenis_alasan }}</option>
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
          <label id="show-info-ss" style="display:none;" class="alert alert-danger text-white m-0 mb-2">Silahkan menghubungi dewan guru untuk mengirimkan foto SS</label>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="fkPresence_id" class="form-control-label">Berikan Alasan yg Jelas (Keperluannya apa dan dimana)</label>
            <textarea class="form-control" name="reason" minlength="10" placeholder="Cth: Sakit" required onkeyup="return checkTextLength(this)"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input id="btn-prsc" class="btn btn-primary form-control" type="submit" value="Ajukan atas {{ auth()->user()->fullname }}">
          </div>
        </div>
      </div>
      @else
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <b class="text-danger">Mohon maaf, Kuota ijin Anda sudah habis :(</b>
          </div>
        </div>
      </div>
      @endif
    </form>
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>
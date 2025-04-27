<style>
.datatable.datatable-sm th {
    background: #f6f9fc !important;
}
.datatable.datatable-sm td {
    padding: 2px 5px !important;
}
.form-control {
    border: transparent;
    border-bottom: solid 1px #dee2e6;
    font-size: 0.7rem !important;
}
</style>

<h6 class="font-weight-bold">Penilaian Mahasiswa</h6>

<div class="card">
  <div class="card-body p-2">
    <div class="datatable" data-mdb-sm="true" data-mdb-entries="200" data-mdb-hover="true" data-mdb-max-height="650" data-mdb-fixed-header="true">
      <table id="table" class="table align-items-center mb-0">
        <thead style="background-color:#f6f9fc;">
          <tr>
            <th data-mdb-width="200" data-mdb-fixed="true">Nama</th>
            <th>KEFAHAMAN</th>
            <th>PELANGGARAN</th>
            <th>% KBM</th>
            <th>AKHLAQ</th>
            <th>TA'DZIM</th>
            <th>AMALSHOLIH</th>
            <th>PERSUS</th>
            <th>KULIAH</th>
            <th>YATIM/PIATU</th>
            <th>AGHNIA/DHUAFA</th>
          </tr>
        </thead>
        <tbody>
          @if(sizeof($mahasiswa) > 0)
            @foreach($mahasiswa as $mhs)
              <tr>
                <td>
                    <span class="santri-name text-left" santri-name="{{ $mhs->fullname }}" onclick="getReport('<?php echo base64_encode($mhs->santri_id); ?>')" style="cursor:pointer;">
                        <b>[{{ $mhs->angkatan }}] {{ $mhs->fullname }}</b>
                    </span>
                </td>
                <td></td>
                <td>
                    <?php
                    $pelanggaran = App\Models\Pelanggaran::where('fkSantri_id', $mhs->santri_id)->get();
                    ?>
                    <select class="form-control">
                        <option>{{count($pelanggaran)}}</option>
                        @if(count($pelanggaran)>0)
                            @foreach($pelanggaran as $p)
                                <option>[{{$p->jenis->kategori_pelanggaran}}] {{$p->jenis->jenis_pelanggaran}}</option>
                            @endforeach
                        @endif
                    </select>
                </td>
                <td></td>
                <td>
                    <select class="form-control" name="akhlaq" id="akhlaq">
                        <option value="-"></option>
                        <option value="0">Tidak Baik</option>
                        <option value="1">Cukup Baik</option>
                        <option value="2">Baik</option>
                        <option value="3">Sangat Baik</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="akhlaq" id="akhlaq">
                        <option value="-"></option>
                        <option value="0">Tidak Baik</option>
                        <option value="1">Cukup Baik</option>
                        <option value="2">Baik</option>
                        <option value="3">Sangat Baik</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="akhlaq" id="akhlaq">
                        <option value="-"></option>
                        <option value="0">Tidak Pernah</option>
                        <option value="1">Sangat Jarang</option>
                        <option value="2">Cukup Jarang</option>
                        <option value="3">Sangat Sering</option>
                    </select>
                </td>
                <td></td>
                <td>
                    <select class="form-control" name="kuliah" id="kuliah">
                        <option value="-"></option>
                        <option value="0">Bermasalah</option>
                        <option value="1">Lumayan Lancar</option>
                        <option value="2">Lancar</option>
                    </select>
                </td>
                <td></td>
                <td></td>
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
</script>
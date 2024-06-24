<style>
  .page {
    display: inline-block;
    margin-bottom: 16px;
    margin-right: 8px;
    border: 1px solid;
    text-align: center;
    border-radius: 4px;
    user-select: none
  }

  .page-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(56px, 1fr));
  }

  .all-santris {
    background: red;
    color: white;
  }

  .partial-santris {
    background: yellow;
  }
</style>

<div class="card shadow border">
  <div class="card-header pb-0">
    <h6 class="font-weight-bolder">Pilih materi dan santri</h6>
  </div>
  <div class="card-body pt-0 pb-2">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Pilih materi</label>
          <select data-mdb-filter="true" name="materi_id" class="select form-control mb-2">
            @foreach($materis as $materi2)
            <option value="{{ $materi2->id }}" {{ isset($materi) ? ($materi->id == $materi2->id ? 'selected' : '') : '' }}>{{ $materi2->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Pilih status materi</label>
          <select data-mdb-filter="true" name="status" class="select form-control">
            <option value="fully-incomplete" {{ isset($status) ? ($status == 'fully-incomplete' ? 'selected' : '') : '' }}>Kosong semua saja</option>
            <option value="partial-incomplete" {{ isset($status) ? ($status != 'fully-incomplete'  ? 'selected' : '') : '' }}>Kosong semua dan kosong sebagian</option>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="example-text-input" class="form-control-label">Pilih santri</label>
        </div>

        @if(sizeof($selectedSantris) > 0)
        @foreach($selectedSantris as $key => $selectedSantri)
        <div class="form-group">
          <div class="row">
            <select data-mdb-filter="true" name="santri_ids[]" class="select form-control mb-2">
              @foreach($users as $user)
              <option value="{{ $user->santri->id }}" {{ $user->santri->id == $selectedSantri->id ? 'selected' : '' }}>{{ $user->fullname }}</option>
              @endforeach
            </select>
            @if($key > 1)
            <button class="btn btn-danger ms-2 remove-santri">
              Remove
            </button>
            @endif
          </div>
        </div>
        @endforeach
        @else
        <div class="form-group">
          <select data-mdb-filter="true" name="santri_ids[]" class="select form-control mb-2">
            <option disabled selected>Pilih santri</option>
            @foreach($users as $user)
            <option value="{{ $user->santri->id }}">{{ $user->fullname }}</option>
            @endforeach
          </select>
        </div>
        <!-- <div class="form-group">
          <select data-mdb-filter="true" name="santri_ids[]" class="select form-control mb-2">
            <option disabled selected>Pilih santri</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->fullname }}</option>
            @endforeach
          </select>
        </div> -->
        @endif
        <div id="santris-list-template" class="d-none d-flex justify-content-center additional-santris-list-item">
          <select data-mdb-filter="true" class="select form-control mb-2 flex-1">
            <option disabled selected>Pilih santri</option>
            @foreach($users as $user)
            <option value="{{ $user->santri->id }}">{{ $user->fullname }}</option>
            @endforeach
          </select>
          <button class="btn btn-danger ms-2 remove-santri">
            Remove
          </button>
        </div>

        <div id="additional-santris-list"></div>

        <!-- <button type="button" class="btn btn-outline-primary btn-block mb-2" id="add-santri">+ Tambah santri</button> -->
      </div>
    </div>
    <div class="form-group">
      <button type="button" class="btn btn-primary btn-block" id="find">Cari</button>
    </div>
  </div>
</div>

<br>

@if(isset($santriMonitoringMateris, $materi, $status))
<div class="card shadow border">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6 class="font-weight-bolder">Hasil Pencarian "Materi yg Kosong"</h6>
  </div>
  <div class="card-body p-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert bg-warning text-white">
          <span style="color:yellow" class="text-bold">Kuning</span> berarti hanya sebagian santri yang halamannya {{ $status == 'fully-incomplete' ? 'kosong semua' : 'kosong semua atau kosong sebagian'}}, sedangkan <span style="color:red" class="text-bold">merah</span> berarti semua santri pada halaman tersebut {{ $status == 'fully-incomplete' ? 'kosong semua' : 'kosong semua atau kosong sebagian'}}.
          <br>
          <b>Format: [No. Halaman] ([Jumlah santri yang kosong pada halaman tersebut])</b>
        </div>
        <div class="form-group">
          <div class="page-container">
            @for($i = 1; $i <= $materi->pageNumbers; $i++)

              <?php
              echo sizeof($santriIds) . ' - ';
              echo (isset($fullPages[$i]) ? $fullPages[$i]['complete'] : 0);
              $numberOfIncompleteSantris = sizeof($santriIds) - (isset($fullPages[$i]) ? $fullPages[$i]['complete'] + ($status == 'fully-incomplete' ? $fullPages[$i]['partial'] : 0) : 0); ?>

              <span page="{{ $i }}" class="page {{ $numberOfIncompleteSantris == sizeof($santriIds) ? 'all-santris' : ($status != 'all-santris' && $numberOfIncompleteSantris > 1 ? 'partial-santris' : '') }}">
                {{ $i }} ({{ $numberOfIncompleteSantris }})
              </span>
              @endfor
          </div>
        </div>
      </div>
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

  $('#add-santri').click(() => {
    let newList = $('#santris-list-template').clone();
    $(newList).removeClass('d-none');
    $(newList).find('select').attr('name', 'santri_ids[]');

    $('#additional-santris-list').append(newList);

    $('.remove-santri').click((e) => {
      $(e.currentTarget).parent().remove();
    })
  })

  $('.remove-santri').click((e) => {
    $(e.currentTarget).parent().remove();
  })

  $('#find').click(() => {
    let santris = $('select[name="santri_ids[]"]');
    let materiId = $('select[name="materi_id"]').val();
    let status = $('select[name="status"]').val();

    let santriIds = [];

    $(santris).each((k, santri) => {
      santriIds.push($(santri).val())
    })

    santriIds = santriIds.join(',');

    getPage(`{{ route("match empty monitoring materi pages") }}?santri_ids=${santriIds}&materi_id=${materiId}&status=${status}`);
  })
</script>
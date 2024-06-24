<style>
  .page {
    display: inline-block;
    margin-bottom: 16px;
    margin-right: 8px;
    border: none;
    text-align: center;
    border-radius: 4px;
    user-select: none;
    padding: 7px;
  }

  .page-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(56px, 1fr));
  }
</style>
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
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif

    @if(isset($materi))
    <a type="button" class="btn btn-rounded btn-outline-warning btn-block m-0 mb-3" onclick="getPrevPage()">Kembali</a>

    <form action="{{ route('update monitoring materi', $materi->id) }}" method="post">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Materi</label>
            <input class="form-control" type="text" value="{{ $materi->name }}" required readonly>
            <input class="form-control" type="hidden" name="fkMateri_id" value="{{ $materi->id }}" required readonly>
            <input class="form-control" type="hidden" name="fkSantri_id" value="{{ $santri->id }}" required readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Nama santri</label>
            <input class="form-control" type="text" name="name" value="{{ $santri->user->fullname }}" readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Persentase</label>
            <?php
            $completedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'complete')->count();
            $partiallyCompletedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'partial')->count();
            $totalPages = $completedPages + ($partiallyCompletedPages / 2);
            ?>
            <input class="form-control" type="text" name="name" value="{{ number_format((float) $totalPages / $materi->pageNumbers * 100, 2, '.', '') }}%" readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="example-text-input" class="form-control-label">Materi santri</label>
            <div class="page-container">
              @for($i = 1; $i <= $materi->pageNumbers; $i++)

                <?php
                $monitoringMateri = $monitoringMateris->firstWhere("page", $i);
                $css_color = '';
                if ($monitoringMateri != null) {
                  if ($monitoringMateri->status == 'complete') {
                    $css_color = $monitoringMateri->status . ' btn-success';
                  } elseif ($monitoringMateri->status == 'partial') {
                    $css_color = $monitoringMateri->status . ' btn-warning';
                  }
                }
                ?>

                <span page="{{ $i }}" class="page {{ $css_color }}" fullness='{{ $monitoringMateri != null ? $monitoringMateri->status : "blank" }}'>
                  {{ $i }}
                </span>
                @endfor
            </div>
          </div>
        </div>
      </div>
      <div id="page-metas">

      </div>

      <div id="page-status-metas">

      </div>

      @can('update monitoring materis')
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Ubah">
          </div>
        </div>
      </div>
      @endcan
    </form>
    @else
    <div class="alert alert-danger text-white">
      Materi tidak ditemukan.
    </div>
    @endif
  </div>
</div>

<script>
  try {
    $(document).ready();
  } catch (e) {
    window.location.replace(`{{ url("/") }}`)
  }
</script>

@can('update monitoring materis')
<script>
  $('.page').click(e => {
    if ($(e.currentTarget).attr('fullness') == 'blank') {
      $(e.currentTarget).css('background', 'greenyellow');
      $(e.currentTarget).attr('fullness', 'complete')

      const pageMeta = $('#page-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageMeta.length == 0) {
        const newPage = document.createElement('input');
        newPage.type = 'hidden';
        newPage.name = 'pages[]';
        newPage.id = 'page-' + $(e.currentTarget).attr('page');
        newPage.value = $(e.currentTarget).attr('page');

        $('#page-metas').append(newPage);
      }

      const pageStatusMeta = $('#page-status-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageStatusMeta.length == 0) {
        const newPageStatus = document.createElement('input');
        newPageStatus.type = 'hidden';
        newPageStatus.name = 'statusOfPages[]';
        newPageStatus.id = 'page-' + $(e.currentTarget).attr('page');
        newPageStatus.value = 'complete';

        $('#page-status-metas').append(newPageStatus);
      } else {
        $(pageStatusMeta).val('complete');
      }

    } else if ($(e.currentTarget).attr('fullness') == 'complete') {
      $(e.currentTarget).css('background', 'yellow');
      $(e.currentTarget).attr('fullness', 'partial')

      const pageMeta = $('#page-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageMeta.length == 0) {
        const newPage = document.createElement('input');
        newPage.type = 'hidden';
        newPage.name = 'pages[]';
        newPage.id = 'page-' + $(e.currentTarget).attr('page');
        newPage.value = $(e.currentTarget).attr('page');

        $('#page-metas').append(newPage);
      }

      const pageStatusMeta = $('#page-status-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageStatusMeta.length == 0) {
        const newPageStatus = document.createElement('input');
        newPageStatus.type = 'hidden';
        newPageStatus.name = 'statusOfPages[]';
        newPageStatus.id = 'page-' + $(e.currentTarget).attr('page');
        newPageStatus.value = 'partial';

        $('#page-status-metas').append(newPageStatus);
      } else {
        $(pageStatusMeta).val('partial');
      }
    } else if ($(e.currentTarget).attr('fullness') == 'partial') {
      $(e.currentTarget).css('background', 'white');
      $(e.currentTarget).attr('fullness', 'blank')

      const pageMeta = $('#page-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageMeta.length == 0) {
        const newPage = document.createElement('input');
        newPage.type = 'hidden';
        newPage.name = 'pages[]';
        newPage.id = 'page-' + $(e.currentTarget).attr('page');
        newPage.value = $(e.currentTarget).attr('page');

        $('#page-metas').append(newPage);
      }

      const pageStatusMeta = $('#page-status-metas').find('#page-' + $(e.currentTarget).attr('page'));

      if (pageStatusMeta.length == 0) {
        const newPageStatus = document.createElement('input');
        newPageStatus.type = 'hidden';
        newPageStatus.name = 'statusOfPages[]';
        newPageStatus.id = 'page-' + $(e.currentTarget).attr('page');
        newPageStatus.value = 'blank';

        $('#page-status-metas').append(newPageStatus);
      } else {
        $(pageStatusMeta).val('blank');
      }
    }
  })
</script>
@endcan
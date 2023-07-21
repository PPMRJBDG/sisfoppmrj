@include('base.start', ['path' => 'materi/monitoring/list', 'title' => 'Daftar Monitoring Materi', 'breadcrumbs' => ['Daftar Monitoring Materi']])

<?php

function printMateriOptions($materis, $santri)
{
  foreach ($materis as $materi) {
    if ($materi->for == 'mubalegh' && !$santri->user->hasRole('mubalegh'))
      continue;

    if ($materi->for != 'mubalegh' && $santri->user->hasRole('mubalegh'))
      continue;

    $completedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'complete')->count();
    $partiallyCompletedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'partial')->count();
    $totalPages = $completedPages + ($partiallyCompletedPages / 2);

?>
    <option value="{{ $materi->id }}">{{ $materi->name }} {{ number_format((float) $totalPages / $materi->pageNumbers * 100, 2, '.', '') }}%</option>
<?php
  }
}

?>
<style>
  /* .members-list-item:hover>.materis-list {
    display: block;
  }

  .materis-list {
    display: block;
  } */

  .santris-list {
    display: none;
  }

  .lorongs-list-item h6 {
    user-select: none;
    cursor: pointer;
  }
</style>
<div class="card">
  <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Daftar Monitoring Materi</h6>
    @if(!auth()->user()->hasRole('santri'))
    <a href="{{ route('create materi') }}" class="btn btn-primary">
      <i class="fas fa-plus" aria-hidden="true"></i>
      Buat Materi
    </a>
    @endif
  </div>
  <div class="card-body pt-4 p-3">
    @if (session('success'))
    <div class="alert alert-success text-white">
      {{ session('success') }}
    </div>
    @endif
    @if(!auth()->user()->hasRole('santri'))
    <input id="search" placeholder="Cari nama..." class="form-control mb-4" type="text">
    @endif
    @if(sizeof($lorongs) <= 0) Belum ada data. @endif <ul class="list-group">
      @if(auth()->user()->santri)
      <li class="list-group-item lorongs-list-item border-0 p-4 mb-4 bg-gray-100 border-radius-lg">
        <div class="d-flex flex-column">
          <h6 class="text-sm">Materi Saya <i class="fas fa-caret-down ms-2" aria-hidden="true"></i></h6>
        </div>
        <ul class="mt-4 list-group">
          <?php
          $monitoringMateris = auth()->user()->santri->monitoringMateris;
          ?>
          <li class="list-group-item members-list-item" style="background:none;border:none">
            <div class="row">
              <div class="col-sm">
                {{ auth()->user()->fullname }}
              </div>
              <div class="col-sm">
                <select class="materis-list ms-4 form-control" name="" id="" santri-id="{{ auth()->user()->santri->id }}">
                  <option value="">Pilih materi</option>
                  <?php printMateriOptions($materis, auth()->user()->santri) ?>
                </select>
              </div>
              <div class="col-sm"></div>
              <div class="col-sm"></div>
            </div>
          </li>
        </ul>
      </li>
      @endif
      @can('view monitoring materis list')
      @foreach($lorongs as $lorong)
      <li class="list-group-item lorongs-list-item border-0 p-4 mb-4 bg-gray-100 border-radius-lg">
        <div class="d-flex flex-column">
          <h6 class="text-sm">{{ $lorong->name }} <i class="fas fa-caret-down ms-2" aria-hidden="true"></i></h6>
        </div>
        <ul class="mt-4 list-group santris-list">
          <?php
          $monitoringMateris = $lorong->leader->monitoringMateris;
          ?>
          <li class="list-group-item members-list-item" style="background:none;border:none">
            <div class="row">
              <div class="col-sm">
                {{ $lorong->leader->user->fullname }} xx
              </div>
              <div class="col-sm">
                <select class="materis-list ms-4 form-control" name="" id="" santri-id="{{ $lorong->leader->id }}">
                  <option value="">Pilih materi</option>
                  <?php printMateriOptions($materis, $lorong->leader) ?>
                </select>
              </div>
              <div class="col-sm"></div>
              <div class="col-sm"></div>
            </div>
          </li>
          @foreach($lorong->members as $member)
          <?php
          $monitoringMateris = $member->monitoringMateris;
          ?>
          <li class="list-group-item members-list-item" style="background:none;border:none">
            <div class="row">
              <div class="col-sm">
                {{ $member->user->fullname }} zz
              </div>
              <div class="col-sm">
                <select class="materis-list ms-4 form-control" name="" id="" santri-id="{{ $member->id }}">
                  <option value="">Pilih materi</option>
                  <?php printMateriOptions($materis, $member) ?>
                </select>
              </div>
              <div class="col-sm"></div>
              <div class="col-sm"></div>
            </div>
          </li>
          @endforeach
        </ul>
      </li>
      @endforeach
      @endcan

      </ul>
  </div>
</div>
<script>
  $('.materis-list').change((e) => {
    if ($(e.currentTarget).val() != "")
      window.open(`{{ url("/") }}/materi/monitoring/list/${$(e.currentTarget).val()}/${$(e.currentTarget).attr('santri-id')}`)

    $(e.currentTarget).children().eq(0).attr('selected', true)
  })

  $('.lorongs-list-item h6').click((e) => {
    // console.log($(e.currentTarget).find('.members-list-item'));
    if ($(e.currentTarget).parent().parent().find('.santris-list').css('display') == 'none')
      $(e.currentTarget).parent().parent().find('.santris-list').show();
    else
      $(e.currentTarget).parent().parent().find('.santris-list').hide();

    if ($(e.currentTarget).find('.fa-caret-down').length > 0)
      $(e.currentTarget).find('.fa-caret-down').removeClass('fa-caret-down').addClass('fa-caret-up');
    else
      $(e.currentTarget).find('.fa-caret-up').removeClass('fa-caret-up').addClass('fa-caret-down');
  })

  $('#search').keyup((e) => {
    if (e.currentTarget.value.toLowerCase() != '')
      $('.santris-list').show();
    else
      $('.santris-list').hide();

    $('.santris-list').children().each((k, r) => {
      let fullname = $(r).text();

      if (!fullname.toLowerCase().includes(e.currentTarget.value.toLowerCase())) {
        if (!$(r).hasClass('selected'))
          $(r).hide();
      } else {
        $(r).show();
      }
    })
  });
</script>
@include('base.end')
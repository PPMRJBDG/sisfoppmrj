@include('base.start_without_bars',['title' => "RAB ".strtoupper($detail_of->nama)])
<script type="text/javascript" src="{{ asset('js/app-custom.js') }}"></script>

<style>
    .new-td {
        padding: 2px 5px !important;
    }
    .form-control {
        border: transparent;
        border-bottom: solid 1px #dee2e6;
        font-size: 0.7rem !important;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@include('keuangan.rab_kegiatan_form_table',[
    'ids' => $ids,
    'detail_of' => $detail_of,
    'detail_kegiatans' => $detail_kegiatans,
    'form_url' => ' public'
])

<!-- New Material Design -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.umd.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/mdb-v2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('ui-kit/js/modules/wow.min.js') }}"></script>

<script>
    $(document).ready(() => {
        new WOW().init();
    });
</script>
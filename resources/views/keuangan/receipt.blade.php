<div class="card border shadow-lg">
    <div class="card-header">
        <div class="d-flex">
            <a href="{{ route('create update rab') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus" aria-hidden="true"></i>
                RAB
            </a>
        </div>
        <div class="card border shadow-lg">
            <div class="datatable datatable-sm">
                <table class="table align-items-center mb-0">
                    <thead style="background-color:#f6f9fc;">
                        <tr>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Divisi</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Pengeluaran</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Periode</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Jumlah</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Biaya</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Total/Tahun</th>
                            <th class="text-uppercase text-sm text-center text-secondary font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($rabs)>0)
                        @foreach ($rabs as $rab)
                        <tr class="text-center text-sm">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
</script>
@include('base.start', ['path' => 'msgtools/report', 'title' => 'Msgtools - Report', 'breadcrumbs' => ['Msgtools - Report']])

<div class="card">
    <div class="card-body">
        <div class="alert alert-success text-white">
            Daftar report yang sudah / belum dibaca oleh Orangtua
        </div>
        <div class="table-responsive">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">Nama</th>
                        <th class="text-uppercase text-sm text-secondary">Link</th>
                        <th class="text-uppercase text-sm text-secondary">Bulan</th>
                        <th class="text-uppercase text-sm text-secondary">Status</th>
                        <th class="text-uppercase text-sm text-secondary">Dibaca</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $d)
                    <tr class="text-sm">
                        <td>
                            {{ $d->santri->user->fullname }}
                        </td>
                        <td>
                            {{ $d->link_url }}
                        </td>
                        <td>
                            {{ $d->month }}
                        </td>
                        <td>
                            @if($d->status==1)
                            <i class="ni ni-check-bold text-info text-sm opacity-10"></i>
                            @endif
                        </td>
                        <td>
                            {{ $d->count . 'x' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('#table-report').DataTable();
</script>
@include('base.end')
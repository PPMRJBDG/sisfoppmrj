<div class="card">
    <div class="card-body">
        <div class="card-title font-weight">
            <b>Daftar Link Laporan Orang Tua</b>
            <br>
            {{ count($status) }} / {{ count($datax) }} sudah membuka
            <hr>
        </div>
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">Nama</th>
                        <th class="text-uppercase text-sm text-secondary">Link</th>
                        <th class="text-uppercase text-sm text-secondary">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $d)
                    <tr class="text-sm">
                        <td onclick="getReport('<?php echo base64_encode($d->santri->id); ?>')" style="cursor:pointer;">
                            {{ $d->santri->user->fullname }}
                        </td>
                        <td>
                            <a href="{{$d->link_url}}" target="_blank">{{ $d->link_url }}</a>
                        </td>
                        <td>
                            @if($d->status==1)
                            <i class="fas fa-check text-info text-sm opacity-10"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
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

    $('#table-report').DataTable();
</script>
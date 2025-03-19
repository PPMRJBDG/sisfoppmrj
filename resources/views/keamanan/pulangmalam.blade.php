<h5 class=""><b>Pulang Malam</b></h5>
<div class="card shadow border mb-2">
    <div class="card-body p-2">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">PETUGAS</th>
                        <th class="text-uppercase text-sm text-secondary">SANTRI</th>
                        <th class="text-uppercase text-sm text-secondary">JAM PULANG</th>
                        <th class="text-uppercase text-sm text-secondary">ALASAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_telatpulang as $d)
                        <tr class="text-sm">
                            <td>
                                {{ $d->jaga->user->fullname }}
                            </td>
                            <td>
                                {{ $d->santri->user->fullname }}
                            </td>
                            <td>
                                {{ date_format(date_create($d->jam_pulang), 'd-m-Y H:i:s') }}
                            </td>
                            <td>
                                {{ $d->alasan }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
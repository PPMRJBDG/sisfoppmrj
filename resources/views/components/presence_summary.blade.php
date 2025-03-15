<div style="font-size:11px;">
    <span class="mb-2 text-xs">Waktu kegiatan: 
        <span class="ms-sm-2 font-weight-bold">
            {{ $presence->event_date }}
        </span>
    </span>
    <br>
    <span class="mb-2 text-xs">Mulai KBM: <span class="ms-sm-2 badge badge-primary font-weight-bold">
        {{ date_format(date_create($presence->start_date_time), 'H:i:s') }}</span></span> - <span class="mb-2 text-xs">Selesai KBM: <span class="ms-sm-2 badge badge-primary font-weight-bold">{{ date_format(date_create($presence->end_date_time), 'H:i:s') }}</span>
    </span>
</div>
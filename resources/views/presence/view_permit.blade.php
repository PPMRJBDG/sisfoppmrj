@include('base.start_without_bars', ['path' => 'permit', 'containerClass' => 'p-0', 'title' => "Link Reject"])

@if($permit)
<div class="card m-2">
    <div class="card-body">
        <h6 class="font-weight-bold mb-0 text-center text-bold">Perijinan dari {{ $permit->santri->user->fullname}}</h6>
    </div>
</div>

<div class="card m-2">
    <div class="card-body">
        <div>
            <tr>
                <td>
                    Presensi<br>
                    <b>{{$permit->presence->name}}</b>
                </td>
            </tr>
            <br><br>
            <tr>
                <td>
                    Kategori<br>
                    <b>{{$permit->reason_category}}</b>
                </td>
            </tr>
            <br><br>
            <tr>
                <td>
                    Alasan<br>
                    <b>{{$permit->reason}}</b>
                </td>
            </tr>
            <br><br>
            @if($message=='')
            <tr>
                <td>
                    <a style="width:100%;" href="{{ route('reject permit', [$permit->ids]) }}" class="btn btn-danger btn-block mb-0" onclick="return confirm('Yakin di tolak ?')">Reject</a>
                </td>
            </tr>
            @else
            <tr>
                <td>
                    Status<br>
                    <b>{{$message}}</b>
                </td>
            </tr>
            @endif
        </div>
    </div>
</div>
@else
<div class="card m-2">
    <div class="card-body">
        <h6 class="font-weight-bold mb-0 text-center">Perijinan tidak ditemukan</h6>
    </div>
</div>
@endif
@if($permit)
<div class="card shadow border m-2">
    <div class="card-body">
        <h6 class="font-weight-bold mb-0 text-center text-bold">Perijinan dari {{ $permit->santri->user->fullname}}</h6>
    </div>
</div>

<div class="card shadow border m-2">
    <div class="card-body p-0">
        <div>
            <table class="table text-md mb-0">
                <tr>
                    <td>
                        Presensi<br>
                        <b>{{$permit->presence->name}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Kategori<br>
                        <b>{{$permit->reason_category}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Alasan<br>
                        <b>{{$permit->reason}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Status<br>
                        <b>{{$message}}</b>
                    </td>
                </tr>
                @if($permit->status=='rejected')
                <tr>
                    <td>
                        Alasan Ditolak<br>
                        <b>{{$permit->alasan_rejected}}</b>
                    </td>
                </tr>
                @endif
                @if($permit->status=='approved' || $permit->status=='pending')
                <tr>
                    <td>
                        <?php
                        $url = route('reject permit', [$permit->ids]);
                        ?>
                        <a style="width:100%;" class="btn btn-danger btn-block mb-0" onclick="promptDeletePermit('{{$url}}','{{$permit->ids}}')">Reject</a>
                    </td>
                </tr>
                @endif
                @if($permit->status=='rejected' || $permit->status=='pending')
                <tr>
                    <td>
                        <a style="width:100%;" href="{{ route('approve permit', [$permit->ids]) }}" class="btn btn-primary btn-block mb-0" onclick="return confirm('Yakin di setujui ?')">Approve</a>
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>
@else
<div class="card shadow border m-2">
    <div class="card-body">
        <h6 class="font-weight-bold mb-0 text-center">Perijinan tidak ditemukan</h6>
    </div>
</div>
@endif

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
</script>
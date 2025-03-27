<?php
$bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
?>

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

@if(auth()->user()->hasRole('superadmin') || (auth()->user()->hasRole('ku') && !isset(auth()->user()->santri)))
    <h6>Butuh Persetujuan</h6>
    <div class="card border py-2">
        <div class="datatable datatable-sm align-items-center justify-content-center" data-mdb-entries="20">
            <table id="table" class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">ANGKATAN</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">NAMA</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">PERIODE</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">NOMINAL</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">TANGGAL</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">BUKTI</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($need_approval))
                        @foreach($need_approval as $na)
                            <tr class="text-sm">
                                <td>
                                    {{ $na->santri->angkatan }}
                                </td>
                                <td>
                                    {{ $na->santri->user->fullname }}
                                </td>
                                <td>
                                    {{ $na->sodaqoh->periode }}
                                </td>
                                <td>
                                    {{ number_format($na->nominal,0) }}
                                </td>
                                <td>
                                    {{ date_format(date_create($na->pay_date), "d-M-Y") }}
                                </td>
                                <td>
                                    <button  type="button" class="btn btn-primary btn-floating btn-sm" data-mdb-ripple-init onclick="showBuktiPreview('{{$na->bukti_transfer}}')"><i class="fa fa-image"></i></button>
                                </td>
                                <td>
                                    <a href="#" role="button" class="btn btn-success btn-sm" data-mdb-ripple-init onclick="actionPayment('approved','{{$na->id}}','menyetujui')"><i class="fa fa-circle-check"></i> Approve</a>
                                    <a href="#" role="button" class="btn btn-danger btn-sm" data-mdb-ripple-init onclick="actionPayment('rejected','{{$na->id}}','menolak')"><i class="fa fa-rectangle-xmark"></i> Reject</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <h6 class="mt-4">Riwayat Pembayaran</h6>
    <div class="card border py-2">
        <div class="datatable datatable-sm align-items-center justify-content-center" data-mdb-entries="50">
            <table id="table" class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">ANGKATAN</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">NAMA</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">PERIODE</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">NOMINAL</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">TANGGAL</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">BUKTI</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($historis))
                        @foreach($historis as $na)
                            <tr class="text-sm">
                                <td>
                                    {{ $na->santri->angkatan }}
                                </td>
                                <td>
                                    {{ $na->santri->user->fullname }}
                                </td>
                                <td>
                                    {{ $na->sodaqoh->periode }}
                                </td>
                                <td>
                                    {{ number_format($na->nominal,0) }}
                                </td>
                                <td>
                                    {{ ($na->pay_date=="") ? "" : date_format(date_create($na->pay_date), "d-M-Y") }}
                                </td>
                                <td>
                                    @if($na->bukti_transfer!="")
                                    <button  type="button" class="btn btn-primary btn-floating btn-sm" data-mdb-ripple-init onclick="showBuktiPreview('{{$na->bukti_transfer}}')"><i class="fa fa-image"></i></button>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{($na->status=='approved') ? 'primary' : 'danger'}}">{{ $na->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card border py-2">
        <div class="datatable datatable-sm align-items-center justify-content-center">
            <table id="table" class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder"></th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Periode</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Nominal</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Terbayar</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder">Kekurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($tagihans))
                        @foreach($tagihans as $tagihan)
                            <tr class="text-sm" id="data{{$tagihan->fkSantri_id}}">
                                <td>
                                    <a block-id="return-false" onclick="openSodaqoh({{$tagihan}},'[{{$tagihan->santri->angkatan}}] {{$tagihan->santri->user->fullname}}',{{$tagihan->histori}})" class="btn btn-{{ ($tagihan->status_lunas) ? 'secondary' : 'primary' }} btn-sm mb-0">
                                        {{ ($tagihan->status_lunas) ? 'Riwayat' : 'Bayar' }}
                                    </a>
                                </td>
                                <td>
                                    {{ $tagihan->periode }}
                                </td>
                                <td>
                                    {{ number_format($tagihan->nominal,0) }}
                                </td>
                                <?php
                                $status_lunas = 0;
                                $kekurangan = 0;
                                foreach ($tagihan->histori as $histori) {
                                    if($histori->status=='approved'){
                                        $status_lunas = $status_lunas + intval($histori->nominal);
                                    }
                                }
                                $kekurangan = $tagihan->nominal - $status_lunas;
                                $text_error = '';
                                if (isset($tagihan->periode)) {
                                    if ($status_lunas < $tagihan->nominal) {
                                        $text_error = 'text-warning';
                                    }
                                }
                                ?>
                                <td id="terbayar{{$tagihan->fkSantri_id}}" class="{{ $text_error }}">
                                    <b>{{ $text_error == '' ? 'Lunas' : number_format($status_lunas,0) }}</b>
                                </td>
                                <td id="kekurangan{{$tagihan->fkSantri_id}}" class="{{ $text_error }}">
                                    <b>{{ number_format($kekurangan,0) }}</b>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
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
@include('base.start_without_bars', [
'path' => 'dwngr/list',
'title' => 'Presensi ' . (isset($presence) ? $presence->name : '')
])

<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<style>
    @media only screen and (max-width: 600px) {

        body,
        h6 {
            font-size: 0.8rem !important;
        }
    }

    .py-4 {
        padding: 7px !important;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        background-color: #f1f1f1;
        border-radius: 8px 8px 0 0;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px !important;
        transition: 0.3s;
        font-size: 17px;
    }

    .tablinks {
        font-weight: bold !important;
        font-size: 14px !important;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
        border-bottom: solid 4px #5e72e4;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border-top: none;
        background-color: #fff;
        border-radius: 0 0 8px 8px;
    }
</style>

@if(isset($presence))
<div class="card">
    <div class="card-body p-3">
        <script>
            function togglePrsc() {
                $("#toggle-prsc").toggle();

                $("#info-update-presence").hide();
                $("#info-update").html('')
            }
        </script>
        <center><button class="btn btn-primary btn-block mb-0" onclick="togglePrsc()">Presensi {{ $presence->name }}</button></center>
        <div id="toggle-prsc" style="display:none;">
            <div class="row p-2 ">
                <div class="card-body p-2" style="background:#f9f9f9;border:#ddd 1px solid;">
                    <div class="col-12 pb-2">
                        <small>{{ ($presence->is_hasda) ? 'Penyampai Dalil / PPG' : 'Pengajar PPM 1' }}</small>
                        <input class="form-control" disabled value="{{($presence->fkDewan_pengajar_1=='') ? '-' : $presence->dewanPengajar1->name}}" type="text">
                    </div>
                    <div class="col-12 pb-2">
                        <small>{{ ($presence->is_hasda) ? 'Penyampai Teks / Naslis' : 'Pengajar PPM 2' }}</small>
                        <input class="form-control" disabled value="{{($presence->fkDewan_pengajar_2=='') ? '-' : $presence->dewanPengajar2->name}}" type="text">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-white text-sm font-weight-bolder text-center mt-2">
    <span>Jumlah Mahasiswa {{ $jumlah_mhs }}</span>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12 mt-2">
        <select class="select_lorong form-control" name="select_lorong" id="select_lorong">
            <option value="-">Semua Lorong</option>
            @foreach($data_lorong as $l)
            <option {{ ($lorong==$l->id) ? 'selected' : '' }} value="{{$l->id}}">{{$l->name}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="tab mt-2">
    <button class="tablinks active" onclick="openTab(event, 'hadir')">Hadir <span id="c-hdr">{{count($presents)}}</span></button>
    <button class="tablinks" onclick="openTab(event, 'ijin')">Ijin {{count($permits)}}</button>
    <button class="tablinks" onclick="openTab(event, 'alpha')">Alpha <span id="c-alp">{{count($mhs_alpha)}}</span></button>
</div>

<div class="card tabcontent" id="hadir" style="display:block;">
    <div class="card-body px-0 pt-0 pb-2">
        <small style="font-size:11px;">Sudah melakukan presensi: <span id="nact" class="text-bold"></span></small>
        <div class="table-responsive">
            <table id="table-hadir" class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($presents as $present)
                    <tr title="{{$present->metadata}}" id="trh{{$present->fkSantri_id}}" class="dtmhsh" val-id="{{$present->fkSantri_id}}" val-name="{{$present->santri->user->fullname}}" updated-by="{{$present->updated_by}}">
                        <td class="text-sm">
                            <b>{{ $present->santri->user->fullname }}</b>
                            <br>
                            <small>{{ $present->created_at }}</small>
                            <!-- | <b>{{ $present->is_late ? 'Telat' : 'Tidak telat' }}</b> -->
                        </td>
                        <td class="align-middle text-center text-sm" id="slbtnh-{{$present->fkSantri_id}}">
                            @if($update)
                            <small style="font-size: 9px;">{{ ($present->updated_by=='') ? '' : 'Updated by '.$present->updated_by}}</small><br>
                            <a class="btn btn-danger btn-block btn-xs mb-0" href="#" onclick="selectForAlpha(<?php echo $present->fkSantri_id; ?>)">Alpha</a>

                            <!-- @if($present->is_late) -->
                            <!-- <a class="btn btn-primary btn-xs mb-0" href="{{ route('is not late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin tidak telat?')">Tidak Telat</a> -->
                            <!-- @else -->
                            <!-- <a class="btn btn-warning btn-xs mb-0" href="{{ route('is late', ['id' => $present->fkPresence_id, 'santriId' => $present->fkSantri_id]) }}" onclick="return confirm('Yakin telat?')">Telat</a> -->
                            <!-- @endif -->
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card tabcontent" id="ijin" style="display:none;">
    @if(count($permits)>0 || count($need_approval)>0)
    <div class="card-header p-2 pb-0">
        <h6 class="mb-0 bg-warning p-1 text-white">
            Perlu persetujuan/ditolak: {{count($need_approval)}}
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table align-items-center mb-0">
            <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- need approval -->
                @foreach($need_approval as $na)
                <tr title="{{ ($na->approved_by=='') ? '' : 'Approved by '.$na->approved_by}} {{ ($na->rejected_by=='') ? '' : ' | Rejected by '.$na->rejected_by }}">
                    <td class="text-sm">
                        <b>{{ $na->santri->user->fullname }}</b>
                        <br>
                        <small>[{{ $na->reason_category }}] - {{ $na->reason }}</small>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-danger font-weight-bolder">{{ $na->status }}</span>
                    </td>
                </tr>
                @endforeach
                @foreach($permits as $permit)
                <tr title="{{ ($permit->approved_by=='') ? '' : 'Approved by '.$permit->approved_by}} {{ ($permit->rejected_by=='') ? '' : ' | Rejected by '.$permit->rejected_by }}">
                    <td class="text-sm">
                        <b>{{ $permit->santri->user->fullname }}</b>
                        <br>
                        <small>[{{ $permit->reason_category }}] - {{ $permit->reason }}</small>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-primary font-weight-bolder">{{ $permit->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-2 text-center text-sm">Tidak ada yang ijin</div>
    @endif
</div>

<div class="card tabcontent" id="alpha" style="display:none;">
    @if(count($mhs_alpha)>0)
    <div class="table-responsive">
        <table id="table-alpha" class="table align-items-center mb-0">
            <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mhs_alpha as $mhs)
                <tr id="tra{{$mhs['santri_id']}}" class="dtmhsa" val-id="{{$mhs['santri_id']}}" val-name="{{$mhs['name']}}">
                    <td class="text-sm">
                        <b>{{ $mhs['name'] }}</b>
                    </td>
                    <td class="align-middle text-center text-sm" id="slbtna-{{$mhs['santri_id']}}">
                        @if($update)
                        <a class="btn btn-primary btn-block btn-xs mb-0" href="#" onclick="selectForHadir(<?php echo $mhs['santri_id']; ?>)">Hadir</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-2 text-center text-sm">Tidak ada yang alpha</div>
    @endif
</div>

@else
<div class="card">
    <div class="card-body pt-4 p-3">
        <div class="alert alert-danger text-white">Presensi tidak ditemukan.</div>
    </div>
</div>
@endif

<?php
try {
    if (isset(auth()->user()->fullname)) {
        $user = auth()->user()->fullname;
    } else {
        $user = 'Dewan Guru';
    }
} catch (Exception  $err) {
    $user = 'Dewan Guru';
}
?>

<style>
    #table-hadir_filter,
    #table-alpha_filter {
        margin-top: 0px !important;
    }

    #table-hadir_filter label,
    #table-alpha_filter label,
    #table-hadir_filter label input,
    #table-alpha_filter label input {
        width: 100% !important;
    }
</style>
<script>
    $('#table-hadir').DataTable({
        "paging": false,
        "ordering": false,
        "info": false
    });
    $('#table-alpha').DataTable({
        "paging": false,
        "ordering": false,
        "info": false
    });
    cekKoorLorong();

    function selectForAlpha(id) {
        const el = document.querySelector("#trh" + id);
        var name = el.getAttribute('val-name')
        $("#trh" + id).addClass('bg-primary text-white');
        $("#slbtnh-" + id).html('<span class="text-white"><small>loading</small></span>');
        savePresensi(id, name, 'delete');
    }

    function selectForHadir(id) {
        const el = document.querySelector("#tra" + id);
        var name = el.getAttribute('val-name')
        $("#tra" + id).addClass('bg-primary text-white');
        $("#slbtna-" + id).html('<span class="text-white"><small>loading</small></span>');
        savePresensi(id, name, 'present');
    }

    async function savePresensi(santriId, name, st) {
        var lorong = '<?php echo $lorong; ?>';
        var user = '<?php echo $user; ?>';
        // console.log(lorong)
        $.get(`{{ url("/") }}/dwngr/` + <?php echo $id; ?> + `/` + st + `/` + santriId + `?lorong=` + lorong + `&json=true`, null,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    var zxca = parseInt($("#c-hdr").html());
                    var zxcb = parseInt($("#c-alp").html());
                    var x = 'a'; // berarti proses delete
                    var tedf = 'Hadir';
                    var ghdf = 'alpha';
                    var btn = 'primary';
                    var datetime = '';
                    if (st == 'present') {
                        x = 'h'; // berarti proses present
                        tedf = 'Alpha';
                        ghdf = 'hadir';
                        btn = 'danger';
                        zxca++;
                        zxcb--;
                        $("#c-hdr").html(zxca)
                        $("#c-alp").html(zxcb)
                        datetime = '<br><small>' + dateTime() + '</small>';
                    } else {
                        zxca--;
                        zxcb++;
                        $("#c-hdr").html(zxca)
                        $("#c-alp").html(zxcb)
                    }

                    var text = '<tr id="tr' + x + santriId + '" class="dtmhs' + x + '" val-id="' + santriId + '" val-name="' + name + '" updated-by="' + user + '">' +
                        '<td class="text-sm">' +
                        '<b>' + name + '</b>' + datetime +
                        '</td>' +
                        '<td class="align-middle text-center text-sm" id="slbtn' + x + '-' + santriId + '">' +
                        '<small style="font-size: 9px;">Updated by ' + user + '</small><br>' +
                        '<a class = "btn btn-' + btn + ' btn-block btn-xs mb-0" href = "#" onclick = "selectFor' + tedf + '(' + santriId + ')">' + tedf + '</a>' +
                        '</td>' +
                        '</tr>';

                    if (x == 'a') {
                        x = 'h';
                    } else {
                        x = 'a';
                    }
                    const element = document.getElementById("tr" + x + "" + santriId);
                    element.remove();

                    text = text + $("#table-" + ghdf + " tbody").html();
                    $("#table-" + ghdf + " tbody").html(text);
                    cekKoorLorong();
                }
            }
        );
    }

    async function cekKoorLorong() {
        var arr_name = [];
        const el = document.querySelectorAll(".dtmhsh");
        for (var i = 0; i < el.length; i++) {
            arr_name.push(el[i].getAttribute('updated-by'));
        }
        var uniqueNames = [];
        $.each(arr_name, function(i, elx) {
            if ($.inArray(elx, uniqueNames) === -1) uniqueNames.push(elx);
        });

        var dz = '';
        $.each(uniqueNames, function(i, x) {
            if (dz == '') {
                dz = x
            } else {
                dz = dz + ', ' + x
            }
        })
        $("#nact").html(dz)
    }

    function dateTime() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        var h = String(today.getHours()).padStart(2, '0');
        var i = String(today.getMinutes()).padStart(2, '0');
        var s = String(today.getSeconds()).padStart(2, '0');

        today = yyyy + '-' + mm + '-' + dd + ' ' + h + ':' + i + ':' + s;
        return today;
    }

    $('.select_lorong').change((e) => {
        window.location.replace(`{{ url("/") }}/dwngr/list/<?php echo $id; ?>?lorong=${$(e.currentTarget).val()}`)
    })
</script>
@include('base.end')
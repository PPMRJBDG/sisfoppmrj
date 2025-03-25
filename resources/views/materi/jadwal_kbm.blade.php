<div class="row">
    <div class="col-md-12">
        <div class="card border shadow-lg mb-2">
            <div class="card-body p-2">
                <div class="card-header p-2 ps-0">
                    <p class="mb-0">Silahkan memilih jam KBM, setiap sesi: <b>Durasinya -+ 1 Jam 30 Menit</b></p>
                </div>
                <div class="datatablex datatable-sm">
                    <table class="table align-items-center mb-2">
                        <thead>
                            <tr class="text-center">
                                <th></th>
                                @foreach($day_kbm as $dn)
                                <th class="text-sm font-weight-bolder">
                                    {{$dn->day_name}}
                                </th>
                                @endforeach
                            </tr>
                            @if (auth()->user()->hasRole('superadmin'))
                            <tr class="text-center">
                                <th></th>
                                @foreach($day_kbm as $dn)
                                <th class="text-sm">
                                    <span class="badge badge-primary" id="total-day-{{$dn->id}}"></span>
                                </th>
                                @endforeach
                            </tr>
                            @else
                            <tr>
                                <th></th>
                                @foreach($day_kbm as $dn)
                                <th class="text-sm">
                                    <div>Sesi 1: <span id="day-s1-{{$dn->id}}"></span></div>
                                    <div>Sesi 2: <span id="day-s2-{{$dn->id}}"></span></div>
                                </th>
                                @endforeach
                            </tr>
                            @endif
                        </thead>
                        <tbody id="">
                            @foreach($hour_kbm as $hn)
                            <tr class="text-sm text-center" id="">
                                <th>{{$hn->hour_name}}</th>
                                @foreach($day_kbm as $dn)
                                <th>
                                    <?php
                                    if (auth()->user()->hasRole('superadmin')) {
                                        $data_mhs = array();
                                        $get_data = App\Models\JadwalHariJamKbms::where('fkHari_kbm_id', $dn->id)->where('fkJam_kbm_id', $hn->id)->get();
                                        if (count($get_data) == 0) {
                                            $jumlah_mhs = '';
                                        } else {
                                            $jumlah_mhs = count($get_data);
                                            foreach ($get_data as $gd) {
                                                $data_mhs[] = $gd->santri->user->fullname;
                                            }
                                        }
                                    } else {
                                        $checked = '';
                                        $get_data = App\Models\JadwalHariJamKbms::where('fkSantri_id', $santri_id)->where('fkHari_kbm_id', $dn->id)->where('fkJam_kbm_id', $hn->id)->first();
                                        if ($get_data != null) {
                                            $checked = 'checked';
                                        }
                                    }
                                    ?>

                                    @if($hn->is_break || $hn->is_disable)
                                    @if($hn->is_break)
                                    <span class="badge badge-warning"><small>break</small></span>
                                    @else
                                    <div class="form-check">
                                        <input style="background: grey;" disabled class="form-check-input" id="jdwl-{{$dn->id}}-{{$hn->id}}-disabled" type="checkbox">
                                        <label class="form-check-label" for="jdwl-{{$dn->id}}-{{$hn->id}}-disabled"></label>
                                    </div>
                                    @endif
                                    @else
                                    @if(auth()->user()->hasRole('superadmin'))
                                    <span style="cursor: pointer;" onclick="viewMahasiswa({{json_encode($data_mhs)}},'{{$dn->day_name}}','{{$hn->hour_name}}')" class="badge badge-secondary" name-day="{{$dn->day_name}}" val-day-hour="{{$jumlah_mhs}}">{{$jumlah_mhs}}</span>
                                    @else
                                    <div class="form-check">
                                        <input {{$checked}} class="form-check-input" name-day="{{$dn->day_name}}" name-hour="{{$hn->hour_name}}" val-day="{{$dn->id}}" val-hour="{{$hn->id}}" type="checkbox" id="jdwl-{{$dn->id}}-{{$hn->id}}" name="item[]" onclick="return setJadwal(this,{{$dn->id}},{{$hn->id}})">
                                        <label class="form-check-label" for="jdwl-{{$dn->id}}-{{$hn->id}}"></label>
                                    </div>
                                    @endif
                                    @endif
                                </th>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalMahasiswa" tabindex="-1" role="dialog" aria-labelledby="modalMahasiswaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalMahasiswaLabel">Jadwal KBM</h6>
                    <h5 class="modal-title" id="modalMahasiswaLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body">
                <table id="modal-body-mhs">

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
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

    function viewMahasiswa(data, day, hour) {
        $('#modalMahasiswa').fadeIn();
        $('#modalMahasiswa').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#modalMahasiswa').css('z-index', '10000');
        $('#nm').text(day + ' | ' + hour);
        var content = '';
        for (var x = 0; x < data.length; x++) {
            content = content + '<tr><td>' + (x + 1) + '.</td><td>' + data[x] + '</td></tr>';
        }
        $("#modal-body-mhs").html(content);
    }
    $('#close').click(function() {
        $('#modalMahasiswa').fadeOut();
    });

    function countHourPerDay() {
        const dayname = <?php echo $day_kbm; ?>;
        dayname.forEach(function(d) {
            var day = 0;
            const cx = document.querySelectorAll("span[name-day=" + d.day_name + "]");
            for (var i = 0; i < cx.length; i++) {
                if (cx[i].getAttribute('val-day-hour') != '')
                    day = day + parseInt(cx[i].getAttribute('val-day-hour'));
            }
            $("#total-day-" + d.id).html(day / 2 + ' Mhs');
        })
    }

    async function setJadwal(x, day_id, hour_id) {
        var datax = {};
        datax['day'] = day_id;
        datax['hour'] = hour_id;

        datax['action'] = 'delete';
        if (x.checked == true) {
            datax['action'] = 'insert';
        }
        $.post(`{{ url("/jadwal-kbm/store") }}`, datax,
            function(data) {
                checkDay();
                return false
            }
        );
    }

    function checkDay() {
        const dayname = <?php echo $day_kbm; ?>;
        dayname.forEach(function(d) {
            const cx = document.querySelectorAll("input[name-day=" + d.day_name + "]:checked");

            if (cx.length == 1) {
                var day_id = cx[0].getAttribute('val-day');
                $("#day-s1-" + day_id).html('');
                $("#day-s2-" + day_id).html('');

                const el = document.querySelectorAll("input[name-day=" + d.day_name + "]");
                for (var x = 0; x < el.length; x++) {
                    if (!el[x].checked) {
                        el[x].disabled = false;
                        el[x].style.background = '';
                    }
                }
                for (var x = 0; x < cx.length; x++) {
                    var day_id = cx[x].getAttribute('val-day');
                    var hour_id = cx[x].getAttribute('val-hour');
                    for (var i = -3; i <= 3; i++) {
                        var checkBox = document.getElementById("jdwl-" + day_id + "-" + (hour_id - i));
                        if (checkBox != undefined && i != 0) {
                            checkBox.disabled = true;
                            checkBox.style.background = 'grey';
                        }
                    }
                }
            } else if (cx.length == 2) {
                var day_id1 = cx[0].getAttribute('val-day');
                var hour_name1 = cx[0].getAttribute('name-hour');
                $("#day-s1-" + day_id1).html(hour_name1);
                var day_id2 = cx[1].getAttribute('val-day');
                var hour_name2 = cx[1].getAttribute('name-hour');
                $("#day-s2-" + day_id2).html(hour_name2);

                const el = document.querySelectorAll("input[name-day=" + d.day_name + "]");
                for (var x = 0; x < el.length; x++) {
                    if (!el[x].checked) {
                        el[x].disabled = true;
                        el[x].style.background = 'grey';
                    }
                }
            } else {
                const el = document.querySelectorAll("input[name-day=" + d.day_name + "]");
                for (var x = 0; x < el.length; x++) {
                    if (!el[x].checked) {
                        el[x].disabled = false;
                        el[x].style.background = '';
                    }
                }
            }
        })
    }
    checkDay();
    countHourPerDay();
</script>
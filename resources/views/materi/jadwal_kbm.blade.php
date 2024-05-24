@include('base.start', ['path' => 'jadwal-kbm', 'title' => 'Jadwal KBM', 'breadcrumbs' => ['Jadwal KBM']])

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg mb-2">
            <div class="card-body p-2">
                <div class="card-header p-2 ps-0">
                    <p class="mb-0">Silahkan memilih jam KBM, setiap sesi: <b>Durasinya -+ 1 Jam 30 Menit</b></p>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center mb-2" style="background:#e0e5ed;border:1px solid #ddd;">
                        <thead>
                            <tr class="text-center">
                                <th></th>
                                @foreach($day_kbm as $dn)
                                <th class="text-sm">
                                    {{$dn->day_name}}
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                <th></th>
                                @foreach($day_kbm as $dn)
                                <th class="text-sm">
                                    <div>Sesi 1: <span id="day-s1-{{$dn->id}}"></span></div>
                                    <div>Sesi 2: <span id="day-s2-{{$dn->id}}"></span></div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="">
                            @foreach($hour_kbm as $hn)
                            <tr class="text-sm text-center" id="">
                                <th>{{$hn->hour_name}}</th>
                                @foreach($day_kbm as $dn)
                                <th>
                                    <?php
                                    $checked = '';
                                    $get_data = App\Models\JadwalHariJamKbms::where('fkSantri_id', $santri_id)->where('fkHari_kbm_id', $dn->id)->where('fkJam_kbm_id', $hn->id)->first();
                                    if ($get_data != null) {
                                        $checked = 'checked';
                                    }
                                    ?>
                                    <input {{$checked}} class="form-check-input" name-day="{{$dn->day_name}}" name-hour="{{$hn->hour_name}}" val-day="{{$dn->id}}" val-hour="{{$hn->id}}" type="checkbox" id="jdwl-{{$dn->id}}-{{$hn->id}}" name="item[]" onclick="return setJadwal(this,{{$dn->id}},{{$hn->id}})">
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
<script>
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
</script>
@include('base.end')
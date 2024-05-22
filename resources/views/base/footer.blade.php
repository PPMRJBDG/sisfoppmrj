<?php
$setting = App\Models\Settings::first();
?>
<footer class="footer pt-2 pb-2">
    <div class="container-fluid p-0">
        <div class="col-md-12">
            <div class="copyright text-center text-sm text-muted text-lg-start">
                <div class="card">
                    <div class="card-body text-primary text-center font-weight-bold" id="tim-it">
                        Tim IT {{$setting->apps_name}} © {{ date('Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel">Report</h6>
                </div>
                <div>
                    <a style="cursor:pointer;" id="close"><i class="ni ni-fat-remove text-lg"></i></a>
                </div>
            </div>
            <div class="modal-body" id="contentReport" style="height:600px!important;">
                <tr>
                    <td colspan="3">
                        <span class="text-center">
                            Loading...
                        </span>
                    </td>
                </tr>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeb" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function showHideCacah() {
        $("#toggle-cacahjiwa").toggle();
    }

    function openTab(evt, tahun) {
        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tahun).style.display = "block";
        evt.currentTarget.className += " active";

        if (tahun == 'tabtable' || tahun == 'tabgrafik') {
            var angkatan = $('#select_angkatan').val();
            var tb = $('#select_tb').val();
            var periode = $('#select_periode').val();
            var presence_group = null;
            var data_presensi = null;

            $.get(`{{ url("/") }}/tabgraf/` + tb + `/` + angkatan + `/` + periode,
                function(data, status) {
                    $('#loading-table').css('display', 'none');
                    $('#card-table').css('display', 'block');
                    $('#loading-grafik').css('display', 'none');
                    $('#card-grafik').css('display', 'block');

                    // TABLE
                    var no = 1;
                    var data_body = '';
                    presence_group = <?php echo App\Models\PresenceGroup::get(); ?>;
                    var datax = data['data_presensi']['detil_presensi'];
                    data_presensi = data['data_presensi'];
                    Object.keys(datax).forEach(function(index) {
                        data_body = data_body + '<tr class="text-sm">' +
                            '<td class="text-center">' + no + '</td>' +
                            '<td class="text-center font-weight-bolder">' + hari_ini(new Date(index).getDay()) + ', ' + index + '</td>';
                        var persentase = 0;
                        var hadir = 0;
                        var alpha = 0;
                        presence_group.forEach(function(pg_key) {
                            data_body = data_body + '<td class="text-center">';
                            if (datax[index][pg_key['id']] != undefined) {
                                data_body = data_body + datax[index][pg_key['id']]['hadir'] + '|' + datax[index][pg_key['id']]['ijin'] + '|' + datax[index][pg_key['id']]['alpha'];
                                hadir = hadir + (datax[index][pg_key['id']]['hadir'] + datax[index][pg_key['id']]['ijin']);
                                alpha = alpha + datax[index][pg_key['id']]['alpha'];
                            }
                            data_body = data_body + '</td>';
                        });
                        persentase = hadir / (hadir + alpha) * 100;
                        var style = '';
                        if (persentase < 80) {
                            style = 'text-warning';
                        }
                        data_body = data_body + '<td class="text-center font-weight-bolder ' + style + '">' + persentase.toFixed(2) + '%</td></tr>';
                        no++;
                    });
                    $('#data-table').html(data_body);

                    // GRAFIK
                    presence_group.forEach(function(item, index) {
                        var ctx7 = document.getElementById("mixed-chart-" + item.id).getContext("2d");
                        var gradientStroke1 = ctx7.createLinearGradient(0, 230, 0, 50);
                        gradientStroke1.addColorStop(1, 'rgba(94,114,228,0.2)');
                        gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                        gradientStroke1.addColorStop(0, 'rgba(94,114,228,0)'); //purple colors
                        new Chart(ctx7, {
                            data: {
                                labels: data_presensi['tanggal_presensi'][item.id],
                                datasets: [{
                                        type: "bar",
                                        label: "Hadir",
                                        weight: 5,
                                        tension: 0.4,
                                        borderWidth: 0,
                                        pointBackgroundColor: "#3A416F",
                                        borderColor: "#3A416F",
                                        backgroundColor: '#3A416F',
                                        borderRadius: 4,
                                        borderSkipped: false,
                                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['hadir'] : [0],
                                        maxBarThickness: 10,
                                    },
                                    {
                                        type: "line",
                                        label: "Ijin",
                                        tension: 0.4,
                                        borderWidth: 0,
                                        pointRadius: 0,
                                        pointBackgroundColor: "#5e72e4",
                                        borderColor: "#5e72e4",
                                        borderWidth: 3,
                                        backgroundColor: gradientStroke1,
                                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['ijin'] : 0,
                                        fill: true,
                                    },
                                    {
                                        type: "line",
                                        label: "Alpha",
                                        tension: 0.4,
                                        borderWidth: 0,
                                        pointRadius: 0,
                                        pointBackgroundColor: "#f56565",
                                        borderColor: "#f56565",
                                        borderWidth: 3,
                                        backgroundColor: gradientStroke1,
                                        data: data_presensi['total_presensi'][item.id] ? data_presensi['total_presensi'][item.id]['alpha'] : 0,
                                        fill: true,
                                    }
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false,
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index',
                                },
                                scales: {
                                    y: {
                                        grid: {
                                            drawBorder: false,
                                            display: true,
                                            drawOnChartArea: true,
                                            drawTicks: false,
                                            borderDash: [5, 5]
                                        },
                                        ticks: {
                                            display: true,
                                            padding: 10,
                                            color: '#b2b9bf',
                                            font: {
                                                size: 11,
                                                family: "Open Sans",
                                                style: 'normal',
                                                lineHeight: 2
                                            },
                                        }
                                    },
                                    x: {
                                        grid: {
                                            drawBorder: false,
                                            display: true,
                                            drawOnChartArea: true,
                                            drawTicks: true,
                                            borderDash: [5, 5]
                                        },
                                        ticks: {
                                            display: true,
                                            color: '#b2b9bf',
                                            padding: 10,
                                            font: {
                                                size: 11,
                                                family: "Open Sans",
                                                style: 'normal',
                                                lineHeight: 2
                                            },
                                        }
                                    },
                                },
                            },
                        });
                    })
                }
            );
        }
    }

    function hari_ini(hari) {
        switch (hari) {
            case 0:
                return "Minggu";
                break;

            case 1:
                return "Senin";
                break;

            case 2:
                return "Selasa";
                break;

            case 3:
                return "Rabu";
                break;

            case 4:
                return "Kamis";
                break;

            case 5:
                return "Jumat";
                break;

            case 6:
                return "Sabtu";
                break;
        }
    }

    function getReport(ids) {
        $('#exampleModal').fadeIn();
        $('#exampleModal').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModal').css('z-index', '10000');
        $('#contentReport').html('<iframe src="{{ url("/") }}/report/' + ids + '"  style="height:100%;width:100%;"></iframe>');
    }

    $('#close').click(function() {
        $('#exampleModal').fadeOut();
        $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    });

    $('#closeb').click(function() {
        $('#exampleModal').fadeOut();
        $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    });

    function checkSS(val) {
        $("#show-ss").hide();
        $("#show-ss-berjangka").hide();
        const el = document.querySelector("#status_ss");
        const elb = document.querySelector("#status_ss_berjangka");
        el.disabled = true
        if (elb != null) {
            elb.disabled = true
        }
        if (val.value.match("Pulang")) {
            $("#show-ss").show();
            $("#show-ss-berjangka").show();
            el.disabled = false
            if (elb != null) {
                elb.disabled = false
            }
        }
    }

    function infoSS(val) {
        $("#show-info-ss").hide();
        $("#show-info-ss-berjangka").hide();
        if (val.value.match("Belum")) {
            $("#show-info-ss").show();
            $("#show-info-ss-berjangka").show();
        }
    }

    function selectAllCheckbox(thisx) {
        const el = document.querySelectorAll(".cls-ckb");
        for (var i = 0; i < el.length; i++) {
            if (thisx.checked) {
                el[i].checked = true
            } else {
                el[i].checked = false
            }

            showInputAlasan(el[i], el[i].getAttribute('presence-id') + '-' + el[i].getAttribute('santri-id'));
        }
    }

    function checkTextLength(th) {
        const el = document.querySelector("#btn-prsc");
        if (th.value.length < 10) {
            // $("#btn-prsc").hide();
            el.disabled = true
        } else {
            // $("#btn-prsc").show();
            el.disabled = false
        }
    }

    function promptDeletePermit(url, id, presence_id = null, santri_id = null) {
        var alasan = prompt('Yakin di tolak ? Berikan alasannya');
        if (alasan) {
            var datax = {}
            if (presence_id != null && santri_id != null) {
                const ival = []
                ival.push([presence_id, santri_id, alasan])
                datax['data_json'] = JSON.stringify(ival);
            } else {
                datax['alasan'] = alasan
            }
            $.get(url, datax,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    // console.log(return_data)
                    if (return_data.status) {
                        // alert(return_data.message)
                        window.location.reload();
                    } else {
                        alert(return_data.message)
                    }
                }
            )
            return true;
        } else {
            alert('Silahkan berikan alasannya')
            return false
        }
    }
</script>
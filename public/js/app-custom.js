const base_url = $("#base-url").val();

$('body').on('click', '#close', function (e) {
    $('#exampleModal').fadeOut();
    $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    $('#alertModal').fadeOut();
    $('#contentAlert').html('');
});

$('#closeb').click(function () {
    $('#exampleModal').fadeOut();
    $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
});

function matchArray(list_tab, value) {
    for (var i = 0; i < list_tab.length; i++) {
        if (list_tab[i] == value) {
            return true;
        }
    }
}

function refreshCurrentUrl() {
    var current_url = $("#current-url").val();
    getPage(current_url);
}

function getPrevPage() {
    if (getCookie('prev_url') == "") {
        getPage("/home");
    } else {
        getPage(getCookie('prev_url'));
    }
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            if (c.substring(name.length, c.length) == "") {
                return "/home";
            } else {
                return c.substring(name.length, c.length);
            }
        }
    }
    return "/home";
}

function getPage(url) {
    $('#content-app').html('');
    $("#loading").fadeIn();
    $("#al-danger").hide();
    $(".modal").fadeOut();
    if (url == 'undefined') {
        url = '/home'
    }
    $("#current-url").val(url);
    $.ajax({
        type: "GET",
        url: url,
        data: {},
        success: function (data) {
            if (data == 'reload') {
                window.location.reload();
            } else {
                $("#loadingSubmit").hide();
                $("#loading").fadeOut();

                document.cookie = "prev_url=" + getCookie('current_url');
                document.cookie = "current_url=" + url;

                var include_start = '<div data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left">';
                var include_end = '</div>' +
                    '<script type="text/javascript" src="./../ui-kit/js/mdb-v2.min.js"></script>' +
                    '<script type="text/javascript" src="./../js/app-custom-form.js"></script>';
                $('#content-app').html(include_start + data + include_end);

                setTimeout(function () {
                    $(".alert").fadeOut();
                }, 5000);
            }
        },
        error: function () {
            $("#loading").hide();
            $("#al-danger").show();
            $("#al-danger").html('Sedang terjadi kesalahan');
        }
    });
}

function showCacahJiwa() {
    $("#sidenav-1").css('transform', 'translateX(-100%)');
    var sidenav = document.querySelectorAll(".sidenav-backdrop");
    if (sidenav != null) {
        for (var i = 0; i < sidenav.length; i++) {
            sidenav[i].remove();
        }
    }

    $('#cacahJiwaModal').fadeIn();
    $('#cacahJiwaModal').css('background', 'rgba(0, 0, 0, 0.7)');
    $('#cacahJiwaModal').css('z-index', '10000');
}

function openTabGraf(tahun, data_presence_group) {
    if (tahun == 'tabtable' || tahun == 'tabgrafik') {
        var angkatan = $('#select_angkatan').val();
        var tb = $('#select_tb').val();
        var periode = $('#select_periode').val();
        var data_presensi = null;
        var presence_group = JSON.parse(data_presence_group);

        $.get(base_url + `/tabgraf/` + tb + `/` + angkatan + `/` + periode,
            function (data, status) {
                $('#loading-table').css('display', 'none');
                $('#card-table').css('display', 'block');
                $('#loading-grafik').css('display', 'none');
                $('#card-grafik').css('display', 'block');

                // TABLE
                var no = 1;
                var data_body = '';
                var datax = data['data_presensi']['detil_presensi'];
                data_presensi = data['data_presensi'];
                Object.keys(datax).forEach(function (index) {
                    data_body = data_body + '<tr class="text-sm">' +
                        '<td class="text-center">' + no + '</td>' +
                        '<td class="text-center font-weight-bolder">' + hari_ini(new Date(index).getDay()) + ', ' + index + '</td>';
                    var persentase = 0;
                    var hadir = 0;
                    var alpha = 0;
                    presence_group.forEach(function (pg_key) {
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
                presence_group.forEach(function (item, index) {
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
                                            family: "Varela Round",
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
                                            family: "Varela Round",
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
    $('#contentReport').html('<iframe src="/report/' + ids + '"  style="height:100%;width:100%;"></iframe>');
}

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

function selectAllCheckboxBerjangka(thisx) {
    const el = document.querySelectorAll(".cls-ckb-berjangka");
    for (var i = 0; i < el.length; i++) {
        if (thisx.checked) {
            el[i].checked = true
        } else {
            el[i].checked = false
        }
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

function promptApprovePermit(url, id, presence_id = null, santri_id = null) {
    if (confirm('Yakin di approve ?')) {
        var datax = {}
        datax['json'] = true;
        const ival = []
        ival.push([presence_id, santri_id])
        datax['data_json'] = JSON.stringify(ival);
        $.get(url, datax,
            function (data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    if (return_data.is_present != '') {
                        $("#alertModal").fadeIn();
                        $("#contentAlert").html(return_data.is_present + ' telah hadir di presensi ini');
                    }
                    refreshCurrentUrl()
                } else {
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html(return_data.message);
                    return false;
                }
            }
        )
        return true;
    }
}

function promptRejectPermit(url, id, presence_id = null, santri_id = null) {
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
            function (data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    refreshCurrentUrl()
                } else {
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html(return_data.message);
                    return false;
                }
            }
        )
        return true;
    } else {
        $("#alertModal").fadeIn();
        $("#contentAlert").html('Silahkan berikan alasannya');
        return false;
    }
}

function showInputAlasan(thisx, sid) {
    if (thisx.checked) {
        $("#asd-b-" + sid).show();
    } else {
        $("#asd-b-" + sid).hide();
    }
}

function promptDeleteAndPresent(id, presence_id = null, santri_id = null) {
    if (confirm('Apakah anda yakin untuk menghadirkan dari perijinan ini ?')) {
        var datax = {};
        datax['presence_id'] = presence_id;
        datax['santri_id'] = santri_id;

        $.get(base_url + `/presensi/izin/pengajuan/delete_and_present`, datax,
            function (data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    refreshCurrentUrl()
                } else {
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html(return_data.message);
                    return false;
                }
            }
        )
    }
}

function actionSavePermit(action) {
    $("#loadingSubmit").show();
    var datax = {};
    datax['json'] = true;

    // harian
    const ival = []
    const el = document.querySelectorAll(".cls-ckb");
    for (var i = 0; i < el.length; i++) {
        if (el[i].checked) {
            if (action == 'reject') {
                var alasan = $("#alasan-" + el[i].getAttribute('presence-id') + '-' + el[i].getAttribute('santri-id')).val();
                if (alasan == '') {
                    $("#alertModal").fadeIn();
                    $("#contentAlert").html('Berikan alasan pada form yang masih kosong');
                    return false;
                }
            }
            ival.push([el[i].getAttribute('presence-id'), el[i].getAttribute('santri-id'), alasan])
        }
    }
    // berjangka
    const ival_berjangka = []
    const el_berjangka = document.querySelectorAll(".cls-ckb-berjangka");
    for (var i = 0; i < el_berjangka.length; i++) {
        if (el_berjangka[i].checked) {
            if (action == 'reject') {
                var alasan = 'reject by admin';
            }
            ival_berjangka.push([el_berjangka[i].getAttribute('presence-id'), el_berjangka[i].getAttribute('santri-id'), alasan])
        }
    }
    if ((ival.length + ival_berjangka.length) == 0) {
        $("#alertModal").fadeIn();
        $("#contentAlert").html('Silahkan pilih minimal satu mahasiswa / perijinan!');
        return false;
    } else {
        if (ival.length > 0) {
            var pesan_action = '';
            var url = '/presensi/izin/saya/'
            if (action == 'delete') {
                pesan_action = 'menghapus';
                url = '/presensi/izin/persetujuan/'
            } else if (action == 'approve') {
                pesan_action = 'menyetujui';
            } else if (action == 'reject') {
                pesan_action = 'menolak';
            }
            // if (confirm('Apakah anda yakin untuk ' + pesan_action + ' perijinan ini ?')) {
            datax['data_json'] = JSON.stringify(ival);
            $.get(base_url + `` + url + action, datax,
                function (data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        if (return_data.is_present != '' && action == 'approve') {
                            $("#alertModal").fadeIn();
                            $("#contentAlert").html(return_data.is_present + ' telah hadir di presensi ini');
                        }

                        refreshCurrentUrl()
                        // ival.forEach(function (iv) {
                        //     var element = document.getElementById("prmt-" + iv[0] + "-" + iv[1]);
                        //     if (element != null) {
                        //         element.remove();
                        //     }
                        // })
                    }
                }
            )
            // }
        }
        if (ival_berjangka.length > 0) {
            var pesan_action = '';
            if (action == 'delete') {
                $("#alertModal").fadeIn();
                $("#contentAlert").html('Perijinan berjangka tidak dapat dihapus');
                return false;
            } else if (action == 'approve') {
                pesan_action = 'menyetujui';
            } else if (action == 'reject') {
                pesan_action = 'menolak';
            }
            // if (confirm('Apakah anda yakin untuk ' + pesan_action + ' perijinan berjangka ini ?')) {
            datax['data_json_berjangka'] = JSON.stringify(ival_berjangka);
            $.get(base_url + `/presensi/izin/pengajuan/berjangka/` + action, datax,
                function (data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        refreshCurrentUrl()
                        // ival_berjangka.forEach(function (iv) {
                        //     var element = document.getElementById("rpg-" + iv[0]);
                        //     if (element != null) {
                        //         element.remove();
                        //     }
                        // })
                    } else {
                        $("#alertModal").fadeIn();
                        $("#contentAlert").html(return_data.message);
                    }
                }
            )
            // }
        }
    }
}

function getMateri(santri_id, nama) {
    $('#exampleModalMateri').fadeIn();
    $('#exampleModalMateri').css('background', 'rgba(0, 0, 0, 0.7)');
    $('#exampleModalMateri').css('z-index', '10000');
    $('#exampleModalLabelMateri span#nm').text(nama);
    $.post(base_url + `/materi/monitoring/materi_santri`, {
        santri_id: santri_id
    },
        function (data) {
            $('#contentMateri').html(data);
        }
    );
}

// function actionSaveRangePermit(rpgId, santriId) {
//     var datax = {};
//     if (confirm('Apakah anda yakin untuk menyetujui perijinan berjangka ini ?')) {
//         datax['rpgId'] = rpgId;
//         datax['santriId'] = santriId;
//         $.get(base_url + `/presensi/izin/pengajuan/berjangka/approve`, datax,
//             function (data, status) {
//                 var return_data = JSON.parse(data);
//                 if (return_data.status) {
//                     const element = document.getElementById("rpg-" + rpgId);
//                     element.remove();
//                 } else {
//                     alert(return_data.message)
//                 }
//             }
//         )
//     }
// }
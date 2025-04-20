const base_url = $("#base-url").val();

$('body').on('click', '#close', function (e) {
    $('#exampleModalReport').fadeOut();
    $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
    $('#alertModal').fadeOut();
    $('#contentAlert').html('');
});

$('#closeb').click(function () {
    $('#exampleModalReport').fadeOut();
    $('#contentReport').html('<tr><td colspan="3"><span class="text-center">Loading...</span></td></tr>');
});

function toNumber(val){
    const format = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });
    return format.format(val);
}

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
    $("#breadcrumb-item").html("")
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
    $("#footer-calendar").hide();
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
            $("#al-danger").html('Sedang terjadi kesalahan<br><a href="/home" class="btn btn-sm btn-info">Kembali ke Beranda</a>');
        }
    });
}

function showCacahJiwa() {
    $("#sidenav-1").css('transform', 'translateX(100%)');
    var sidenav = document.querySelectorAll(".sidenav-backdrop");
    if (sidenav != null) {
        for (var i = 0; i < sidenav.length; i++) {
            sidenav[i].remove();
        }
    }

    $('#cacahJiwaModal').fadeIn();
}

function dateFormat(date){
    let tanggalSekarang = new Date(date);
    return new Intl.DateTimeFormat('id-ID', {    year: 'numeric',    month: '2-digit',    day: '2-digit'}).format(tanggalSekarang);
}

function openTab(data_presence_group) {
        var angkatan = $('#select_angkatan').val();
        var tb = $('#select_tb').val();
        var periode = $('#select_periode').val();
        var presence_group = JSON.parse(data_presence_group);

        $.get(base_url + `/tabgraf/` + tb + `/` + angkatan + `/` + periode,
            function (data, status) {
                $('#loading-table').css('display', 'none');
                $('#card-table').css('display', 'block');
                var prev_persentase = 0;
                var no = 1;
                var data_body = '';
                var datax = data['data_presensi']['detil_presensi'];
                data_presensi = data['data_presensi'];

                // TABLE
                Object.keys(datax).forEach(function (index) {
                    let formatIndonesia = dateFormat(index);
                    
                    data_body = data_body + '<tr class="text-sm">' +
                        '<td class="text-center">' + no + '</td>' +
                        '<td class="text-left font-weight-bolder">' + hari_ini(new Date(index).getDay()) + ', ' + formatIndonesia + '</td>';
                    var persentase = 0;
                    var hadir = 0;
                    var alpha = 0;
                    presence_group.forEach(function (pg_key) {
                        // data_body = data_body + '<td class="text-center">';
                        if (datax[index][pg_key['id']] != undefined) {
                            data_body = data_body + '<td class="text-center">' + datax[index][pg_key['id']]['hadir'] + '</td><td class="text-center">' + datax[index][pg_key['id']]['ijin'] + '</td><td class="text-center">' + datax[index][pg_key['id']]['alpha'] + '</td>';
                            hadir = hadir + (datax[index][pg_key['id']]['hadir'] + datax[index][pg_key['id']]['ijin']);
                            alpha = alpha + datax[index][pg_key['id']]['alpha'];
                        }else{
                            data_body = data_body + '<td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td>';
                        }
                        // data_body = data_body + '</td>';
                    });
                    persentase = hadir / (hadir + alpha) * 100;
                    var style = '';
                    if (persentase < 80) {
                        style = 'text-warning';
                    }else if (persentase < 50) {
                        style = 'text-danger';
                    }

                    var caret = '<i class="fa fa-caret-down text-danger" style="font-size:18px;"><i/>';
                    if(prev_persentase < persentase){
                        caret = '<i class="fa fa-caret-up text-success" style="font-size:18px;"><i/>';
                    }
                    data_body = data_body + '<td class="text-center font-weight-bolder ' + style + '">' + persentase.toFixed(2) + '%' +' ' + caret + '</td></tr>';
                    prev_persentase = persentase;
                    no++;
                });
                $('#data-table').html(data_body);

                var elem = document.getElementById("section-1");
                elem.scrollIntoView();
            }
        );
}

function openGraf(data_presence_group) {
        var angkatan = $('#select_angkatan').val();
        var tb = $('#select_tb').val();
        var periode = $('#select_periode').val();
        var data_presensi = null;
        var presence_group = JSON.parse(data_presence_group);

        $.get(base_url + `/tabgraf/` + tb + `/` + angkatan + `/` + periode,
            function (data, status) {
                $('#loading-grafik').css('display', 'none');
                $('#card-grafik').css('display', 'block');
                data_presensi = data['data_presensi'];

                // GRAFIK
                presence_group.forEach(function (item, index) {
                    var ctx7 = document.getElementById("mixed-chart-" + item.id).getContext("2d");
                    var gradientStroke1 = ctx7.createLinearGradient(0, 230, 0, 50);
                    gradientStroke1.addColorStop(1, 'rgba(94,114,228,0.2)');
                    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                    gradientStroke1.addColorStop(0, 'rgba(94,114,228,0)'); 
                    
                    new Chart(ctx7, {
                        type: "line",
                        data: {
                            labels: (data_presensi['tanggal_presensi'][item.id]!=undefined) ? data_presensi['tanggal_presensi'][item.id] : "0",
                            datasets: [
                            {
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
                                data: (data_presensi['total_presensi'][item.id]!=undefined) ? data_presensi['total_presensi'][item.id]['hadir'] : [0],
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
                                data: (data_presensi['total_presensi'][item.id]!=undefined) ? data_presensi['total_presensi'][item.id]['ijin'] : [0],
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
                                data: (data_presensi['total_presensi'][item.id]!=undefined) ? data_presensi['total_presensi'][item.id]['alpha'] : [0],
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

                var elem = document.getElementById("section-1");
                elem.scrollIntoView();
            }
        );
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
    $('#exampleModalReport').fadeIn();
    $('#exampleModalReport').css('background', 'rgba(0, 0, 0, 0.7)');
    $('#exampleModalReport').css('z-index', '10000');
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
    if (val.value.match("Pulang") || val.value.match("Magang")) {
        if (val.value.match("Pulang - Permintaan Ortu")) {
            alert("Perhatian! Sesuai peraturan, diperbolehkan pulang hanya di minggu ke 3 atau 4 saja.");
        }
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
    $('#exampleModalLabelMateri span#nm').text(nama);
    $.post(base_url + `/materi/monitoring/materi_santri`, {
        santri_id: santri_id
    },
        function (data) {
            $('#contentMateri').html(data);
        }
    );
}

function setChangeLiburan(data){
    $("#liburan_id").val(data.id);
    $("#liburan_from").val(data.liburan_from);
    $("#liburan_to").val(data.liburan_to);
    $("#keterangan").val(data.keterangan);
}

function setChangePelanggaran(data){
    $("#pelanggaran_id").val(data.id);
    $("#jenis_pelanggaran").val(data.jenis_pelanggaran);
    $("#kategori_pelanggaran").val(data.kategori_pelanggaran.toLowerCase());
}

function searchDataSantri(id,value){
    var santris = document.querySelectorAll("#"+id+" table tbody tr td span.santri-name");
    var loop_id = document.querySelectorAll("#"+id+" table tbody tr");
    for (var i = 0; i < santris.length; i++) {
        var name = santris[i].getAttribute('santri-name')
        if(name!=null){
            name = name.toLowerCase()
            if(name.includes(value.toLowerCase())){
                loop_id[i].setAttribute('style','display:table-row;')
            }else{
                loop_id[i].setAttribute('style','display:none;');
            }
        }
    }
}
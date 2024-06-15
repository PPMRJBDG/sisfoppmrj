$(document.body).ready(function () {
    $("form").submit(function (e) {
        e.preventDefault();
        $("#loadingSubmit").show();

        if ($(this).attr('id') == 'upload-file') {
            var form = $("#upload-file")[0];
            var datax = new FormData(form);
            var option = {
                type: "POST",
                enctype: 'multipart/form-data',
                cache: false,
                url: $(this).attr('action'),
                data: datax,
                processData: false,
                contentType: false
            }
        } else {
            var datax = $(this).serialize();
            var option = {
                type: "POST",
                cache: false,
                url: $(this).attr('action'),
                data: datax,
            }
        }

        $.ajax(option).done(function (data) {
            if (data == 'reload') {
                window.location.reload();
            } else {
                $("#loadingSubmit").hide();

                var js = '<script type="text/javascript" src="./../js/app-custom-form.js"></script>';
                $('#content-app').html(data + js);

                setTimeout(function () {
                    $(".alert").fadeOut();
                }, 5000);
            }
        });
    });
});
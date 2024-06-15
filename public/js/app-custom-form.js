$(document.body).ready(function () {
    $("form").submit(function (e) {
        e.preventDefault();
        $("#loadingSubmit").show();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
        }).done(function (data) {
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
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

        try{
            $.ajax(option).done(function (data) {
                try{
                    if (data == 'reload') {
                        window.location.reload();
                    } else {
                        $("#loadingSubmit").hide();
                        window.scrollTo(0, 0);

                        var include_start = '<div data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left">';
                        var include_end = '</div>' +
                            '<script type="text/javascript" src="./../ui-kit/js/mdb-v2.min.js"></script>' +
                            '<script type="text/javascript" src="./../js/app-custom-form.js"></script>';
                        $('#content-app').html(include_start + data + include_end);

                        setTimeout(function () {
                            $(".alert").fadeOut();
                        }, 8000);
                    }
                }catch(err){
                    console.log(err.message)
                }
            });
        }catch(err){
            console.log(err)
        }
    });
});
<div class="modal" id="exampleModalBulk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:700px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel">Bulk Whatsapp</h6>
                </div>
            </div>
            <div class="modal-body p-2" style="background:#f9f9ff;">
                <div id="alert-success" class="alert alert-success text-white mb-0" style="display:none;"></div>
                <div id="alert-error" class="alert alert-danger text-white mb-0" style="display:none;"></div>
                <div class="p-2">
                    <label class="form-control-label">Nama Contact</label>
                    <input class="form-control" readonly type="hidden" id="bulk_id" name="bulk_id" value="">
                    <input class="form-control" readonly type="text" id="bulk_name" name="bulk_name" value="">

                    <label class="form-control-label">Nomor Kontak</label>
                    <input class="form-control" readonly type="text" id="bulk_contact" name="bulk_contact" value="">

                    <label class="form-control-label">Subject Pesan</label>
                    <input class="form-control" type="text" id="bulk_subject" name="bulk_subject" value="">

                    <label class="form-control-label">Pesan</label>
                    <textarea class="form-control" id="bulk_message" name="bulk_message" rows="10"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" type="button" id="close-bulk" class="btn btn-secondary" data-dismiss="modal">Keluar</a>
                <a href="#" type="button" id="send-bulk" class="btn btn-primary">Kirim</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="exampleModal-group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-group" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel-group">Create Group Contact</h6>
                </div>
            </div>
            <div class="modal-body p-2" style="background:#f9f9ff;">
                <div id="alert-success-group" class="alert alert-success text-white mb-0" style="display:none;"></div>
                <div id="alert-error-group" class="alert alert-danger text-white mb-0" style="display:none;"></div>
                <div class="p-2" style="background:#f9f9ff;">
                    <label class="form-control-label">Nama Group (note: awali dengan kata "Group")</label>
                    <input class="form-control" type="text" id="group_name" name="group_name" value="Group " placeholder="Group Kebahagiaan">
                    <label class="form-control-label">ID Group</label>
                    <input class="form-control" type="text" id="group_id" name="group_id" value="">
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" href="#" id="close-group" class="btn btn-secondary" data-dismiss="modal">Keluar</a>
                <a type="button" href="#" id="add-group" class="btn btn-primary">Kirim</a>
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

    function changeBulk(id, name, contact) {
        $('#exampleModalBulk').fadeIn();
        $('#exampleModalBulk').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModalBulk').css('z-index', '10000');
        $('#bulk_id').val(id);
        $('#bulk_contact').val(contact);
        $('#bulk_name').val('[' + id + '] ' + name);
    }

    function deleteContact(id) {
        var c = confirm('Apakah anda yakin menghapus group whatsapp ini?');
        if (c) {
            $.post("{{ route('stdbot delete contact') }}", {
                    contact_id: id,
                },
                function(data) {
                    data = JSON.parse(data);
                    if (data.status) {
                        window.location.reload()
                    } else {
                        $('#alert-error').show();
                        $('#alert-error').text(data.message);
                    }
                }
            );
        }
    }

    $('#send-bulk').click(function() {
        if ($('#bulk_subject').val() == '') {
            alert('Subject Pesan harus diisi');
            return false;
        }
        if ($('#bulk_message').val() == '') {
            alert('Pesan harus diisi');
            return false;
        }
        $.post("{{ route('stdbot send wa') }}", {
                bulk_id: $('#bulk_id').val(),
                bulk_subject: $('#bulk_subject').val(),
                bulk_message: $('#bulk_message').val()
            },
            function(data) {
                data = JSON.parse(data);
                if (data.status) {
                    $('#alert-success').show();
                    $('#alert-success').text(data.message);
                    $('#bulk_subject').val('');
                    $('#bulk_message').val('');
                } else {
                    $('#alert-error').show();
                    $('#alert-error').text(data.message);
                }
            }
        );
    });

    function createGroup(){
        $('#exampleModal-group').fadeIn();
    };

    $('#add-group').click(function() {
        if ($('#group_name').val() == '') {
            alert('Nama group harus diisi');
            return false;
        }
        if ($('#group_id').val() == '') {
            alert('ID group harus diisi');
            return false;
        }
        $.post("{{ route('stdbot create group') }}", {
                group_name: $('#group_name').val(),
                group_id: $('#group_id').val()
            },
            function(data) {
                data = JSON.parse(data);
                if (data.status) {
                    $('#alert-success-group').show();
                    $('#alert-success-group').text(data.message);
                } else {
                    $('#alert-error-group').show();
                    $('#alert-error-group').text(data.message);
                }
            }
        );
    })

    $('#close-bulk').click(function() {
        $('#exampleModalBulk').fadeOut();
        $('#alert-success').hide();
        $('#alert-error').hide();
    });

    $('#close-group').click(function() {
        $('#exampleModal-group').fadeOut();
        $('#alert-success-group').hide();
        $('#alert-error-group').hide();
        $('#group_name').val('');
        $('#group_id').val('');
    });

    // $('#table-bulk').DataTable();
    // $('#table-contact').DataTable();
    // $('#table-user').DataTable();
</script>
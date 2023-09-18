@include('base.start', ['path' => 'msgtools/contact', 'title' => 'Msgtools - Contact', 'breadcrumbs' => ['Msgtools - Contact']])
@if ($errors->any())
<div class="alert alert-danger text-white">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if (session('success'))
<div class="alert alert-success text-white">
    {{ session('success') }}
</div>
@endif

<div class="card mb-2">
    <div class="card-body">
        <div class="d-flex">
            <a href="{{ route('msgtools generate bulk') }}" id="generate-contact" class="btn btn-primary">
                <i class="ni ni-curved-next" aria-hidden="true"></i>
                Generate Contact
            </a>
        </div>
        <div class="table-responsive">
            <table id="table-bulk" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Bulk</th>
                        <th class="text-uppercase text-sm text-secondary align-middle text-center font-weight-bolder">Contact</th>
                        <th class="text-uppercase text-sm text-secondary align-middle text-center font-weight-bolder"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bulk_user as $bu)
                    <tr class="text-sm">
                        <td>
                            {{ $bu['bulk_id'] }}
                        </td>
                        <td>
                            {{ $bu['bulk_name'] }}
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a class="btn btn-secondary btn-xs mb-0">{{ count($bu['data']) }}</a>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a class="btn btn-primary btn-xs mb-0" onclick="changeBulk('<?php echo $bu['bulk_id']; ?>','<?php echo $bu['bulk_name']; ?>','Bulk')">Kirim Pesan</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body">
        <div class="d-flex">
            <a id="modal-create-group" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                Tambah Group Whatsapp
            </a>
        </div>

        <div class="table-responsive">
            <table id="table-group" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Group</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder ps-2">Contact</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder ps-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group_user as $gu)
                    <tr class="text-sm">
                        <td>
                            {{ $gu['group_id'] }}
                        </td>
                        <td>
                            {{ $gu['group_name'] }}
                        </td>
                        <td>
                            {{ $gu['phone'] }}
                        </td>
                        <td class="align-middle text-center text-sm">
                            <a class="btn btn-primary btn-xs mb-0" onclick="changeBulk('<?php echo $gu['group_id']; ?>','<?php echo $gu['group_name']; ?>','<?php echo $gu['phone']; ?>')">Kirim Pesan</a>
                            <a class="btn btn-danger btn-xs mb-0" onclick="deleteContact('<?php echo $gu['group_id']; ?>')">Hapus</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="table-contact" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Pribadi</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder">Nohp Pribadi</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">ID</th>
                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Ortu</th>
                        <th class="text-uppercase text-sm text-secondary align-middle font-weight-bolder">Nohp Ortu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contact_user as $gu)
                    <tr class="text-sm">
                        <td>
                            {{ $gu['pribadi_id'] }}
                        </td>
                        <td>
                            {{ $gu['nama_pribadi'] }}
                        </td>
                        <td>
                            <a onclick="changeBulk('<?php echo $gu['pribadi_id']; ?>','<?php echo $gu['nama_pribadi']; ?>','<?php echo $gu['nohp_pribadi']; ?>')" class="btn btn-primary btn-sm mb-0">{{ $gu['nohp_pribadi'] }}</a>
                        </td>
                        <td>
                            {{ $gu['ortu_id'] }}
                        </td>
                        <td>
                            {{ $gu['nama_ortu'] }}
                        </td>
                        <td>
                            <a onclick="changeBulk('<?php echo $gu['ortu_id']; ?>','<?php echo $gu['nama_ortu']; ?>','<?php echo $gu['nohp_ortu']; ?>')" class="btn btn-primary btn-sm mb-0">{{ $gu['nohp_ortu'] }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:700px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel">Bulk Whatsapp</h6>
                </div>
            </div>
            <div class="modal-body">
                <div id="alert-success" class="alert alert-success text-white" style="display:none;"></div>
                <div id="alert-error" class="alert alert-danger text-white" style="display:none;"></div>
                <div class="p-2" style="background:#f9f9ff;">
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
                <a type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Keluar</a>
                <a type="button" id="send" class="btn btn-primary">Kirim</a>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="exampleModal-group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-group" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:700px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="exampleModalLabel-group">Create Group Contact</h6>
                </div>
            </div>
            <div class="modal-body">
                <div id="alert-success-group" class="alert alert-success text-white" style="display:none;"></div>
                <div id="alert-error-group" class="alert alert-danger text-white" style="display:none;"></div>
                <div class="p-2" style="background:#f9f9ff;">
                    <label class="form-control-label">Nama Group (note: awali dengan kata "Group")</label>
                    <input class="form-control" type="text" id="group_name" name="group_name" value="Group " placeholder="Group Kebahagiaan">
                    <label class="form-control-label">ID Group</label>
                    <input class="form-control" type="text" id="group_id" name="group_id" value="">
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" href="" id="close-group" class="btn btn-secondary" data-dismiss="modal">Keluar</a>
                <a type="button" id="add-group" class="btn btn-primary">Kirim</a>
            </div>
        </div>
    </div>
</div>

<script>
    function changeBulk(id, name, contact) {
        $('#exampleModal').fadeIn();
        $('#exampleModal').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModal').css('z-index', '10000');
        $('#bulk_id').val(id);
        $('#bulk_contact').val(contact);
        $('#bulk_name').val('[' + id + '] ' + name);
    }

    function deleteContact(id) {
        var c = confirm('Apakah anda yakin menghapus group whatsapp ini?');
        if (c) {
            $.post("{{ route('msgtools delete contact') }}", {
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

    $('#send').click(function() {
        if ($('#bulk_subject').val() == '') {
            alert('Subject Pesan harus diisi');
            return false;
        }
        if ($('#bulk_message').val() == '') {
            alert('Pesan harus diisi');
            return false;
        }
        $.post("{{ route('msgtools send wa') }}", {
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

    $('#modal-create-group').click(function() {
        $('#exampleModal-group').fadeIn();
        $('#exampleModal-group').css('background', 'rgba(0, 0, 0, 0.7)');
        $('#exampleModal-group').css('z-index', '10000');
    });

    $('#add-group').click(function() {
        if ($('#group_name').val() == '') {
            alert('Nama group harus diisi');
            return false;
        }
        if ($('#group_id').val() == '') {
            alert('ID group harus diisi');
            return false;
        }
        $.post("{{ route('msgtools create group') }}", {
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

    $('#close').click(function() {
        $('#exampleModal').fadeOut();
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

    $('#table-bulk').DataTable();
    $('#table-contact').DataTable();
    $('#table-user').DataTable();
</script>
@include('base.end')
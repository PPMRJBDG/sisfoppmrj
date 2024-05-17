@include('base.start', ['path' => 'dewan-pengajar', 'title' => 'Dewan Pengajar', 'breadcrumbs' => ['Dewan Pengajar']])

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <form action="#">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">
                                            Nama Pengajar
                                        </label>
                                        <input class="form-control" type="text" id="pengajar" name="pengajar" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <a onclick="return savePengajar();" class="btn btn-primary btn-sm form-control mb-0">Tambah Pengajar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table id="table" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Pengajar</th>
                                        <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2"></th>
                                    </tr>
                                </thead>
                                <tbody id="list-pengajar">
                                    @if(isset($pengajar))
                                    @foreach($pengajar as $data)
                                    <tr class="text-sm" id="p{{$data->id}}">
                                        <td>
                                            <input tytpe="text" class="form-control" disabled value="{{ $data->name }}" id="data-name{{$data->id}}">
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <a href="#" onclick="return ubahPengajar(<?php echo $data->id; ?>)" class="btn btn-primary btn-xs mb-0">Ubah</a>
                                            <a href="#" onclick="return deletePengajar(<?php echo $data->id; ?>)" class="btn btn-danger btn-xs mb-0">Hapus</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#table').DataTable({
        order: [
            // [1, 'desc']
        ],
    });

    async function savePengajar() {
        if ($("#pengajar").val() == '') {
            alert("Nama pengajar masih kosong")
            return false;
        }
        var datax = {};
        datax['name'] = $("#pengajar").val();
        $.post(`{{ url("/dewan-pengajar/store") }}`, datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {
                    var body_pengajar = $("#list-pengajar").html();
                    var add_pengajar = '<tr class="text-sm" id="p' + return_data.id + '">' +
                        '<td><input tytpe="text" class="form-control" disabled value="' + $("#pengajar").val() + '" id="data-name' + return_data.id + '"></td>' +
                        '<td class="align-middle text-center text-sm">' +
                        '<a href="#" onclick="return ubahPengajar(' + return_data.id + ')" class="btn btn-primary btn-xs mb-0">Ubah</a>' +
                        '<a href="#" onclick="return deletePengajar(' + return_data.id + ')" class="btn btn-danger btn-xs mb-0">Hapus</a>' +
                        '</td></tr>';
                    body_pengajar = add_pengajar + body_pengajar;
                    $("#list-pengajar").html(body_pengajar);
                }
                return false
            }
        );
    }

    async function ubahPengajar(id) {
        const element = document.getElementById("data-name" + id);
        if (element.disabled) {
            element.disabled = false;
        } else {
            var datax = {};
            datax['name'] = $("#data-name" + id).val();
            $.post(`{{ url("/") }}/dewan-pengajar/update/` + id, datax,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        element.disabled = true;
                    } else {
                        alert(return_data.message)
                    }
                    return false
                }
            );
        }
    }

    async function deletePengajar(id) {
        if (confirm('Apakah yakin data ini akan dihapus?')) {
            $.get(`{{ url("/") }}/dewan-pengajar/delete/` + id, null,
                function(data, status) {
                    var return_data = JSON.parse(data);
                    if (return_data.status) {
                        const element = document.getElementById("p" + id);
                        element.remove();
                    }
                    return false
                }
            );
        } else {

        }
    }
</script>
@include('base.end')
<div class="card mb-2">
    <div class="card-body pb-0">
        <div class="card-title font-weight">
            <div class="row">
                <div class="col-md-4 mb-2 p-1">
                    <select data-mdb-filter="true" class="select form-control" value="" id="fkSantri_id" name="fkSantri_id" required>
                        <option value="">--Pilih Mahasiswa--</option>
                        @foreach($santris as $s)
                        <option value="{{$s->santri_id}}">{{$s->fullname}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-2">
                    <dv class="row">
                        <div class="col-md-6 p-0">
                            <a href="#" class="btn btn-primary mb-2 btn-block" onclick="simpanPanitia()">
                                <i class="fas fa-save" aria-hidden="true"></i>
                                TAMBAHKAN
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead style="background-color:#f6f9fc;">
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">PANITIA</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $d)
                        <tr class="text-sm">
                            <td>
                                {{ $d->santri->user->fullname }}
                            </td>
                            <td>
                                <a href="{{ route('delete panitia pmb', [$d->id])}}" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Yakin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }

    function simpanPanitia() {
        var datax = {};
        datax['fkSantri_id'] = $("#fkSantri_id").val();
        $("#loadingSubmit").show();
        $.post("{{ route('store panitia') }}", datax,
            function(data, status) {
                window.location.reload();
            }
        )
    }
</script>
<div class="accordion" id="accordionExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button
            data-mdb-collapse-init
            class="accordion-button"
            type="button"
            data-mdb-target="#collapseOne"
            aria-expanded="true"
            aria-controls="collapseOne"
            >
            Konfigurasi PMB
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-mdb-parent="#accordionExample">
            <div class="accordion-body">
                <form action="{{ route('store konfigurasi pmb') }}" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-2 p-1">
                            <label>Tahun</label>
                            <input class="form-control" type="text" value="{{date('Y')}}" id="tahun_pmb" name="tahun_pmb" required>
                        </div>

                        <div class="col-md-3 mb-2 p-1">
                            <label>Gelombang 1</label>
                            <input class="form-control" type="date" value="" id="gelombang1" name="gelombang1" required>
                        </div>

                        <div class="col-md-3 mb-2 p-1">
                            <label>Gelombang 2</label>
                            <input class="form-control" type="date" value="" id="gelombang2" name="gelombang2" required>
                        </div>

                        <div class="col-md-4 mb-2 p-3">
                            <!-- <label>Informasi PMB</label>
                            <textarea rows="10" class="form-control" id="informasi_pmb" name="informasi_pmb"></textarea> -->
                            
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <input class="btn btn-primary btn-sm btn-block mb-2" type="submit" value="SIMPAN">
                                </div>
                                <div class="col-md-6">
                                    <a href="#" class="btn btn-warning btn-sm mb-2 btn-block" id="batal" onclick="batalPmb(this)" >
                                        <i class="fas fa-save" aria-hidden="true"></i>
                                        BATAL
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.datatable table td, .datatable table th {
    white-space: pre-line;
}
</style>
<div class="card shadow border mb-2 mt-2">
    <div class="card-body p-2">
        <div class="datatable datatable-sm">
            <table id="table-report" class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-sm text-secondary">TAHUN</th>
                        <th class="text-uppercase text-sm text-secondary">GELOMBANG 1</th>
                        <th class="text-uppercase text-sm text-secondary">GELOMBANG 2</th>
                        <!-- <th class="text-uppercase text-sm text-secondary">INFORMASI</th> -->
                        <th class="text-uppercase text-sm text-secondary"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datax as $data)
                        <tr class="text-sm">
                            <td>
                                {{ $data->tahun_pmb }}
                            </td>
                            <td>
                                {{ $data->gelombang1 }}
                            </td>
                            <td>
                                {{ $data->gelombang2 }}
                            </td>
                            <!-- <td>
                                {{ $data->informasi_pmb }}
                            </td> -->
                            <td class="align-middle text-center text-sm">
                                <a href="#" class="btn btn-secondary btn-sm mb-0" onclick="ubahPmb({{$data}})">Ubah</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function ubahPmb(data){
        $("#tahun_pmb").val(data.tahun_pmb);
        $("#gelombang1").val(data.gelombang1);
        $("#gelombang2").val(data.gelombang2);
        // $("#informasi_pmb").val(data.informasi_pmb);
    }
    function batalPmb(ata){
        $("#tahun_pmb").val("");
        $("#gelombang1").val("");
        $("#gelombang2").val("");
        // $("#informasi_pmb").val(data.informasi_pmb);
    }
</script>
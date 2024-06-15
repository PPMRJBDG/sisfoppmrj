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
                                    Nama Divisi
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
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="table" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama Pengajar</th>
                                @foreach($presence as $prs)
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$prs->name}}</th>
                                @endforeach
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Total KBM</th>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">% Kehadiran Mhs</th>
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
                                <?php
                                $total_kbm = 0;
                                ?>
                                @foreach($presence as $prs)
                                <?php
                                $sum_kbm = App\Helpers\CountDashboard::sumPresentByPengajar('sum_kbm', $prs->id, $data->id);
                                $total_kbm += $sum_kbm;
                                ?>
                                <td>
                                    {{ $sum_kbm }}
                                </td>
                                @endforeach
                                <td>
                                    {{$total_kbm}}
                                </td>
                                <td>
                                    {{App\Helpers\CountDashboard::sumPresentByPengajar('persentase',null, $data->id)}}%
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

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
</script>
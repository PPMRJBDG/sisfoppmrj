@include('base.start', ['path' => 'dewan-pengajar/jadwal', 'title' => 'Jadwal Pengajar', 'breadcrumbs' => ['Jadwal Pengajar']])

<div class="row">
    <div class="col-md-12">
        @for($x=1; $x<=2; $x++) <div class="card shadow-lg mb-2">
            <div class="card-body">
                <div class="card-header p-2">
                    <h5>PPM {{$x}}</h5>
                </div>
                @if(isset($presence_group))
                @foreach($presence_group as $data)
                <div class="table-responsive">
                    <?php
                    $explode_day = explode(',', $data->days);
                    ?>
                    <table class="table align-items-center mb-2" style="background:#f8f9fa;">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">Nama KBM</th>
                                @foreach($explode_day as $ed)
                                <th class="text-uppercase text-sm text-secondary font-weight-bolder ps-2">{{$ed}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="presence-group">
                            <tr class="text-sm" id="p{{$data->id}}">
                                <td>
                                    <input tytpe="text" class="form-control btn-primary text-white text-bold" disabled value="{{ $data->name }}" id="data-name{{$data->id}}">
                                </td>
                                @foreach($explode_day as $ed)
                                <td class="align-middle text-center text-sm">
                                    <select name="dewan_pengajar" id="dewan_pengajar{{$data->id}}{{trim($ed)}}{{$x}}" class="form-control" onchange="return savePengajar(this, '{{$data->id}}','{{trim($ed)}}', '{{$x}}')">
                                        <option value="">-</option>
                                        @foreach($pengajar as $dp)
                                        <?php
                                        $jadwal = App\Models\JadwalPengajars::where('fkPresence_group_id', $data->id)->where('fkDewan_pengajar_id', $dp->id)->where('day', trim($ed))->where('ppm', $x)->first();
                                        $jadwal_id = 0;
                                        if ($jadwal != null) {
                                            $jadwal_id = $jadwal->fkDewan_pengajar_id;
                                        }
                                        ?>
                                        <option {{($jadwal_id==$dp->id) ? 'selected' : '' }} value="{{$dp->id}}">{{$dp->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endforeach
                @endif
            </div>
    </div>
    @endfor
</div>
</div>
<script>
    async function savePengajar(pengajar, presence, day, ppm) {
        console.log(pengajar.value + ' - ' + presence + ' - ' + day + ' - ' + ppm)
        var datax = {};
        datax['pengajar'] = pengajar.value;
        datax['presence'] = presence;
        datax['day'] = day;
        datax['ppm'] = ppm;

        $.post(`{{ url("/dewan-pengajar/jadwal/store") }}`, datax,
            function(data, status) {
                var return_data = JSON.parse(data);
                if (return_data.status) {

                }
                return false
            }
        );
    }
</script>
@include('base.end')
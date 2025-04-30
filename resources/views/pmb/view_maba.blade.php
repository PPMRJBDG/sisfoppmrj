<div class="card border mb-2">
    <div class="card-body p-0">
        <div class="row p-3">
            <div class="col-md-6">
                <h6 class="align-items-center"><b>Daftar Calon Mahasiswa Baru</b></h6>
            </div>
            <div class="col-md-6">
                <select data-mdb-filter="true" class="select select_angkatan form-control bg-white" name="select_angkatan" id="select_angkatan">
                    @foreach($list_angkatan as $la)
                    <option {{ ($select_angkatan == $la->angkatan) ? 'selected' : '' }} value="{{$la->angkatan}}">Angkatan {{$la->angkatan}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a data-mdb-ripple-init onclick="return false;" class="nav-link active font-weight-bolder" id="nav-profile-tab" data-bs-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="true">PROFIL</a>
                <a data-mdb-ripple-init onclick="return false;" class="nav-link font-weight-bolder" id="nav-nilai-tab" data-bs-toggle="tab" href="#nav-nilai" role="tab" aria-controls="nav-nilai">NILAI</a>
            </div>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="p-2">
                        <div class="datatable" data-mdb-sm="true" data-mdb-entries="50" data-mdb-pagination="false" data-mdb-hover="true" data-mdb-bordered="true">
                            <table id="table-profile" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-sm text-secondary"></th>
                                        <th class="text-uppercase text-sm text-secondary">NAMA</th>
                                        <th class="text-uppercase text-sm text-secondary">KELAMIN</th>
                                        <th class="text-uppercase text-sm text-secondary">NOMOR WA</th>
                                        <th class="text-uppercase text-sm text-secondary">ORTU/WALI</th>
                                        <th class="text-uppercase text-sm text-secondary">SELEKSI</th>
                                        <th class="text-uppercase text-sm text-secondary">LURING</th>
                                        <th class="text-uppercase text-sm text-secondary">DARING</th>
                                        <th class="text-uppercase text-sm text-secondary">MENTOR 1</th>
                                        <th class="text-uppercase text-sm text-secondary">MENTOR 2</th>
                                        <th class="text-uppercase text-sm text-secondary">MENTOR 3</th>
                                        <th class="text-uppercase text-sm text-secondary">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($camabas as $camaba)
                                        <tr class="text-sm">
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary" onclick="detilMaba({{$camaba}})">DETIL</a>
                                            </td>
                                            <td>
                                                {{ strtoupper($camaba->fullname) }}
                                            </td>
                                            <td>
                                                {{ strtoupper($camaba->gender) }}
                                            </td>
                                            <td>
                                                {{ $camaba->nomor_wa }}
                                            </td>
                                            <td>
                                                {{ ($camaba->nama_ayah!="") ? $camaba->nama_ayah : $camaba->nama_wali }}
                                            </td>
                                            <td>
                                                {{ date_format(date_create($camaba->tanggal_seleksi), 'd M Y') }}
                                            </td>
                                            <td>
                                                {{ $camaba->seleksi_luring }}
                                            </td>
                                            <td>
                                                {{ $camaba->alasan_seleksi_daring }}
                                            </td>
                                            <td>
                                                <select style="width:200px;font-size: 13px !important;" class="form-control" id="mentor1-maba{{$camaba->id}}" name="mentor1" onchange="return changeMentor(1,{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->mentor1) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:200px;font-size: 13px !important;" class="form-control" id="mentor2-maba{{$camaba->id}}" name="mentor2" onchange="return changeMentor(2,{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->mentor2) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:200px;font-size: 13px !important;" class="form-control" id="mentor3-maba{{$camaba->id}}" name="mentor3" onchange="return changeMentor(3,{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->mentor3) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                $bg_status = 'transparent';
                                                if($camaba->status=='tes'){
                                                    $bg_status = '#b5bbfb';
                                                }elseif($camaba->status=='diterima'){
                                                    $bg_status = '#b5fbb6';
                                                }elseif($camaba->status=='ditolak'){
                                                    $bg_status = '#fbb5b5';
                                                }
                                                ?>
                                                <select style="background:{{$bg_status}};width:200px;" class="form-control" id="status-maba{{$camaba->id}}" name="status" onchange="return changeStatus({{$camaba->id}},this.value)">
                                                    <option {{ ($camaba->status=='pending') ? 'selected' : '' }} value="pending">PENDING</option>
                                                    <option {{ ($camaba->status=='interview') ? 'selected' : '' }} value="interview">INTERVIEW</option>
                                                    <option {{ ($camaba->status=='tes') ? 'selected' : '' }} value="tes">TES</option>
                                                    <option {{ ($camaba->status=='diterima') ? 'selected' : '' }} value="diterima">DITERIMA</option>
                                                    <option {{ ($camaba->status=='ditolak') ? 'selected' : '' }} value="ditolak">DITOLAK</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="nav-nilai" role="tabpanel" aria-labelledby="nav-nilai-tab">
                    <div class="p-2">
                        <style>
                            .datatable.datatable-sm td, .datatable table td{
                                line-height:0.2;
                            }
                        </style>
                        <div class="datatable" data-mdb-sm="true" data-mdb-entries="50" data-mdb-pagination="false" data-mdb-hover="true" data-mdb-bordered="true">
                            <table id="table-bacaan" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-sm text-secondary">NAMA</th>
                                        <th class="text-uppercase text-sm text-secondary">NILAI BACAAN</th>
                                        <th class="text-uppercase text-sm text-secondary">NILAI PENY. QURAN</th>
                                        <th class="text-uppercase text-sm text-secondary">NILAI ADZAN</th>
                                        <th class="text-uppercase text-sm text-secondary">NILAI PEGON</th>
                                        <th class="text-uppercase text-sm text-secondary">WAWANCARA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($camabas as $camaba)
                                        <tr class="text-sm">
                                            <td>
                                                <div style="line-height:1.6;">
                                                    {{ strtoupper($camaba->fullname) }} <i id="spin{{$camaba->id}}" class="fa fa-spinner fa-spin" style="display:none;"></i>
                                                    <br>
                                                    <?php
                                                    $bg_status = 'transparent';
                                                    if($camaba->status=='tes'){
                                                        $bg_status = '#b5bbfb';
                                                    }elseif($camaba->status=='diterima'){
                                                        $bg_status = '#b5fbb6';
                                                    }elseif($camaba->status=='ditolak'){
                                                        $bg_status = '#fbb5b5';
                                                    }
                                                    ?>
                                                    <select style="width:320px;background:{{$bg_status}};" class="form-control" id="status-nilai-maba{{$camaba->id}}" name="status" onchange="return changeStatus({{$camaba->id}},this.value)">
                                                        <option {{ ($camaba->status=='pending') ? 'selected' : '' }} value="pending">PENDING</option>
                                                        <option {{ ($camaba->status=='interview') ? 'selected' : '' }} value="interview">INTERVIEW</option>
                                                        <option {{ ($camaba->status=='tes') ? 'selected' : '' }} value="tes">TES</option>
                                                        <option {{ ($camaba->status=='diterima') ? 'selected' : '' }} value="diterima">DITERIMA</option>
                                                        <option {{ ($camaba->status=='ditolak') ? 'selected' : '' }} value="ditolak">DITOLAK</option>
                                                    </select>
                                                </div>
                                            </td>

                                            <td>
                                                <select style="width:320px;" class="form-control" id="nilai-bacaan-maba{{$camaba->id}}" onchange="return changeNilai('bacaan',{{$camaba->id}},this.value)">
                                                    <option {{($camaba->nilai_bacaan=='-') ? 'selected' : ''}} value="-">-</option>
                                                    <option {{($camaba->nilai_bacaan=='kurang') ? 'selected' : ''}} value="kurang">Kurang</option>
                                                    <option {{($camaba->nilai_bacaan=='cukup') ? 'selected' : ''}} value="cukup">Cukup</option>
                                                    <option {{($camaba->nilai_bacaan=='baik') ? 'selected' : ''}} value="baik">Baik</option>
                                                    <option {{($camaba->nilai_bacaan=='sangatbaik') ? 'selected' : ''}} value="sangatbaik">Sangat Baik</option>
                                                </select>
                                                <br>
                                                <input style="width:320px;" placeholder="Keterangan" class="form-control" type="text" value="{{$camaba->nilai_bacaan_ket}}" id="nilai-bacaan-ket{{$camaba->id}}" onkeyup="return changeNilai('bacaan_ket',{{$camaba->id}},this.value)">
                                                <br>
                                                <select style="width:320px;" class="form-control" id="nilai-bacaan-mentor{{$camaba->id}}" name="nilai-bacaan-mentor" onchange="return changeNilai('bacaan_mentor',{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->nilai_bacaan_mentor) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select style="width:320px;" class="form-control" id="nilai-quran-maba{{$camaba->id}}" onchange="return changeNilai('penyampaian_quran',{{$camaba->id}},this.value)">
                                                    <option {{($camaba->nilai_penyampaian_quran=='-') ? 'selected' : ''}} value="-">-</option>
                                                    <option {{($camaba->nilai_penyampaian_quran=='kurang') ? 'selected' : ''}} value="kurang">Kurang</option>
                                                    <option {{($camaba->nilai_penyampaian_quran=='cukup') ? 'selected' : ''}} value="cukup">Cukup</option>
                                                    <option {{($camaba->nilai_penyampaian_quran=='baik') ? 'selected' : ''}} value="baik">Baik</option>
                                                    <option {{($camaba->nilai_penyampaian_quran=='sangatbaik') ? 'selected' : ''}} value="sangatbaik">Sangat Baik</option>
                                                </select>
                                                <br>
                                                <input style="width:320px;" placeholder="Keterangan" class="form-control" type="text" value="{{$camaba->nilai_penyampaian_quran_ket}}" id="nilai-quran-ket{{$camaba->id}}" onkeyup="return changeNilai('penyampaian_quran_ket',{{$camaba->id}},this.value)">
                                                <br>
                                                <select style="width:320px;" class="form-control" id="nilai-quran-mentor{{$camaba->id}}" name="nilai-quran-mentor" onchange="return changeNilai('penyampaian_quran_mentor',{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->nilai_penyampaian_quran_mentor) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select style="width:320px;" class="form-control" id="nilai-adzan-maba{{$camaba->id}}" onchange="return changeNilai('adzan',{{$camaba->id}},this.value)">
                                                    <option {{($camaba->nilai_adzan=='-') ? 'selected' : ''}} value="-">-</option>
                                                    <option {{($camaba->nilai_adzan=='kurang') ? 'selected' : ''}} value="kurang">Kurang</option>
                                                    <option {{($camaba->nilai_adzan=='cukup') ? 'selected' : ''}} value="cukup">Cukup</option>
                                                    <option {{($camaba->nilai_adzan=='baik') ? 'selected' : ''}} value="baik">Baik</option>
                                                    <option {{($camaba->nilai_adzan=='sangatbaik') ? 'selected' : ''}} value="sangatbaik">Sangat Baik</option>
                                                </select>
                                                <br>
                                                <input style="width:320px;" placeholder="Keterangan" class="form-control" type="text" value="{{$camaba->nilai_adzan_ket}}" id="nilai-adzan-ket{{$camaba->id}}" onkeyup="return changeNilai('adzan_ket',{{$camaba->id}},this.value)">
                                                <br>
                                                <select style="width:320px;" class="form-control" id="nilai-adzan-mentor{{$camaba->id}}" name="nilai-adzan-mentor" onchange="return changeNilai('adzan_mentor',{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->nilai_adzan_mentor) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select style="width:320px;" class="form-control" id="nilai-pegon-maba{{$camaba->id}}" onchange="return changeNilai('pegon',{{$camaba->id}},this.value)">
                                                    <option {{($camaba->nilai_bacaan=='-') ? 'selected' : ''}} value="-">-</option>
                                                    <option {{($camaba->nilai_pegon=='kurang') ? 'selected' : ''}} value="kurang">Kurang</option>
                                                    <option {{($camaba->nilai_pegon=='cukup') ? 'selected' : ''}} value="cukup">Cukup</option>
                                                    <option {{($camaba->nilai_pegon=='baik') ? 'selected' : ''}} value="baik">Baik</option>
                                                    <option {{($camaba->nilai_pegon=='sangatbaik') ? 'selected' : ''}} value="sangatbaik">Sangat Baik</option>
                                                </select>
                                                <br>
                                                <input style="width:320px;" placeholder="Keterangan" class="form-control" type="text" value="{{$camaba->nilai_pegon_ket}}" id="nilai-pegon-ket{{$camaba->id}}" onkeyup="return changeNilai('pegon_ket',{{$camaba->id}},this.value)">
                                                <br>
                                                <select style="width:320px;" class="form-control" id="nilai-pegon-mentor{{$camaba->id}}" name="nilai-pegon-mentor" onchange="return changeNilai('pegon_mentor',{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->nilai_pegon_mentor) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select style="width:320px;" class="form-control" id="nilai-wawancara-maba{{$camaba->id}}" onchange="return changeNilai('wawancara',{{$camaba->id}},this.value)">
                                                    <option {{($camaba->nilai_bacaan=='-') ? 'selected' : ''}} value="-">-</option>
                                                    <option {{($camaba->nilai_wawancara=='1') ? 'selected' : ''}} value="1">Ortu OK - Anak OK</option>
                                                    <option {{($camaba->nilai_wawancara=='2') ? 'selected' : ''}} value="2">Ortu OK - Anak NOK</option>
                                                    <option {{($camaba->nilai_wawancara=='3') ? 'selected' : ''}} value="3">Ortu NOK - Anak OK</option>
                                                    <option {{($camaba->nilai_wawancara=='4') ? 'selected' : ''}} value="4">Ortu NOK - Anak NOK</option>
                                                </select>
                                                <br>
                                                <input style="width:320px;" placeholder="Keterangan" class="form-control" type="text" value="{{$camaba->nilai_wawancara_ket}}" id="nilai-wawancara-ket{{$camaba->id}}" onkeyup="return changeNilai('wawancara_ket',{{$camaba->id}},this.value)">
                                                <br>
                                                <select style="width:320px;" class="form-control" id="nilai-wawancara-mentor{{$camaba->id}}" name="nilai-wawancara-mentor" onchange="return changeNilai('wawancara_mentor',{{$camaba->id}},this.value)">
                                                    <option value="">-- pilih mentor --</option>
                                                    @foreach($panitias as $panitia)
                                                        <option {{ ($panitia->santri->user->fullname==$camaba->nilai_wawancara_mentor) ? 'selected' : '' }} value="{{$panitia->santri->user->fullname}}">{{$panitia->santri->user->fullname}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>

<script>
    try {
        $(document).ready();
    } catch (e) {
        window.location.replace(`{{ url("/") }}`)
    }
</script>
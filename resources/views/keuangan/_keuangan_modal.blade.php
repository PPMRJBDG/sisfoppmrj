<?php
$bulan = ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
?>
<div class="modal" data-mdb-toggle="animation" data-mdb-animation-start="onLoad" data-mdb-animation="fade-in-left" id="modalSodaqoh" tabindex="-1" role="dialog" aria-labelledby="modalSodaqohLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title" id="modalSodaqohLabel">Input Sodaqoh</h6>
                    <h5 class="modal-title" id="modalSodaqohLabel"><span id="nm"></span></h5>
                </div>
            </div>
            <div class="modal-body pb-0">
                <div class="p-2" style="background:#f9f9ff;border:1px #ddd solid;">
                    <input class="form-control" readonly type="hidden" id="sodaqoh_id" name="sodaqoh_id" value="">
                    <input class="form-control" readonly type="hidden" id="periode" name="periode" value="">
                    <input class="form-control" readonly type="hidden" id="santri_id" name="santri_id" value="">

                    <label class="form-control-label">Default Sodaqoh / Tahun</label>
                    <input class="form-control" <?php if (!auth()->user()->hasRole('superadmin')) {
                                                    echo 'disabled';
                                                } ?> type="number" id="nominal" name="nominal" value="">
                    <hr>

                    <div class="form-group">
                        <label class="custom-control-label m-0">Periode Bulan</label>
                        <select class="form-control" name="periode_bulan" id="periode_bulan">
                            <?php foreach ($bulan as $bl) { ?>
                                <option value="{{$bl}}">{{ucfirst($bl)}}</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-outline form-group">
                        <input class="form-control" type="date" id="date" name="date">
                        <label class="form-label m-0"></label>
                    </div>

                    <div class="form-outline form-group">
                        <input class="form-control" type="number" id="nominal_bayar" name="nominal_bayar" placeholder="0">
                        <label class="form-label m-0">Nominal</label>
                    </div>

                    <div class="form-outline form-group">
                        <input class="form-control" type="text" id="ket" name="ket" value="">
                        <label class="form-label">Keterangan</label>
                    </div>

                    <br>
                    <label class="form-control-label">*Jika terdapat pembayaran lebih dari 1x dalam 1 bulan, harap yang diinput adalah total dari jumlah pembayaran tersebut</label>
                    <label class="form-control-label">**Jika terdapat perubahan, dapat memilih bulan sesuai yang akan diubah</label>
                </div>

                <table class="table align-items-center mb-0">
                    <tbody>
                        <tr>
                            <td class="text-sm font-weight-bolder ps-0" colspan="2" style="border:none!important;">Riwayat Pembayaran</td>
                        </tr>
                        <?php foreach ($bulan as $bl) {
                        ?>
                            <tr class="text-sm m-1" id="idx{{$bl}}">
                                <td class="p-0" style="border-bottom-width:0!important;width:20%;">
                                    <input class="form-control" disabled type="text" value="{{$bl}}">
                                </td>
                                <td class="p-0" style="border-bottom-width:0!important;width:40%;">
                                    <input class="form-control" disabled type="date" id="{{$bl}}_date" name="{{$bl}}_date">
                                </td>
                                <td class="p-0" style="border-bottom-width:0!important;">
                                    <input class="form-control" disabled type="text" id="{{$bl}}" name="{{$bl}}" placeholder="0">
                                </td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td class="text-sm font-weight-bolder ps-0 text-warning" colspan="2" style="border:none!important;">Kekurangan: <span id="kekurangan"></span></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="form-group form-check mb-0">
                    <label class="custom-control-label">Info via WA</label>
                    <input class="form-check-input" type="checkbox" id="info-wa" name="info-wa">
                </div>
            </div>
            <div class="card-header p-2" id="info-update-sodaqoh" style="display:none;border-radius:4px;">
                <h6 id="bg-warning" class="mb-0 bg-warning p-1 text-white" style="display:none;">
                    <span id="info-warning"></span>
                </h6>
                <h6 id="bg-success" class="mb-0 bg-success p-1 text-white" style="display:none;">
                    <span id="info-success"></span>
                </h6>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="save" class="btn btn-primary mb-0" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modalPeriodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalPeriodeLabel">Set Periode</h6>
            </div>
            <div class="modal-body p-0">
                <!-- <div class="tab">
                    @for($i=1; $i<=12; $i++) <button class="tablinks {{($i==1) ? 'active' : ''}}" onclick="openTab(event, 'bln_{{$i}}')">{{$i}}</button>@endfor
                </div> -->
                <?php
                for ($i = 1; $i <= 12; $i++) {
                ?>
                    <div class="card shadow border tabcontent" id="bln_{{$i}}" style="{{($i==1) ? 'display:block;' : ''}}">
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table id="table" class="table align-items-center mb-4">
                                    <thead class="text-center">
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <th colspan="5" style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">Bulan {{$i}}</th>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                    <th style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">{{$x}}</th>
                                                <?php } ?>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                <?php for ($x = 1; $x <= 5; $x++) { ?>
                                                    <td style="{{$i%2==0 ? 'background:#e9ecef;' : ''}}">
                                                        <div class="form-group form-check mb-0" style="margin-left:10px!important;">
                                                            <input class="form-check-input" type="checkbox" id="bln-{{$i}}-mg-{{$x}}" name="bln-{{$i}}-mg-{{$x}}">
                                                        </div>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="close_1" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
                <button type="button" id="close_2" class="btn btn-secondary mb-0" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>
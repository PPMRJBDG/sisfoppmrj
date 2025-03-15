<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\WaSchedules;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;
use App\Helpers\PresenceGroupsChecker;
use App\Models\Presence;
use App\Models\Present;
use App\Models\Periode;
use App\Models\Permit;
use App\Models\User;
use App\Models\Settings;
use App\Models\Liburan;
use App\Models\PresenceGroup;
use App\Models\Pelanggaran;
use App\Models\Sodaqoh;
use App\Models\Materi;
use App\Models\Santri;
use App\Models\Lorong;
use App\Models\ReportScheduler;
use App\Models\SpWhatsappPhoneNumbers;
use App\Models\SpWhatsappContacts;
use App\Models\ReminderTatatertib;
use App\Models\CatatanPenghubungs;
use App\Models\JagaMalams;

use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function schedule($time, $presence_id = null)
    {
        $setting = Settings::find(1);
        $contact_id = $setting->wa_ortu_group_id;
        $name = '';
        $caption = '';
        $yesterday = strtotime('-1 day', strtotime(date("Y-m-d")));
        $yesterday = date('Y-m-d', $yesterday);

        // PREVIEW + DAILY
        if ($time == 'preview-daily') {
            if(!$setting->cron_preview_daily){
                echo json_encode(['status' => false, 'message' => '[preview-daily] scheduler off']);
                exit;
            }

            $contact_id = $setting->wa_info_presensi_group_id;
            $time_post = 1;
            $check_liburan = Liburan::where('liburan_from', '<', $yesterday)->where('liburan_to', '>', $yesterday)->get();
            if (count($check_liburan) == 0) {
                $caption = '*[Preview]*
*Amshol Cek Daftar Kehadiran Kemarin:*';
                $get_presence = Presence::where('event_date', $yesterday)->where('is_deleted', 0)->get();
                if (count($get_presence) > 0) {
                    foreach ($get_presence as $presence) {
                        // hadir
                        $presents = CountDashboard::mhs_hadir($presence->id, 'all');

                        // ijin berdasarkan lorong masing2
                        $permits = CountDashboard::mhs_ijin($presence->id, 'all');

                        // alpha
                        $mhs_alpha = CountDashboard::mhs_alpha($presence->id, 'all', $presence->event_date);

                        $caption = $caption . '
________________________
*_' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M") . ' | ' . $presence->name . '_*
Hadir: ' . count($presents) . ' | Ijin: ' . count($permits) . ' | Alpha: ' . count($mhs_alpha) . '
Link: ' . $setting->host_url . '/presensi/list/' . $presence->id . '

';
                        if (count($mhs_alpha) > 0) {
                            $caption = $caption . '*Daftar Mahasiswa Alpha*
';
                            foreach ($mhs_alpha as $d) {
                                $caption = $caption . '- ' . $d['name'] . ' [' . $d['angkatan'] . ']
';
                            }
                        }

                        if ($presence->fkDewan_pengajar_1 == '' || $presence->fkDewan_pengajar_2 == '') {
                            $infodp_xxz = $setting->org_name . ' 1 dan 2';
                            if ($presence->fkDewan_pengajar_1 != '' && $presence->fkDewan_pengajar_2 == '') {
                                $infodp_xxz = $setting->org_name . ' 2';
                            } elseif ($presence->fkDewan_pengajar_1 == '' && $presence->fkDewan_pengajar_2 != '') {
                                $infodp_xxz = $setting->org_name . ' 1';
                            }
                            $infodp = 'Dewan Pengajar ' . $infodp_xxz . ' pada '.$presence->name.' belum disesuaikan';
                            WaSchedules::save('Check Dewan Pengajar', $infodp, $contact_id, $time_post, true);
                            $time_post++;
                        }
                    }
                }

                $name = '[Preview] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                if ($contact_id != '' && count($get_presence) > 0) {
                    $insert = WaSchedules::save($name, $caption, $contact_id, $time_post, true);
                    if ($insert) {
                        echo json_encode(['status' => true, 'message' => 'success insert scheduler']);
                    } else {
                        echo json_encode(['status' => false, 'message' => 'failed insert scheduler']);
                    }
                } else {
                    echo json_encode(['status' => false, 'message' => 'group id not found']);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'holiday']);
            }
        } else if ($time == 'daily') {
            if(!$setting->cron_daily){
                echo json_encode(['status' => false, 'message' => '[daily] scheduler off']);
                exit;
            }
            
            $time_post = 1;
            // update pemutihan
            $get_pelanggaran = Pelanggaran::where('is_archive', 0)->get();
            foreach ($get_pelanggaran as $gp) {
                $today = date("Y-m-d");
                $first_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($gp->is_surat_peringatan)) . " + 1 year"));
                if ($first_date == $today) {
                    $set_archive = Pelanggaran::find($gp->id);
                    $set_archive->is_archive = 1;
                    if ($set_archive->save()) {
                        $caption = 'Pemutihan SP ' . $gp->keringanan_sp . ' an. ' . $gp->santri->user->fullname;
                        WaSchedules::save($caption, $caption, 'wa_info_presensi_group_id', $time_post, true);
                        $time_post++;
                    }
                }
                // khusus KBM status masih dipantau
                if ($gp->fkJenis_pelanggaran_id == 14) {
                    if ($gp->periode_tahun != CommonHelpers::periode() && $gp->is_surat_peringatan == '') {
                        $set_archive = Pelanggaran::find($gp->id);
                        $set_archive->is_archive = 1;
                        $set_archive->save();
                    }
                }
            }

            // bulk presensi harian ke wa group ortu
            $check_liburan = Liburan::where('liburan_from', '<', $yesterday)->where('liburan_to', '>', $yesterday)->get();
            if (count($check_liburan) == 0) {
                $caption = 'Berikut kami informasikan daftar kehadiran pada hari *' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M Y") . '*.

*Total Mahasiswa: ' . CountDashboard::total_mhs('all') . '*';
                $get_presence = Presence::where('event_date', $yesterday)->where('is_deleted', 0)->get();
                if (count($get_presence) > 0) {
                    foreach ($get_presence as $presence) {
                        // hadir
                        $presents = CountDashboard::mhs_hadir($presence->id, 'all');

                        // ijin berdasarkan lorong masing2
                        $permits = CountDashboard::mhs_ijin($presence->id, 'all');

                        // alpha
                        $mhs_alpha = CountDashboard::mhs_alpha($presence->id, 'all', $presence->event_date);

                        $caption = $caption . '
________________________
*_' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M") . ' | ' . $presence->name . '_*
Hadir: ' . count($presents) . ' | Ijin: ' . count($permits) . ' | Alpha: ' . count($mhs_alpha) . '

';
//                         if (count($mhs_alpha) > 0) {
//                             $caption = $caption . '*Daftar Mahasiswa Alpha*
// ';
//                             foreach ($mhs_alpha as $d) {
//                                 $caption = $caption . '- ' . $d['name'] . ' [' . $d['angkatan'] . ']
// ';
//                             }
//                         }
                    }
                }
                $caption = $caption . '
NB:
- Jika ada keperluan atau berhalangan hadir, dapat melakukan input ijin melalui Sisfo
- Jika dalam 1 bulan kehadiran < 80%, akan ada pemanggilan dan kafaroh
- Apabila terdapat ketidaksesuaian, amalsholih menghubungi Pengurus atau Koor Lorong';

                $name = '[Ortu Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                if ($contact_id != '' && count($get_presence) > 0) {
                    $insert = WaSchedules::save($name, $caption, $contact_id);

                    $contact_id = SpWhatsappContacts::where('name', 'Group PPM RJ Maurus')->first();
                    if ($contact_id != null) {
                        $name = '[Maurus Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                        $insert = WaSchedules::save($name, $caption, $contact_id->id, 2);
                    }

                    if ($insert) {
                        echo json_encode(['status' => true, 'message' => 'success insert scheduler']);
                    } else {
                        echo json_encode(['status' => false, 'message' => 'failed insert scheduler']);
                    }
                } else {
                    echo json_encode(['status' => false, 'message' => 'group id not found']);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'holiday']);
            }
        }

        // WEEKLY
        elseif ($time == 'weekly') {
            if(!$setting->cron_weekly){
                echo json_encode(['status' => false, 'message' => '[weekly] scheduler off']);
                exit;
            }

            $lm = strtotime(date("Y-m-d"));
            $last_month = date('Y-m', $lm);

            // $periode_tahun = Periode::latest('periode_tahun')->first();
            $list_angkatan = DB::table('santris')
                ->select('angkatan')
                ->whereNull('exit_at')
                ->groupBy('angkatan')
                ->get();
            $data_presensi_weekly = '*[Laporan Mingguan] Presensi KBM kurang dari 80%: ' . date("M Y") . '*
';
            $data_mhs = array();
            foreach ($list_angkatan as $la) {
                $result = (new HomeController)->dashboard($last_month, $la->angkatan, '-', true);
                $view_usantri = $result['view_usantri'];
                $presence_group = $result['presence_group'];
                $presences = $result['presences'];
                $all_presences = $result['all_presences'];
                $all_permit = $result['all_permit'];

                foreach ($view_usantri as $vu) {
                    $all_persentase = 0;
                    $all_kbm = 0;
                    $all_hadir = 0;
                    $all_ijin = 0;
                    foreach ($presence_group as $pg) {
                        foreach ($presences[$vu->santri_id][$pg->id] as $listcp) {
                            $ijin = 0;
                            if (isset($all_permit[$pg->id][$vu->santri_id])) {
                                $ijin = $all_permit[$pg->id][$vu->santri_id];
                            }
                            $all_kbm = $all_kbm + $all_presences[$vu->santri_id][$pg->id][0]->c_all;
                            $all_hadir = $all_hadir + $listcp->cp;
                            $all_ijin = $all_ijin + $ijin;
                        }
                    }

                    if ($all_kbm > 0) {
                        $all_persentase = ($all_hadir + $all_ijin) / $all_kbm * 100;
                        // jika kbm < 80%, then auto create pelanggaran and send wa to ketertiban
                        if ($all_persentase < 80) {
                            $all_persentase = number_format($all_persentase, 2);
                            $data_mhs[$vu->santri_id]['nohp'] = $vu->nohp_ortu;
                            $data_mhs[$vu->santri_id]['caption'] = '*[Laporan Mingguan] Presensi KBM ' . $vu->fullname . ' pada bulan ' . date("M Y") . ': ' . $all_persentase . '%*';

                            $data_presensi_weekly = $data_presensi_weekly . '
- [' . $vu->angkatan . '] ' . $vu->fullname . ': *' . $all_persentase . '%*';
                        }
                    }
                }
            }

            if (count($data_mhs) > 0) {
                WaSchedules::save('Weekly Report', $data_presensi_weekly, $setting->wa_info_presensi_group_id, 1);

                // kirim ke ortu
                $time_post = 2;
                foreach ($data_mhs as $dm) {
                    // echo var_dump($dm);
                    WaSchedules::save('[Ortu] Weekly Report', $dm['caption'], WaSchedules::getContactId($dm['nohp']), $time_post);
                    $time_post++;
                }
            }

            echo json_encode(['status' => true, 'message' => '[weekly] success running scheduler']);
        }

        // MONTHLY
        elseif ($time == 'monthly') {
            PresenceGroupsChecker::createPresence();
            if(!$setting->cron_monthly){
                echo json_encode(['status' => false, 'message' => '[monthly] scheduler off']);
                exit;
            }
            
            $time_post = 1;
            $no = 1;
            // daftar mahasiswa yang presensi < 80%
            $lsm = strtotime('-1 month', strtotime(date("Y-m-d")));
            $last_month = date('Y-m', $lsm);

            $ym_periode = CommonHelpers::periode();
            $split_periode = explode("-", $ym_periode);
            $loop_month = [$split_periode[0] . '-09', $split_periode[0] . '-10', $split_periode[0] . '-11', $split_periode[0] . '-12', $split_periode[1] . '-01', $split_periode[1] . '-02', $split_periode[1] . '-03', $split_periode[1] . '-04', $split_periode[1] . '-05', $split_periode[1] . '-06', $split_periode[1] . '-07', $split_periode[1] . '-08'];

            // cek hasda
            $get_presence_hasda = Presence::where('event_date', 'like', $last_month.'%')->where('is_hasda', 1)->where('is_deleted', 0)->get();
            $view_usantri = DB::table('v_user_santri')->orderBy('fullname', 'ASC')->get();
            $caption_hasda = '*DAFTAR MAHASISWA YANG BELUM MENGIKUTI HASDA BULAN ' . strtoupper(date('M Y', $lsm)) . '*
';

            if($get_presence_hasda!=null){
                $set_presence = '';
                foreach($get_presence_hasda as $gph){
                    if($set_presence!=$gph->id){
                        $caption_hasda = $caption_hasda.'
*'.$gph->name.'*';
                        $set_presence = $gph->id;
                    }
                    foreach ($view_usantri as $mhs) {
                        $check_present = Present::where('fkPresence_id', $gph->id)->where('fkSantri_id', $mhs->santri_id)->first();
                        if($check_present==null){
                            $caption_hasda = $caption_hasda.'
- '.$mhs->fullname;
                        }
                    }
                }
                $caption_hasda = $caption_hasda.'

*Silahkan mengikuti Hasda susulan yang dilaksanakan bulan '.date('M Y').' dengan jadwal sesuai hasil musyawarah. Namun jika ternyata ada yang sudah mengikuti Hasda diluar PPM silahkan konfirmasi ke Pengurus.*';

                WaSchedules::save('Daftar Mahasiswa Yang Belum mengikuti Hasda', $caption_hasda, $setting->wa_maurus_group_id);
                WaSchedules::save('Daftar Mahasiswa Yang Belum mengikuti Hasda', $caption_hasda, $setting->wa_ortu_group_id);
                WaSchedules::save('Daftar Mahasiswa Yang Belum mengikuti Hasda', $caption_hasda, $setting->wa_info_presensi_group_id);
            }

            $list_angkatan = DB::table('santris')
                ->select('angkatan')
                ->whereNull('exit_at')
                ->groupBy('angkatan')
                ->get();

            $caption = '*[LAPORAN BULANAN]*
';
            $caption = $caption . '*DAFTAR KEHADIRAN MAHASISWA < 80% [BULAN ' . strtoupper(date('M Y', $lsm)) . ']*';
            foreach ($list_angkatan as $la) {
                foreach ($loop_month as $lm) {
                    if (date('Y-m', strtotime(date($lm))) < date('Y-m')) {
                        $result = (new HomeController)->dashboard($lm, $la->angkatan, null, true);
                        $view_usantri = $result['view_usantri'];
                        $presence_group = $result['presence_group'];
                        $presences = $result['presences'];
                        $all_presences = $result['all_presences'];
                        $all_permit = $result['all_permit'];

                        foreach ($view_usantri as $vu) {
                            $all_persentase = 0;
                            $all_kbm = 0;
                            $all_hadir = 0;
                            $all_ijin = 0;
                            foreach ($presence_group as $pg) {
                                foreach ($presences[$vu->santri_id][$pg->id] as $listcp) {
                                    if ($listcp->santri_id == $vu->santri_id) {
                                        $ijin = 0;
                                        if (isset($all_permit[$pg->id][$vu->santri_id])) {
                                            $ijin = $all_permit[$pg->id][$vu->santri_id];
                                        }
                                        $all_kbm = $all_kbm + $all_presences[$vu->santri_id][$pg->id][0]->c_all;
                                        $all_hadir = $all_hadir + $listcp->cp;
                                        $all_ijin = $all_ijin + $ijin;
                                    }
                                }
                            }

                            if ($all_kbm > 0) {
                                $all_persentase = ($all_hadir + $all_ijin) / $all_kbm * 100;
                                $all_persentase = number_format($all_persentase, 2);
                                // jika kbm < 80%, then auto create pelanggaran and send wa to ketertiban
                                if ($all_persentase < 80) {
                                    $check_peringatan = Pelanggaran::where('fkSantri_id', $vu->santri_id)
                                        ->where('fkJenis_pelanggaran_id', 14)
                                        // ->whereNull('kategori_sp_real')
                                        ->where('periode_tahun', CommonHelpers::periode())
                                        ->first();

                                    $is_50 = false;
                                    if ($check_peringatan == null) {
                                        $store['fkSantri_id'] = $vu->santri_id;
                                        $store['fkJenis_pelanggaran_id'] = 14; // Amrin Jami' Tanpa Ijin
                                        $store['tanggal_melanggar'] = date("Y-m-d");
                                        if ($all_persentase < 50) {
                                            $store['saksi'] = 'sisfo 50%';
                                        } else {
                                            $store['saksi'] = 'sisfo 80%';
                                        }
                                        $store['keterangan'] = '[Laporan Bulanan] Presensi kehadiran ' . $lm . ': ' . $all_persentase . '%';
                                        $store['is_archive'] = 0;
                                        $store['is_peringatan_keras'] = 0;
                                        $store['peringatan_kbm'] = 1;
                                        $store['periode_bulan_kbm'] = json_encode([$lm]);
                                        $store['periode_tahun'] = CommonHelpers::periode();
                                        $data = Pelanggaran::create($store);
                                    } else {
                                        $periode_bulan_kbm = json_decode($check_peringatan->periode_bulan_kbm);
                                        if (!in_array($lm, $periode_bulan_kbm)) {
                                            if ($check_peringatan->kategori_sp_real == null) {
                                                $check_peringatan->peringatan_kbm = $check_peringatan->peringatan_kbm + 1;
                                                $check_peringatan->keterangan = $check_peringatan->keterangan . ' | [Laporan Bulanan] Presensi kehadiran ' . $lm . ': ' . $all_persentase . '%';
                                            }
                                            if ($check_peringatan->peringatan_kbm == 3 || ($check_peringatan->peringatan_kbm == 2 && $check_peringatan->saksi == 'sisfo 50%' && $all_persentase < 50)) {
                                                $check_peringatan->kategori_sp_real = '2';
                                                $check_peringatan->keringanan_sp = '1';
                                                $check_peringatan->is_surat_peringatan = date("Y-m-d");
                                                $check_peringatan->is_peringatan_keras = 0;
                                                if ($all_persentase < 50) {
                                                    $is_50 = true;
                                                    $check_peringatan->keterangan = $check_peringatan->keterangan . ' | Sudah 2 bulan berturut-turut kehadiran dibawah < 50%';
                                                } else {
                                                    $check_peringatan->keterangan = $check_peringatan->keterangan . ' | Sudah 3 bulan kehadiran dibawah < 80%';
                                                }
                                            } else {
                                                $check_peringatan->saksi = 'sisfo 80%';
                                            }

                                            array_push($periode_bulan_kbm, $lm);
                                            $check_peringatan->periode_bulan_kbm = json_encode(array_unique($periode_bulan_kbm));
                                        }

                                        $check_peringatan->save();
                                        $data = Pelanggaran::find($check_peringatan->id);
                                    }

                                    if ($data) {
                                        if ($lm == $last_month) {
                                            $caption = $caption . '
' . $no . '. *[' . $data->santri->angkatan . '] ' . $data->santri->user->fullname . ' (' . $all_persentase . '%)*';
                                            if ($is_50) {
                                                $caption = $caption . ' - Keterangan: Sudah 2 bulan berturut-turut kehadiran < 50%';
                                            } elseif ($data->peringatan_kbm == 3) {
                                                $caption = $caption . ' - Keterangan: Sudah mencapai 3 bulan kehadiran < 80%';
                                            }
                                            $no++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $contact_id = $setting->wa_info_presensi_group_id;
            WaSchedules::save('Amrin Jami Tanpa Ijin Bulan ' . $last_month, $caption, $contact_id, $time_post);
            $time_post++;

            // kirim absensi bulanan - bulk ortu
            $view_usantri = DB::table('v_user_santri')->whereNotNull('nohp_ortu')->orderBy('fullname', 'ASC')->get();
            foreach ($view_usantri as $vs) {
                $check_report = ReportScheduler::where('fkSantri_id', $vs->santri_id)->first();
                if ($check_report == null) {
                    $create_report = ReportScheduler::create([
                        'fkSantri_id' => $vs->santri_id,
                        'link_url' => $setting->host_url . '/report/' . $vs->ids,
                        'month' => date("m"),
                        'status' => 0,
                        'count' => 0,
                        'ids' => $vs->ids
                    ]);
                } else {
                    $check_report->month = date("m");
                    $check_report->status = 0;
                    $check_report->count = 0;
                    $create_report = $check_report->save();
                }
                if ($create_report) {
                    $nohp = $vs->nohp_ortu;
                    if ($nohp != '') {
                        if ($nohp[0] == '0') {
                            $nohp = '62' . substr($nohp, 1);
                        }
                        $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                            $query->where('name', 'NOT LIKE', '%Bulk%');
                        })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                        if ($wa_phone != null) {
                            $caption = 'Berikut kami informasikan laporan mahasiswa an. *' . $vs->fullname . '*
Silahkan klik link dibawah ini:
' . $setting->host_url . '/report/' . $vs->ids;
                            WaSchedules::save('All Report: ' . $vs->fullname, $caption, $wa_phone->pid, $time_post);
                        }
                        $time_post++;
                    }
                }
            }

            $check_akumulasi_sp = Pelanggaran::where('is_archive', 0)->whereNotNull('keringanan_sp')->orderBy('fkSantri_id')->get();
            $caption_pelanggaran = '*[DAFTAR PELANGGARAN AKTIF]*
';
            $nox = 1;
            foreach ($check_akumulasi_sp as $casp) {
                $caption_pelanggaran = $caption_pelanggaran . '
' . $nox . '. *' . $casp->santri->user->fullname . '*: ' . $casp->jenis->jenis_pelanggaran; // . ' (SP ' . $casp->keringanan_sp . ')';
                $nox++;
            }
            $contact_id = $setting->wa_info_presensi_group_id;
            WaSchedules::save('Daftar Pelanggaran Aktif', $caption_pelanggaran, $contact_id, $time_post);
            $time_post++;

            echo json_encode(['status' => true, 'message' => '[monthly] success running scheduler']);
        }

        // LINK PRESENSI
        elseif ($time == 'presence') {
            if(!$setting->cron_presence){
                echo json_encode(['status' => false, 'message' => '[presence] scheduler off']);
                exit;
            }
            
            PresenceGroupsChecker::checkPresenceGroups();
            $get_presence_today = Presence::where('event_date', date("Y-m-d"))->where('fkPresence_group_id', $presence_id)->where('is_deleted', 0)->first();
            if ($get_presence_today != null) {
                if ($setting->auto_generate_hadir) {
                    $view_usantri = DB::table('v_user_santri')->orderBy('fullname', 'ASC')->get();
                    foreach ($view_usantri as $mhs) {
                        $permit = Permit::where('fkPresence_id', $get_presence_today->id)->where('fkSantri_id', $mhs->santri_id)->where('status', 'approved')->first();
                        if (!$permit) {
                            $existingPresent = Present::where('fkPresence_id', '=', $get_presence_today->id, 'and')->where('fkSantri_id', '=', $mhs->santri_id)->first();
                            if (!$existingPresent) {
                                $inserted = Present::create([
                                    'fkSantri_id' => $mhs->santri_id,
                                    'fkPresence_id' => $get_presence_today->id,
                                    'is_late' => 0
                                ]);
                            }
                        }
                    }
                }

                // alpha
                $mhs_alpha = CountDashboard::mhs_alpha($get_presence_today->id, 'all', $get_presence_today->event_date);
                if (count($mhs_alpha) > 0) {
                    foreach ($mhs_alpha as $d) {
                        // info ke ortu
                        if ($setting->wa_info_alpha_ortu == 1) {
                            $caption_ortu = 'Menginformasikan bahwa *' . $d['name'] . '* tadi tidak hadir tanpa ijin pada ' . $get_presence_today->name . '.

NB:
- Jika ternyata hadir, silahkan melaporkan ke Pengurus untuk disesuaikan presensinya
- Jika ada kendala lainnya, silahkan menghubungi:
*' . $d['lorong'] . '*.';
                            WaSchedules::save('Info Alpha ke Ortu ' . $d['name'], $caption_ortu, WaSchedules::getContactId($d['nohp_ortu']), $time_post, false);
                            $time_post++;
                        }
                    }
                }
            }

            echo json_encode(['status' => true, 'message' => '[presence] success running scheduler']);
        } elseif ($time == 'jam-malam') {
            if(!$setting->cron_jam_malam){
                echo json_encode(['status' => false, 'message' => '[jam-malam] scheduler off']);
                exit;
            }
            
            DB::table('santris')->whereNull('exit_at')->update(array('jaga_malam' => 0));
            $contact_id = SpWhatsappContacts::where('name', 'Group PPM RJ Maurus')->first();
            if ($contact_id != null) {
                $jaga_malam1 = JagaMalams::where('ppm',1)->where('status',1)->first();
                $jaga_malam2 = JagaMalams::where('ppm',2)->where('status',1)->first();
                $check_liburan = Liburan::where('liburan_from', '<=', date('Y-m-d'))->where('liburan_to', '>=', date('Y-m-d'))->get();
                if (count($check_liburan) == 0){
                    $info_jam_malam = '*PPM 1*
';
                
                    $split_team1 = explode(",", $jaga_malam1->anggota);
                    foreach($split_team1 as $st){
                        if($st!=""){
                            $snt = Santri::find($st);
                            $snt->jaga_malam = 1;
                            $snt->save();
                            $nohp = $snt->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                            }
                            $info_jam_malam = $info_jam_malam.$snt->user->fullname.' wa.me/'.$nohp.'
';

                            $capner = 'Monggo Mas *'.$snt->user->fullname.'* segera persiapan Jaga Malam, supaya ditetapi dengan hati ridho sakdermo karena Allah.';
                            WaSchedules::save('Nerobos Jaga Malam' . $snt->user->fullname, $capner, WaSchedules::getContactId($snt->user->nohp));
                        }
                    }
                    $info_jam_malam = $info_jam_malam.'
*PPM 2*
';
                    $split_team2 = explode(",", $jaga_malam2->anggota);
                    foreach($split_team2 as $st){
                        if($st!=""){
                            $snt = Santri::find($st);
                            $snt->jaga_malam = 1;
                            $snt->save();
                            $nohp = $snt->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                            }
                            $info_jam_malam = $info_jam_malam.$snt->user->fullname.' wa.me/'.$nohp.'
';

                            $capner = 'Monggo Mas *'.$snt->user->fullname.'* segera persiapan Jaga Malam, supaya ditetapi dengan hati ridho sakdermo karena Allah.';
                            WaSchedules::save('Nerobos Jaga Malam' . $snt->user->fullname, $capner, WaSchedules::getContactId($snt->user->nohp));
                        }
                    }
                }else{
                    $info_jam_malam = '*Amalsholih Yang Masih Berada Di Lingkungan PPM Turut Menjaga Keamanan.*
';
                }

                $jaga_malam1->status = 0;
                $jaga_malam1->save();
                $jaga_malam2->status = 0;
                $jaga_malam2->save();

                $next_jaga1 = JagaMalams::where('ppm',1)->where('putaran_ke',($jaga_malam1->putaran_ke+1))->first();
                if($next_jaga1==null){
                    $update = JagaMalams::where('ppm',1)->where('putaran_ke',1)->first();
                    $update->status = 1;
                    $update->save();
                }else{
                    $next_jaga1->status = 1;
                    $next_jaga1->save();
                }
                $next_jaga2 = JagaMalams::where('ppm',2)->where('putaran_ke',($jaga_malam2->putaran_ke+1))->first();
                if($next_jaga2==null){
                    $update = JagaMalams::where('ppm',2)->where('putaran_ke',1)->first();
                    $update->status = 1;
                    $update->save();
                }else{
                    $next_jaga2->status = 1;
                    $next_jaga2->save();
                }

                $info_jam_malam = $info_jam_malam.'
'.$setting->wa_info_jaga_malam;
                WaSchedules::save('Jam Malam ' . date('d-m-Y'), $info_jam_malam, $contact_id->id);
            }

            echo json_encode(['status' => true, 'message' => '[jam-malam] success running scheduler']);
        } elseif ($time=='tatib') {
            if(!$setting->cron_tatib){
                echo json_encode(['status' => false, 'message' => '[tatib] scheduler off']);
                exit;
            }

            $tatib = ReminderTatatertib::where('status', 1)->first();
            $caption = "*PEMBACAAN TATA TERTIB PPM RJ*
*".$tatib->kategori."*

".$tatib->konten_tatib;

            WaSchedules::save('Tatib #'.$tatib->id, $caption, $setting->wa_maurus_group_id);
            WaSchedules::save('Tatib #'.$tatib->id, $caption, $setting->wa_ortu_group_id);
            $tatib->status = 0;
            $tatib->save();

            $next_tatib = ReminderTatatertib::where('id', ($tatib->id+1))->first();
            if($next_tatib==null){
                $update = ReminderTatatertib::where('id', 1)->first();
                $update->status = 1;
                $update->save();
            }else{
                $next_tatib->status = 1;
                $next_tatib->save();
            }
            echo json_encode(['status' => true, 'message' => '[tatib] success running scheduler']);
        } elseif ($time=='minutes') {
            if(!$setting->cron_minutes){
                echo json_encode(['status' => false, 'message' => '[minutes] scheduler off']);
                exit;
            }
            // Nerobos KBM
            $event_date = date('Y-m-d');
            $currentDateTime = date('Y-m-d H:i');
            $add_mins = date('Y-m-d H:i', strtotime("+{$setting->reminder_kbm} minutes", strtotime($currentDateTime)));
            $get_presence_today = Presence::where('event_date', $event_date)->where('start_date_time','like', $add_mins.'%')->where('is_deleted', 0)->first();
            if($get_presence_today!=null){
                $is_put_together = "";
                if($get_presence_today->is_put_together){
                    $is_put_together = "
- *Disatukan di PPM 1*";
                }
                $is_hasda = "";
                if($get_presence_today->is_hasda){
                    $is_hasda = " - *HASDA*";
                }
                $caption = "*[INFO ".strtoupper($get_presence_today->name)."]*".$is_hasda."

".CommonHelpers::hari_ini(date_format(date_create($get_presence_today->event_date), 'D')).", ".date_format(date_create($get_presence_today->event_date), 'd-m-Y')."
-
Mulai KBM: *".date_format(date_create($get_presence_today->start_date_time), 'H:i')."*
Selesai KBM: *".date_format(date_create($get_presence_today->end_date_time), 'H:i')."*
-
Fingerprint (in): *".date_format(date_create($get_presence_today->presence_start_date_time), 'H:i')."*
Fingerprint (out): *".date_format(date_create($get_presence_today->presence_end_date_time), 'H:i')."*
-
NB:".$is_put_together."
- Amalsholih untuk hadir tepat waktu
- Dalam pelaksanaan KBM supaya dipersungguh dan diniati mencari kefahaman";
                WaSchedules::save('Reminder #'.$get_presence_today->name, $caption, $setting->wa_maurus_group_id);
            }

            // nerobos jika 30 menit belum dateng
            if($setting->cron_nerobos){
                $currentDateTime = date('Y-m-d H:i');
                $min_mins = date('Y-m-d H:i', strtotime("-30 minutes", strtotime($currentDateTime)));
                $get_presence_today = Presence::where('event_date', $event_date)->where('start_date_time','like', $min_mins.'%')->where('is_deleted', 0)->first();
                if($get_presence_today!=null){
                    $mhs_alpha = CountDashboard::mhs_alpha($get_presence_today->id, 'all', $get_presence_today->event_date);
                    if (count($mhs_alpha) > 0) {
                        foreach ($mhs_alpha as $vs) {
                            $caption = 'Assalaamu Alaikum *'.$vs->fullname.'*,
Mohon maaf dipersilahkan untuk segera menghadiri KBM, jika memang berhalangan jangan lupa input ijin melalui Sisfo.
الحمد للّٰه جزاكم اللّٰه خيرًا 😇🙏🏻';
                            WaSchedules::save('Nerobos: ' . $vs->fullname, $caption, WaSchedules::getContactId($vs->nohp), null, true);
                        }
                    }
                }
            }

            // jam FP off -> info alpha ke ortu
            if ($setting->wa_info_alpha_ortu) {
                $currentDateTime = date('Y-m-d H:i');
                $min_mins = date('Y-m-d H:i', strtotime("-45 minutes", strtotime($currentDateTime)));
                $get_presence_today = Presence::where('event_date', $event_date)->where('end_date_time','like', $min_mins.'%')->where('is_deleted', 0)->first();
                if($get_presence_today!=null){
                    $mhs_alpha = CountDashboard::mhs_alpha($get_presence_today->id, 'all', $get_presence_today->event_date);
                    if (count($mhs_alpha) > 0) {
                        foreach ($mhs_alpha as $vs) {
                            $caption_ortu = 'Menginformasikan bahwa *' . $d['name'] . '* kemarin tidak hadir tanpa ijin pada ' . $presence->name . '.

Jika ada *kendala*, silahkan menghubungi *Pengurus Koor Lorong*:
*' . $d['lorong'] . '*.';
                            WaSchedules::save('Info Alpha ke Ortu: ' . $vs->fullname, $caption_ortu, WaSchedules::getContactId($vs->nohp_ortu));
                        }
                    }
                }
            }

            echo json_encode(['status' => true, 'message' => '[minutes] success running scheduler']);
        }
    }

    public function generator()
    {
        PresenceGroupsChecker::checkPresenceGroups();
        PresenceGroupsChecker::checkPermitGenerators();
    }

    public function report($ids)
    {
        $rs = ReportScheduler::where('ids', $ids)->first();
        if ($rs != null) {
            $last_update = date_format(date_create($rs->updated_at), "Y-m-d");
            $today = date('Y-m-d');
            if ($last_update != $today || $rs->count == 0) {
                $rs->count = $rs->count + 1;
            }
            $rs->status = 1;
            $rs->save();

            $santri = Santri::where('ids', $ids)->first();
        } else {
            $ids = base64_decode($ids);

            $santri = Santri::find($ids);
        }

        $santri_id = $santri->id;

        $tahun_bulan = DB::table('presences')
            ->select(DB::raw('DATE_FORMAT(event_date, "%Y-%m") as ym'))
            ->where('event_date', '>=', $santri->angkatan . '-09-01')
            ->where('is_deleted',0)
            ->groupBy('ym')
            ->get();

        $tahun = DB::table('presences as a')
            ->select(DB::raw('DATE_FORMAT(a.event_date, "%Y") as y'))
            ->leftJoin('presents as b', function ($join) {
                $join->on('a.id', '=', 'b.fkPresence_id');
            })
            ->where('b.fkSantri_id', $santri_id)
            ->where('a.is_deleted',0)
            ->orderBy('y', 'DESC')
            ->groupBy('y')
            ->get();

        // loop presensi berdasarkan tahun bulan
        $presence_group = PresenceGroup::get();
        $datapg = array();
        foreach ($tahun_bulan as $tb) {
            $presences = DB::table('presences as a')
                ->leftJoin('presents as b', function ($join) use ($santri_id) {
                    $join->on('a.id', '=', 'b.fkPresence_id');
                    $join->where('b.fkSantri_id', $santri_id);
                })
                ->select('a.name', 'a.fkPresence_group_id', 'b.*')
                ->where('a.event_date', 'like', '%' . $tb->ym . '%')
                ->where('a.is_deleted',0)
                ->orderBy('a.event_date', 'ASC')
                ->get();
            if ($presences != null) {
                foreach ($presence_group as $pg) {
                    $datapg[$tb->ym][$pg->id]['kbm']     = 0;
                    $datapg[$tb->ym][$pg->id]['hadir']   = 0;
                    $datapg[$tb->ym][$pg->id]['ijin']    = 0;
                    $datapg[$tb->ym][$pg->id]['alpha']   = 0;
                    $kbm = 0;
                    $hadir = 0;
                    $ijin = 0;
                    $alpha = 0;
                    foreach ($presences as $ps) {
                        if ($pg->id == $ps->fkPresence_group_id) {
                            $kbm++;
                            if ($ps->fkSantri_id != "") {
                                $hadir++;
                            }
                            $datapg[$tb->ym][$pg->id]['kbm'] = $kbm;
                        }
                        $datapg[$tb->ym][$pg->id]['hadir'] = $hadir;
                    }

                    $permit = DB::select("SELECT a.fkSantri_id, count(a.fkSantri_id) as approved FROM `permits` a JOIN `presences` b ON a.fkPresence_id=b.id WHERE a.fkSantri_id = $santri_id AND a.status='approved' AND b.is_deleted=0 AND b.event_date LIKE '%" . $tb->ym . "%' AND b.fkPresence_group_id = " . $pg->id . " GROUP BY a.fkSantri_id");
                    if ($permit != null) {
                        foreach ($permit as $p) {
                            $ijin = $ijin + $p->approved;
                        }
                        $datapg[$tb->ym][$pg->id]['ijin'] = $ijin;
                    }
                    $datapg[$tb->ym][$pg->id]['alpha'] = $datapg[$tb->ym][$pg->id]['kbm'] - ($datapg[$tb->ym][$pg->id]['hadir'] + $datapg[$tb->ym][$pg->id]['ijin']);
                }
            }
        }

        // get pelanggaran
        $pelanggaran = Pelanggaran::where('fkSantri_id', $santri_id)->whereNotNull('keringanan_sp')->get();
        $sodaqoh = Sodaqoh::where('fkSantri_id', $santri_id)->get();

        // get pencapaian materi
        $materis = Materi::all();
        $data_materi = '';
        if ($santri != null) {
            foreach ($materis as $materi) {
                if ($materi->for == 'mubalegh' && !$santri->user->hasRole('mubalegh'))
                    continue;
                if ($materi->for != 'mubalegh' && $santri->user->hasRole('mubalegh'))
                    continue;
                $completedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'complete')->count();
                $partiallyCompletedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'partial')->count();
                $totalPages = $completedPages + ($partiallyCompletedPages / 2);
                if ($totalPages > 0) {
                    $data_materi = $data_materi . '
                    <tr class="text-sm">
                        <td class="p-1 ps-2">' . $materi->name . '</td>
                        <td class="p-1 ps-2">' . $totalPages . ' / ' . $materi->pageNumbers . '</td>
                        <td class="p-1 ps-2">' . number_format((float) $totalPages / $materi->pageNumbers * 100, 2, ".", "") . '%</td>
                    </tr>';
                }
            }
        }
        $catatan_penghubungs = CatatanPenghubungs::where('fkSantri_id',$santri_id)->first();

        return view('report.all_report', [
            'santri' => $santri,
            'tahun' => $tahun,
            'tahun_bulan' => $tahun_bulan,
            'presence_group' => $presence_group,
            'datapg' => $datapg,
            'data_materi' => $data_materi,
            'pelanggaran' => $pelanggaran,
            'catatan_penghubungs' => $catatan_penghubungs,
            'sodaqoh' => $sodaqoh
        ]);
    }

    public function daily_presences($year, $month, $date, $angkatan)
    {
        $presencesInDate = Presence::whereDate('event_date', '=', "$year-$month-$date")->get();
        $mahasiswa = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->whereHas('santri', function ($query) use ($angkatan) {
            $query->where('angkatan', $angkatan);
        })->orderBy('fullname', 'asc')->get();
        $presents = array();
        foreach ($presencesInDate as $pid) {
            $presence = Presence::find($pid->id);
            $presents[$pid->id] = $presence ?
                $presence->presents()
                ->select('presents.*')
                ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                ->join('users', 'users.id', '=', 'santris.fkUser_id')
                ->where('angkatan', $angkatan)
                ->orderBy('users.fullname')
                ->get()
                :
                null;

            $permits[$pid->id] = $presence ?
                Permit::where('fkPresence_id', [$pid->id])->where('status', 'approved')->get()
                :
                null;
        }

        return view('report.daily_presences', [
            'mahasiswa' => $mahasiswa,
            'presence' => $presence,
            'permits' => $permits,
            'presents' => $presents,
            'presencesInDate' => $presencesInDate,
            'year' => $year,
            'month' => $month,
            'date' => $date,
            'angkatan' => $angkatan
        ]);
    }

    public function view_permit($ids)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        if ($permit != null) {
            if ($permit->status == 'approved') {
                $message = 'Permintaan ijin sudah disetujui';
            } elseif ($permit->status == 'pending') {
                $message = 'Permintaan ijin masih pending';
            } else {
                $message = 'Permintaan ijin sudah ditolak';
            }
        } else {
            $message = 'Perijinan tidak ditemukan.';
        }
        return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }

    public function reject_permit($ids, Request $request)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        $statusx = false;
        if ($permit != null) {
            try {
                if (isset(auth()->user()->fullname)) {
                    $rejected_by = auth()->user()->fullname;
                } else {
                    $rejected_by = $_SERVER['HTTP_USER_AGENT'];
                }
            } catch (Exception  $err) {
                $rejected_by = $_SERVER['HTTP_USER_AGENT'];
            }

            $permit->status = 'rejected';
            $permit->rejected_by = $rejected_by;
            $permit->alasan_rejected = $request->get('alasan');
            $permit->metadata = $_SERVER['HTTP_USER_AGENT'];

            if ($permit->save()) {
                $caption = '*' . $rejected_by . '* Menolak perijinan dari *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ': [' . $permit->reason_category . '] ' . $permit->reason . '
*Alasan Ditolak:* Karena ' . $permit->alasan_rejected;
                WaSchedules::save('Permit Rejected', $caption, $setting->wa_info_presensi_group_id, null, true);

                $name = 'Perijinan Dari ' . $permit->santri->user->fullname;
                // kirim ke yg ijin
                $nohp = $permit->santri->user->nohp;
                if ($nohp != '') {
                    if ($nohp[0] == '0') {
                        $nohp = '62' . substr($nohp, 1);
                    }
                    $setting = Settings::find(1);
                    $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                        $query->where('name', 'NOT LIKE', '%Bulk%');
                    })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                    if ($wa_phone != null) {
                        $caption = 'Perijinan pada ' . $permit->presence->name . ' Anda di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                        WaSchedules::save($name, $caption, $wa_phone->pid);
                    }
                }

                // kirim ke orangtua
                $nohp_ortu = $permit->santri->nohp_ortu;
                if ($nohp_ortu != '') {
                    if ($nohp_ortu[0] == '0') {
                        $nohp_ortu = '62' . substr($nohp_ortu, 1);
                    }
                    $setting = Settings::find(1);
                    $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                        $query->where('name', 'NOT LIKE', '%Bulk%');
                    })->where('team_id', $setting->wa_team_id)->where('phone', $nohp_ortu)->first();
                    if ($wa_phone != null) {
                        $caption = 'Perijinan *' . $permit->santri->user->fullname . '* pada ' . $permit->presence->name . ' di Tolak oleh Pengurus karena *' . $permit->alasan_rejected . '*.';
                        WaSchedules::save($name, $caption, $wa_phone->pid, 2);
                    }
                }
                $message = 'Permintaan ijin berhasil ditolak';
                $statusx = true;
            } else {
                $message = 'Terjadi kesalahan sistem';
            }
        } else {
            $message = 'Perijinan tidak ditemukan';
        }

        return json_encode(['status' => $statusx, 'permit' => $permit, 'message' => $message]);
        // return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }

    public function approve_permit($ids)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        if ($permit != null) {
            $permit->status = 'approved';

            try {
                if (isset(auth()->user()->fullname)) {
                    $approved_by = auth()->user()->fullname;
                } else {
                    $approved_by = $_SERVER['HTTP_USER_AGENT'];
                }
            } catch (Exception  $err) {
                $approved_by = $_SERVER['HTTP_USER_AGENT'];
            }

            $permit->approved_by = $approved_by;
            $permit->alasan_rejected = '';
            $permit->metadata = $_SERVER['HTTP_USER_AGENT'];

            if ($permit->save()) {
                $message = 'Permintaan ijin berhasil disetujui';
            }
        } else {
            $message = 'Perijinan tidak ditemukan';
        }

        return redirect()->route('view permit', $ids)->with(['success', $message]);
        // return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }

    // PRESENCE
    public function presence_view($id, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $presence = Presence::find($id);

        $for = 'all';
        // jumlah mhs / anggota lorong
        $jumlah_mhs = CountDashboard::total_mhs($for, $lorong);

        // hadir
        $presents = CountDashboard::mhs_hadir($id, $for, $lorong);

        // ijin berdasarkan lorong masing2
        $permits = CountDashboard::mhs_ijin($id, $for, $lorong);
        // need approval
        $need_approval = Permit::where('fkPresence_id', $id)->whereNotIn('status', ['approved'])->get();

        // alpha
        $mhs_alpha = CountDashboard::mhs_alpha($id, $for, $presence->event_date, $lorong);

        $update = true;
        if ($presence != null) {
            $selisih = strtotime(date("Y-m-d")) - strtotime($presence->event_date);
            $selisih = $selisih / 60 / 60 / 24;
            if ($selisih > 1 && $for != 'all') {
                $update = false;
            }
        }

        return view('presence.view_public', [
            'id' => $id,
            'presence' => $presence,
            'jumlah_mhs' => $jumlah_mhs,
            'mhs_alpha' => $mhs_alpha,
            'permits' => $permits,
            'need_approval' => $need_approval,
            'presents' => $presents == null ? [] : $presents,
            'data_lorong' => Lorong::all(),
            'lorong' => $lorong,
            'update' => $update
        ]);
    }

    public function presence_delete_present($id, $santriId, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId);

        if ($present) {
            $deleted = $present->delete();

            if (!$deleted)
                return redirect()->route('dwngr view presence', $id)->withErrors(['failed_deleting_present', 'Gagal menghapus presensi.']);
        }

        if ($request->get('json') == 'true') {
            return json_encode(array("status" => true));
        } else {
            return redirect()->route('dwngr view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil menghapus presensi');
        }
    }

    public function presence_is_present($id, $santriId, Request $request)
    {
        $lorong = $request->get('lorong');
        if ($lorong == null) {
            $lorong = '-';
        }
        $present = Present::where('fkPresence_id', $id)->where('fkSantri_id', $santriId)->first();

        try {
            if (isset(auth()->user()->fullname)) {
                $presented_by = auth()->user()->fullname;
            } else {
                $presented_by = 'Dewan Guru';
            }
        } catch (Exception  $err) {
            $presented_by = 'Dewan Guru';
        }

        if ($present == null) {
            Present::create([
                'fkSantri_id' => $santriId,
                'fkPresence_id' => $id,
                'is_late' => 0,
                'updated_by' => $presented_by,
                'metadata' => $_SERVER['HTTP_USER_AGENT']
            ]);
        }

        if ($request->get('json') == 'true') {
            return json_encode(array("status" => true));
        } else {
            return redirect()->route('dwngr view presence', [$id, 'lorong' => $lorong])->with('success', 'Berhasil menginput presensi');
        }
    }
}

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
use App\Models\SodaqohHistoris;
use App\Models\Materi;
use App\Models\Santri;
use App\Models\Lorong;
use App\Models\ReportScheduler;
use App\Models\SpWhatsappPhoneNumbers;
use App\Models\SpWhatsappContacts;
use App\Models\ReminderTatatertib;
use App\Models\CatatanPenghubungs;
use App\Models\JagaMalams;
use App\Models\LaporanKeamanans;
use App\Models\RabManagBuildings;
use App\Models\RabManagBuildingDetails;
use App\Models\RabKegiatans;
use App\Models\RabKegiatanDetails;
use App\Models\Rabs;
use App\Models\Jurnals;
use App\Models\DewanPengajars;
use App\Models\KalenderPpmTemplates;
use App\Models\KalenderPpms;
use App\Models\SpWhatsappSchedules;
use Carbon\Carbon;
use Error;
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
                $caption = '*[PREVIEW]*
Amalsholih cek kehadiran KBM kemarin, apabila ada yang tidak sesuai silahkan menghubungi RJ/WK:';
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
*_' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M") . ' | ' . $presence->presenceGroup->name . '_*
Hadir: ' . count($presents) . ' | Ijin: ' . count($permits) . ' | Alpha: ' . count($mhs_alpha) . '

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
                            $infodp = 'Pemateri ' . $infodp_xxz . ' pada '.$presence->name.' belum disesuaikan';
                            WaSchedules::save('Check Pemateri', $infodp, $contact_id, $time_post, true);
                            $time_post++;
                        }
                    }
                }

                $name = '[Preview] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                if ($contact_id != '' && count($get_presence) > 0) {
                    $insert = WaSchedules::save($name, $caption, $setting->wa_maurus_group_id, $time_post, true);
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
                        WaSchedules::save($caption, $caption, $setting->wa_info_presensi_group_id, $time_post, true);
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

👮🏼‍♂️ *Total Mahasiswa: ' . CountDashboard::total_mhs('all') . '*';
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
------------------------
📆 *_' . $presence->name . '_*
Hadir: ' . count($presents) . ' | Ijin: ' . count($permits) . ' | Alpha: ' . count($mhs_alpha) . '

*Pemateri*
- PPM 1: '.(($presence->fkDewan_pengajar_1!=null) ? $presence->dewanPengajar1->name : '').'
- PPM 2: '.(($presence->fkDewan_pengajar_2!=null) ? $presence->dewanPengajar2->name : '').'
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
📝 NB:
- Jika ada keperluan atau berhalangan hadir, dapat melakukan input ijin melalui Sisfo
- Jika dalam 1 bulan kehadiran < 80%, akan ada pemanggilan dan kafaroh
- Apabila terdapat ketidaksesuaian, amalsholih menghubungi Pengurus atau Koor Lorong';

                $name = '[Ortu Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                if ($contact_id != '' && count($get_presence) > 0) {
                    $insert = WaSchedules::save($name, $caption, $contact_id);

                    // $contact_id = SpWhatsappContacts::where('name', 'Group PPM RJ Maurus')->first();
                    // if ($contact_id != null) {
                    //     $name = '[Maurus Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                    //     $insert = WaSchedules::save($name, $caption, $contact_id->id, 2);
                    // }

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
            $data_presensi_weekly = '*[Laporan Mingguan] Presensi KBM kurang dari 80%: ' . date("M Y") . '*';
            $data_mhs = array();
            $set_angkatan = "";
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
                            if($set_angkatan!=$vu->angkatan){
                                $data_presensi_weekly = $data_presensi_weekly."

*".$la->angkatan."*";
                                $set_angkatan = $vu->angkatan;
                            }
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
            $caption_hasda = '*DAFTAR MAHASISWA YANG BELUM MENGIKUTI HASDA BULAN ' . strtoupper(date('M Y', $lsm)) . '*';

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

                WaSchedules::save('Daftar Mahasiswa Yang Belum mengikuti Hasda', $caption_hasda, $setting->wa_maurus_group_id, 1);
                WaSchedules::save('Daftar Mahasiswa Yang Belum mengikuti Hasda', $caption_hasda, $setting->wa_ortu_group_id, 2);
            }

            $list_angkatan = DB::table('santris')
                ->select('angkatan')
                ->whereNull('exit_at')
                ->groupBy('angkatan')
                ->get();
            foreach ($list_angkatan as $la) {
                $result = (new HomeController)->dashboard($last_month, $la->angkatan, null, true);
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
                        // if kbm < 80%, then auto create pelanggaran
                        if ($all_persentase < 80) {
                            $store['fkSantri_id'] = $vu->santri_id;
                            $store['fkJenis_pelanggaran_id'] = 14; // Amrin Jami' Tanpa Ijin
                            $store['tanggal_melanggar'] = date("Y-m-d");
                            $store['saksi'] = 'Sisfo';
                            $store['keterangan'] = $last_month . ': ' . $all_persentase . '%';
                            $store['is_archive'] = 0;
                            $store['is_peringatan_keras'] = 0;
                            $store['peringatan_kbm'] = 1;
                            $store['periode_bulan_kbm'] = json_encode([$last_month]);
                            $store['periode_tahun'] = CommonHelpers::periode();
                            // $data = Pelanggaran::create($store);
                        }
                    }
                }
            }

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
                    $caption = 'Berikut kami informasikan *LAPORAN MAHASISWA* an. *' . $vs->fullname . '*
Silahkan klik link dibawah ini:
' . $setting->host_url . '/report/' . $vs->ids;
                    WaSchedules::save('All Report: ' . $vs->fullname, $caption, WaSchedules::getContactId($vs->nohp_ortu), $time_post);
                    $time_post++;
                }
            }

            echo json_encode(['status' => true, 'message' => '[monthly] success running scheduler']);
        } elseif ($time == 'jam-malam') {
            if(!$setting->cron_jam_malam){
                echo json_encode(['status' => false, 'message' => '[jam-malam] scheduler off']);
                exit;
            }
            $info_jam_malam = '🔰 *PETUGAS JAGA MALAM*

';
            DB::table('santris')->whereNull('exit_at')->update(array('jaga_malam' => 0));
            $contact_id = SpWhatsappContacts::where('name', 'Group PPM RJ Maurus')->first();
            if ($contact_id != null) {
                $jaga_malam1 = JagaMalams::where('ppm',1)->where('status',1)->first();
                $jaga_malam2 = JagaMalams::where('ppm',2)->where('status',1)->first();
                $check_liburan = Liburan::where('liburan_from', '<=', date('Y-m-d'))->where('liburan_to', '>=', date('Y-m-d'))->get();
                if (count($check_liburan) == 0){
                    $info_jam_malam = $info_jam_malam.'👮🏼‍♂️ *PPM 1*
';
                
                    $split_team1 = explode(",", $jaga_malam1->anggota);
                    foreach($split_team1 as $st){
                        if($st!=""){
                            $insertLap = LaporanKeamanans::create([
                                'fkSantri_id' => $st,
                                'event_date' => date('Y-m-d'),
                            ]);
                            $snt = Santri::find($st);
                            $snt->jaga_malam = 1;
                            $snt->fkLaporan_keamanan_id = $insertLap->id;
                            $snt->save();
                            $nohp = $snt->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                            }
                            $info_jam_malam = $info_jam_malam.$snt->user->fullname.' wa.me/'.$nohp.'
';

                            $capner = 'Monggo Mas *'.$snt->user->fullname.'* segera persiapan Jaga Malam, supaya ditetapi dengan hati ridho sakdermo karena Allah.
Jangan lupa mengunci gerbang, melaporkan jobdesk dan mencatat mahasiswa yang pulang lewat jam 23:00 di Sisfo.';
                            WaSchedules::save('Nerobos Jaga Malam' . $snt->user->fullname, $capner, WaSchedules::getContactId($snt->user->nohp));
                        }
                    }
                    $info_jam_malam = $info_jam_malam.'
*👮🏼‍♂️ PPM 2*
';
                    $split_team2 = explode(",", $jaga_malam2->anggota);
                    foreach($split_team2 as $st){
                        if($st!=""){
                            $insertLap = LaporanKeamanans::create([
                                'fkSantri_id' => $st,
                                'event_date' => date('Y-m-d'),
                            ]);
                            $snt = Santri::find($st);
                            $snt->jaga_malam = 1;
                            $snt->fkLaporan_keamanan_id = $insertLap->id;
                            $snt->save();
                            $nohp = $snt->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                            }
                            $info_jam_malam = $info_jam_malam.$snt->user->fullname.' wa.me/'.$nohp.'
';

                            $capner = 'Monggo Mas *'.$snt->user->fullname.'* segera persiapan Jaga Malam, supaya ditetapi dengan hati ridho sakdermo karena Allah.
Jangan lupa mengunci gerbang, melaporkan jobdesk  dan mencatat mahasiswa yang pulang lewat jam 23:00 di Sisfo.';
                            WaSchedules::save('Nerobos Jaga Malam' . $snt->user->fullname, $capner, WaSchedules::getContactId($snt->user->nohp));
                        }
                    }
                }else{
                    $info_jam_malam = '*Amalsholih yang masih berada di lingkungan PPM turut menjaga keamanan.*
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
            $currentDateTime = date('Y-m-d H:i');
            $add_mins = date('Y-m-d H:i', strtotime("+{$setting->reminder_kbm} minutes", strtotime($currentDateTime)));
            $event_date = date('Y-m-d', strtotime("+{$setting->reminder_kbm} minutes", strtotime($currentDateTime)));
            $get_presence_today = Presence::where('event_date', $event_date)->where('start_date_time','like', $add_mins.'%')->whereNot('is_deleted', 1)->first();
            
            if($get_presence_today!=null){
                $is_put_together = "";
                if($get_presence_today->is_put_together){
                    $is_put_together = "
- *Disatukan di PPM 1 (Fingerprint yang diaktifkan hanya di PPM 1)*";
                }

                $dewan_pengajar = "";
                if($get_presence_today->fkDewan_pengajar_1!=""){
                    $dewan_pengajar .= "
👳🏻 ".$get_presence_today->dewanPengajar1->name;
                }
                if($get_presence_today->fkDewan_pengajar_2!=""){
                    $dewan_pengajar .= "
👳🏻 ".$get_presence_today->dewanPengajar2->name;
                }
                if($get_presence_today->pre_fkDewan_pengajar_mt!=""){
                    $dewan_pengajar .= "
👳🏻 MT: ".$get_presence_today->dewanPengajar('mt')->name;
                }
                if($get_presence_today->pre_fkDewan_pengajar_reg!=""){
                    $dewan_pengajar .= "
👳🏻 Reguler: ".$get_presence_today->dewanPengajar('reg')->name;
                }
                if($get_presence_today->pre_fkDewan_pengajar_pemb!=""){
                    $dewan_pengajar .= "
👳🏻 Pembinaan: ".$get_presence_today->dewanPengajar('pemb')->name;
                }
                
                $caption = "*".strtoupper($get_presence_today->name)."*
🗓️ ".CommonHelpers::hari_ini(date_format(date_create($get_presence_today->event_date), 'D')).", ".date_format(date_create($get_presence_today->event_date), 'd M Y')."
⏰️ Mulai KBM: *".date_format(date_create($get_presence_today->start_date_time), 'H:i')."*
⏰️ Selesai KBM: *".date_format(date_create($get_presence_today->end_date_time), 'H:i')."*

*Pemateri* ".$dewan_pengajar."

*FINGERPRINT*
📥 Mulai Sign In: *".date_format(date_create($get_presence_today->presence_start_date_time), 'H:i')."*
📤 Batas Sign Out: *".date_format(date_create($get_presence_today->presence_end_date_time), 'H:i')."*

🗒️ *NB*:".$is_put_together."
- *Untuk Presensi, semua diwajibkan scan Fingerprint*
- Amalsholih untuk dapat hadir tepat waktu, tertib, dan disiplin
- Supaya mempersiapkan diri sebelum jam KBM dimulai, menuju masjid/mushola untuk sholat berjamaah sekaligus membawa materi yang sudah ditentukan
- Dalam pelaksanaan KBM supaya ta'dzim, dipersungguh dan diniati mencari kefahaman";
                WaSchedules::save('Reminder #'.$get_presence_today->name, $caption, $setting->wa_maurus_group_id);
                if($get_presence_today->is_put_together || $get_presence_today->is_hasda){
                    WaSchedules::save('Reminder Ortu #'.$get_presence_today->name, $caption, $setting->wa_ortu_group_id);
                }
            }

            // nerobos belum dateng
            if($setting->cron_nerobos){
                $currentDateTime = date('Y-m-d H:i');
                $min_mins = date('Y-m-d H:i', strtotime("-{$setting->reminder_nerobos} minutes", strtotime($currentDateTime)));
                $event_date = date('Y-m-d', strtotime("-{$setting->reminder_nerobos} minutes", strtotime($currentDateTime)));
                $get_presence_today = Presence::where('event_date', $event_date)->where('start_date_time','like', $min_mins.'%')->where('is_deleted', 0)->first();
                if($get_presence_today!=null){
                    $mhs_alpha = CountDashboard::mhs_alpha($get_presence_today->id, 'all', $get_presence_today->event_date);
                    if (count($mhs_alpha) > 0) {
                        foreach ($mhs_alpha as $vs) {
                            $caption = 'Assalaamu Alaikum *'.$vs['name'].'*,
Mohon maaf dipersilahkan untuk segera menghadiri KBM, jika memang berhalangan jangan lupa input ijin melalui Sisfo.
الحمد للّٰه جزاكم اللّٰه خيرًا 😇🙏🏻';
                            WaSchedules::save('Nerobos: ' . $vs['name'], $caption, WaSchedules::getContactId($vs['nohp']), null, true);
                        }
                    }
                }
            }

            // jam FP off -> info alpha ke ortu
            if ($setting->wa_info_alpha_ortu) {
                $currentDateTime = date('Y-m-d H:i');
                $min_mins = date('Y-m-d H:i', strtotime("-{$setting->reminder_alpha_ortu} minutes", strtotime($currentDateTime)));
                $event_date = date('Y-m-d', strtotime("-{$setting->reminder_alpha_ortu} minutes", strtotime($currentDateTime)));
                $get_presence_today = Presence::where('event_date', $event_date)->where('end_date_time','like', $min_mins.'%')->where('is_deleted', 0)->first();
                if($get_presence_today!=null){
                    $mhs_alpha = CountDashboard::mhs_alpha($get_presence_today->id, 'all', $get_presence_today->event_date);
                    if (count($mhs_alpha) > 0) {
                        foreach ($mhs_alpha as $vs) {
                            $caption_ortu = 'Mohon maaf mengganggu,
Menginformasikan bahwa *' . $vs['name'] . '* tadi tidak hadir tanpa ijin pada *' . $get_presence_today->name . '*.

Jika ada *kendala*, silahkan menghubungi *Pengurus Koor Lorong*:
*' . $vs['lorong'] . '*.';
                            WaSchedules::save('Info Alpha ke Ortu: ' . $vs['name'], $caption_ortu, WaSchedules::getContactId($vs['nohp_ortu']));
                        }
                    }
                }
            }

            // Cek WA gagal
            $get_wa_failed = SpWhatsappSchedules::where('failed',1)->get();
            if($get_wa_failed){
                foreach($get_wa_failed as $gwf){
                    WaSchedules::save($gwf->name, $gwf->caption, $gwf->contact_id, null, false, true);
                    SpWhatsappSchedules::find($gwf->id)->delete();
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
        // $pelanggaran = Pelanggaran::where('fkSantri_id', $santri_id)->whereNotNull('keringanan_sp')->get();
        $pelanggaran = Pelanggaran::where('fkSantri_id', $santri_id)->get();
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

    public function rab_kegiatan($ids=null){
        $kegiatans = RabKegiatans::where('ids',$ids)->first();
        $detail_kegiatans = RabKegiatanDetails::where('fkRabKegiatan_id',$kegiatans->id)->orderBy('divisi','ASC')->get();
        
        return view('keuangan.rab_kegiatan_public', [
            'ids' => $ids,
            'detail_of' => $kegiatans,
            'detail_kegiatans' => $detail_kegiatans,
        ]);
    }

    public function store_detail_rab_kegiatan(Request $request){
        if($request->input('id')==""){
            if($request->input('status')!="approved"){
                $biaya = explode("RP ", $request->input('biaya'));
                $biaya = preg_replace('/\./', '',$biaya[1]);
                $create = RabKegiatanDetails::create([
                    'fkRabKegiatan_id' => $request->input('parent_id_detail'),
                    'uraian' => $request->input('uraian'),
                    'qty' => $request->input('qty'),
                    'satuan' => $request->input('satuan'),
                    'biaya' => $biaya,
                    'realisasi' => $request->input('realisasi'),
                    'divisi' => $request->input('divisi'),
                ]);
                return redirect()->route('rab kegiatan public',$request->input('ids'));
            }else{
                return redirect()->route('rab kegiatan public',$request->input('ids'))->withErrors(['failed' => 'Status Approved tidak dapat menambah item baru']);
            }
        }
        // else{
        //     $create = RabKegiatanDetails::find($request->input('id'));
        //     $create->uraian = $request->input('uraian');
        //     $create->qty = $request->input('qty');
        //     $create->satuan = $request->input('satuan');
        //     $create->biaya = $request->input('biaya');
        //     $create->qty_realisasi = $request->input('qty_realisasi');
        //     $create->satuan_realisasi = $request->input('satuan_realisasi');
        //     $create->biaya_realisasi = $request->input('biaya_realisasi');
        //     $create->divisi = $request->input('divisi');
        //     $create->save();
        //     return redirect()->route('rab kegiatan public',$request->input('ids'));
        // }
    }

    public function store_detail_by_field(Request $request){
        $datax = RabKegiatanDetails::find($request->input('id'));
        if($datax->kegiatan->status=='rejected'){
            return json_encode(array("status" => false, "message" => "Rejected"));
        }elseif(($datax->kegiatan->status=='draft' && $request->input('t')==1) || ($datax->kegiatan->status=='approved' && $request->input('t')==2) || $request->input('field')=='uraian'){
            $field = $request->input('field');
            $datax->$field = $request->input('value');
            if($datax->save()){
                return json_encode(array("status" => true));
            }else{
                return json_encode(array("status" => false));
            }
        }else{
            return json_encode(array("status" => false, "message" => "Data tidak dapat diperbarui"));
        }
    }    

    public function delete_detail_rab_kegiatan($id){
        $data = RabKegiatanDetails::find($id);
        if ($data) {
            if ($data->delete()) {
                return json_encode(array("status" => true, "message" => 'Berhasil menghapus detail pengajuan'));
            } else {
                return json_encode(array("status" => false, "message" => 'Gagal menghapus detail pengajuan'));
            }
        } else {
            return json_encode(array("status" => false, "message" => 'Detail pengajuan tidak ditemukan'));
        }
    }

    public function laporan_pusat($select_bulan = null, $print = false)
    {
        $bulans = DB::table('jurnals')
                ->select(DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as ym'))
                ->groupBy('ym')
                ->orderBy('ym', 'DESC')
                ->get();

        if ($select_bulan == null) {
            $select_bulan = date('Y-m');
        }

        // laporan ke belakang
        $periode = CommonHelpers::periode();
        $periode = explode("-",$periode);
        $month = ['09','10','11','12','01','02','03','04','05','06','07','08'];
        $year = [$periode[0],$periode[0],$periode[0],$periode[0],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1],$periode[1]];
        $prev_total = null;

        $prev_saldo_awal = 0;
        $prev_saldo_jurnal = Jurnals::where('tanggal', '<', $year[0].'-'.$month[0].'-01')->orderBy('tanggal','ASC')->get();
        if($prev_saldo_jurnal!=null){
            foreach($prev_saldo_jurnal as $j){
                if($j->jenis=="in"){
                    $prev_saldo_awal = $prev_saldo_awal + ($j->qty*$j->nominal);
                }else if($j->jenis=="out"){
                    $prev_saldo_awal = $prev_saldo_awal - ($j->qty*$j->nominal);
                }
            }
        }

        for($x=0;$x<12;$x++){
            $prev_total_in = 0;
            $prev_total_out_rutin = 0;
            $prev_total_out_nonrutin = 0;
            $prev_year_month = date_format(date_create($year[$x].'-'.$month[$x]), 'Y-m');

            $prev_jurnals = Jurnals::where('tanggal', 'like', $prev_year_month . '%')->orderBy('tanggal','ASC')->get();
            $prev_total[$prev_year_month]['saldo_awal'] = $prev_saldo_awal;

            foreach($prev_jurnals->where('jenis','in')->whereNull('sub_jenis') as $in){
                $prev_total_in = $prev_total_in + ($in->qty*$in->nominal);
            }
            $prev_total[$prev_year_month]['total_in'] = $prev_total_in;
            $prev_saldo_awal = $prev_saldo_awal + $prev_total_in;

            foreach($prev_jurnals->where('jenis','out')->where('tipe_pengeluaran','Rutin')->whereNull('sub_jenis') as $outr){
                $prev_total_out_rutin = $prev_total_out_rutin + ($outr->qty*$outr->nominal);
            }
            $prev_total[$prev_year_month]['total_out_rutin'] = $prev_total_out_rutin;
            $prev_saldo_awal = $prev_saldo_awal - $prev_total_out_rutin;

            foreach($prev_jurnals->where('jenis','out')->where('tipe_pengeluaran','Non Rutin')->whereNull('sub_jenis') as $outnr){
                $prev_total_out_nonrutin = $prev_total_out_nonrutin + ($outnr->qty*$outnr->nominal);
            }
            $prev_total[$prev_year_month]['total_out_nonrutin'] = $prev_total_out_nonrutin;
            $prev_saldo_awal = $prev_saldo_awal - $prev_total_out_nonrutin;

            $prev_total[$prev_year_month]['saldo_akhir'] = $prev_saldo_awal;
        }
        // echo "<pre>".json_encode($prev_total, JSON_PRETTY_PRINT)."</pre>"; 
        // exit;
        // end

        $nextmonth = strtotime('+1 month', strtotime($select_bulan));
        $nextmonth = date('Y-m', $nextmonth);
        $rabs = Rabs::where('periode_tahun', CommonHelpers::periode())->where('biaya','!=',0)->orderBy('fkDivisi_id','ASC')->get();

        if($select_bulan=="all"){
            $jurnals = Jurnals::orderBy('tanggal','ASC')->get();
        }else{
            $jurnals = Jurnals::where('tanggal', 'like', $select_bulan . '%')->orderBy('tanggal','ASC')->get();
        }

        $total_in = 0;
        foreach($jurnals->where('jenis','in')->whereNull('sub_jenis') as $in){ // ->where('fkBank_id',2)
            $total_in = $total_in + ($in->qty*$in->nominal);
        }

        $total_out_rutin = 0;
        foreach($jurnals->where('jenis','out')->where('tipe_pengeluaran','Rutin')->whereNull('sub_jenis') as $outr){
            $total_out_rutin = $total_out_rutin + ($outr->qty*$outr->nominal);
        }
        $total_out_nonrutin = 0;
        foreach($jurnals->where('jenis','out')->where('tipe_pengeluaran','Non Rutin')->whereNull('sub_jenis') as $outnr){
            $total_out_nonrutin = $total_out_nonrutin + ($outnr->qty*$outnr->nominal);
        }

        $manag_building = $jurnals->whereNotNull('fkRabManagBuilding_id')->where('fkRabManagBuilding_id','!=',0);
        $rab_kegiatan = $jurnals->whereNotNull('fkRabKegiatan_id')->where('fkRabKegiatan_id','!=',0);

        $pengajuan_manag_buildings = RabManagBuildings::where('status','submit')->get();
        
        $saldo_awal_kubmt = 0;
        $saldo_awal_bendahara = 0;
        if($select_bulan!='all'){
            $saldo_jurnal = Jurnals::where('tanggal', '<', $select_bulan.'-01')->orderBy('tanggal','ASC')->get();
            if($saldo_jurnal!=null){
                foreach($saldo_jurnal->where('fkBank_id',2) as $j){
                    if($j->jenis=="in"){
                        $saldo_awal_kubmt = $saldo_awal_kubmt + ($j->qty*$j->nominal);
                    }else if($j->jenis=="out"){
                        $saldo_awal_kubmt = $saldo_awal_kubmt - ($j->qty*$j->nominal);
                    }
                }
                foreach($saldo_jurnal->where('fkBank_id',1) as $j){
                    if($j->jenis=="in"){
                        $saldo_awal_bendahara = $saldo_awal_bendahara + ($j->qty*$j->nominal);
                    }else if($j->jenis=="out"){
                        $saldo_awal_bendahara = $saldo_awal_bendahara - ($j->qty*$j->nominal);
                    }
                }
            }
        }

        $list_periode = Sodaqoh::select('periode')->groupBy('periode')->get();
        $last_update = SodaqohHistoris::select('updated_at')->orderBy('updated_at', 'DESC')->limit(1)->first();
        
        return view('keuangan.laporan_pusat', [
            'print' => $print,
            'saldo_awal_kubmt' => $saldo_awal_kubmt,
            'saldo_awal_bendahara' => $saldo_awal_bendahara,
            'jurnals' => $jurnals,
            'bulans' => $bulans,
            'select_bulan' => $select_bulan,
            'manag_building' => $manag_building,
            'rab_kegiatan' => $rab_kegiatan,
            'pengajuan_manag_buildings' => $pengajuan_manag_buildings,
            'nextmonth' => $nextmonth,
            'rabs' => $rabs,
            'total_in' => $total_in,
            'total_out_rutin' => $total_out_rutin,
            'total_out_nonrutin' => $total_out_nonrutin,
            'prev_total' => $prev_total,
            'list_periode' => $list_periode,
            'last_update' => $last_update,
        ]);
    }

    public function kalender_ppm()
    {
        $today = date('Y-m-d', strtotime(today()));
        $pengajars = DewanPengajars::all();
        $template = KalenderPpmTemplates::select('sequence')->groupBy('sequence')->get();
        $templates = KalenderPpmTemplates::orderBy('waktu', 'ASC')->get();
        $kalenders = KalenderPpms::get();
        return view('kalender_ppm', ['today' => $today, 'pengajars' => $pengajars, 'template' => $template, 'templates' => $templates, 'kalenders' => $kalenders]);
    }
}

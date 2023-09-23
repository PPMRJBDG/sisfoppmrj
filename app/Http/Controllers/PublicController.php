<?php

namespace App\Http\Controllers;

use App\Helpers\WaSchedules;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;
use App\Helpers\PresenceGroupsChecker;
use App\Models\Presence;
use App\Models\Present;
use App\Models\Permit;
use App\Models\User;
use App\Models\Settings;
use App\Models\Liburan;
use App\Models\PresenceGroup;
use App\Models\Pelanggaran;
use App\Models\Sodaqoh;
use App\Models\Materi;
use App\Models\Santri;
use App\Models\JenisPelanggaran;
use App\Models\ReportScheduler;
use App\Models\SpWhatsappPhoneNumbers;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    // 0 8 * * * https://sisfo.ppmrjbandung.com/schedule/daily
    // 0 6 1 * * https://sisfo.ppmrjbandung.com/schedule/monthly

    public function schedule($time, $presence_id = null)
    {
        $setting = Settings::find(1);
        $contact_id = $setting->wa_ortu_group_id;
        $name = '';
        $caption = '';
        $yesterday = strtotime('-1 day', strtotime(date("Y-m-d")));
        $yesterday = date('Y-m-d', $yesterday);

        // DAILY
        if ($time == 'daily') {
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
                        WaSchedules::save($caption, $caption, 'wa_dewanguru_group_id');
                        echo json_encode(['status' => true, 'message' => $caption]);
                    }
                }
            }

            // bulk presensi harian ke wa group ortu
            $check_liburan = Liburan::where('liburan_from', '<', $yesterday)->where('liburan_to', '>', $yesterday)->get();
            if (count($check_liburan) == 0) {
                //                 $list_angkatan = DB::table('santris')
                //                     ->select('angkatan')
                //                     ->whereNull('exit_at')
                //                     ->groupBy('angkatan')
                //                     ->orderBy('angkatan', 'ASC')
                //                     ->get();

                //                 $angkatan_caption = '';
                //                 foreach ($list_angkatan as $la) {
                //                     $angkatan_caption = $angkatan_caption . '*Angkatan ' . $la->angkatan . '*
                // ';
                //                     $angkatan_caption = $angkatan_caption . CommonHelpers::settings()->host_url . '/daily/' . date_format(date_create($yesterday), "Y/m/d") . '/' . $la->angkatan . '

                // ';
                //                 }

                //                 $caption = 'Berikut kami informasikan daftar kehadiran pada hari *' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M Y") . '*.
                // Silahkan klik link dibawah ini sesuai angkatannya:

                // ' . $angkatan_caption;

                $caption = 'Berikut kami informasikan daftar kehadiran pada hari *' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M Y") . '*.

*Total Mahasiswa: ' . CountDashboard::total_mhs('all') . '*';
                $get_presence = Presence::where('event_date', $yesterday)->get();
                if (count($get_presence) > 0) {
                    foreach ($get_presence as $presence) {
                        // hadir
                        $presents = CountDashboard::mhs_hadir($presence->id, 'all');

                        // ijin berdasarkan lorong masing2
                        $permits = CountDashboard::mhs_ijin($presence->id, 'all');

                        // alpha
                        $mhs_alpha = CountDashboard::mhs_alpha($presence->id, 'all');

                        $caption = $caption . '
________________________
*_' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M") . ' | ' . $presence->name . '_*
Hadir: ' . count($presents) . '
Ijin: ' . count($permits) . '
Alpha: ' . count($mhs_alpha) . '

';
                        if (count($mhs_alpha) > 0) {
                            $caption = $caption . '*Daftar Mahasiswa Alpha*
';
                            foreach ($mhs_alpha as $d) {
                                $caption = $caption . '- ' . $d['name'] . ' [' . $d['angkatan'] . ']
';
                            }
                        }
                    }
                }
                $caption = $caption . '
NB:
- Apabila terdapat ketidaksesuaian, amalsholih menghubungi pengurus';

                $name = '[Ortu Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");
                if ($contact_id != '') {
                    $insert = WaSchedules::save($name, $caption, $contact_id);
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
            // laporan presensi mingguan
            // bulk mahasiswa + ortu
            // jika all_ijin > 1/3 KBM diberi peringatan
        }

        // MONTHLY
        elseif ($time == 'monthly') {
            // daftar mahasiswa yang presensi < 80%
            $last_month = strtotime('-1 month', strtotime(date("Y-m-d")));
            $last_month = date('Y-m', $last_month);
            $list_angkatan = DB::table('santris')
                ->select('angkatan')
                ->whereNull('exit_at')
                ->groupBy('angkatan')
                ->get();
            foreach ($list_angkatan as $la) {
                $result = (new HomeController)->dashboard($last_month, $la->angkatan, true);
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
                        foreach ($presences[$pg->id] as $listcp) {
                            if ($listcp->santri_id == $vu->santri_id) {
                                $ijin = 0;
                                if (isset($all_permit[$pg->id][$vu->santri_id])) {
                                    $ijin = $all_permit[$pg->id][$vu->santri_id];
                                }
                                $all_kbm = $all_kbm + $all_presences[$pg->id][0]->c_all;
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
                            $store['fkSantri_id'] = $vu->santri_id;
                            $store['fkJenis_pelanggaran_id'] = 14; // Amrin Jami' Tanpa Ijin
                            $store['tanggal_melanggar'] = date("Y-m-d");
                            $store['keterangan'] = 'Presensi kehadiran ' . $last_month . ': ' . $all_persentase . '%';
                            $store['is_archive'] = 0;
                            $data = Pelanggaran::create($store);
                            if ($data) {
                                $jenis_pelanggaran = JenisPelanggaran::find(14);
                                $caption = 'Penambahan data pelanggaran dari Mahasiswa:
- Nama: *' . $data->santri->user->fullname . '*
- Angkatan: *' . $data->santri->angkatan . '*
- Jenis Pelanggaran: *' . $jenis_pelanggaran->jenis_pelanggaran . '*
- Kategori: *' . $jenis_pelanggaran->kategori_pelanggaran . '*
- Ket: *Presensi kehadiran ' . $last_month . ': ' . $all_persentase . '%*';
                                $contact_id = 'wa_ketertiban_group_id';
                                WaSchedules::save('Amrin Jami Tanpa Ijin: ' . $data->santri->user->fullname, $caption, $contact_id);
                            } else {
                                $contact_id = 'wa_ketertiban_group_id';
                                WaSchedules::save('[Gagal] Amrin Jami Tanpa Ijin: ' . $data->santri->user->fullname, 'Gagal menambahkan data pelanggaran Amrin Jami Tanpa Ijin: ' . $data->santri->user->fullname, $contact_id);
                            }
                        }
                    }
                }
            }

            // kirim absensi bulanan - bulk ortu
            $view_usantri = DB::table('v_user_santri')->whereNotNull('nohp_ortu')->orderBy('fullname', 'ASC')->get();
            $time_post = 1;
            foreach ($view_usantri as $vs) {
                $check_report = ReportScheduler::where('fkSantri_id', $vs->santri_id)->first();
                if ($check_report == null) {
                    $create_report = ReportScheduler::create([
                        'fkSantri_id' => $vs->santri_id,
                        'link_url' => $setting->host_url . '/report/' . $vs->ids,
                        'month' => date("m"),
                        'status' => 0,
                        'scheduler' => 0,
                        'ids' => $vs->ids
                    ]);
                } else {
                    $check_report->month = date("m");
                    $check_report->status = 0;
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
                            $caption = 'Berikut kami informasikan laporan mahasiswa an. ' . $vs->fullname . '
Silahkan klik link dibawah ini:
' . $setting->host_url . '/report/' . $vs->ids;
                            WaSchedules::save('All Report: ' . $vs->fullname, $caption, $wa_phone->pid, $time_post);
                        }
                        $time_post++;
                    }
                }
            }

            echo json_encode(['status' => true, 'message' => 'success running scheduler']);
        }

        // LINK PRESENSI
        elseif ($time == 'presence') {
            PresenceGroupsChecker::checkPresenceGroups();
            $get_presence_today = Presence::where('event_date', date("Y-m-d"))->where('fkPresence_group_id', $presence_id)->first();
            if ($get_presence_today != null) {
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

                $contact_id = 'wa_ketertiban_group_id';
                $caption = 'Link Presensi *' . $get_presence_today->name . '*:
' . $setting->host_url . '/presensi/list/' . $get_presence_today->id . '

Amalsholih dicek kembali, yang *Tidak Hadir* diubah jadi alpha.
Besok pukul 12:00 WIB sistem akan mengirim laporan presensi ke group orangtua.';
                WaSchedules::save('Link Presensi', $caption, $contact_id, 1, true);
                WaSchedules::save('Link Presensi', $caption, 'Bulk Koor Lorong', 2, true);
            }
        } elseif ($time == 'jam-malam') {
            $contact_id = 'wa_ketertiban_group_id';
            $caption = '*Waktu sudah menunjukan pukul 22:50*, waktunya mengingatkan:

- Kepada rekan-rekan yang masih berada di luar lingkungan PPM untuk bisa segera kembali ke PPM
- Segera istirahat, tidak ada keributan yang mengganggu tetangga sebelah
- Piket jaga malam sesuai tugas amalsholihnya
- Mematikan Wifi
- Mematikan listrik yang tidak digunakan
- Mengecek gerbang
- *Evaluasi diri, mudah-mudahan Allah menjadikan kita kefahaman dan pribadi yang lebih baik lagi :)*';
            WaSchedules::save('Jam Malam ' . date('d-m-Y'), $caption, $contact_id, 1, true);
        }
    }

    public function report($ids)
    {
        $rs = ReportScheduler::where('ids', $ids)->first();
        if ($rs != null) {
            $last_update = date_format(date_create($rs->updated_at), "Y-m-d");
            $today = date('Y-m-d');
            if ($last_update != $today) {
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
            ->groupBy('ym')
            ->get();

        $tahun = DB::table('presences as a')
            ->select(DB::raw('DATE_FORMAT(a.event_date, "%Y") as y'))
            ->leftJoin('presents as b', function ($join) {
                $join->on('a.id', '=', 'b.fkPresence_id');
            })
            ->where('b.fkSantri_id', $santri_id)
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

                    $permit = DB::select("SELECT a.fkSantri_id, count(a.fkSantri_id) as approved FROM `permits` a JOIN `presences` b ON a.fkPresence_id=b.id WHERE a.fkSantri_id = $santri_id AND a.status='approved' AND b.event_date LIKE '%" . $tb->ym . "%' AND b.fkPresence_group_id = " . $pg->id . " GROUP BY a.fkSantri_id");
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
                        <td class="p-0"><h6 class="mb-0">' . ucfirst(strtolower($materi->name)) . '</h6></td>
                        <td class="p-0"><h6 class="mb-0">' . $totalPages . ' / ' . $materi->pageNumbers . '</h6></td>
                        <td class="p-0"><h6 class="mb-0">' . number_format((float) $totalPages / $materi->pageNumbers * 100, 2, ".", "") . '%</h6></td>
                    </tr>';
                }
            }
        }

        return view('report.all_report', [
            'santri' => $santri,
            'tahun' => $tahun,
            'tahun_bulan' => $tahun_bulan,
            'presence_group' => $presence_group,
            'datapg' => $datapg,
            'data_materi' => $data_materi,
            'pelanggaran' => $pelanggaran,
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
            } else {
                $message = 'Permintaan ijin sudah ditolak.';
            }
        } else {
            $message = 'Perijinan tidak ditemukan.';
        }
        return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }

    public function reject_permit($ids)
    {
        $permit = Permit::where('ids', $ids)->first();
        $message = '';
        if ($permit != null) {
            if ($permit->status == 'approved') {
                $permit->status = 'rejected';
                if ($permit->save()) {
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
                            $caption = 'Perijinan Anda di Tolak oleh Pengurus.';
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
                            $caption = 'Perijinan *' . $permit->santri->user->fullname . '* di Tolak oleh Pengurus.';
                            WaSchedules::save($name, $caption, $wa_phone->pid, 2);
                        }
                    }

                    $message = 'Permintaan ijin berhasil ditolak.';
                }
            } else {
                $message = 'Permintaan ijin sudah ditolak.';
            }
        } else {
            $message = 'Perijinan tidak ditemukan.';
        }

        return view('presence.view_permit', ['permit' => $permit, 'message' => $message]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\WaSchedules;
use App\Helpers\CommonHelpers;
use App\Models\Presence;
use App\Models\Permit;
use App\Models\User;
use App\Models\Settings;
use App\Models\Liburan;
use App\Models\PresenceGroup;
use App\Models\Pelanggaran;
use App\Models\Materi;
use App\Models\Santri;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function report_schedule($time)
    {
        $setting = Settings::find(1);
        $contact_id = $setting->wa_ortu_group_id;
        $name = '';
        $caption = '';
        $yesterday = strtotime('-1 day', strtotime(date("Y-m-d")));
        $yesterday = date('Y-m-d', $yesterday);

        // DAILY
        if ($time == 'daily') {
            $check_liburan = Liburan::where('liburan_from', '<', $yesterday)->where('liburan_to', '>', $yesterday)->get();
            if (count($check_liburan) == 0) {
                $list_angkatan = DB::table('santris')
                    ->select('angkatan')
                    ->whereNull('exit_at')
                    ->groupBy('angkatan')
                    ->get();

                $angkatan_caption = '';
                foreach ($list_angkatan as $la) {
                    $angkatan_caption = $angkatan_caption . '*Angkatan ' . $la->angkatan . '*
';
                    $angkatan_caption = $angkatan_caption . CommonHelpers::settings()->host_url . '/daily/' . date_format(date_create($yesterday), "Y/m/d") . '/' . $la->angkatan . '

';
                }
                $name = '[Ortu Group] Daily Report ' . date_format(date_create($yesterday), "d M Y");

                $caption = '
*[SISFO PPMRJ]*

Assalamualaikum Ayah Bunda, berikut kami informasikan daftar kehadiran pada hari ' . CommonHelpers::hari_ini(date_format(date_create($yesterday), "D")) . ', ' . date_format(date_create($yesterday), "d M Y") . '.
Silahkan klik link dibawah ini sesuai angkatannya:

' . $angkatan_caption . '
Alhamdulillah Jazakumullohu Khoiro 😇🙏🏻
';

                if ($contact_id != '') {
                    $insert = WaSchedules::report_schedule($contact_id, $name, $caption);
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
        }

        // MONTHLY
        elseif ($time == 'monthly') {
        }

        // ALL REPORT
        elseif ($time == 'all_report') {
        }
    }

    public function report($nohp, $ids)
    {
        $santri = Santri::where('id', $ids)->where('nohp_ortu', $nohp)->first();
        // get all tahun bulan
        $tahun_bulan = DB::table('presences as a')
            ->select(DB::raw('DATE_FORMAT(a.event_date, "%Y-%m") as ym'))
            ->leftJoin('presents as b', function ($join) {
                $join->on('a.id', '=', 'b.fkPresence_id');
            })
            ->where('b.fkSantri_id', $ids)
            ->orderBy('ym', 'DESC')
            ->groupBy('ym')
            ->get();

        $tahun = DB::table('presences as a')
            ->select(DB::raw('DATE_FORMAT(a.event_date, "%Y") as y'))
            ->leftJoin('presents as b', function ($join) {
                $join->on('a.id', '=', 'b.fkPresence_id');
            })
            ->where('b.fkSantri_id', $ids)
            ->orderBy('y', 'DESC')
            ->groupBy('y')
            ->get();

        // loop presensi berdasarkan tahun bulan
        $presence_group = PresenceGroup::get();
        $datapg = array();
        foreach ($tahun_bulan as $tb) {
            $presences = DB::table('presences as a')
                ->leftJoin('presents as b', function ($join) use ($ids) {
                    $join->on('a.id', '=', 'b.fkPresence_id');
                    $join->where('b.fkSantri_id', $ids);
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
                    $permit = DB::select("SELECT a.fkSantri_id, count(a.fkSantri_id) as approved FROM `permits` a JOIN `presences` b ON a.fkPresence_id=b.id WHERE a.fkSantri_id = $ids AND a.status='approved' AND a.created_at LIKE '%" . $tb->ym . "%' AND b.fkPresence_group_id = " . $pg->id . " GROUP BY a.fkSantri_id");
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
        $pelanggaran = Pelanggaran::where('fkSantri_id', $ids)->whereNotNull('keringanan_sp')->get();

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
                $data_materi = $data_materi . '
            <tr class="text-sm">
                <td class="p-0">' . ucfirst(strtolower($materi->name)) . '</td>
                <td class="p-0">' . $totalPages . ' / ' . $materi->pageNumbers . '</td>
                <td class="p-0">' . number_format((float) $totalPages / $materi->pageNumbers * 100, 2, ".", "") . '%</td>
            </tr>';
            }
        }

        return view('report.all_report', [
            'santri' => $santri,
            'tahun' => $tahun,
            'tahun_bulan' => $tahun_bulan,
            'presence_group' => $presence_group,
            'datapg' => $datapg,
            'data_materi' => $data_materi,
            'pelanggaran' => $pelanggaran
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

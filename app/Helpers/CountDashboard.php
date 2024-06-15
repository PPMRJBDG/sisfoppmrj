<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\ModelHasRole;
use App\Models\Presence;
use App\Models\Permit;
use App\Models\Present;
use App\Models\Lorong;

class CountDashboard
{
    public static function index()
    {
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->orderBy('angkatan', 'ASC')
            ->groupBy('angkatan')
            ->get();

        $content_tr = '';
        $total_muballigh_laki = 0;
        $total_muballigh_perempuan = 0;
        $total_reguler_laki = 0;
        $total_reguler_perempuan = 0;
        foreach ($list_angkatan as $la) {
            $view_usantri = DB::table('v_user_santri')->where('angkatan', $la->angkatan)->get();

            $muballigh_laki = 0;
            $muballigh_perempuan = 0;
            $reguler_laki = 0;
            $reguler_perempuan = 0;
            foreach ($view_usantri as $vs) {
                $check_is_mt = ModelHasRole::where('model_id', $vs->id)->where('role_id', 10)->first();
                if ($check_is_mt == null) {
                    if ($vs->gender == 'male') {
                        $reguler_laki++;
                    } elseif ($vs->gender == 'female') {
                        $reguler_perempuan++;
                    }
                } else {
                    if ($vs->gender == 'male') {
                        $muballigh_laki++;
                    } elseif ($vs->gender == 'female') {
                        $muballigh_perempuan++;
                    }
                }
            }

            $total_muballigh_laki += $muballigh_laki;
            $total_muballigh_perempuan += $muballigh_perempuan;
            $total_reguler_laki += $reguler_laki;
            $total_reguler_perempuan += $reguler_perempuan;

            $content_tr =  $content_tr . '
                                    <tr>
                                        <td class="text-uppercase text-center text-sm">' . $la->angkatan . '</td>
                                        <td class="text-uppercase text-center text-sm">' . $muballigh_laki . '</td>
                                        <td class="text-uppercase text-center text-sm">' . $muballigh_perempuan . '</td>
                                        <td class="text-uppercase text-center text-sm">' . $reguler_laki . '</td>
                                        <td class="text-uppercase text-center text-sm">' . $reguler_perempuan . '</td>
                                        <td class="text-uppercase text-center text-sm">' . ($muballigh_laki + $muballigh_perempuan + $reguler_laki + $reguler_perempuan) . '</td>
                                    </tr>
                        ';
        }

        $content_body = '
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">ANGKATAN</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">MT (L)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">MT (P)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">REG (L)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">REG (P)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                            ' . $content_tr . '
                            </tbody>
                            <tfooter>                            
                            <tr>
                                <th class="text-uppercase text-center text-sm font-weight-bolder"></th>
                                <th class="text-uppercase text-center text-sm font-weight-bolder">' . $total_muballigh_laki . '</th>
                                <th class="text-uppercase text-center text-sm font-weight-bolder">' . $total_muballigh_perempuan . '</th>
                                <th class="text-uppercase text-center text-sm font-weight-bolder">' . $total_reguler_laki . '</th>
                                <th class="text-uppercase text-center text-sm font-weight-bolder">' . $total_reguler_perempuan . '</th>
                                <th class="text-uppercase text-center text-sm font-weight-bolder">' . ($total_muballigh_laki + $total_muballigh_perempuan + $total_reguler_laki + $total_reguler_perempuan) . '</th>
                            </tr>
                            </tfooter>
                        </table>
                    </div>';

        return $content_body;
    }

    public static function total_mhs($for, $lorong = '-')
    {
        $view_usantri = array();
        if ($for == 'all') {
            if ($lorong == '-') {
                $view_usantri = DB::table('v_user_santri')->orderBy('fullname', 'ASC')->get();
            } else {
                $view_usantri = DB::table('v_user_santri')->where('fkLorong_id', $lorong)->orderBy('fullname', 'ASC')->get();
            }
        } elseif ($for == 'lorong') {
            if (auth()->user()->santri->lorongUnderLead) {
                $view_usantri = auth()->user()->santri->lorongUnderLead->members;
            }
        } else {
            $view_usantri = DB::table('v_user_santri')->where('angkatan', $for)->orderBy('fullname', 'ASC')->get();
        }
        return count($view_usantri);
    }

    public static function mhs_hadir($presence_id, $for, $lorong = '-')
    {
        $presence = Presence::find($presence_id);
        $presents = null;

        if ($for == 'all') {
            if ($presence != null) {
                if ($lorong == '-') {
                    $presents = $presence->presents()
                        ->select('presents.*')
                        ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                        ->join('users', 'users.id', '=', 'santris.fkUser_id')
                        ->whereNull('santris.exit_at')
                        ->orderBy('users.fullname')
                        ->get();
                } else {
                    $presents = $presence->presents()
                        ->select('presents.*')
                        ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                        ->join('users', 'users.id', '=', 'santris.fkUser_id')
                        ->whereNull('santris.exit_at')
                        ->where('santris.fkLorong_id', $lorong)
                        ->orderBy('users.fullname')
                        ->get();
                }
            }
        } elseif ($for == 'lorong') {
            $lorong = Lorong::where('fkSantri_leaderId', auth()->user()->santri->id)->first();
            if ($lorong != null) {
                $presents = $presence->presents()
                    ->select('presents.*')
                    ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                    ->join('users', 'users.id', '=', 'santris.fkUser_id')
                    ->where('fkLorong_id', $lorong->id)
                    ->orderBy('users.fullname')
                    ->get();
            }
        } else {
            if ($presence != null) {
                $presents = $presence->presents()
                    ->select('presents.*')
                    ->join('santris', 'santris.id', '=', 'presents.fkSantri_id')
                    ->join('users', 'users.id', '=', 'santris.fkUser_id')
                    ->where('santris.angkatan', $for)
                    ->whereNull('santris.exit_at')
                    ->orderBy('users.fullname')
                    ->get();
            }
        }

        return $presents;
    }

    public static function mhs_ijin($presence_id, $for, $lorong = '-')
    {
        $arr_santri = [];
        if ($for == 'all') {
            if ($lorong == '-') {
                $permits = Permit::where('fkPresence_id', $presence_id)->where('status', 'approved')->get();
            } else {
                $view_usantri = DB::table('v_user_santri')->where('fkLorong_id', $lorong)->orderBy('fullname', 'ASC')->get();
                foreach ($view_usantri as $vu) {
                    $arr_santri[] = $vu->santri_id;
                }
                $permits = Permit::where('fkPresence_id', $presence_id)->where('status', 'approved')->whereIn('fkSantri_id', $arr_santri)->get();
            }
        } elseif ($for == 'lorong') {
            if (auth()->user()->santri->lorongUnderLead) {
                foreach (auth()->user()->santri->lorongUnderLead->members as $santri) {
                    $arr_santri[] = $santri->id;
                }
            }
            $permits = Permit::where('fkPresence_id', $presence_id)->where('status', 'approved')->whereIn('fkSantri_id', $arr_santri)->get();
        } else {
            $view_usantri = DB::table('v_user_santri')->where('angkatan', $for)->orderBy('fullname', 'ASC')->get();
            foreach ($view_usantri as $santri) {
                $arr_santri[] = $santri->id;
            }
            $permits = Permit::where('fkPresence_id', $presence_id)->where('status', 'approved')->whereIn('fkSantri_id', $arr_santri)->get();
        }

        return $permits;
    }

    public static function mhs_alpha($presence_id, $for, $event_date, $lorong = '-')
    {

        $mhs_alpha = array();
        if ($for == 'all') {
            $event_angkatan = explode("-", $event_date);
            if (intval($event_angkatan[1]) < 9) {
                if ($lorong == '-') {
                    $view_usantri = DB::table('v_user_santri')
                        ->where('angkatan', '<', $event_angkatan[0])
                        ->orderBy('fullname', 'ASC')->get();
                } else {
                    $view_usantri = DB::table('v_user_santri')
                        ->where('angkatan', '<', $event_angkatan[0])
                        ->where('fkLorong_id', $lorong)
                        ->orderBy('fullname', 'ASC')->get();
                }
            } else {
                if ($lorong == '-') {
                    $view_usantri = DB::table('v_user_santri')
                        ->where('angkatan', '<=', $event_angkatan[0])
                        ->orderBy('fullname', 'ASC')->get();
                } else {
                    $view_usantri = DB::table('v_user_santri')
                        ->where('angkatan', '<=', $event_angkatan[0])
                        ->where('fkLorong_id', $lorong)
                        ->orderBy('fullname', 'ASC')->get();
                }
            }

            foreach ($view_usantri as $mhs) {
                $check_alpha = Present::where('fkPresence_id', $presence_id)
                    ->where('fkSantri_id', $mhs->santri_id)
                    ->first();
                if ($check_alpha == null) {
                    $check_permit = Permit::where('fkPresence_id', $presence_id)
                        ->where('status', 'approved')
                        ->where('fkSantri_id', $mhs->santri_id)->first();
                    if ($check_permit == null) {
                        $mhs_alpha[$mhs->santri_id]['presence_id'] = $presence_id;
                        $mhs_alpha[$mhs->santri_id]['santri_id'] = $mhs->santri_id;
                        $mhs_alpha[$mhs->santri_id]['name'] = $mhs->fullname;
                        $mhs_alpha[$mhs->santri_id]['angkatan'] = $mhs->angkatan;
                        $mhs_alpha[$mhs->santri_id]['nohp_ortu'] = $mhs->nohp_ortu;
                        $mhs_alpha[$mhs->santri_id]['fkLorong_id'] = $mhs->fkLorong_id;
                        $lorong = Lorong::find($mhs->fkLorong_id);
                        if ($lorong == '-') {
                            $mhs_alpha[$mhs->santri_id]['lorong'] = '-';
                        } else {
                            if (!$lorong) {
                                $mhs_alpha[$mhs->santri_id]['lorong'] = 'Belum ditentukan lorongnya';
                            } else {
                                $mhs_alpha[$mhs->santri_id]['lorong'] = $lorong->leader->user->fullname . ' - ' . $lorong->leader->user->nohp;
                            }
                        }
                    }
                }
            }
        } elseif ($for == 'lorong') {
            if (auth()->user()->santri->lorongUnderLead) {
                foreach (auth()->user()->santri->lorongUnderLead->members as $mhs) {
                    $check_alpha = Present::where('fkPresence_id', $presence_id)
                        ->where('fkSantri_id', $mhs->id)
                        ->first();
                    if ($check_alpha == null) {
                        $check_permit = Permit::where('fkPresence_id', $presence_id)
                            ->where('status', 'approved')
                            ->where('fkSantri_id', $mhs->id)->first();
                        if ($check_permit == null) {
                            $mhs_alpha[$mhs->id]['presence_id'] = $presence_id;
                            $mhs_alpha[$mhs->id]['santri_id'] = $mhs->id;
                            $mhs_alpha[$mhs->id]['name'] = $mhs->user->fullname;
                            $mhs_alpha[$mhs->id]['angkatan'] = $mhs->angkatan;
                            $mhs_alpha[$mhs->id]['nohp_ortu'] = $mhs->nohp_ortu;
                            $mhs_alpha[$mhs->id]['fkLorong_id'] = $mhs->fkLorong_id;
                            $lorong = Lorong::find($mhs->fkLorong_id);
                            if ($lorong == '-') {
                                $mhs_alpha[$mhs->id]['lorong'] = '-';
                            } else {
                                $mhs_alpha[$mhs->id]['lorong'] = $lorong->leader->user->fullname . ' - ' . $lorong->leader->user->nohp;
                            }
                        }
                    }
                }
            }
        } else {
            $view_usantri = DB::table('v_user_santri')->where('angkatan', $for)->orderBy('fullname', 'ASC')->get();
            foreach ($view_usantri as $mhs) {
                $check_alpha = Present::where('fkPresence_id', $presence_id)
                    ->where('fkSantri_id', $mhs->santri_id)
                    ->first();
                if ($check_alpha == null) {
                    $check_permit = Permit::where('fkPresence_id', $presence_id)
                        ->where('status', 'approved')
                        ->where('fkSantri_id', $mhs->santri_id)->first();
                    if ($check_permit == null) {
                        $mhs_alpha[$mhs->santri_id]['presence_id'] = $presence_id;
                        $mhs_alpha[$mhs->santri_id]['santri_id'] = $mhs->santri_id;
                        $mhs_alpha[$mhs->santri_id]['name'] = $mhs->fullname;
                        $mhs_alpha[$mhs->santri_id]['angkatan'] = $mhs->angkatan;
                        $mhs_alpha[$mhs->santri_id]['nohp_ortu'] = $mhs->nohp_ortu;
                        $mhs_alpha[$mhs->santri_id]['fkLorong_id'] = $mhs->fkLorong_id;
                        $lorong = Lorong::find($mhs->fkLorong_id);
                        // var_dump($lorong->leader->user->fullname);
                        if ($lorong == '-') {
                            $mhs_alpha[$mhs->santri_id]['lorong'] = '-';
                        } else {
                            if ($lorong == null) {
                                $mhs_alpha[$mhs->santri_id]['lorong'] = '-';
                            } else {
                                $mhs_alpha[$mhs->santri_id]['lorong'] = $lorong->leader->user->fullname . ' - ' . $lorong->leader->user->nohp;
                            }
                        }
                    }
                }
            }
        }
        return $mhs_alpha;
    }

    public static function sumPresentByPengajar($st, $presence_id, $pengajar_id)
    {
        if ($st == 'sum_kbm') {
            $get_presence = Presence::where('fkPresence_group_id', $presence_id)->where('fkDewan_pengajar_1', $pengajar_id)->orWhere('fkDewan_pengajar_2', $pengajar_id)->get();
            return count($get_presence);
        } elseif ($st == 'persentase') {
            $get_presence = Presence::where('fkDewan_pengajar_1', $pengajar_id)->orWhere('fkDewan_pengajar_2', $pengajar_id)->get();
            $persentase = 0;
            if ($get_presence != null) {
                $total_present = 0;
                $loop = 0;
                foreach ($get_presence as $gp) {
                    $loop++;
                    $get_present = Present::where('fkPresence_id', $gp->id)->get();
                    $total_present += (count($get_present) / $gp->total_mhs * 100);
                }
                if ($total_present > 0) {
                    $persentase = number_format($total_present / $loop, 2);
                }
            }
            return $persentase;
        } else {
            return 0;
        }
    }
}

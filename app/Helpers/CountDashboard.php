<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\ModelHasRole;

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
                            <thead class="thead-light">
                                <tr style="background-color:#f6f9fc;">
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">ANGKATAN</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">MUBALLIGH (L)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">MUBALLIGH (P)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">REGULER (L)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">REGULER (P)</th>
                                    <th class="text-uppercase text-center text-sm font-weight-bolder">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                            ' . $content_tr . '
                            </tbody>
                            <tfooter>                            
                            <tr style="background-color:#f6f9fc;">
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
}

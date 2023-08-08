<?php

namespace App\Helpers;

use App\Models\SpWhatsappContacts;
use App\Models\SpWhatsappPhoneNumbers;
use App\Models\Settings;

class CommonHelpers
{
    public static function settings()
    {
        return Settings::find(1);
    }

    public static function hari_ini($hari)
    {
        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return $hari_ini;
    }

    public static function createWaContact($request)
    {
        $team_id = Settings::find(1);
        if ($team_id != null) {
            $nomor_hp = array();
            if ($request->input('nohp') != null) {
                $nohp = $request->input('nohp');
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $nomor_hp[0]['nohp'] = $nohp;
                $nomor_hp[0]['name'] = 'PPM ' . $request->input('fullname');
            }
            if ($request->input('nohp_ortu') != null) {
                $nohp = $request->input('nohp_ortu');
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $nomor_hp[1]['nohp'] = $nohp;
                if ($request->input('nama_ortu') == '') {
                    $nomor_hp[1]['name'] = 'Ortu PPM ' . $request->input('fullname');
                } else {
                    $nomor_hp[1]['name'] = 'Ortu PPM ' . $request->input('nama_ortu');
                }
            }
            if (count($nomor_hp) > 0) {
                foreach ($nomor_hp as $data) {
                    $check_number = SpWhatsappPhoneNumbers::where('team_id', $team_id->wa_team_id)->where('phone', $data['nohp'])->get();

                    if (count($check_number) == 0) {
                        $contact_id = SpWhatsappContacts::create([
                            'ids' => uniqid(),
                            'team_id' => $team_id->wa_team_id,
                            'name' => $data['name'],
                            'status' => 1,
                            'changed' => time(),
                            'created' => time()
                        ]);

                        SpWhatsappPhoneNumbers::create([
                            'ids' => uniqid(),
                            'team_id' => $team_id->wa_team_id,
                            'pid' => $contact_id->id,
                            'phone' => $data['nohp']
                        ]);
                    }
                }
            }
        }
    }
}

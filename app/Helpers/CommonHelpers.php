<?php

namespace App\Helpers;

use App\Models\SpWhatsappContacts;
use App\Models\SpWhatsappPhoneNumbers;
use App\Models\Settings;
use App\Models\Periode;
use App\Models\User;
use App\Models\Santri;
use App\Models\Lorong;
use App\Models\Permit;
use App\Models\RabKegiatans;

class CommonHelpers
{
    public static function settings()
    {
        return Settings::find(1);
    }

    public static function periode()
    {
        $periode_tahun = Periode::latest('periode_tahun')->first();
        return $periode_tahun->periode_tahun;
    }

    public static function lorong($id)
    {
        if ($id == null) {
            return '-';
        } else {
            $get = Lorong::find($id);
            return $get->leader->user->fullname;
        }
    }

    public static function bulan()
    {
        return ['sept', 'okt', 'nov', 'des', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags'];
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
                $nomor_hp[0]['name'] = $team_id->org_name . ' ' . $request->input('fullname');
            }
            if ($request->input('nohp_ortu') != null) {
                $nohp = $request->input('nohp_ortu');
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $nomor_hp[1]['nohp'] = $nohp;
                if ($request->input('nama_ortu') == '') {
                    $nomor_hp[1]['name'] = 'Ortu ' . $team_id->org_name . ' ' . $request->input('fullname');
                } else {
                    $nomor_hp[1]['name'] = 'Ortu ' . $team_id->org_name . ' ' . $request->input('nama_ortu');
                }
            }
            if (count($nomor_hp) > 0) {
                foreach ($nomor_hp as $data) {
                    $check_number = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                        $query->where('name', 'NOT LIKE', '%Bulk%');
                    })->where('team_id', $team_id->wa_team_id)->where('phone', $data['nohp'])->get();

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
                    } else {
                        foreach ($check_number as $cn) {
                            $update = SpWhatsappContacts::find($cn->pid);
                            $update->name = $data['name'];
                            $update->save();
                        }
                    }
                }
            }
        }
    }

    public static function createBulk($name, $data, $field)
    {
        $team_id = Settings::find(1);
        $contact_id = null;
        $contact = SpWhatsappContacts::where('team_id', $team_id->wa_team_id)->where('name', $name)->get();
        if (count($contact) == 0) {
            $contact = SpWhatsappContacts::create([
                'ids' => uniqid(),
                'team_id' => $team_id->wa_team_id,
                'name' => $name,
                'status' => 1,
                'changed' => time(),
                'created' => time()
            ]);
            $contact_id = $contact->id;
        } else {
            $contact_id = $contact[0]->id;
            $getdel = SpWhatsappPhoneNumbers::where('pid', $contact_id)->where('team_id', $team_id->wa_team_id)->get();
            if ($getdel != null) {
                foreach ($getdel as $gd) {
                    $delete = SpWhatsappPhoneNumbers::find($gd->id);
                    $delete->delete();
                }
            }
        }
        if ($name == 'Bulk Koor Lorong') {
            foreach ($data as $d) {
                $nohp = $d->leader->user->nohp;
                if ($nohp != '') {
                    if ($nohp[0] == '0') {
                        $nohp = '62' . substr($nohp, 1);
                    }
                    SpWhatsappPhoneNumbers::create([
                        'ids' => uniqid(),
                        'team_id' => $team_id->wa_team_id,
                        'pid' => $contact_id,
                        'phone' => $nohp
                    ]);
                }
            }
        } else {
            foreach ($data as $d) {
                $nohp = $d->$field;
                if ($nohp != '') {
                    if ($nohp[0] == '0') {
                        $nohp = '62' . substr($nohp, 1);
                    }
                    SpWhatsappPhoneNumbers::create([
                        'ids' => uniqid(),
                        'team_id' => $team_id->wa_team_id,
                        'pid' => $contact_id,
                        'phone' => $nohp
                    ]);
                }
            }
        }
    }

    public static function checkWaContact()
    {
        $setting = Settings::find(1);
        $contact = SpWhatsappPhoneNumbers::where('team_id', $setting->wa_team_id)->get();
        foreach ($contact as $c) {
            $setnohp = $c->phone;
            // kondisi nama adalah bukan dari grup
            if (!str_contains($setnohp, '@g.us')) {
                if ($c->phone[0] == '6' && $c->phone[1] == '2') {
                    $setnohp = '0' . substr($c->phone, 2);
                }
                $user = User::where('nohp', $setnohp)->first();
                if ($user == null) {
                    $santri = Santri::where('nohp_ortu', $setnohp)->first();
                    if ($santri == null) {
                        $contact_pid = SpWhatsappContacts::find($c->pid);
                        if ($contact_pid != null) {
                            // dilarang hapus bulk testing
                            if ($contact_pid->name != 'Bulk Testing') {
                                // jangan hapus nama yg mengandung bulk
                                if (!str_contains($contact_pid->name, 'Bulk')) {
                                    $contact_pid->delete();
                                }
                                $del_contact = SpWhatsappPhoneNumbers::find($c->id);
                                $del_contact->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    public static function statusPerijinan($santri_id = null)
    {
        // $periode_tahun = Periode::latest('periode_tahun')->first();
        // $split_periode = explode("-", $periode_tahun->periode_tahun);
        // $get_total_kbm = Presence::where('event_date', '>=', $split_periode[0] . '-09-01')->get();
        // $get_total_permit = Permit::where('created_at', '>=', $split_periode[0] . '-09-01')->where('fkSantri_id', auth()->user()->santri->id)->get();
        // $persentase = count($get_total_kbm) * 30 / 100;

        if ($santri_id == null) {
            $santri_id = auth()->user()->santri->id;
        }
        $get_total_kbm = (cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')) - 4) * 2;
        $get_total_permit = Permit::where('created_at', 'LIKE', '%' . date('Y-m') . '%')->where('fkSantri_id', $santri_id)->where('status', 'approved')->get();
        $kuota = number_format($get_total_kbm * 30 / 100, 0);

        $status_ijin = true;
        if (count($get_total_permit) > $kuota) {
            $status_ijin = false;
        }

        $setting = Settings::find(1);
        if ($setting->status_perijinan == 0) {
            $status_ijin = true;
        }

        $data_kbm_ijin = ['kbm' => $get_total_kbm, 'ijin' => count($get_total_permit), 'status' => $status_ijin, 'kuota' => $kuota];
        return $data_kbm_ijin;
    }

    public static function isKetuaBendahara(){
        $santri_id = null;
        if(isset(auth()->user()->santri)){
            $santri_id = auth()->user()->santri->id;
        }
        $get = RabKegiatans::where('fkSantri_id_ketua', $santri_id)->orWhere('fkSantri_id_bendahara', $santri_id)->get();
        if(count($get)>0){
            return true;
        }else{
            if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('ku')){
                return true;
            }else{
                return false;
            }
        }
    }
}

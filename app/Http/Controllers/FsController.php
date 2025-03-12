<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\Settings;
use App\Models\Present;
use App\Models\Santri;
use App\Models\FsLogs;
use App\Models\SpWhatsappPhoneNumbers;
use App\Models\DewanPengajars;
use App\Helpers\WaSchedules;

// Set UserInfo

class FsController extends Controller
{
    public function sync_setuserinfo(Request $request){
        $setting = Settings::find(1);
        $cloud_fs = $setting->cloud_fs;
        $split_cloud_fs = explode(",", $cloud_fs);

        $loop = 1;
        foreach($split_cloud_fs as $cfs){
            // SET USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                if($vs->template_fs1=="" && $loop==1){
                    setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
                
                if($vs->template_fs2=="" && $loop==2){
                    setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
                
                if($vs->template_fs3=="" && $loop==3){
                    setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
            }

            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                if($degur->cloud_fs1=="" && $loop==1){
                    setUserInfo($degur->pin, $degur->name, $cfs);
                }
                
                if($degur->cloud_fs2=="" && $loop==2){
                    setUserInfo($degur->pin, $degur->name, $cfs);
                }
                
                if($degur->cloud_fs3=="" && $loop==3){
                    setUserInfo($degur->pin, $degur->name, $cfs);
                }
            }

            $loop++;
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - set user info.');
    }

    function setUserInfo($pin, $name, $cfs){
        $setting = Settings::find(1);
        $authorization = "Authorization: Bearer ".$setting->token_fs;
        $url = 'https://developer.fingerspot.io/api/set_userinfo';
        $data_fs = '{
            "trans_id":"'.date("YmdHis").'", 
            "cloud_id":"'.$cfs.'", 
            "data":{
                "pin":"'.$pin.'", 
                "name":"'.$name.'", 
                "privilege":"1", 
                "password":"159", 
                "rfid": "0", 
                "template":""
                }
            }';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fs);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function sync_getuserinfo(Request $request){
        $setting = Settings::find(1);
        $cloud_fs = $setting->cloud_fs;
        $split_cloud_fs = explode(",", $cloud_fs);

        $loop = 1;
        foreach($split_cloud_fs as $cfs){
            // GET USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                if($vs->template_fs1=="" && $loop==1){
                    getUserInfo($vs->santri_id, $cfs);
                }

                if($vs->template_fs2=="" && $loop==2){
                    getUserInfo($vs->santri_id, $cfs);
                }

                if($vs->template_fs3=="" && $loop==3){
                    getUserInfo($vs->santri_id, $cfs);
                }
            }

            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                if($degur->cloud_fs1=="" && $loop==1){
                    getUserInfo($degur->pin, $cfs);
                }
                
                if($degur->cloud_fs2=="" && $loop==2){
                    getUserInfo($degur->pin, $cfs);
                }
                
                if($degur->cloud_fs3=="" && $loop==3){
                    getUserInfo($degur->pin, $cfs);
                }
            }
            $loop++;
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - get user info.');
    }

    function getUserInfo($pin, $cfs){
        $setting = Settings::find(1);
        $authorization = "Authorization: Bearer ".$setting->token_fs;
        $url = 'https://developer.fingerspot.io/api/get_userinfo';
        $data = '{"trans_id":"'.date("YmdHis").'", "cloud_id":"'.$cfs.'", "pin":"'.$pin.'"}';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function sync_deleteuserinfo(Request $request){
        $setting = Settings::find(1);
        $cloud_fs = $setting->cloud_fs;
        $split_cloud_fs = explode(",", $cloud_fs);

        foreach($split_cloud_fs as $cfs){
            // DELETE USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                deleteUserInfo($vs->santri_id, $cfs);
                $get_santri = Santri::find($vs->santri_id);
                $get_santri->template_fs1 = null;
                $get_santri->template_fs2 = null;
                $get_santri->template_fs3 = null;
                $get_santri->save();
            }
            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                deleteUserInfo($degur->pin, $cfs);
                $get_degur = DewanPengajars::find($degur->id);
                $get_degur->cloud_fs1 = null;
                $get_degur->cloud_fs2 = null;
                $get_degur->cloud_fs3 = null;
                $get_degur->save();
            }
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - delete user info.');
    }

    function deleteUserInfo($pin, $cfs){
        $setting = Settings::find(1);
        $authorization = "Authorization: Bearer ".$setting->token_fs;
        $url = 'https://developer.fingerspot.io/api/delete_userinfo';
        $data = '{"trans_id":"'.date("YmdHis").'", "cloud_id":"'.$cfs.'", pin":"'.$pin.'" }';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function fs01(Request $request)
    {
        $decoded_data   = $request->all();

        if($decoded_data!=null){
            $created_at = date('Y-m-d H:i:s');
            $type       = $decoded_data['type'];
            $cloud_id   = $decoded_data['cloud_id'];
            if($type!='attlog'){
                $trans_id   = $decoded_data['trans_id'];
            }
            if($type=='attlog' || $type=='get_userinfo'){
                $santri_id  = $decoded_data['data']['pin'];
            }

            FsLogs::create([
                'cloud_id' => $cloud_id,
                'type' => $type,
                'trans_id' => ($type!='attlog') ? $trans_id : null,
                'created_at' => $created_at,
                'original_data' => json_encode($decoded_data)
            ]);

            if($type=='attlog'){
                $scan_verify  = $decoded_data['data']['verify'];
                try {
                    $datetime = $decoded_data['data']['scan'];
                    $presence = Presence::where('is_deleted', 0)->where('presence_start_date_time', '<=', $datetime)
                        ->where('presence_end_date_time', '>=', $datetime)->first();
                    $setting = Settings::find(1);
                    $get_santri = Santri::find($santri_id);
                    $get_degur = DewanPengajars::where('pin',$santri_id)->first();

                    if ($presence == null) {
                        if($get_degur==null){
                            // kirim WA ke mahasiswa
                            $nohp = $get_santri->user->nohp;
                            if ($nohp != '') {
                                if ($nohp[0] == '0') {
                                    $nohp = '62' . substr($nohp, 1);
                                }
                                $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                    $query->where('name', 'NOT LIKE', '%Bulk%');
                                })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                                if ($wa_phone != null) {
                                    WaSchedules::save('Presensi: Null', '*[Fingerprint]* Maaf, saat ini belum ada KBM.', $wa_phone->pid, null, true);
                                }
                            }
                        }
                    } else {
                        if($get_degur!=null){
                            // C26308525F1E1B32,C263045107151123 -> PPM 1
                            // C2630451072F3523 -> PPM 2
                            if($cloud_id=="C26308525F1E1B32" || $cloud_id=="C263045107151123"){
                                $presence->fkDewan_pengajar_1 = $get_degur->id;
                            }elseif($cloud_id=="C2630451072F3523"){
                                $presence->fkDewan_pengajar_2 = $get_degur->id;
                            }
                            $presence->save();
                        }else{
                            $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri_id)->first();
                            if ($existingPresent == null) {
                                // sign in
                                $sign_in = $datetime;
                                $is_late = 0;
                                if ($sign_in > $presence->start_date_time) {
                                    $is_late = 1;
                                }

                                $sign_out = null;
                                if($existingPresent->sign_in!=""){
                                    $existingPresent->sign_out = $datetime;
                                    $existingPresent->save();
                                }else{
                                    $inserted = Present::create([
                                        'fkSantri_id' => $santri_id,
                                        'fkPresence_id' => $presence->id,
                                        'sign_in' => $sign_in,
                                        'updated_by' => 'Fingerprint',
                                        'is_late' => $is_late
                                    ]);

                                    // kirim WA ke mahasiswa
                                    if ($inserted) {
                                        $nohp_ortu = $get_santri->nohp_ortu;
                                        if ($nohp_ortu != '') {
                                            if ($nohp_ortu[0] == '0') {
                                                $nohp_ortu = '62' . substr($nohp_ortu, 1);
                                            }
                                            $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                                $query->where('name', 'NOT LIKE', '%Bulk%');
                                            })->where('team_id', $setting->wa_team_id)->where('phone', $nohp_ortu)->first();
                                            if ($wa_phone != null) {
                                                $text_late = '';
                                                if($is_late){
                                                    $text_late = '*terlambat*';
                                                    $nohp = $get_santri->user->nohp;
                                                    if ($nohp != '') {
                                                        if ($nohp[0] == '0') {
                                                            $nohp = '62' . substr($nohp, 1);
                                                        }
                                                        $wa_phone_santri = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                                            $query->where('name', 'NOT LIKE', '%Bulk%');
                                                        })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                                                        if ($wa_phone_santri != null) {
                                                            WaSchedules::save('Presensi Terlambat', '*[Terlambat KBM]* Silahkan istighfar sebanyak 30x.', $wa_phone_santri->pid, null, true);
                                                        }
                                                    }
                                                }else{
                                                    $text_late = '*tepat waktu*';
                                                }
                                                $sign_in = date_format(date_create($sign_in),"d-m-Y H:i:s");
                                                WaSchedules::save('Presensi: Berhasil', '*'.$get_santri->user->fullname.'* telah hadir '.$text_late.' pada '.$presence->name.' | Tanggal & Jam: '.$sign_in.'.', $wa_phone->pid);
                                            }
                                        }
                                    }else{
                                        $nohp = $get_santri->user->nohp;
                                        if ($nohp != '') {
                                            if ($nohp[0] == '0') {
                                                $nohp = '62' . substr($nohp, 1);
                                            }
                                            $wa_phone = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                                                $query->where('name', 'NOT LIKE', '%Bulk%');
                                            })->where('team_id', $setting->wa_team_id)->where('phone', $nohp)->first();
                                            if ($wa_phone != null) {
                                                WaSchedules::save('Presensi: Gagal', '*[Fingerprint]* Anda gagal melakukan scan presensi pada '.$presence->name,', silahkan menghubungi pengurus.', $wa_phone->pid, null, true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $err) {
                    WaSchedules::save('Fingerprint Error', '*[Fingerprint Error]*, Segera lakukan perbaikan, dan jika masih terkendala silahkan koor lorong melakukan input presensi melalui Sisfo', 'wa_ketertiban_group_id', null, true);
                }
            }elseif($type=='set_userinfo'){
                echo "Sukses - Set User Info";
            }elseif($type=='get_userinfo'){
                $setting = Settings::find(1);
                $cloud_fs = $setting->cloud_fs;
                $split_cloud_fs = explode(",", $cloud_fs);

                $i=1;
                foreach($split_cloud_fs as $cfs){
                    if($cfs==$decoded_data['cloud_id']){
                        $get_santri = Santri::find($santri_id);
                        if($get_santri==null){
                            $get_degur = DewanPengajars::where('pin',$santri_id)->first();
                            if($get_degur!=null){
                                if($i==1 && $get_degur->cloud_fs1==""){
                                    $get_degur->cloud_fs1 = $cfs;
                                }
                                if($i==2 && $get_degur->cloud_fs2==""){
                                    $get_degur->cloud_fs2 = $cfs;
                                }
                                if($i==3 && $get_degur->cloud_fs3==""){
                                    $get_degur->cloud_fs3 = $cfs;
                                }
                                $get_degur->save();
                                echo "Ok - Get User Info";
                            }
                        }else{
                            if($i==1 && $get_santri->template_fs1==""){
                                $get_santri->template_fs1 = $cfs;
                            }
                            if($i==2 && $get_santri->template_fs2==""){
                                $get_santri->template_fs2 = $cfs;
                            }
                            if($i==3 && $get_santri->template_fs3==""){
                                $get_santri->template_fs3 = $cfs;
                            }
                            $get_santri->save();
                            echo "Ok - Get User Info";
                        }
                    }
                    $i++;
                }
            }elseif($type=='delete_userinfo'){
                echo "Sukses - Delete User Info";
            }
        }else{
            echo "Null";
        }
    }
}
?>
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
use App\Helpers\WaSchedules;

// Set UserInfo

class FsController extends Controller
{
    public function sync_setuserinfo(Request $request){
        $authorization = "Authorization: Bearer ".env('TOKEN_FS');
        $cloud_fs = env('CLOUD_FS_ID01');
        $split_cloud_fs = explode(",", $cloud_fs);

        foreach($split_cloud_fs as $cfs){
            // SET USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                if($vs->template_fs==""){
                    $url = 'https://developer.fingerspot.io/api/set_userinfo';
                    $data_fs = '{
                            "trans_id":"'.date("YmdHis").'", 
                            "cloud_id":"'.$cfs.'", 
                            "data":{
                                "pin":"'.$vs->santri_id.'", 
                                "name":"'.$vs->fullname.'", 
                                "privilege":"1", 
                                "password":"159", 
                                "rfid": "0", 
                                "template":"'.$vs->template_fs.'"
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
            }
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - set user info.');
    }

    public function sync_getuserinfo(Request $request){
        $authorization = "Authorization: Bearer ".env('TOKEN_FS');
        $cloud_fs = env('CLOUD_FS_ID01');
        $split_cloud_fs = explode(",", $cloud_fs);

        foreach($split_cloud_fs as $cfs){
            // GET USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                if($vs->template_fs==""){
                    $url = 'https://developer.fingerspot.io/api/get_userinfo';
                    $data = '{"trans_id":"'.date("YmdHis").'", "cloud_id":"'.$cfs.'", "pin":"'.$vs->santri_id.'"}';

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
            }
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - get user info.');
    }

    public function sync_deleteuserinfo(Request $request){
        $authorization = "Authorization: Bearer ".env('TOKEN_FS');
        $cloud_fs = env('CLOUD_FS_ID01');
        $split_cloud_fs = explode(",", $cloud_fs);

        foreach($split_cloud_fs as $cfs){
            // DELETE USERINFO
            $set_santri = DB::table('v_user_santri')->get();
            foreach ($set_santri as $vs) {
                $url = 'https://developer.fingerspot.io/api/delete_userinfo';
                $data = '{"trans_id":"'.date("YmdHis").'", "cloud_id":"'.$cfs.'", pin":"'.$vs->santri_id.'" }';

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

                $get_santri = Santri::find($vs->santri_id);
                $get_santri->template_fs = null;
                $get_santri->save();
            }
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - delete user info.');
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
                    if ($presence == null) {
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
                    } else {
                        $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri_id)->first();
                        if ($existingPresent == null) {
                            // sign in
                            $sign_in = $datetime;
                            $is_late = 0;
                            if ($sign_in > $presence->start_date_time) {
                                $is_late = 1;
                            }
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
                                            $text_late = 'terlambat';
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
                                            $text_late = 'tepat waktu';
                                        }

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
                } catch (Exception $err) {
                    WaSchedules::save('Fingerprint Error', '*[Fingerprint Error]*, Segera lakukan perbaikan, dan jika masih terkendala silahkan koor lorong melakukan input presensi melalui Sisfo', 'wa_ketertiban_group_id', null, true);
                }
            }elseif($type=='set_userinfo'){
                echo "Sukses - Set User Info";
            }elseif($type=='get_userinfo'){
                $get_santri = Santri::find($santri_id);
                $get_santri->template_fs = $decoded_data['data']['template'];
                if($get_santri->save()){
                    echo "Sukses - Get User Info";
                }else{
                    echo "Gagal - Get User Info";
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
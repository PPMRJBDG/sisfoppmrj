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
use App\Models\Liburan;
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
                    $this->setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
                
                if($vs->template_fs2=="" && $loop==2){
                    $this->setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
                
                if($vs->template_fs3=="" && $loop==3){
                    $this->setUserInfo($vs->santri_id, $vs->fullname, $cfs);
                }
            }

            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                if($degur->cloud_fs1=="" && $loop==1){
                    $this->setUserInfo($degur->pin, $degur->name, $cfs);
                }
                
                if($degur->cloud_fs2=="" && $loop==2){
                    $this->setUserInfo($degur->pin, $degur->name, $cfs);
                }
                
                if($degur->cloud_fs3=="" && $loop==3){
                    $this->setUserInfo($degur->pin, $degur->name, $cfs);
                }
            }

            $loop++;
        }

        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('list setting'))->with('success', 'Berhasil sinkronisasi - set user info.');
    }

    public function setUserInfo($pin, $name, $cfs){
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
                    $this->getUserInfo($vs->santri_id, $cfs);
                }

                if($vs->template_fs2=="" && $loop==2){
                    $this->getUserInfo($vs->santri_id, $cfs);
                }

                if($vs->template_fs3=="" && $loop==3){
                    $this->getUserInfo($vs->santri_id, $cfs);
                }
            }

            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                if($degur->cloud_fs1=="" && $loop==1){
                    $this->getUserInfo($degur->pin, $cfs);
                }
                
                if($degur->cloud_fs2=="" && $loop==2){
                    $this->getUserInfo($degur->pin, $cfs);
                }
                
                if($degur->cloud_fs3=="" && $loop==3){
                    $this->getUserInfo($degur->pin, $cfs);
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
                $this->deleteUserInfo($vs->santri_id, $cfs);
                $get_santri = Santri::find($vs->santri_id);
                $get_santri->template_fs1 = null;
                $get_santri->template_fs2 = null;
                $get_santri->template_fs3 = null;
                $get_santri->save();
            }
            $data_degur = DewanPengajars::whereNotNull('pin')->get();
            foreach($data_degur as $degur){
                $this->deleteUserInfo($degur->pin, $cfs);
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
                    $setting = Settings::find(1);
                    $get_santri = Santri::find($santri_id);

                    $date_format = date_format(date_create($datetime), "Y-m-d");
                    $check_liburan = Liburan::where('liburan_from', '<=', $date_format)->where('liburan_to', '>=', $date_format)->get();
                    if (count($check_liburan) > 0) {
                        WaSchedules::save('Liburan', '*[Fingerprint]* Mohon maaf saat ini sedang libur KBM, silahkan memanfaatkan waktu liburan dengan baik.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                        echo "Nok - Liburan";
                        exit;
                    }

                    $presence = Presence::where('is_deleted', 0)->where('presence_start_date_time', '<=', $datetime)
                        ->where('presence_end_date_time', '>=', $datetime)->first();
                    $get_degur = DewanPengajars::where('pin',$santri_id)->first();

                    if ($presence == null) {
                        if($get_degur==null){
                            // kirim WA ke mahasiswa
                            WaSchedules::save('Presensi: Null', '*[Fingerprint]* Mohon maaf *'.$get_santri->user->fullname.'*, mungkin KBM sudah selesai atau belum mulai KBM selanjutnya.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                            echo "Nok - Presensi Null";
                            exit;
                        }
                    } else {
                        if($get_degur!=null){
                            // C26308525F1E1B32,C263045107151123 -> PPM 1
                            // C2630451072F3523 -> PPM 2
                            $is_late = 0;
                            if ($datetime > $presence->start_date_time) {
                                $is_late = 1;
                            }
                            if($presence->is_put_together==1){
                                if($presence->fkDewan_pengajar_1==""){
                                    $presence->fkDewan_pengajar_1 = $get_degur->id;
                                    $presence->sign_in_degur1 = $datetime;
                                    $presence->status_terlambat_degur1 = $is_late;
                                }else{
                                    $presence->fkDewan_pengajar_2 = $get_degur->id;
                                    $presence->sign_in_degur2 = $datetime;
                                    $presence->status_terlambat_degur2 = $is_late;
                                }
                            }else{
                                if($cloud_id=="C26308525F1E1B32" || $cloud_id=="C263045107151123"){
                                    $presence->fkDewan_pengajar_1 = $get_degur->id;
                                    $presence->sign_in_degur1 = $datetime;
                                    $presence->status_terlambat_degur1 = $is_late;
                                }elseif($cloud_id=="C2630451072F3523"){
                                    $presence->fkDewan_pengajar_2 = $get_degur->id;
                                    $presence->sign_in_degur2 = $datetime;
                                    $presence->status_terlambat_degur2 = $is_late;
                                }
                            }

                            $presence->save();
                            echo "Ok - Presensi Dewan Guru";
                            exit;
                        }else{
                            // cek awal scan dewan guru
                            if($setting->status_scan_degur){
                                $status_scan_degur = false;
                                if($presence->is_put_together==1){
                                    if($presence->fkDewan_pengajar_1==""){
                                        $status_scan_degur = true;
                                    }
                                }else{
                                    if($cloud_id=="C26308525F1E1B32" || $cloud_id=="C263045107151123"){
                                        if($presence->fkDewan_pengajar_1==""){
                                            $status_scan_degur = true;
                                        }
                                    }elseif($cloud_id=="C2630451072F3523"){
                                        if($presence->fkDewan_pengajar_2==""){
                                            $status_scan_degur = true;
                                        }
                                    }
                                }

                                if($status_scan_degur){
                                    WaSchedules::save('KBM Belum Mulai', '*[Fingerprint]* Mohon maaf untuk ketertiban, mekanisme scan fingerprint diawali oleh Dewan Guru terlebih dahulu (jika scan sebelum Dewan Guru, maka statusnya masih alpha meskipun mesin fingerprint OK).', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                                    echo "Nok - KBM Belum Dimulai";
                                    exit;
                                }
                            }

                            // Disatukan di PPM 1 -> cek scan FP di PPM 2
                            if($presence->is_put_together==1){
                                if($cloud_id=="C2630451072F3523"){
                                    // kirim WA ke mahasiswa
                                    WaSchedules::save('FP di PPM 1', '*[Fingerprint]* Mohon maaf *'.$get_santri->user->fullname.'*, silahkan melakukan scan fingerprint di PPM 1.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                                    echo "FP di PPM 1";
                                    exit;
                                }
                            }

                            $existingPresent = Present::where('fkPresence_id', $presence->id)->where('fkSantri_id', $santri_id)->first();
                            if ($existingPresent == null) {
                                if($presence->end_date_time < $datetime){
                                    WaSchedules::save('Presensi: Terlambat KBM Selesai', '*[Fingerprint]* Mohon maaf *'.$get_santri->user->fullname.'*, KBM sudah selesai.
Jika ternyata hadir dan belum atau lupa scan fingerprint, silahkan menghubungi RJ / WK.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                                    echo "Nok - Presensi: Terlambat KBM Selesai";
                                    exit;
                                }else{
                                    $sign_in = $datetime;
                                    $is_late = 0;
                                    if ($sign_in > $presence->start_date_time) {
                                        $is_late = 1;
                                    }
                                    // Kondisi jika dewan guru terlambat, kemurahan buat semua -> status tidak telat (tepat waktu)
                                    if($presence->is_put_together==1){
                                        if($presence->status_terlambat_degur1){
                                            $is_late = 0;
                                        }
                                    }else{
                                        if($cloud_id=="C26308525F1E1B32" || $cloud_id=="C263045107151123"){
                                            if($presence->status_terlambat_degur1){
                                                $is_late = 0;
                                            }
                                        }elseif($cloud_id=="C2630451072F3523"){
                                            if($presence->status_terlambat_degur2){
                                                $is_late = 0;
                                            }
                                        }
                                    }

                                    $inserted = Present::create([
                                        'fkSantri_id' => $santri_id,
                                        'fkPresence_id' => $presence->id,
                                        'sign_in' => $sign_in,
                                        'updated_by' => $cloud_id,
                                        'is_late' => $is_late
                                    ]);

                                    // kirim WA ke mahasiswa
                                    if ($inserted) {
                                        if($is_late){
                                            $text_late = '🟨 *terlambat*';
                                            WaSchedules::save('Presensi Terlambat', '🟨 *[Terlambat KBM]* Sebelum masuk masjid / mushola, Silahkan berdiri dan istighfar sebanyak 30x.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                                        }else{
                                            $text_late = '✅ *tepat waktu*';
                                        }
                                        $sign_in = date_format(date_create($sign_in),"d-m-Y H:i:s");
                                        WaSchedules::save('Presensi: Berhasil', '*'.$get_santri->user->fullname.'* telah hadir '.$text_late.' pada '.$presence->name.' | Tanggal & Jam: '.$sign_in.'.', WaSchedules::getContactId($get_santri->nohp_ortu), null, true);
                                    }else{
                                        WaSchedules::save('Presensi: Gagal', '*[Fingerprint]* Anda gagal melakukan scan presensi pada '.$presence->name,', silahkan menghubungi pengurus.', WaSchedules::getContactId($get_santri->user->nohp), null, true);
                                    }
                                    echo "Ok - Presensi Sign In";
                                    exit;
                                }
                            }else{
                                if ($datetime < $presence->end_date_time) {
                                    $existingPresent->is_go_home_early = 1;
                                }
                                $existingPresent->sign_out = $datetime;
                                $existingPresent->save();
                                echo "Ok - Presensi Sign Out";
                                exit;
                            }
                        }
                    }
                } catch (Exception $err) {
                    WaSchedules::save('Fingerprint Error', '*[Fingerprint Error]*, Segera lakukan perbaikan, dan jika masih terkendala silahkan koor lorong melakukan input presensi melalui Sisfo', 'wa_ketertiban_group_id', null, true);
                    echo "Nok - Fingerprint Error";
                    exit;
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
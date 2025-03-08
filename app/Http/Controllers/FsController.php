<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

// use App\Models\PresenceGroup;
// use App\Models\Presence;
// use App\Models\Present;
use App\Models\Santri;
use App\Models\FsLogs;
use App\Helpers\WaSchedules;

// Set UserInfo

class FsController extends Controller
{
    public function fs01(Request $request)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $decoded_data   = json_decode($data, true);
            WaSchedules::save('Testing', 'Masuk FS01 - Fingerprint', 'wa_ketertiban_group_id');

            $type       = $decoded_data['type'];
            $cloud_id   = $decoded_data['cloud_id'];
            $created_at = date('Y-m-d H:i:s');

            FsLogs::create([
                'cloud_id' => $cloud_id,
                'type' => $type,
                'created_at' => $created_at,
                'original_data' => json_encode($decoded_data)
            ]);
        } else {
            echo "Permintaan bukan POST";
        }
        exit;

        // $original_data  = file_get_contents('php://input');
        // $decoded_data   = json_decode($original_data, true);
        // WaSchedules::save('Testing', 'Masuk FS01 - Fingerprint', 'wa_ketertiban_group_id');

        // $type       = $decoded_data['type'];
        // $cloud_id   = $decoded_data['cloud_id'];
        // $created_at = date('Y-m-d H:i:s');

        // FsLogs::create([
        //     'cloud_id' => $cloud_id,
        //     'type' => $type,
        //     'created_at' => $created_at,
        //     'original_data' => json_encode($decoded_data)
        // ]);

        // if($type=='attlog'){
        //     echo "OK";
        // }elseif($type=='set_userinfo'){
        //     echo "OK";
        // }elseif($type=='get_userinfo'){
        //     $pin_santri_id   = $decoded_data['data']['pin'];
        //     $santri = Santri::find($pin_santri_id);
        //     $santri->template_fs = $decoded_data['data']['template'];
        //     $santri->save();
        //     echo "OK";
        // }
    }
}
?>
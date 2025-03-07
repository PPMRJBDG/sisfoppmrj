<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PresenceGroup;
use App\Models\Presence;
use App\Models\Present;
use App\Models\Santri;

// Set UserInfo

class FsController extends Controller
{
    public function fs01()
    {
        $original_data  = file_get_contents('php://input');
        $decoded_data   = json_decode($original_data, true);
        $encoded_data   = json_encode($decoded_data);

        if (isset($decoded_data['type']) AND isset($decoded_data['cloud_id'])){
            $type       = $decoded_data['type'];
            $cloud_id   = $decoded_data['cloud_id'];
            $created_at = date('Y-m-d H:i:s');

            if($type=='set_userinfo'){
                echo "OK - SET USER INFO";
            }elseif($type=='get_userinfo'){
                $pin_santri_id   = $decoded_data['data']['pin'];
                $santri = Santri::find($pin_santri_id);
                $santri->template_fs = $decoded_data['data']['template'];
                $santri->save();
                echo "OK - GET USER INFO";
            }
        }
    }
}
?>
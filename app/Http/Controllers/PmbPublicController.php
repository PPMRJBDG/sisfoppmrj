<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmbPublicController extends Controller
{

    public function index()
    {
        
        return view('pmb.index', [
            
        ]);
    }

    public function store_maba(Request $request)
    {
        $insert = PanitiaPmbs::create([
            'fkSantri_id' => $request->input('fkSantri_id')
        ]);

        $santri = Santri::find($request->input('fkSantri_id'));

        $user = User::find($santri->user->id);
        $checkRole = Role::findByName('panitia pmb');
        $user->assignRole($checkRole);
        if($insert){
            return json_encode(array("status" => true));
        }else{
            return json_encode(array("status" => false));
        }
    }
    
}

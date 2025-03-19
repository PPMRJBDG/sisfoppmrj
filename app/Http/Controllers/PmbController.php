<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\PanitiaPmbs;
use App\Models\ModelHasRole;
use App\Models\Santri;
use App\Models\User;

class PmbController extends Controller
{

    public function view_panitia()
    {
        $datax = PanitiaPmbs::get();
        $santris = DB::table('v_user_santri as a')
        ->leftJoin('panitia_pmbs as b', function ($join) {
            $join->on('a.santri_id', '=', 'b.fkSantri_id');
        })
        ->whereNull('b.fkSantri_id')
        ->where('angkatan', (date('Y')-1))
        ->orderBy('fullname','ASC')
        ->get();

        return view('pmb.view_panitia', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }

    public function store_panitia(Request $request)
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

    public function delete_panitia($id)
    {
        $data = PanitiaPmbs::find($id);
        if($data){
            $santri = Santri::find($data->fkSantri_id);
            if($data->delete()){
                $user = User::find($santri->user->id);
                $checkRole = Role::findByName('panitia pmb');
                $user->removeRole($checkRole);
            }
        }
        return redirect()->route('view panitia', $id)->with('success', 'Berhasil menghapus panitia');
    }

    public function view_maba()
    {
        
        return view('pmb.view_maba', [
            
        ]);
    }
    
}

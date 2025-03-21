<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\PmbPanitias;
use App\Models\ModelHasRole;
use App\Models\Santri;
use App\Models\User;
use App\Models\PmbCamabas;
use App\Models\PmbKonfigurasis;

class PmbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function konfigurasi()
    {
        $datax = PmbKonfigurasis::get();

        return view('pmb.konfigurasi', [
            'datax' => $datax,
        ]);
    }

    public function store_konfigurasi(Request $request)
    {

        $tahun_pmb = PmbKonfigurasis::where('tahun_pmb',$request->input('tahun_pmb'))->first();
        if($tahun_pmb==null){
            $insert = PmbKonfigurasis::create([
                'tahun_pmb' => $request->input('tahun_pmb'),
                'gelombang1' => $request->input('gelombang1'),
                'gelombang2' => $request->input('gelombang2'),
                'informasi_pmb' => $request->input('informasi_pmb'),
            ]);
        }else{
            $tahun_pmb->gelombang1 = $request->input('gelombang1');
            $tahun_pmb->gelombang2 = $request->input('gelombang2');
            $tahun_pmb->informasi_pmb = $request->input('informasi_pmb');
            $tahun_pmb->save();
        }

        return redirect()->route('konfigurasi pmb');
    }
    
    public function view_panitia()
    {
        $datax = PmbPanitias::get();
        $santris = DB::table('v_user_santri as a')
        ->leftJoin('pmb_panitias as b', function ($join) {
            $join->on('a.santri_id', '=', 'b.fkSantri_id');
        })
        ->whereNull('b.fkSantri_id')
        ->where('b.angkatan', (date('Y')-1))
        ->orderBy('fullname','ASC')
        ->get();

        return view('pmb.view_panitia', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }

    public function store_panitia(Request $request)
    {
        $insert = PmbPanitias::create([
            'fkSantri_id' => $request->input('fkSantri_id'),
            'angkatan' => date('Y')
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
        $data = PmbPanitias::find($id);
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

    public function view_maba($select_angkatan=0)
    {
        if(auth()->user()->hasRole('panitia pmb')){
            $select_angkatan = date('Y');
            $list_angkatan = DB::table('pmb_camabas')
                            ->select('angkatan')
                            ->where('angkatan',$select_angkatan)
                            ->orderBy('angkatan', 'ASC')
                            ->groupBy('angkatan')
                            ->get();
        }else{
            $list_angkatan = DB::table('pmb_camabas')
                            ->select('angkatan')
                            ->orderBy('angkatan', 'ASC')
                            ->groupBy('angkatan')
                            ->get();
        }

        if($select_angkatan==0){
            $camabas = PmbCamabas::get();
        }else{
            $camabas = PmbCamabas::where('angkatan',$select_angkatan)->get();
        }

        return view('pmb.view_maba', [
            'list_angkatan' => $list_angkatan,
            'select_angkatan' => $select_angkatan,
            'camabas' => $camabas,
        ]);
    }

    public function change_status_maba(Request $request)
    {
        $change_status = PmbCamabas::find($request->input('id'));
        $change_status->status = $request->input('status');
        $change_status->save();

        if($change_status){
            return json_encode(array("status" => true, "change_status" => $request->input('status')));
        }else{
            return json_encode(array("status" => false));
        }
    }
    
}

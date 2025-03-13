<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JagaMalams;

class KeamananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $datax = JagaMalams::get();
        $santris = DB::table('v_user_santri')->where('gender','male')->orderBy('fullname','ASC')->get();

        return view('keamanan.jagamalam', [
            'datax' => $datax,
            'santris' => $santris,
        ]);
    }

    public function store_jagamalam(Request $request)
    {
        $check = JagaMalams::where('ppm',$request->input('ppm'))->get();

        $putaran = 0;
        if($check==null){
            $putaran = 1;
        }else{
            $putaran = count($check)+1;
        }
        $insert = JagaMalams::create([
            'ppm' => $request->input('ppm'),
            'putaran_ke' => $putaran,
            'anggota' => $request->input('anggota'),
        ]);

        if($insert){
            return json_encode(array("status" => true));
        }else{
            return json_encode(array("status" => false));
        }
    }

    public function delete_jagamalam($id)
    {
        $data = JagaMalams::find($id);
        if (!$data)
            return redirect()->route('index keamanan', $id)->withErrors(['periode_not_found' => 'Periode tidak ditemukan.']);
        $data->delete();
        return redirect()->route('index keamanan', $id)->with('success', 'Berhasil menghapus periode');
    }
}

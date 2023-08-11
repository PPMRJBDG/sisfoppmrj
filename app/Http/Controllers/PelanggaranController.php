<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Pelanggaran;
use App\Models\JenisPelanggaran;
use Illuminate\Support\Facades\DB;

class PelanggaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $is_archive = 0;
        $count_pelanggaran = array(); //Pelanggaran::select(DB::raw('fkJenis_pelanggaran_id, COUNT(fkJenis_pelanggaran_id) as kategori'))->where('is_archive', $is_archive)->groupBy('fkJenis_pelanggaran_id')->get();
        $list_pelanggaran = Pelanggaran::where('is_archive', $is_archive)->get();

        foreach ($list_pelanggaran as $lp) {
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['kategori'] = $lp->jenis->kategori_pelanggaran;
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pelanggaran'] = $lp->jenis->jenis_pelanggaran;
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'] = 0;
            }
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'] = 0;
            }
            if ($lp->keringanan_sp == '') {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan']++;
            } else {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix']++;
            }
        }

        $column = [
            'Nama',
            'Angkatan',
            'Pelanggaran',
            'Tanggal Melangar',
            'SP',
            'Tanggal Pemberian ST',
            'Peringatan Keras',
            'Keterangan'
        ];

        return view('pelanggaran.list', [
            'column' => $column,
            'is_archive' => $is_archive,
            'list_pelanggaran' => $list_pelanggaran,
            'count_pelanggaran' => $count_pelanggaran
        ]);
    }

    public function list_archive()
    {
        $is_archive = 1;
        $count_pelanggaran = array(); //Pelanggaran::select(DB::raw('fkJenis_pelanggaran_id, COUNT(fkJenis_pelanggaran_id) as kategori'))->where('is_archive', $is_archive)->groupBy('fkJenis_pelanggaran_id')->get();
        $list_pelanggaran = Pelanggaran::where('is_archive', $is_archive)->get();

        foreach ($list_pelanggaran as $lp) {
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['kategori'] = $lp->jenis->kategori_pelanggaran;
            $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pelanggaran'] = $lp->jenis->jenis_pelanggaran;
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan'] = 0;
            }
            if (!isset($count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'])) {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix'] = 0;
            }
            if ($lp->keringanan_sp == '') {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['pemantauan']++;
            } else {
                $count_pelanggaran[$lp->fkJenis_pelanggaran_id]['fix']++;
            }
        }

        $column = [
            'Nama',
            'Angkatan',
            'Pelanggaran',
            'Tanggal Melangar',
            'SP',
            'Tanggal Pemberian ST',
            'Peringatan Keras',
            'Keterangan'
        ];

        return view('pelanggaran.list', [
            'column' => $column,
            'is_archive' => $is_archive,
            'list_pelanggaran' => $list_pelanggaran,
            'count_pelanggaran' => $count_pelanggaran
        ]);
    }

    public function create()
    {
        $list_santri = DB::table('v_user_santri')->orderBy('fullname')->get();
        $list_jenis_pelanggaran = JenisPelanggaran::get();
        $column = Pelanggaran::attr();; //DB::getSchemaBuilder()->getColumnListing('pelanggarans');

        return view('pelanggaran.create', [
            'column' => $column,
            'list_santri' => $list_santri,
            'list_jenis_pelanggaran' => $list_jenis_pelanggaran
        ]);
    }

    public function edit($id)
    {
        $list_santri = DB::table('v_user_santri')->orderBy('fullname')->get();
        $list_jenis_pelanggaran = JenisPelanggaran::get();
        $datax = Pelanggaran::find($id);
        $column = Pelanggaran::attr();; //DB::getSchemaBuilder()->getColumnListing('pelanggarans');

        return view('pelanggaran.create', [
            'id' => $id,
            'column' => $column,
            'datax' => $datax,
            'list_santri' => $list_santri,
            'list_jenis_pelanggaran' => $list_jenis_pelanggaran
        ]);
    }

    public function archive($id)
    {
        $datax = Pelanggaran::find($id);
        $datax->is_archive = 1;
        $message = 'Data gagal diarsipkan';
        if ($datax->save()) {
            $message = 'Data berhasil diarsipkan';
        }

        return redirect()->route('pelanggaran tm')->with('success', $message);
    }

    public function delete($id)
    {
        $datax = Pelanggaran::find($id);
        $message = 'Data gagal dihapus';
        if ($datax->delete()) {
            $message = 'Data berhasil dihapus';
        }

        return redirect()->route('pelanggaran tm')->with('success', $message);
    }

    public function store(Request $request)
    {
        $message = '';
        $update = false;
        $column = DB::getSchemaBuilder()->getColumnListing('pelanggarans');
        if ($request->input('id') != null) {
            $update = true;
            $request->validate([
                'fkSantri_id' => 'required'
            ]);
            $data = Pelanggaran::find($request->input('id'));
            foreach ($column as $c) {
                if ($c != 'is_archive' && $c != 'created_at' && $c != 'updated_at') {
                    $data->$c = $request->input($c);
                }
            }
            $message = 'Berhasil mengubah data';
        } else {
            $store = [];
            foreach ($column as $c) {
                $store[$c] = $request->input($c);
            }
            $data = Pelanggaran::create($store);
            $message = 'Berhasil menambahkan data';
        }

        if (!$data->save()) {
            $message = 'Data gagal disimpan';
        }

        if ($update) {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit pelanggaran', $request->input('id')))->with('success', $message);
        } else {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('pelanggaran tm'))->with('success', $message);
        }
    }
}

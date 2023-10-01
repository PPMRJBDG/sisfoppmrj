<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Pelanggaran;
use App\Models\JenisPelanggaran;
use Illuminate\Support\Facades\DB;
use App\Helpers\WaSchedules;

class PelanggaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id = null)
    {
        $is_archive = 0;
        $count_pelanggaran = array(); //Pelanggaran::select(DB::raw('fkJenis_pelanggaran_id, COUNT(fkJenis_pelanggaran_id) as kategori'))->where('is_archive', $is_archive)->groupBy('fkJenis_pelanggaran_id')->get();
        if ($id == null) {
            $list_pelanggaran = Pelanggaran::where('is_archive', $is_archive)->get();
        } else {
            $list_pelanggaran = Pelanggaran::where('fkJenis_pelanggaran_id', $id)->where('is_archive', $is_archive)->get();
        }

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
            // 'Angkatan',
            // 'Pelanggaran',
            'Tanggal',
            'SP',
            // 'Peringatan Keras',
            'Keterangan'
        ];

        return view('pelanggaran.list', [
            'id' => $id,
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
            if ($data->save()) {
                $message = 'Berhasil mengubah data';
            } else {
                $message = 'Data gagal disimpan';
            }
        } else {
            $store = [];
            foreach ($column as $c) {
                $store[$c] = $request->input($c);
            }
            $store['is_archive'] = 0;
            $data = Pelanggaran::create($store);
            if ($data) {
                $message = 'Berhasil menambahkan data';
            } else {
                $message = 'Gagal menambahkan data';
            }
        }

        if ($request->input('is_wa') == 1) {
            $jenis_pelanggaran = JenisPelanggaran::find($request->input('fkJenis_pelanggaran_id'));
            $caption = 'Penambahan data pelanggaran dari Mahasiswa:
- Nama: *' . $data->santri->user->fullname . '*
- Angkatan: *' . $data->santri->angkatan . '*
- Kategori: *' . $jenis_pelanggaran->kategori_pelanggaran . '*
- Jenis Pelanggaran: *' . $jenis_pelanggaran->jenis_pelanggaran . '*
- Waktu: *' . $data->tanggal_melanggar . '*
- Keterangan: *' . $data->keterangan . '*';
            $contact_id = 'wa_dewanguru_group_id';
            WaSchedules::save('Data Pelanggaran ' . $data->santri->user->fullname, $caption, $contact_id);
        }

        if ($update) {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit pelanggaran', $request->input('id')))->with('success', $message);
        } else {
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('pelanggaran tm'))->with('success', $message);
        }
    }
}

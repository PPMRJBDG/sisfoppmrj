<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\DewanPengajars;
use App\Models\JadwalPengajars;
use App\Models\PresenceGroup;

class MateriController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the list and manage table of materis.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage()
    {
        $materis = Materi::all();

        return view('materi.list_and_manage', ['materis' => $materis]);
    }

    /**
     * Show the create form of materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        return view('materi.create');
    }

    /**
     * Insert new materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'pageNumbers' => 'required|integer',
            'for' => 'string'
        ]);

        $inserted = Materi::create($request->all());

        return redirect()->route('materi tm')->with($inserted ? 'success' : 'failed', $inserted ? 'Materi baru berhasil ditambahkan.' : 'Gagal menambah materi baru.');
    }

    /**
     * Show the create form of materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $materi = Materi::find($id);

        return view('materi.edit', ['materi' => $materi]);
    }

    /**
     * Update materi.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $materiIdToUpdate = $request->route('id');

        $request->validate([
            'name' => 'required',
            'pageNumbers' => 'required|integer',
        ]);

        // validate availability of materi existence
        $materi = Materi::find($materiIdToUpdate);

        if (!$materi)
            return redirect()->route('edit materi', $materiIdToUpdate)->withErrors(['materi_not_found' => 'Can\'t update unexisting Materi.']);

        $materi->name = $request->input('name');
        $materi->pageNumbers = $request->input('pageNumbers');

        $updated = $materi->save();

        if (!$updated)
            return redirect()->route('edit materi', $materiIdToUpdate)->withErrors(['failed_updating_materi' => 'Gagal mengubah materi.']);

        return redirect()->route('edit materi', $materiIdToUpdate)->with('success', 'Berhasil mengubah materi.');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete($id)
    {
        $materi = Materi::find($id);

        if ($materi) {
            $deleted = $materi->delete();

            if (!$deleted)
                return redirect()->route('materi tm')->withErrors(['failed_deleting_materi', 'Gagal menghapus Materi.']);
        }

        return redirect()->route('materi tm')->with('success', 'Berhasil menghapus Materi');
    }

    public function list_pengajar()
    {
        $pengajar = DewanPengajars::all();

        return view('materi.list_pengajar', ['pengajar' => $pengajar]);
    }

    public function store_pengajar(Request $request)
    {
        $inserted = DewanPengajars::create($request->all());

        return json_encode(['status' => true, 'message' => 'Berhasil menambahkan pengajar', 'id' => $inserted->id]);
    }

    public function update_pengajar(Request $request)
    {
        $id = $request->route('id');
        $pengajar = DewanPengajars::find($id);

        if ($pengajar) {
            $pengajar->name = $request->input('name');
            $updated = $pengajar->save();

            if (!$updated)
                return json_encode(['status' => false, 'message' => 'Gagal mengubah pengajar']);
        }

        return json_encode(['status' => true, 'message' => 'Berhasil mengubah pengajar']);
    }

    public function delete_pengajar($id)
    {
        $pengajar = DewanPengajars::find($id);

        if ($pengajar) {
            $deleted = $pengajar->delete();

            if (!$deleted)
                return json_encode(['status' => false, 'message' => 'Gagal menghapus pengajar']);
        }

        return json_encode(['status' => true, 'message' => 'Berhasil menghapus pengajar']);
    }

    public function jadwal()
    {
        $pengajar = DewanPengajars::all();
        $jadwal_pengajar = JadwalPengajars::all();
        $presence_group = PresenceGroup::whereIn('id', [1, 2])->get();

        return view('materi.list_jadwal', ['jadwal_pengajar' => $jadwal_pengajar, 'presence_group' => $presence_group, 'pengajar' => $pengajar]);
    }

    public function jadwal_store(Request $request)
    {
        $pengajar = JadwalPengajars::where('fkPresence_group_id', $request->input('presence'))->where('day', $request->input('day'))->where('ppm', $request->input('ppm'))->first();

        if ($pengajar) {
            $pengajar->fkDewan_pengajar_id = $request->input('pengajar');
            $updated = $pengajar->save();
        } else {
            $updated = JadwalPengajars::create([
                'fkPresence_group_id' => $request->input('presence'),
                'fkDewan_pengajar_id' => $request->input('pengajar'),
                'day' => $request->input('day'),
                'ppm' => $request->input('ppm')
            ]);
        }

        if ($updated) {
            return json_encode(['status' => true, 'message' => 'Berhasil mengubah pengajar']);
        } else {
            return json_encode(['status' => false, 'message' => 'Gagal mengubah pengajar']);
        }
    }
}

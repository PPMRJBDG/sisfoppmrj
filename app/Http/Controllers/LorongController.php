<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lorong;
use App\Models\Santri;
use App\Models\User;
use Spatie\Permission\Models\Role;

class LorongController extends Controller
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
     * Show the list and manage table of lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage()
    {
        $lorongs = Lorong::all();

        return view('lorong.list_and_manage', ['lorongs' => $lorongs]);
    }

    /**
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        // $users = User::whereHas('santri')->orderBy('fullname')->get();
        $users = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->orderBy('fullname', 'asc')->get();

        return view('lorong.create', ['users' => $users]);
    }

    /**
     * Insert new lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'fkSantri_leaderId' => 'required|max:100|integer|exists:santris,id',
        ]);

        $santri = Santri::find($request->input('fkSantri_leaderId'));

        if (!$santri)
            return redirect()->route('create lorong')->withErrors(['santri_not_found' => 'Santri (koor) tidak ditemukan.']);

        // validate that picked santri is not a leader in another lorong
        $lorongsUnderLead = Lorong::where('fkSantri_leaderId', $request->input('fkSantri_leaderId'))->get();

        if (sizeof($lorongsUnderLead) >= 1)
            return redirect()->route('create lorong')->withErrors(['santri_already_a_leader' => 'Santri sudah menjadi koor di lorong lain.']);

        $inserted = Lorong::create($request->all());

        // give role koor lorong
        $santri->user->assignRole('koor lorong');

        return redirect()->route('lorong tm')->with($inserted ? 'success' : 'failed', $inserted ? 'Lorong baru berhasil ditambahkan.' : 'Gagal menambah lorong baru.');
    }

    /**
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $users = User::whereHas('santri')->orderBy('fullname')->get();
        $lorong = Lorong::find($id);

        return view('lorong.edit', ['users' => $users, 'lorong' => $lorong]);
    }

    /**
     * Update lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $lorongIdToUpdate = $request->route('id');

        $request->validate([
            'name' => 'required',
            'fkSantri_leaderId' => 'required|integer|exists:santris,id',
        ]);

        $newSantriLeader = Santri::find($request->input('fkSantri_leaderId'));

        if (!$newSantriLeader)
            return redirect()->route('edit lorong', $lorongIdToUpdate)->withErrors(['santri_not_found' => 'Santri (koor) tidak ditemukan.']);

        // validate santri is not part of any lorong
        if ($newSantriLeader->fkLorong_id)
            return redirect()->route('edit lorong', $lorongIdToUpdate)->withErrors(['santri_already_inside_lorong' => 'Santri (koor) merupakan anggota sebuah lorong.']);

        // validate availability of lorong existence
        $lorong = Lorong::find($lorongIdToUpdate);

        if (!$lorong)
            return redirect()->route('edit lorong', $lorongIdToUpdate)->withErrors(['lorong_not_found' => 'Can\'t update unexisting Lorong.']);

        // validate that picked santri is not a leader in another lorong
        $lorongsUnderLead = Lorong::where('fkSantri_leaderId', $request->input('fkSantri_leaderId'))->get();

        if (sizeof($lorongsUnderLead) >= 1) {
            foreach ($lorongsUnderLead as $lorongUnderLead) {
                if ($lorongUnderLead->fkSantri_leaderId == $request->input('fkSantri_leaderId') && $lorongUnderLead->id != $lorong->id)
                    return redirect()->route('edit lorong', $lorongIdToUpdate)->withErrors(['santri_already_a_leader' => 'Santri sudah menjadi koor di lorong lain.']);
            }
        }

        $currentSantriLeaderId = $lorong->fkSantri_leaderId;

        $lorong->name = $request->input('name');
        $lorong->fkSantri_leaderId = $request->input('fkSantri_leaderId');

        $updated = $lorong->save();

        if (!$updated)
            return redirect()->route('edit lorong', $lorongIdToUpdate)->withErrors(['failed_updating_lorong' => 'Gagal mengubah lorong.']);

        // update santri's role
        $newSantriLeader->user->assignRole('koor lorong');

        if ($currentSantriLeaderId != $request->input('fkSantri_leaderId')) {
            $currentSantriLeader = Santri::find($currentSantriLeaderId);

            if ($currentSantriLeader) {
                $currentSantriLeader->user->removeRole('koor lorong');
            }
        }

        return redirect()->route('edit lorong', $lorongIdToUpdate)->with('success', 'Berhasil mengubah lorong.');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete($id)
    {
        $lorong = Lorong::find($id);

        if ($lorong) {
            // get lorong leader
            $santriLeader = Santri::find($lorong->fkSantri_leaderId);
            $roleKoorLorong = Role::findByName('koor lorong');

            $santriLeader->user->removeRole($roleKoorLorong);

            $deleted = $lorong->delete();

            if (!$deleted)
                return redirect()->route('lorong tm')->withErrors(['failed_deleting_lorong', 'Gagal menghapus Lorong.']);
        }

        return redirect()->route('lorong tm')->with('success', 'Berhasil menghapus Lorong');
    }

    /**
     * Show the presence info and its lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function view($id)
    {
        $lorong = Lorong::find($id);

        return view('lorong.view', ['lorong' => $lorong]);
    }

    /**
     * Show the presence info and its lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_member($id)
    {
        $lorong = Lorong::find($id);
        $santris = Santri::whereNull('exit_at')->get();
        // $santris = User::whereHas('santri', function ($query) {
        //     $query->whereNull('exit_at');
        // })->orderBy('fullname', 'asc')->get();

        return view('lorong.add_member', ['lorong' => $lorong, 'santris' => $santris]);
    }

    /**
     * Show the presence info and its lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_member(Request $request)
    {
        $lorongId = $request->route('id');

        $request->validate([
            'santri_id' => 'required|integer|exists:santris,id',
        ]);

        $lorong = Lorong::find($lorongId);

        if (!$lorong)
            return redirect()->route('add lorong member', $lorongId)->withErrors(['lorong_not_found' => 'Lorong tidak ditemukan.']);

        $santri = Santri::find($request->input('santri_id'));

        if (!$santri)
            return redirect()->route('add lorong member', $lorongId)->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        // validate that picked santri is not a leader
        $lorongsUnderLead = Lorong::where('fkSantri_leaderId', $request->input('santri_id'))->get();

        if (sizeof($lorongsUnderLead) >= 1)
            return redirect()->route('add lorong member', $lorongId)->withErrors(['santri_already_a_leader' => 'Santri sudah menjadi koor di lorong ini atau lorong lain.']);

        $santri->fkLorong_id = $request->route('id');

        $updated = $santri->save();

        if (!$updated)
            return redirect()->route('add lorong member', $lorongId)->withErrors(['failed_updating_fkLorong_id' => 'Gagal menambah anggota lorong.']);

        return redirect()->route('view lorong', $lorongId)->with('success', 'Berhasil menambah anggota lorong.');
    }

    /**
     * Delete member of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete_member($id, $santriId)
    {
        $lorong = Lorong::find($id);

        if (!$lorong)
            return redirect()->route('view lorong', $id)->withErrors(['lorong_not_found' => 'Lorong tidak ditemukan.']);

        $santri = Santri::find($santriId);

        if (!$santri)
            return redirect()->route('view lorong', $id)->withErrors(['santri_not_found' => 'Santri tidak ditemukan.']);

        $santri->fkLorong_id = null;

        $updated = $santri->save();

        if (!$updated)
            return redirect()->route('view lorong', $id)->withErrors(['failed_nullifyng_fkLorong_id' => 'Gagal menghapus anggota lorong.']);

        return redirect()->route('view lorong', $id)->with('success', 'Berhasil menghapus anggota Lorong');
    }

    /**
     * Show the lorong of current user if exists.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function my_lorong()
    {
        if (isset(auth()->user()->santri->id)) {
            $lorong = Lorong::where('fkSantri_leaderId', auth()->user()->santri->id)->first();
        } else {
            $lorong = null;
        }
        if (!$lorong) {
            if (isset(auth()->user()->santri->fkLorong_id)) {
                $lorong = Lorong::find(auth()->user()->santri->fkLorong_id);
            }
        }

        return view('lorong.my_lorong', ['lorong' => $lorong]);
    }
}

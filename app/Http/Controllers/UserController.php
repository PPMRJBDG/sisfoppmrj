<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Santri;
use App\Models\Lorong;
use App\Helpers\CommonHelpers;
use App\Helpers\CountDashboard;
use App\Helpers\WaSchedules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function my_profile()
    {
        $user = auth()->user();

        return view('user.my_profile', ['user' => $user]);
    }

    /**
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit_my_profile()
    {
        $user = auth()->user();
        $lorongs = Lorong::all();

        return view('user.edit_my_profile', ['user' => $user, 'lorongs' => $lorongs]);
    }

    public function edit_version()
    {
        $user = User::find(auth()->user()->id);
        $user->themes = (auth()->user()->themes == 'dark') ? 'light' : 'dark';
        $user->save();

        return 'reload';
    }

    /**
     * Show the list and manage table of lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage($angkatan = null, $role = null)
    {
        $count_dashboard = CountDashboard::index();
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'ASC')
            ->get();
        $list_role = DB::table('roles')
            ->select('id', 'name')
            ->whereNotIn('name', ['dewan guru', 'ku', 'pengabsen', 'santri', 'superadmin'])
            ->get();

        $is_all = true;
        if (isset($angkatan) and isset($role)) {
            $is_all = false;
            if ($angkatan != '-' and $role == '-') {
                $users = User::whereHas('santri', function ($query) {
                    $query->whereNull('exit_at');
                })->whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->orderBy('fullname', 'asc')->get();
            } elseif ($angkatan == '-' and $role != '-') {
                $users = User::whereHas('santri', function ($query) {
                    $query->whereNull('exit_at');
                })->whereHas('model_has_roles', function ($query) use ($role) {
                    $query->where('role_id', $role);
                })->orderBy('fullname', 'asc')->get();
            } elseif ($angkatan != '-' and $role != '-') {
                $users = User::whereHas('santri', function ($query) {
                    $query->whereNull('exit_at');
                })->whereHas('santri', function ($query) use ($angkatan) {
                    $query->where('angkatan', $angkatan);
                })->whereHas('model_has_roles', function ($query) use ($role) {
                    $query->where('role_id', $role);
                })->orderBy('fullname', 'asc')->get();
            } else {
                $is_all = true;
            }
        } else {
            $is_all = true;
        }
        if ($is_all) {
            $users = User::whereHas('santri', function ($query) {
                $query->whereNull('exit_at');
            })->orderBy('fullname', 'asc')->get();
        }
        $lorong = Lorong::select('fkSantri_leaderId')->get();

        return view('user.list_and_manage', [
            'count_dashboard' => $count_dashboard,
            'users' => $users,
            'select_angkatan' => $angkatan,
            'list_angkatan' => $list_angkatan,
            'select_role' => $role,
            'lorong' => $lorong,
            'list_role' => $list_role
        ]);
    }

    public function list_alumni($angkatan = null)
    {
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->whereNotNull('exit_at')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'ASC')
            ->get();
        if (isset($angkatan)) {
            $users = User::whereHas('santri', function ($query) {
                $query->where('exit_at', '!=', null);
            })->whereHas('santri', function ($query) use ($angkatan) {
                $query->where('angkatan', $angkatan);
            })->orderBy('fullname', 'asc')->get();
        } else {
            $users = User::whereHas('santri', function ($query) {
                $query->where('exit_at', '!=', null);
            })->orderBy('fullname', 'asc')->get();
        }

        return view('user.list_alumni', ['users' => $users, 'select_angkatan' => $angkatan, 'list_angkatan' => $list_angkatan]);
    }

    public function list_muballigh($angkatan = null)
    {
        $list_angkatan = DB::table('santris')
            ->select('angkatan')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'ASC')
            ->get();

        if (isset($angkatan)) {
            $users = DB::table('users')
                ->join('santris', 'fkUser_id', '=', 'users.id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('model_has_roles.role_id', 10)
                ->where('santris.angkatan', $angkatan)
                ->get();
        } else {
            $users = DB::table('users')
                ->join('santris', 'fkUser_id', '=', 'users.id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('model_has_roles.role_id', 10)
                ->get();
        }

        return view('user.list_muballigh', ['users' => $users, 'select_angkatan' => $angkatan, 'list_angkatan' => $list_angkatan]);
    }

    public function list_others()
    {
        // $users = DB::table('users')
        //     ->join('model_has_roles', 'model_id', '=', 'users.id')
        //     ->whereIn('model_has_roles.role_id', [1, 6, 11])
        //     ->get();
        $users = User::whereHas('model_has_roles', function ($query) {
            $query->whereIn('role_id', [1, 6, 11]);
        })->get();

        return view('user.list_others', ['users' => $users]);
    }

    public function list_pelanggaran()
    {
        $users = DB::table('users')
            ->join('santris', 'fkUser_id', '=', 'users.id')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->where('model_has_roles.role_id', 10)
            ->get();

        return view('user.list_pelanggaran', ['users' => $users]);
    }

    /**
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $lorongs = Lorong::all();

        return view('user.create', ['lorongs' => $lorongs]);
    }

    /**
     * Insert user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('create users')) return redirect()->route('presence report');

        $request->validate([
            'fullname' => 'required|max:100',
            'email' => 'required|max:100|email|unique:users',
            'password' => 'required|max:100',
            'birthdate' => 'required|date',
            'gender' => 'required|string'
        ]);

        if ($request->input('role-santri'))
            $request->validate([
                'nis' => 'required|integer|unique:santris',
                'angkatan' => 'required|integer',
                'join_at' => 'required|date'
            ]);

        if ($request->input('fkLorong_id')) {
            $request->validate([
                'fkLorong_id' => 'integer|nullable'
            ]);
        }

        // check for image first
        if ($request->hasFile('profileImg')) {
            $request->validate([
                'profileImg' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
            ]);

            // Save the file locally in the storage/public/ folder under a new folder named /destinations
            $request->profileImg->store('users', 'public');
        }

        // GUARD for role superadmin
        if ($request->input('role-superadmin') && !auth()->user()->hasRole('superadmin'))
            return redirect()->route('create user')->withErrors(['superadmin_only' => `Hanya superadmin yang bisa memberi role superadmin.`]);

        // GUARD for role RJ1
        if ($request->input('role-rj1') && !auth()->user()->hasRole('superadmin'))
            return redirect()->route('create user')->withErrors(['superadmin_only' => `Hanya superadmin yang bisa memberi role rj1.`]);

        // start inserting user
        $inserted_user = User::create([
            'fullname' => $request->input('fullname'),
            'nohp' => $request->input('nohp'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'birthdate' => $request->input('birthdate'),
            'gender' => $request->input('gender'),
            'profileImgUrl' => $request->hasFile('profileImg') ? $request->profileImg->hashName() : null
        ]);

        CommonHelpers::createWaContact($request);

        if (!$inserted_user)
            return redirect()->route('create user')->withErrors(['failed_adding_user' => 'Gagal menambah user baru.']);

        if ($request->input('role-santri')) {
            $inserted_santri = Santri::create([
                'nama_ortu' => $request->input('nama_ortu'),
                'nohp_ortu' => $request->input('nohp_ortu'),
                'nis' => $request->input('nis'),
                'angkatan' => $request->input('angkatan'),
                'fkLorong_id' => $request->input('fkLorong_id') ? $request->input('fkLorong_id') : null,
                'join_at' => $request->input('join_at'),
                'fkUser_id' => $inserted_user->id,
                'ids' => uniqid()
            ]);

            if (!$inserted_santri)
                return redirect()->route('create user')->withErrors(['failed_updating_santri_data' => 'User berhasil ditambah namun data santri gagal ditambah.']);
        }

        $all_role = Role::get();
        foreach ($all_role as $vrl) {
            if ($request->input($vrl->role_name)) {
                // assign role
                $checkRole = Role::findByName($vrl->name);

                if (!$checkRole)
                    return redirect()->route('create user')->withErrors(['failed_giving_role' => 'User dan data mahasiswa berhasil ditambah namun gagal memberi role ' . $vrl->name . ' (Role ' . $vrl->name . ' tidak ditemukan). Harap hilangkan role ' . $vrl->name . ' dan tambahkan kembali atau hubungi Developer.']);

                $inserted_user->assignRole($checkRole);
            }
        }

        if ($request->input('role-santri'))
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('create user'))->with('success', 'Berhasil menambah user baru dengan role santri.');
        else
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('create user'))->with('success', 'Berhasil menambah user baru.');
    }

    /**
     * Show the create form of lorong.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        if (!auth()->user()->can('update users')) return redirect()->route('presence report');

        $user = User::find($id);
        $lorongs = Lorong::all();

        return view('user.edit', ['user' => $user, 'lorongs' => $lorongs]);
    }

    /**
     * Update user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $userIdToUpdate = $request->route('id');

        if (auth()->user()->id != $userIdToUpdate)
            if (!auth()->user()->can('update users')) return redirect()->route('presence report');

        $request->validate([
            'fullname' => 'required|max:100',
            'email' => 'required|max:100|email',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
        ]);

        CommonHelpers::createWaContact($request);

        // GUARD for role superadmin
        if ($request->input('role-superadmin') && !auth()->user()->hasRole('superadmin'))
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['superadmin_only' => `Hanya superadmin yang bisa memberi role superadmin.`]);

        // GUARD for role RJ1
        if ($request->input('role-rj1') && !auth()->user()->hasRole('superadmin'))
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['superadmin_only' => `Hanya superadmin yang bisa memberi role RJ1.`]);

        if ($request->input('role-santri')) {
            if ($request->input('santri_id')) {
                $santri = Santri::find($request->input('santri_id'));

                $request->validate([
                    'nis' => 'required|integer|unique:santris,nis,' . $santri->id,
                    'angkatan' => 'required|integer',
                    'fkLorong_id' => 'integer|nullable',
                    'join_at' => 'required|date'
                ]);
            } else
                $request->validate([
                    'nis' => 'required|integer|unique:santris',
                    'angkatan' => 'required|integer',
                    'fkLorong_id' => 'integer|nullable',
                    'join_at' => 'required|date'
                ]);
        }

        // validate availability of user existence
        $user = User::find($userIdToUpdate);

        if (!$user)
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['user_not_found' => `Can't update unexisting user.`]);

        // check for image first
        if ($request->hasFile('profileImg')) {
            $request->validate([
                'profileImg' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
            ]);

            // Save the file locally in the storage/public/ folder under a new folder named /destinations
            $request->profileImg->store('users', 'public');
        }

        // start updating user
        $user->fullname = $request->input('fullname');
        $user->nohp = $request->input('nohp');
        $user->email = $request->input('email');
        $user->birthdate = $request->input('birthdate');
        $user->gender = $request->input('gender');

        if ($request->input('exit_at'))
            $user->password = Hash::make('Bismillah@354');
        if ($request->input('password'))
            $user->password = Hash::make($request->input('password'));

        if ($request->hasFile('profileImg'))
            $user->profileImgurl = $request->profileImg->hashName();

        $updated_user = $user->save();

        if (!$updated_user)
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_updating_user' => 'Gagal mengubah user.']);

        // handling role changes, only authorized are able to update
        if (auth()->user()->can('update users')) {
            if ($request->input('role-santri')) {
                // if ($user->santri && $request->input('fkLorong_id')) {
                // validate that picked santri is not a leader
                // $lorongsUnderLead = Lorong::where('fkSantri_leaderId', $user->santri->id)->get();
                // if (sizeof($lorongsUnderLead) >= 1)
                //     return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['santri_already_a_leader' => 'Santri sudah menjadi koor lorong, tidak bisa menjadi anggota.']);
                // }

                $updated_santri = Santri::updateOrCreate(
                    ['fkUser_id' => $userIdToUpdate],
                    [
                        'nama_ortu' => $request->input('nama_ortu'),
                        'nohp_ortu' => $request->input('nohp_ortu'),
                        'angkatan' => $request->input('angkatan'),
                        'nis' => $request->input('nis'),
                        'fkLorong_id' => $request->input('exit_at') ? null : $request->input('fkLorong_id'),
                        'join_at' => $request->input('join_at'),
                        'exit_at' => $request->input('exit_at') ? $request->input('exit_at') : null,
                        'alasan_keluar' => $request->input('alasan_keluar'),
                    ]
                );

                if (!$updated_santri)
                    return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_updating_santri_data' => 'User berhasil diubah namun data santri gagal diubah.']);

                // assign role
                $roleSantri = Role::findByName('santri');

                if (!$roleSantri)
                    return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_giving_role_santri' => 'User dan data santri berhasil diubah namun gagal memberi role santri (Role santri tidak ditemukan). Ini fatal, harap hilangkan role santri dan tambahkan kembali atau hubungi developer.']);

                if (!$user->hasRole('santri'))
                    $user->assignRole($roleSantri);
            } else {
                // assign role
                $roleSantri = Role::findByName('santri');

                if (!$roleSantri)
                    return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_removing_role_santri' => 'User berhasil diubah namun gagal menghapus role dan data santri (Role santri tidak ditemukan). Ini fatal, harap hilangkan role santri dan tambahkan kembali atau hubungi developer.']);

                $user->removeRole($roleSantri);

                if ($request->input('santri_id')) {
                    $santri = Santri::find($request->input('santri_id'));

                    if (!$santri)
                        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_deleting_santri_data' => 'User berhasil diubah dan role berhasil dihapus, namun gagal menghapus data santri (Data santri tidak ditemukan). Ini fatal, harap hilangkan role santri dan tambahkan kembali atau hubungi developer.']);
                }
            }

            $all_role = Role::get();
            foreach ($all_role as $vrl) {
                if ($vrl->name != 'santri' && $vrl->name != 'koor lorong') {
                    $checkRole = Role::findByName($vrl->name);
                    if (!$checkRole)
                        return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->withErrors(['failed_giving_role' => 'User dan data santri berhasil diubah namun gagal menambah/menghapus role ' . $vrl->name . ' (Role ' . $vrl->name . ' tidak ditemukan). Ini fatal, harap hilangkan role ' . $vrl->name . ' dan tambahkan kembali atau hubungi developer.']);

                    if ($request->input($vrl->role_name)) {
                        $user->assignRole($checkRole);
                        echo $vrl->role_name . ': ' . $request->input($vrl->role_name) . '<br>';
                    } else {
                        $user->removeRole($checkRole);
                        echo $vrl->role_name . ': ' . $request->input($vrl->role_name) . '<br>';
                    }
                }
            }
            // exit;
        }

        // ucapan selamat dan arahan sesuai mekanisme
        // cek sodaqoh, membawa barang, pamitan, SS, left group
        if ($request->input('alasan_keluar') == 'Sudah Lulus') {
            $caption = CommonHelpers::settings()->wa_info_lulus;
            WaSchedules::save($request->input('fullname') . ' Lulus', $caption, WaSchedules::getContactId($request->input('nohp')));
        }

        if ($request->input('role-santri'))
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->with('success', 'Berhasil mengubah user dan data santri.');
        else
            return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('edit user', $userIdToUpdate))->with('success', 'Berhasil mengubah user.');
    }

    /**
     * Update user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update_my_profile(Request $request)
    {
        $request->validate([
            'fullname' => 'required|max:100',
            'email' => 'required|max:100|email',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
        ]);

        CommonHelpers::createWaContact($request);

        // validate availability of user existence
        $user = auth()->user();

        if (!$user)
            return redirect()->route('edit my profile')->withErrors(['user_not_found' => `Can't update unexisting user.`]);

        // check for image first
        if ($request->hasFile('profileImg')) {
            $request->validate([
                'profileImg' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
            ]);

            // Save the file locally in the storage/public/ folder under a new folder named /destinations
            $request->profileImg->store('users', 'public');
        }

        // start updating user
        $user->fullname = $request->input('fullname');
        $user->nohp = $request->input('nohp');
        $user->email = $request->input('email');
        $user->birthdate = $request->input('birthdate');
        $user->gender = $request->input('gender');

        if ($request->input('password'))
            $user->password = Hash::make($request->input('password'));

        if ($request->hasFile('profileImg'))
            $user->profileImgurl = $request->profileImg->hashName();

        $updated_user = $user->save();

        if (!$updated_user)
            return redirect()->route('edit my profile')->withErrors(['failed_updating_user' => 'Gagal mengubah user.']);

        return redirect()->route('edit my profile')->with('success', 'Berhasil mengubah user.');
    }

    /**
     * Show the presence info and its lorongs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function view()
    {
        return view('user.view');
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete(Request $request)
    {

        // $id = $request->route('id');
        // $user = User::find($id);

        // if ($user) {
        //     // check if user is a lorong leader
        //     if ($user->santri) {
        //         $isALorongLeader = Lorong::where('fkSantri_leaderId', $user->santri->id)->first();

        //         if ($isALorongLeader)
        //             return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('user tm'))->withErrors(['user_is_lorong_leader' => 'User merupakan koor lorong.']);

        //         $updated_santri = Santri::updateOrCreate(
        //             ['fkUser_id' => $id],
        //             [
        //                 'fkLorong_id' => null,
        //                 'exit_at' => date('Y-m-d')
        //             ]
        //         );

        //         if (!$updated_santri)
        //             return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('user tm'))->withErrors(['failed_deleting_user' => 'Gagal menghapus user.']);
        //     }
        //     echo var_dump($request);
        //     exit;
        //     return ($request->input('previous_url') ? redirect()->to($request->input('previous_url')) : redirect()->route('user tm'))->with('success', 'Berhasil menghapus user');
        // }
    }
}

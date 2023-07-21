<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\SystemMetaData;
use App\Helpers\PresenceGroupsChecker;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Santri;
use App\Models\Lorong;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }   
    
    /**
     * Show the list table of latest presences.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function register(Request $request)
    {
        $lorongs = Lorong::all();
        
        return view('auth.register', ['lorongs' => $lorongs]);
    }
    
    /**
     * Insert user from public.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store_from_public(Request $request)
    {
        $request->validate([
            'fullname' => 'required|max:100',
            'email' => 'required|max:100|email|unique:users',
            'password' => 'required|max:100',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
            'fkLorong_id' => 'integer|nullable'
        ]);

        // start inserting user
        $inserted_user = User::create([
            'fullname' => $request->input('fullname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'birthdate' => $request->input('birthdate'),
            'gender' => $request->input('gender'),
        ]);
        
        if(!$inserted_user)
            return redirect()->route('register')->withErrors(['failed_adding_user' => 'Gagal menambah user baru.']);

        $inserted_santri = Santri::create([
            'nis' => rand(10,10000000000),
            'angkatan' => 2022,
            'fkLorong_id' => $request->input('fkLorong_id'),
            'join_at' => '2022-09-24',
            'fkUser_id' => $inserted_user->id
        ]);

        if(!$inserted_santri)
            return redirect()->route('register')->withErrors(['failed_updating_santri_data' => 'User berhasil ditambah namun data santri gagal ditambah.']);

        // assign role
        $roleSantri = Role::findByName('santri');

        if(!$roleSantri)
            return redirect()->route('register')->withErrors(['failed_giving_role_santri' => 'User dan data santri berhasil ditambah namun gagal memberi role santri (Role santri tidak ditemukan). Ini fatal, harap hilangkan role santri dan tambahkan kembali atau hubungi Developer.']);

        $inserted_user->assignRole($roleSantri);

        if($request->input('role-santri'))
            return redirect()->route('login')->with('success', 'Berhasil menambah user baru dengan role santri.');
        else
            return redirect()->route('login')->with('success', 'Berhasil menambah user baru.');
    }
}
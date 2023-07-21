<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::findOrCreate('view users list');
        Permission::findOrCreate('create users');
        Permission::findOrCreate('delete users');
        Permission::findOrCreate('update users');
        
        Permission::findOrCreate('view lorongs list');
        Permission::findOrCreate('create lorongs');
        Permission::findOrCreate('delete lorongs');
        Permission::findOrCreate('update lorongs');
        Permission::findOrCreate('add lorong members');
        Permission::findOrCreate('remove lorong members');

        Permission::findOrCreate('view presences list');
        Permission::findOrCreate('create presences');
        Permission::findOrCreate('delete presences');
        Permission::findOrCreate('update presences');
        Permission::findOrCreate('create presents');
        Permission::findOrCreate('delete presents');

        Permission::findOrCreate('view my lorong permits list');
        Permission::findOrCreate('view my permits list');
        Permission::findOrCreate('approve permits');
        
        Permission::findOrCreate('view materis list');
        Permission::findOrCreate('create materis');
        Permission::findOrCreate('delete materis');
        Permission::findOrCreate('update materis');

        Permission::findOrCreate('view monitoring materis list');
        Permission::findOrCreate('update monitoring materis');

        // superadmin
        $superadmin = Role::findOrCreate('superadmin');
        $superadmin->givePermissionTo('view users list');
        $superadmin->givePermissionTo('create users');
        $superadmin->givePermissionTo('delete users');
        $superadmin->givePermissionTo('update users');

        $superadmin->givePermissionTo('view lorongs list');
        $superadmin->givePermissionTo('create lorongs');
        $superadmin->givePermissionTo('delete lorongs');
        $superadmin->givePermissionTo('update lorongs');
        $superadmin->givePermissionTo('add lorong members');
        $superadmin->givePermissionTo('remove lorong members');

        $superadmin->givePermissionTo('view presences list');
        $superadmin->givePermissionTo('create presences');
        $superadmin->givePermissionTo('delete presences');
        $superadmin->givePermissionTo('update presences');
        $superadmin->givePermissionTo('create presents');
        $superadmin->givePermissionTo('delete presents');

        $superadmin->givePermissionTo('approve permits');

        $superadmin->givePermissionTo('view materis list');
        $superadmin->givePermissionTo('create materis');
        $superadmin->givePermissionTo('delete materis');
        $superadmin->givePermissionTo('update materis');
        
        $superadmin->givePermissionTo('view monitoring materis list');
        $superadmin->givePermissionTo('update monitoring materis');

        // santri
        $santri = Role::findOrCreate('santri');     

        $santri->givePermissionTo('view my permits list');   
        $santri->revokePermissionTo('view monitoring materis list');
        
        // koor lorong
        $koorlorong = Role::findOrCreate('koor lorong'); 

        $koorlorong->givePermissionTo('view my lorong permits list');
        $koorlorong->givePermissionTo('approve permits');    
        $koorlorong->givePermissionTo('view presences list');
        $koorlorong->givePermissionTo('create presences');
        $koorlorong->givePermissionTo('create presents');
        $koorlorong->givePermissionTo('delete presents');

        // wakil koor lorong
        // $wakil = Role::findOrCreate('wakil koor lorong');
        // $wakil->givePermissionTo('create presents');

        // IT
        $it = Role::findOrCreate('divisi it'); 

        $it->givePermissionTo('view users list');
        $it->givePermissionTo('create users');
        $it->givePermissionTo('delete users');
        $it->givePermissionTo('update users');
        $it->givePermissionTo('add lorong members');
        $it->givePermissionTo('remove lorong members');
        $it->givePermissionTo('view lorongs list');

        // Kurikulum
        $kurikulum = Role::findOrCreate('divisi kurikulum'); 

        $kurikulum->givePermissionTo('view presences list');
        $kurikulum->givePermissionTo('create presences');
        $kurikulum->givePermissionTo('delete presences');
        $kurikulum->givePermissionTo('update presences');
        $kurikulum->givePermissionTo('view materis list');
        $kurikulum->givePermissionTo('create materis');
        $kurikulum->givePermissionTo('delete materis');
        $kurikulum->givePermissionTo('update materis');
        $kurikulum->givePermissionTo('view monitoring materis list');
        $kurikulum->givePermissionTo('update monitoring materis');

        // Dewan guru
        $dewanGuru = Role::findOrCreate('dewan guru'); 

        $dewanGuru->givePermissionTo('view presences list');
        $dewanGuru->givePermissionTo('view users list');
        $dewanGuru->givePermissionTo('view monitoring materis list');

        // RJ1
        $rj1 = Role::findOrCreate('rj1'); 

        $rj1->givePermissionTo('approve permits');   
        $rj1->givePermissionTo('view materis list');
        $rj1->givePermissionTo('create materis');
        $rj1->givePermissionTo('delete materis');
        $rj1->givePermissionTo('update materis');
        $rj1->givePermissionTo('view monitoring materis list');
        $rj1->givePermissionTo('update monitoring materis');

        // WK
        $wk = Role::findOrCreate('wk'); 

        $wk->givePermissionTo('approve permits');   
        $wk->givePermissionTo('view materis list');
        $wk->givePermissionTo('create materis');
        $wk->givePermissionTo('delete materis');
        $wk->givePermissionTo('update materis');
        $wk->givePermissionTo('view monitoring materis list');
        $wk->givePermissionTo('update monitoring materis');
        $wk->givePermissionTo('view presences list');
        $wk->givePermissionTo('create presences');
        $wk->givePermissionTo('create presents');
        $wk->givePermissionTo('delete presents');
        
        // Pengabsen
        $pengabsen = Role::findOrCreate('pengabsen'); 

        $pengabsen->givePermissionTo('create presents');
        $pengabsen->givePermissionTo('view presences list');
        $pengabsen->givePermissionTo('approve permits');    
        $pengabsen->givePermissionTo('delete presents');

        $mubalegh = Role::findOrCreate('mubalegh'); 
    }
}

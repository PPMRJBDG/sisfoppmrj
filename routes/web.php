<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');
// Route::get('/{tb}', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/home/{tb}/{select_angkatan}/{select_periode}', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/tabgraf/{tb}/{select_angkatan}/{select_periode}', [App\Http\Controllers\HomeController::class, 'tabgraf']);

Route::get('/register', [App\Http\Controllers\RegisterController::class, 'register'])->name('register');
Route::post('/register/store', [App\Http\Controllers\RegisterController::class, 'store_from_public'])->name('store user from public');

Auth::routes(['register' => false]);

Route::get('/profil', [App\Http\Controllers\UserController::class, 'my_profile'])->name('my profile');
Route::get('/profil/edit', [App\Http\Controllers\UserController::class, 'edit_my_profile'])->name('edit my profile');
Route::post('/profile/update', [App\Http\Controllers\UserController::class, 'update_my_profile'])->name('update my profile');

Route::get('/presensi/izin/persetujuan/create', [App\Http\Controllers\PresenceController::class, 'create_permit'])->name('create presence permit')->middleware('role:koor lorong|superadmin|rj1|wk');
Route::post('/presensi/izin/persetujuan/store', [App\Http\Controllers\PresenceController::class, 'store_permit'])->name('store presence permit')->middleware('role:koor lorong|superadmin|rj1|wk');
Route::get('/presensi/izin/persetujuan/delete', [App\Http\Controllers\PresenceController::class, 'delete_permit'])->name('delete presence permit')->middleware('role:koor lorong|superadmin|rj1|wk');

Route::get('/presensi/izin/pengajuan', [App\Http\Controllers\PresenceController::class, 'create_my_permit'])->name('presence permit submission')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/pengajuan/berjangka', [App\Http\Controllers\PresenceController::class, 'create_my_ranged_permit'])->name('ranged presence permit submission')->middleware('role:santri|superadmin');
Route::post('/presensi/izin/pengajuan/store', [App\Http\Controllers\PresenceController::class, 'store_my_permit'])->name('store my presence permit')->middleware('role:santri|superadmin');
Route::post('/presensi/izin/pengajuan/berjangka/store', [App\Http\Controllers\PresenceController::class, 'store_my_ranged_permit'])->name('store my ranged presence permit')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/persetujuan', [App\Http\Controllers\PresenceController::class, 'permit_approval'])->name('presence permit approval')->middleware('permission:approve permits');
Route::get('/presensi/izin/persetujuan/{tb}', [App\Http\Controllers\PresenceController::class, 'permit_approval'])->name('presence permit approval tb')->middleware('permission:approve permits');
Route::get('/presensi/izin/saya', [App\Http\Controllers\PresenceController::class, 'my_permits'])->name('my presence permits')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/saya/edit', [App\Http\Controllers\PresenceController::class, 'edit_permit'])->name('edit presence permit')->middleware('role:santri|superadmin');
Route::post('/presensi/izin/saya/update', [App\Http\Controllers\PresenceController::class, 'update_permit'])->name('update presence permit')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/saya/delete', [App\Http\Controllers\PresenceController::class, 'delete_my_permit'])->name('delete my presence permit')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/saya/berjangka/delete/{id}', [App\Http\Controllers\PresenceController::class, 'delete_my_ranged_permit'])->name('delete my ranged presence permit')->middleware('role:santri|superadmin');
Route::get('/presensi/izin/saya/approve', [App\Http\Controllers\PresenceController::class, 'approve_permit'])->name('approve presence permit')->middleware('permission:approve permits');
Route::get('/presensi/izin/saya/reject', [App\Http\Controllers\PresenceController::class, 'reject_permit'])->name('reject presence permit')->middleware('permission:approve permits');
Route::get('/presensi/izin/list', [App\Http\Controllers\PresenceController::class, 'permits_list'])->name('permits list')->middleware('role:dewan guru|superadmin|rj1');
Route::get('/presensi/izin/list/{tb}', [App\Http\Controllers\PresenceController::class, 'permits_list'])->name('permits list')->middleware('role:dewan guru|superadmin|rj1');

// Route::get('/presensi/laporan', [App\Http\Controllers\PresenceController::class, 'report'])->name('presence report');

// presences
Route::get('/presensi/terbaru', [App\Http\Controllers\PresenceController::class, 'latest_list'])->name('latest presences')->middleware('role:santri|superadmin|koor lorong');
Route::get('/presensi/laporan-umum', [App\Http\Controllers\PresenceController::class, 'report'])->name('presence report');
Route::get('/presensi/list', [App\Http\Controllers\PresenceController::class, 'list_and_manage'])->name('presence tm')->middleware('permission:view presences list');
Route::get('/presensi/list/create', [App\Http\Controllers\PresenceController::class, 'create'])->name('create presence')->middleware('permission:create presences');
Route::get('/presensi/list/{id}', [App\Http\Controllers\PresenceController::class, 'view'])->name('view presence')->middleware('permission:view presences list');
Route::get('/presensi/list/{id}/present/create', [App\Http\Controllers\PresenceController::class, 'create_present'])->name('create present')->middleware('permission:create presents');
Route::post('/presensi/list/store', [App\Http\Controllers\PresenceController::class, 'store'])->name('store presence')->middleware('permission:create presents');
Route::post('/presensi/list/update/{id}', [App\Http\Controllers\PresenceController::class, 'update'])->name('update presence')->middleware('permission:update presences');
Route::get('/presensi/list/edit/{id}', [App\Http\Controllers\PresenceController::class, 'edit'])->name('edit presence')->middleware('permission:update presences');
Route::get('/presensi/list/delete/{id}', [App\Http\Controllers\PresenceController::class, 'delete'])->name('delete presence')->middleware('permission:delete presences');
Route::get('/presensi/{id}', [App\Http\Controllers\PresenceController::class, 'create_my_present'])->name('create my present')->middleware('role:santri');
Route::post('/presensi/{id}/store/me', [App\Http\Controllers\PresenceController::class, 'store_my_present'])->name('store my present')->middleware('role:santri');
Route::post('/presensi/{id}/store', [App\Http\Controllers\PresenceController::class, 'store_present'])->name('store present')->middleware('permission:create presents');
Route::get('/presensi/{id}/delete/{santriId}', [App\Http\Controllers\PresenceController::class, 'delete_present'])->name('delete present')->middleware('role:superadmin|rj1|wk|koor lorong'); //permission:delete presents
Route::get('/presensi/{id}/present/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_present'])->name('is present')->middleware('role:superadmin|rj1|wk|koor lorong');
Route::get('/presensi/{id}/late/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_late'])->name('is late');
Route::get('/presensi/{id}/notlate/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_not_late'])->name('is not late');

// presence groups
Route::get('/presensi/list/group/check-schedules', [App\Http\Controllers\PresenceController::class, 'check_schedules'])->name('check presence schedules');
Route::get('/presensi/list/group/create', [App\Http\Controllers\PresenceController::class, 'create_group'])->name('create presence group')->middleware('permission:create presences');;
Route::get('/presensi/list/group/{id}', [App\Http\Controllers\PresenceController::class, 'view_group'])->name('view presence group')->middleware('permission:view presences list');;
Route::post('/presensi/list/group/store', [App\Http\Controllers\PresenceController::class, 'store_group'])->name('store presence group')->middleware('permission:create presences');
Route::post('/presensi/list/group/update/{id}', [App\Http\Controllers\PresenceController::class, 'update_group'])->name('update presence group')->middleware('permission:update presences');
Route::get('/presensi/list/group/edit/{id}', [App\Http\Controllers\PresenceController::class, 'edit_group'])->name('edit presence group')->middleware('permission:update presences');
Route::get('/presensi/list/group/delete/{id}', [App\Http\Controllers\PresenceController::class, 'delete_group'])->name('delete presence group')->middleware('permission:delete presences');
Route::get('/presensi/list/group/{id}/recap', [App\Http\Controllers\PresenceController::class, 'select_presence_group_recap'])->name('select presence group recap')->middleware('permission:view presences list');
Route::get('/presensi/list/group/{id}/recap/{fromDate}/{toDate}/{lorongId}', [App\Http\Controllers\PresenceController::class, 'view_presence_group_recap'])->name('view presence group recap')->middleware('permission:view presences list');
Route::get('/presensi/list/group/{id}/presensi/create', [App\Http\Controllers\PresenceController::class, 'create_in_group'])->name('create presence in group')->middleware('permission:create presences');
Route::post('/presensi/list/group/{id}/presensi/store', [App\Http\Controllers\PresenceController::class, 'store_in_group'])->name('store presence in group')->middleware('permission:create presences');
Route::get('/permit/{ids}', [App\Http\Controllers\PublicController::class, 'view_permit'])->name('view permit');
Route::get('/permit/reject/{ids}', [App\Http\Controllers\PublicController::class, 'reject_permit'])->name('reject permit');

// Lorongs
Route::get('/lorong/saya', [App\Http\Controllers\LorongController::class, 'my_lorong'])->name('my lorong')->middleware('role:santri|koor lorong|superadmin');
Route::get('/lorong/list', [App\Http\Controllers\LorongController::class, 'list_and_manage'])->name('lorong tm')->middleware('permission:view lorongs list');
Route::get('/lorong/list/create', [App\Http\Controllers\LorongController::class, 'create'])->name('create lorong')->middleware('permission:create lorongs');
Route::get('/lorong/list/{id}', [App\Http\Controllers\LorongController::class, 'view'])->name('view lorong')->middleware('permission:view lorongs list');
Route::get('/lorong/list/{id}/add-member', [App\Http\Controllers\LorongController::class, 'add_member'])->name('add lorong member')->middleware('permission:add lorong members');
Route::get('/lorong/list/{id}/add-member/store', [App\Http\Controllers\LorongController::class, 'store_member'])->name('store lorong member')->middleware('permission:add lorong members');
Route::get('/lorong/list/{id}/delete-member/{santriId}', [App\Http\Controllers\LorongController::class, 'delete_member'])->name('delete lorong member')->middleware('permission:remove lorong members');
Route::post('/lorong/list/store', [App\Http\Controllers\LorongController::class, 'store'])->name('store lorong')->middleware('permission:create lorongs');
Route::post('/lorong/list/update/{id}', [App\Http\Controllers\LorongController::class, 'update'])->name('update lorong')->middleware('permission:update lorongs');
Route::get('/lorong/list/edit/{id}', [App\Http\Controllers\LorongController::class, 'edit'])->name('edit lorong')->middleware('permission:update lorongs');
Route::get('/lorong/list/delete/{id}', [App\Http\Controllers\LorongController::class, 'delete'])->name('delete lorong')->middleware('permission:delete lorongs');

// users
Route::get('/user/list/santri', [App\Http\Controllers\UserController::class, 'list_and_manage'])->name('user tm')->middleware('permission:view users list');
Route::get('/user/list/santri/{angkatan}', [App\Http\Controllers\UserController::class, 'list_and_manage'])->name('user tm a')->middleware('permission:view users list');
Route::get('/user/list/santri/{angkatan}/{role}', [App\Http\Controllers\UserController::class, 'list_and_manage'])->name('user tm ar')->middleware('permission:view users list');
Route::get('/user/list/muballigh', [App\Http\Controllers\UserController::class, 'list_muballigh'])->name('user mt')->middleware('permission:view users list');
Route::get('/user/list/muballigh/{angkatan}', [App\Http\Controllers\UserController::class, 'list_muballigh'])->name('user mt')->middleware('permission:view users list');
Route::get('/user/list/others', [App\Http\Controllers\UserController::class, 'list_others'])->name('user others')->middleware('role:superadmin');
Route::get('/user/list/alumni', [App\Http\Controllers\UserController::class, 'list_alumni'])->name('user alumni')->middleware('permission:view users list');
Route::get('/user/list/alumni/{angkatan}', [App\Http\Controllers\UserController::class, 'list_alumni'])->name('user alumni')->middleware('permission:view users list');
Route::get('/user/list/create', [App\Http\Controllers\UserController::class, 'create'])->name('create user')->middleware('permission:create users');
Route::get('/user/list/{id}', [App\Http\Controllers\UserController::class, 'view'])->name('view user')->middleware('permission:view users list');
Route::post('/user/list/store', [App\Http\Controllers\UserController::class, 'store'])->name('store user')->middleware('permission:create users');
Route::post('/user/list/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('update user')->middleware('permission:update users');
Route::get('/user/list/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('edit user')->middleware('permission:update users');
Route::get('/user/list/delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->name('delete user')->middleware('permission:delete users');

// materis
Route::get('/materi/list', [App\Http\Controllers\MateriController::class, 'list_and_manage'])->name('materi tm')->middleware('permission:view materis list');
Route::get('/materi/list/create', [App\Http\Controllers\MateriController::class, 'create'])->name('create materi')->middleware('permission:create materis');
Route::post('/materi/list/store', [App\Http\Controllers\MateriController::class, 'store'])->name('store materi')->middleware('permission:create materis');
Route::get('/materi/list/edit/{id}', [App\Http\Controllers\MateriController::class, 'edit'])->name('edit materi')->middleware('permission:update materis');
Route::post('/materi/list/update/{id}', [App\Http\Controllers\MateriController::class, 'update'])->name('update materi')->middleware('permission:update materis');
Route::get('/materi/list/delete/{id}', [App\Http\Controllers\MateriController::class, 'delete'])->name('delete materi')->middleware('permission:delete materis');

// monitoring materis
Route::get('/materi/monitoring/list', [App\Http\Controllers\MonitoringMateriController::class, 'list_and_manage'])->name('monitoring materi tm');
Route::get('/materi/monitoring/list/{materiId}/{santriId}', [App\Http\Controllers\MonitoringMateriController::class, 'edit'])->name('edit monitoring materi');
Route::post('/materi/monitoring/list/update', [App\Http\Controllers\MonitoringMateriController::class, 'update'])->name('update monitoring materi')->middleware('permission:update monitoring materis');
Route::get('/materi/monitoring/matching', [App\Http\Controllers\MonitoringMateriController::class, 'match_empty_pages'])->name('match empty monitoring materi pages');
Route::post('/materi/monitoring/materi_santri', [App\Http\Controllers\MonitoringMateriController::class, 'materi_santri'])->name('materi santri');

// pelanggaran
Route::get('/pelanggaran', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('pelanggaran tm')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/id/{id}', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('filter pelanggaran tm')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/archive', [App\Http\Controllers\PelanggaranController::class, 'list_archive'])->name('pelanggaran archive')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/create', [App\Http\Controllers\PelanggaranController::class, 'create'])->name('create pelanggaran')->middleware('role:superadmin|rj1|wk');
Route::post('/pelanggaran/store', [App\Http\Controllers\PelanggaranController::class, 'store'])->name('store pelanggaran')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/edit/{id}', [App\Http\Controllers\PelanggaranController::class, 'edit'])->name('edit pelanggaran')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/delete/{id}', [App\Http\Controllers\PelanggaranController::class, 'delete'])->name('delete pelanggaran')->middleware('role:superadmin|rj1|wk');
Route::get('/pelanggaran/archive/{id}', [App\Http\Controllers\PelanggaranController::class, 'archive'])->name('archive pelanggaran')->middleware('role:superadmin|rj1|wk');

// SODAQOH
Route::get('/sodaqoh/list', [App\Http\Controllers\SodaqohController::class, 'list'])->name('list sodaqoh')->middleware('role:ku|superadmin');
Route::get('/sodaqoh/delete/{id}/{periode}/{angkatan}', [App\Http\Controllers\SodaqohController::class, 'delete'])->name('delete sodaqoh')->middleware('role:ku|superadmin');
Route::get('/sodaqoh/list/{periode}', [App\Http\Controllers\SodaqohController::class, 'list'])->name('list periode sodaqoh')->middleware('role:ku|superadmin');
Route::get('/sodaqoh/list/{periode}/{angkatan}', [App\Http\Controllers\SodaqohController::class, 'list'])->name('list periode sodaqoh')->middleware('role:ku|superadmin');
Route::post('/sodaqoh/list/store', [App\Http\Controllers\SodaqohController::class, 'store'])->name('store sodaqoh')->middleware('role:ku|superadmin');

// KEUANGAN
Route::get('/receipt', [App\Http\Controllers\KeuanganController::class, 'receipt'])->name('view receipt')->middleware('role:superadmin|ku|rj1');
Route::get('/rab', [App\Http\Controllers\KeuanganController::class, 'rab'])->name('view rab')->middleware('role:superadmin|ku|rj1');
Route::get('/rab/create-update', [App\Http\Controllers\KeuanganController::class, 'rab_create_update'])->name('create update rab')->middleware('role:superadmin|ku|rj1');
Route::post('/rab/store', [App\Http\Controllers\KeuanganController::class, 'rab_store'])->name('store rab')->middleware('role:superadmin|ku|rj1');
Route::get('/op/in-out', [App\Http\Controllers\KeuanganController::class, 'inout'])->name('view inout')->middleware('role:superadmin|ku|rj1');
Route::post('/op/in-out/store', [App\Http\Controllers\KeuanganController::class, 'store_inout'])->name('store inout')->middleware('role:superadmin|ku|rj1');

// setting
Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('list setting')->middleware('role:superadmin|rj1');
Route::post('/setting/store_periode', [App\Http\Controllers\SettingController::class, 'store_periode'])->name('store periode')->middleware('role:superadmin|rj1');
Route::post('/setting/store_liburan', [App\Http\Controllers\SettingController::class, 'store_liburan'])->name('store liburan')->middleware('role:superadmin|rj1');
Route::post('/setting/store_jenis_pelanggaran', [App\Http\Controllers\SettingController::class, 'store_jenis_pelanggaran'])->name('store jenis pelanggaran')->middleware('role:superadmin|rj1');
Route::get('/setting/delete_periode/{id}', [App\Http\Controllers\SettingController::class, 'delete_periode'])->name('delete periode tahun')->middleware('role:superadmin|rj1');
Route::get('/setting/delete_liburan/{id}', [App\Http\Controllers\SettingController::class, 'delete_liburan'])->name('delete liburan')->middleware('role:superadmin|rj1');
Route::get('/setting/delete_jenis_pelanggaran/{id}', [App\Http\Controllers\SettingController::class, 'delete_jenis_pelanggaran'])->name('delete jenis pelanggaran')->middleware('role:superadmin|rj1');
Route::post('/setting/store_generate_sodaqoh', [App\Http\Controllers\SettingController::class, 'store_generate_sodaqoh'])->name('store generate sodaqoh')->middleware('role:superadmin|rj1');
Route::post('/setting/store_settings', [App\Http\Controllers\SettingController::class, 'store_settings'])->name('store settings')->middleware('role:superadmin');

// report
Route::get('/schedule/{time}', [App\Http\Controllers\PublicController::class, 'schedule']); // CRON
Route::get('/schedule/{time}/{presence_id}', [App\Http\Controllers\PublicController::class, 'schedule']); // CRON
Route::get('/daily/{year}/{month}/{date}/{angkatan}', [App\Http\Controllers\PublicController::class, 'daily_presences'])->name('view daily public presences recaps');
Route::get('/report/{ids}', [App\Http\Controllers\PublicController::class, 'report'])->name('view daily public presences recaps');
Route::get('/generator', [App\Http\Controllers\PublicController::class, 'generator']);

// msgtools
Route::get('/msgtools/contact', [App\Http\Controllers\MsgtoolsController::class, 'contact'])->name('msgtools view contact')->middleware('role:superadmin|rj1');
Route::get('/msgtools/report', [App\Http\Controllers\MsgtoolsController::class, 'report'])->name('msgtools view report')->middleware('role:superadmin|rj1');
Route::get('/msgtools/generate_bulk', [App\Http\Controllers\MsgtoolsController::class, 'generate_bulk'])->name('msgtools generate bulk')->middleware('role:superadmin|rj1');
Route::post('/msgtools/delete_contact', [App\Http\Controllers\MsgtoolsController::class, 'delete_contact'])->name('msgtools delete contact')->middleware('role:superadmin|rj1');
Route::post('/msgtools/create_group', [App\Http\Controllers\MsgtoolsController::class, 'create_group'])->name('msgtools create group')->middleware('role:superadmin|rj1');
Route::post('/msgtools/send_wa', [App\Http\Controllers\MsgtoolsController::class, 'send_wa'])->name('msgtools send wa')->middleware('role:superadmin|rj1');

Route::get('/run/migrate', function (Request $request) {
    return Artisan::call('migrate', ["--force" => true]);
});

Route::get('/run/seed', function (Request $request) {
    Artisan::call('db:seed', ['--class' => 'PermissionRoleSeeder', "--force" => true]);
    return Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', "--force" => true]);
});

Route::get('/run/link', function () {
    return Artisan::call('storage:link');
});

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

NEW MODEL:
- tingkatans
- banks
- poses
- rabs (update)

ENHANCHEMENT:
- in out ku
- rab kegiatan
- rab pengadaan barang (non rutin)
- hasil musya
- field niat tes muballigh
- role dewan guru
- rumusan target materi

*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
// Route::get('/{tb}', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');

Route::post('/fs01', [App\Http\Controllers\FsController::class, 'fs01']);
Route::post('/sync_setuserinfo', [App\Http\Controllers\FsController::class, 'sync_setuserinfo'])->name('sync set fs');
Route::post('/sync_getuserinfo', [App\Http\Controllers\FsController::class, 'sync_getuserinfo'])->name('sync get fs');
Route::post('/sync_deleteuserinfo', [App\Http\Controllers\FsController::class, 'sync_deleteuserinfo'])->name('sync delete fs');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');
Route::get('/home/{tb}/{select_angkatan}/{select_periode}', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/tabgraf/{tb}/{select_angkatan}/{select_periode}', [App\Http\Controllers\HomeController::class, 'tabgraf']);

// Route::get('/register', [App\Http\Controllers\RegisterController::class, 'register'])->name('register');
// Route::post('/register/store', [App\Http\Controllers\RegisterController::class, 'store_from_public'])->name('store user from public');

Auth::routes(['register' => false]);

Route::get('/profil', [App\Http\Controllers\UserController::class, 'my_profile'])->name('my profile');
Route::get('/profil/edit', [App\Http\Controllers\UserController::class, 'edit_my_profile'])->name('edit my profile');
Route::post('/profile/update', [App\Http\Controllers\UserController::class, 'update_my_profile'])->name('update my profile');
Route::get('/profil/version', [App\Http\Controllers\UserController::class, 'edit_version'])->name('edit version');

Route::get('/presensi/barcode', [App\Http\Controllers\PresenceController::class, 'barcode'])->name('barcode');
Route::post('/presensi/barcode/check', [App\Http\Controllers\PresenceController::class, 'check_barcode'])->name('check barcode')->middleware('role:superadmin|barcode');
Route::get('/presensi/generate-barcode', [App\Http\Controllers\PresenceController::class, 'generate_barcode'])->name('generate barcode')->middleware('role:superadmin|barcode');
Route::post('/presensi/barcode/store_present', [App\Http\Controllers\PresenceController::class, 'store_present_barcode'])->name('store present');

Route::get('/presensi/izin/persetujuan/create', [App\Http\Controllers\PresenceController::class, 'create_permit'])->name('create presence permit')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::post('/presensi/izin/persetujuan/store', [App\Http\Controllers\PresenceController::class, 'store_permit'])->name('store presence permit')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::post('/presensi/izin/persetujuan/store/ranged', [App\Http\Controllers\PresenceController::class, 'store_permit_ranged'])->name('store presence permit ranged')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/persetujuan/delete', [App\Http\Controllers\PresenceController::class, 'delete_permit'])->name('delete presence permit')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');

Route::get('/presensi/izin/pengajuan', [App\Http\Controllers\PresenceController::class, 'create_my_permit'])->name('presence permit submission')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/pengajuan/berjangka', [App\Http\Controllers\PresenceController::class, 'create_my_ranged_permit'])->name('ranged presence permit submission')->middleware('role:santri|superadmin|dewan guru');
Route::post('/presensi/izin/pengajuan/store', [App\Http\Controllers\PresenceController::class, 'store_my_permit'])->name('store my presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::post('/presensi/izin/pengajuan/berjangka/store', [App\Http\Controllers\PresenceController::class, 'store_my_ranged_permit'])->name('store my ranged presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/persetujuan', [App\Http\Controllers\PresenceController::class, 'permit_approval'])->name('presence permit approval')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/persetujuan/{tb}/{status}', [App\Http\Controllers\PresenceController::class, 'permit_approval'])->name('presence permit approval tb')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/saya', [App\Http\Controllers\PresenceController::class, 'my_permits'])->name('my presence permits')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/saya/edit', [App\Http\Controllers\PresenceController::class, 'edit_permit'])->name('edit presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::post('/presensi/izin/saya/update', [App\Http\Controllers\PresenceController::class, 'update_permit'])->name('update presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/saya/delete', [App\Http\Controllers\PresenceController::class, 'delete_my_permit'])->name('delete my presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/saya/berjangka/delete/{id}', [App\Http\Controllers\PresenceController::class, 'delete_my_ranged_permit'])->name('delete my ranged presence permit')->middleware('role:santri|superadmin|dewan guru');
Route::get('/presensi/izin/saya/approve', [App\Http\Controllers\PresenceController::class, 'approve_reject_permit'])->name('approve presence permit')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/saya/reject', [App\Http\Controllers\PresenceController::class, 'approve_reject_permit'])->name('reject presence permit')->middleware('role:koor lorong|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/pengajuan/berjangka/approve', [App\Http\Controllers\PresenceController::class, 'approve_reject_range_permit'])->name('approve presence range permit')->middleware('role:superadmin|rj1|dewan guru');
Route::get('/presensi/izin/pengajuan/berjangka/reject', [App\Http\Controllers\PresenceController::class, 'approve_reject_range_permit'])->name('reject presence range permit')->middleware('role:superadmin|rj1|dewan guru');
Route::get('/presensi/izin/pengajuan/delete_and_present', [App\Http\Controllers\PresenceController::class, 'delete_and_present'])->name('delete and present permit')->middleware('role:superadmin|rj1|dewan guru');
Route::get('/presensi/izin/list', [App\Http\Controllers\PresenceController::class, 'permits_list'])->name('permits list')->middleware('role:dewan guru|superadmin|rj1|wk|dewan guru');
Route::get('/presensi/izin/list/{tb}', [App\Http\Controllers\PresenceController::class, 'permits_list'])->name('permits list')->middleware('role:dewan guru|superadmin|rj1|wk|dewan guru');

// Route::get('/presensi/laporan', [App\Http\Controllers\PresenceController::class, 'report'])->name('presence report');

// presences
Route::get('/presensi/terbaru', [App\Http\Controllers\PresenceController::class, 'latest_list'])->name('latest presences')->middleware('role:santri|superadmin|koor lorong');
Route::get('/presensi/laporan-umum', [App\Http\Controllers\PresenceController::class, 'report'])->name('presence report');
Route::get('/presensi/list', [App\Http\Controllers\PresenceController::class, 'list_and_manage'])->name('presence tm')->middleware('permission:view presences list');
Route::get('/presensi/list/create', [App\Http\Controllers\PresenceController::class, 'create'])->name('create presence')->middleware('permission:create presences');
Route::get('/presensi/list/{id}', [App\Http\Controllers\PresenceController::class, 'view'])->name('view presence')->middleware('permission:view presences list');
// Route::get('/presensi/list/{id}/{lorong}', [App\Http\Controllers\PresenceController::class, 'view'])->name('view presence lorong')->middleware('permission:view presences list');
Route::get('/presensi/list/{id}/present/create', [App\Http\Controllers\PresenceController::class, 'create_present'])->name('create present')->middleware('permission:create presents');
Route::post('/presensi/list/store', [App\Http\Controllers\PresenceController::class, 'store'])->name('store presence')->middleware('permission:create presents');
Route::post('/presensi/list/update/{id}', [App\Http\Controllers\PresenceController::class, 'update'])->name('update presence')->middleware('role:superadmin|rj1|wk|divisi kurikulum|dewan guru');
Route::get('/presensi/list/edit/{id}', [App\Http\Controllers\PresenceController::class, 'edit'])->name('edit presence')->middleware('permission:update presences');
Route::get('/presensi/list/delete/{id}', [App\Http\Controllers\PresenceController::class, 'delete'])->name('delete presence')->middleware('permission:delete presences');
Route::get('/presensi/{id}', [App\Http\Controllers\PresenceController::class, 'create_my_present'])->name('create my present')->middleware('role:santri');
Route::post('/presensi/{id}/store/me', [App\Http\Controllers\PresenceController::class, 'store_my_present'])->name('store my present')->middleware('role:santri');
Route::post('/presensi/{id}/store', [App\Http\Controllers\PresenceController::class, 'store_present'])->name('store present')->middleware('permission:create presents');
Route::get('/presensi/{id}/delete/{santriId}', [App\Http\Controllers\PresenceController::class, 'delete_present'])->name('delete present')->middleware('role:superadmin|rj1|wk|koor lorong|dewan guru'); //permission:delete presents
Route::get('/presensi/{id}/present/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_present'])->name('is present')->middleware('role:superadmin|rj1|wk|koor lorong|dewan guru');
Route::get('/presensi/{id}/late/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_late'])->name('is late');
Route::get('/presensi/{id}/notlate/{santriId}', [App\Http\Controllers\PresenceController::class, 'is_not_late'])->name('is not late');
Route::get('/presensi/daily/{select_tb}/{select_kbm}', [App\Http\Controllers\PresenceController::class, 'daily_presences'])->name('view daily public presences recaps');

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
// Public
Route::get('/permit/{ids}', [App\Http\Controllers\PublicController::class, 'view_permit'])->name('view permit');
Route::get('/permit/reject/{ids}', [App\Http\Controllers\PublicController::class, 'reject_permit'])->name('reject permit');
Route::get('/permit/approve/{ids}', [App\Http\Controllers\PublicController::class, 'approve_permit'])->name('approve permit');

// Lorongs
Route::get('/lorong/saya', [App\Http\Controllers\LorongController::class, 'my_lorong'])->name('my lorong')->middleware('role:santri|koor lorong|superadmin');
Route::get('/lorong/list', [App\Http\Controllers\LorongController::class, 'list_and_manage'])->name('lorong tm')->middleware('permission:view lorongs list');
Route::get('/lorong/list/create', [App\Http\Controllers\LorongController::class, 'create'])->name('create lorong')->middleware('permission:create lorongs');
Route::get('/lorong/list/{id}', [App\Http\Controllers\LorongController::class, 'view'])->name('view lorong')->middleware('permission:view lorongs list');
Route::get('/lorong/list/{id}/add-member', [App\Http\Controllers\LorongController::class, 'add_member'])->name('add lorong member')->middleware('permission:add lorong members');
Route::post('/lorong/list/{id}/add-member/store', [App\Http\Controllers\LorongController::class, 'store_member'])->name('store lorong member')->middleware('permission:add lorong members');
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
Route::get('/materi/list', [App\Http\Controllers\MateriController::class, 'list_and_manage'])->name('materi tm')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::get('/materi/list/create', [App\Http\Controllers\MateriController::class, 'create'])->name('create materi')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::post('/materi/list/store', [App\Http\Controllers\MateriController::class, 'store'])->name('store materi')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::get('/materi/list/edit/{id}', [App\Http\Controllers\MateriController::class, 'edit'])->name('edit materi')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::post('/materi/list/update/{id}', [App\Http\Controllers\MateriController::class, 'update'])->name('update materi')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::get('/materi/list/delete/{id}', [App\Http\Controllers\MateriController::class, 'delete'])->name('delete materi')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');

// materis dewan pengajar
Route::get('/jadwal-kbm', [App\Http\Controllers\MateriController::class, 'jadwal_kbm'])->name('jadwal kbm')->middleware('role:superadmin|dewan guru|santri');
Route::post('/jadwal-kbm/store', [App\Http\Controllers\MateriController::class, 'jadwal_kbm_store'])->name('jadwal kbm store')->middleware('role:superadmin|dewan guru|santri');
Route::get('/dewan-pengajar', [App\Http\Controllers\MateriController::class, 'list_pengajar'])->name('materi list pengajar')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::get('/dewan-pengajar/delete/{id}', [App\Http\Controllers\MateriController::class, 'delete_pengajar'])->name('materi delete pengajar')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::post('/dewan-pengajar/store', [App\Http\Controllers\MateriController::class, 'store_pengajar'])->name('materi store pengajar')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::post('/dewan-pengajar/update/{id}', [App\Http\Controllers\MateriController::class, 'update_pengajar'])->name('materi update pengajar')->middleware('role:superadmin|dewan guru|rj1|divisi kurikulum');
Route::get('/kalender-ppm', [App\Http\Controllers\PublicController::class, 'kalender_ppm'])->name('kalender_ppm');
Route::get('/kalender-ppm/template', [App\Http\Controllers\MateriController::class, 'template_kalender_ppm'])->name('template_kalender_ppm')->middleware('role:superadmin|rj1|wk|divisi kurikulum|dewan guru');
Route::post('/kalender-ppm/template/store', [App\Http\Controllers\MateriController::class, 'store_template_kalender_ppm'])->name('store_template_kalender_ppm')->middleware('role:superadmin|rj1|divisi kurikulum|dewan guru');
Route::get('/kalender-ppm/template/reset', [App\Http\Controllers\MateriController::class, 'reset_degur_template_kalender_ppm'])->name('reset_degur_template_kalender_ppm')->middleware('role:superadmin|rj1|divisi kurikulum|dewan guru');
Route::post('/kalender-ppm/store', [App\Http\Controllers\MateriController::class, 'store_kalender_ppm'])->name('store_kalender_ppm')->middleware('role:superadmin|rj1|wk|divisi kurikulum|dewan guru');
Route::get('/kalender-ppm/reset', [App\Http\Controllers\MateriController::class, 'reset_kalender_ppm'])->name('reset_kalender_ppm')->middleware('role:superadmin');

// monitoring materis
Route::get('/materi/monitoring/list', [App\Http\Controllers\MonitoringMateriController::class, 'list_and_manage'])->name('monitoring materi tm');
Route::get('/materi/monitoring/list/{materiId}/{santriId}', [App\Http\Controllers\MonitoringMateriController::class, 'edit'])->name('edit monitoring materi');
Route::post('/materi/monitoring/list/update', [App\Http\Controllers\MonitoringMateriController::class, 'update'])->name('update monitoring materi')->middleware('permission:update monitoring materis');
Route::get('/materi/monitoring/matching', [App\Http\Controllers\MonitoringMateriController::class, 'match_empty_pages'])->name('match empty monitoring materi pages');
Route::post('/materi/monitoring/materi_santri', [App\Http\Controllers\MonitoringMateriController::class, 'materi_santri'])->name('materi santri');

// pelanggaran
Route::get('/pelanggaran', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('pelanggaran tm1')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/s/{is_archive}', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('pelanggaran tm2')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/s/{is_archive}/param/{value}', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('filter pelanggaran tm')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/s/{is_archive}/param/{value}/{id}', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('filter pelanggaran id')->middleware('role:superadmin|rj1|wk|dewan guru');
// Route::get('/pelanggaran/archive', [App\Http\Controllers\PelanggaranController::class, 'list_archive'])->name('pelanggaran archive')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/create', [App\Http\Controllers\PelanggaranController::class, 'create'])->name('create pelanggaran')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::post('/pelanggaran/store', [App\Http\Controllers\PelanggaranController::class, 'store'])->name('store pelanggaran')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/edit/{id}', [App\Http\Controllers\PelanggaranController::class, 'edit'])->name('edit pelanggaran')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/delete/{id}', [App\Http\Controllers\PelanggaranController::class, 'delete'])->name('delete pelanggaran')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/archive/{id}', [App\Http\Controllers\PelanggaranController::class, 'archive'])->name('archive pelanggaran')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/pelanggaran/by/mahasiswa', [App\Http\Controllers\PelanggaranController::class, 'by_mahasiswa'])->name('pelanggaran by mhs')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::post('/pelanggaran/wa', [App\Http\Controllers\PelanggaranController::class, 'wa'])->name('pelanggaran wa')->middleware('role:superadmin|dewan guru');
Route::post('/pelanggaran/selesai_kafaroh', [App\Http\Controllers\PelanggaranController::class, 'selesai_kafaroh'])->name('selesai kafaroh')->middleware('role:superadmin|dewan guru');
Route::post('/pelanggaran/update_pelanggaran', [App\Http\Controllers\PelanggaranController::class, 'update_pelanggaran'])->name('update_pelanggaran')->middleware('role:superadmin|dewan guru');

// KEUANGAN
Route::get('/keuangan/mekanisme', [App\Http\Controllers\KeuanganController::class, 'mekanisme'])->name('mekanisme')->middleware('role:ku|superadmin|santri');
Route::get('/keuangan/sodaqoh', [App\Http\Controllers\KeuanganController::class, 'list_sodaqoh'])->name('list sodaqoh')->middleware('role:ku|superadmin');
Route::post('/keuangan/approve_payment', [App\Http\Controllers\KeuanganController::class, 'approve_payment'])->name('approve payment')->middleware('role:ku|superadmin');
Route::get('/keuangan/delete_sodaqoh/{id}/{periode}/{angkatan}/{select_lunas}', [App\Http\Controllers\KeuanganController::class, 'delete_sodaqoh'])->name('delete sodaqoh')->middleware('role:ku|superadmin');
Route::post('/keuangan/reminder_sodaqoh', [App\Http\Controllers\KeuanganController::class, 'reminder_sodaqoh'])->name('reminder sodaqoh')->middleware('role:ku|superadmin');
Route::get('/keuangan/sodaqoh/{periode}', [App\Http\Controllers\KeuanganController::class, 'list_sodaqoh'])->name('list periode sodaqoh')->middleware('role:ku|superadmin');
Route::get('/keuangan/sodaqoh/{periode}/{angkatan}', [App\Http\Controllers\KeuanganController::class, 'list_sodaqoh'])->name('list periode sodaqoh')->middleware('role:ku|superadmin');
Route::get('/keuangan/sodaqoh/{periode}/{angkatan}/{status}', [App\Http\Controllers\KeuanganController::class, 'list_sodaqoh'])->name('list periode sodaqoh')->middleware('role:ku|superadmin');
Route::post('/keuangan/sodaqoh/store', [App\Http\Controllers\KeuanganController::class, 'store_sodaqoh'])->name('store sodaqoh')->middleware('role:ku|superadmin');
Route::post('/keuangan/sodaqoh/store_santri', [App\Http\Controllers\KeuanganController::class, 'store_sodaqoh'])->name('store sodaqoh santri')->middleware('role:santri');
Route::get('/keuangan/tagihan', [App\Http\Controllers\KeuanganController::class, 'tagihan'])->name('tagihan')->middleware('role:superadmin|ku|rj1|santri');
Route::post('/keuangan/rab-tahunan/set-create', [App\Http\Controllers\KeuanganController::class, 'set_create_rab'])->name('set create rab')->middleware('role:superadmin|ku|rj');
Route::get('/keuangan/rab-tahunan', [App\Http\Controllers\KeuanganController::class, 'rab_tahunan'])->name('view rab tahunan')->middleware('role:superadmin|ku|rj1|wk');
Route::get('/keuangan/rab-tahunan/{select_periode}', [App\Http\Controllers\KeuanganController::class, 'rab_tahunan'])->name('view rab tahunan')->middleware('role:superadmin|ku|rj1|wk');
Route::post('/keuangan/rab-tahunan/store', [App\Http\Controllers\KeuanganController::class, 'rab_tahunan_store'])->name('store rab tahunan')->middleware('role:superadmin|ku|rj1|wk');
Route::get('/keuangan/rab-tahunan/delete/{id}', [App\Http\Controllers\KeuanganController::class, 'rab_tahunan_delete'])->name('delete rab tahunan')->middleware('role:superadmin|ku|rj1|wk');
Route::post('/keuangan/rab-tahunan/duplicate', [App\Http\Controllers\KeuanganController::class, 'duplicate_rab'])->name('duplicate rab')->middleware('role:superadmin');
Route::post('/keuangan/rab-tahunan/lock-unlock', [App\Http\Controllers\KeuanganController::class, 'lock_unlock'])->name('lock unlock rab')->middleware('role:superadmin');
Route::get('/keuangan/jurnal', [App\Http\Controllers\KeuanganController::class, 'jurnal'])->name('view jurnal')->middleware('role:superadmin|ku|rj1');
Route::get('/keuangan/jurnal/{bank}/{divisi}/{rab}/{tahun_bulan}/{penerimaan}', [App\Http\Controllers\KeuanganController::class, 'jurnal'])->name('view jurnal')->middleware('role:superadmin|ku|rj1');
Route::post('/keuangan/jurnal/store', [App\Http\Controllers\KeuanganController::class, 'jurnal_store'])->name('store jurnal')->middleware('role:superadmin|ku|rj1');
Route::post('/keuangan/jurnal/delete', [App\Http\Controllers\KeuanganController::class, 'jurnal_delete'])->name('delete jurnal')->middleware('role:superadmin|ku|rj1');
Route::get('/keuangan/rab-management-building', [App\Http\Controllers\KeuanganController::class, 'rab_management_building'])->name('rab management building')->middleware('role:superadmin|ku');
Route::get('/keuangan/rab-management-building/{id}', [App\Http\Controllers\KeuanganController::class, 'rab_management_building'])->name('rab management building id')->middleware('role:superadmin|ku');
Route::post('/keuangan/rab-management-building/store', [App\Http\Controllers\KeuanganController::class, 'store_management_building'])->name('store management building')->middleware('role:superadmin|ku');
Route::post('/keuangan/rab-detail-management-building/store', [App\Http\Controllers\KeuanganController::class, 'store_detail_management_building'])->name('store detail management building')->middleware('role:superadmin|ku');
Route::get('/keuangan/rab-management-building/delete/{id}', [App\Http\Controllers\KeuanganController::class, 'delete_management_building'])->name('delete management building')->middleware('role:superadmin|ku');
Route::get('/keuangan/rab-management-building/delete-detail/{id}', [App\Http\Controllers\KeuanganController::class, 'delete_detail_management_building'])->name('delete detail management building')->middleware('role:superadmin|ku');
Route::get('/keuangan/laporan-pusat', [App\Http\Controllers\KeuanganController::class, 'laporan_pusat'])->name('laporan pusat')->middleware('role:superadmin|ku');
Route::get('/keuangan/laporan-pusat/{tahun_bulan}', [App\Http\Controllers\KeuanganController::class, 'laporan_pusat'])->name('laporan pusat')->middleware('role:superadmin|ku');
Route::get('/keuangan/laporan-pusat/{tahun_bulan}/{print}', [App\Http\Controllers\KeuanganController::class, 'laporan_pusat'])->name('print laporan pusat')->middleware('role:superadmin|ku');
Route::get('/keuangan/rab-kegiatan', [App\Http\Controllers\KeuanganController::class, 'rab_kegiatan'])->name('rab kegiatan')->middleware('role:superadmin|ku|santri');
Route::get('/keuangan/rab-kegiatan/{id}', [App\Http\Controllers\KeuanganController::class, 'rab_kegiatan'])->name('rab kegiatan id')->middleware('role:superadmin|ku|santri');
Route::post('/keuangan/rab-kegiatan/store', [App\Http\Controllers\KeuanganController::class, 'store_rab_kegiatan'])->name('store rab kegiatan')->middleware('role:superadmin|ku|santri');
Route::post('/keuangan/rab-detail-kegiatan/store', [App\Http\Controllers\KeuanganController::class, 'store_detail_rab_kegiatan'])->name('store detail rab kegiatan')->middleware('role:superadmin|ku|santri');
Route::get('/keuangan/rab-kegiatan/delete/{id}', [App\Http\Controllers\KeuanganController::class, 'delete_rab_kegiatan'])->name('delete rab kegiatan')->middleware('role:superadmin|ku|santri');
Route::get('/keuangan/rab-kegiatan/delete-detail/{id}', [App\Http\Controllers\KeuanganController::class, 'delete_detail_rab_kegiatan'])->name('delete detail rab kegiatan')->middleware('role:superadmin|ku|santri');
Route::post('/keuangan/rab-detail-by-field/store', [App\Http\Controllers\KeuanganController::class, 'store_detail_by_field'])->name('store detail by field')->middleware('role:superadmin|ku|santri');
Route::get('/rab/{ids}', [App\Http\Controllers\PublicController::class, 'rab_kegiatan'])->name('rab kegiatan public');
Route::post('/rab/store-detail', [App\Http\Controllers\PublicController::class, 'store_detail_rab_kegiatan'])->name('store detail rab kegiatan public');
Route::get('/rab/delete-detail/{id}', [App\Http\Controllers\PublicController::class, 'delete_detail_rab_kegiatan'])->name('delete detail rab kegiatan public');
Route::post('/rab/store-rab-detail-by-field', [App\Http\Controllers\PublicController::class, 'store_detail_by_field'])->name('store detail by field public');
Route::get('/ku/{tahun_bulan}/{print}', [App\Http\Controllers\PublicController::class, 'laporan_pusat'])->name('print laporan pusat public');

// setting
Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('list setting')->middleware('role:superadmin|rj1');
Route::post('/setting/store_apps', [App\Http\Controllers\SettingController::class, 'store_apps'])->name('store apps')->middleware('role:superadmin|rj1');
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
Route::get('/generator', [App\Http\Controllers\PublicController::class, 'generator']);
Route::get('/report/{ids}', [App\Http\Controllers\PublicController::class, 'report'])->name('view report');
Route::get('/reporting/link_ortu', [App\Http\Controllers\ReportController::class, 'link_ortu'])->name('report link ortu')->middleware('role:superadmin|rj1|wk|dewan guru');
Route::get('/penilaian', [App\Http\Controllers\ReportController::class, 'penilaian'])->name('penilaian')->middleware('role:superadmin|dewan guru');
Route::post('/evaluation/store', [App\Http\Controllers\ReportController::class, 'store_evaluation'])->name('store evaluation')->middleware('role:superadmin|dewan guru');

Route::get('/dwngr/{id}/delete/{santriId}', [App\Http\Controllers\PublicController::class, 'presence_delete_present'])->name('dwngr delete present');
Route::get('/dwngr/{id}/present/{santriId}', [App\Http\Controllers\PublicController::class, 'presence_is_present'])->name('dwngr is present');
Route::get('/dwngr/{id}/late/{santriId}', [App\Http\Controllers\PublicController::class, 'presence_is_late'])->name('dwngr is late');
Route::get('/dwngr/{id}/notlate/{santriId}', [App\Http\Controllers\PublicController::class, 'presence_is_not_late'])->name('dwngr is not late');
Route::get('/dwngr/list/{id}', [App\Http\Controllers\PublicController::class, 'presence_view'])->name('dwngr view presence');
// Route::get('/dwngr/list/{id}/{lorong}', [App\Http\Controllers\PublicController::class, 'presence_view'])->name('dwngr view presence');

// Catatan Penghubung
Route::get('/catatan-penghubung', [App\Http\Controllers\CatatanPenghubungController::class, 'index'])->name('index')->middleware('role:superadmin|rj1|wk');
Route::post('/catatan-penghubung/store', [App\Http\Controllers\CatatanPenghubungController::class, 'store'])->name('store catatan')->middleware('role:superadmin|rj1|wk');

// stdbot
Route::get('/stdbot/contact', [App\Http\Controllers\StudioBotController::class, 'contact'])->name('stdbot view contact')->middleware('role:superadmin|rj1');
Route::get('/stdbot/scheduler', [App\Http\Controllers\StudioBotController::class, 'scheduler'])->name('stdbot view scheduler')->middleware('role:superadmin|rj1');
Route::get('/stdbot/generate_bulk', [App\Http\Controllers\StudioBotController::class, 'generate_bulk'])->name('stdbot generate bulk')->middleware('role:superadmin|rj1');
Route::post('/stdbot/delete_contact', [App\Http\Controllers\StudioBotController::class, 'delete_contact'])->name('stdbot delete contact')->middleware('role:superadmin|rj1');
Route::post('/stdbot/create_group', [App\Http\Controllers\StudioBotController::class, 'create_group'])->name('stdbot create group')->middleware('role:superadmin|rj1');
Route::post('/stdbot/send_wa', [App\Http\Controllers\StudioBotController::class, 'send_wa'])->name('stdbot send wa')->middleware('role:superadmin|rj1');

// keamanan
Route::get('/keamanan', [App\Http\Controllers\KeamananController::class, 'index'])->name('index keamanan')->middleware('role:superadmin|rj1|wk|divisi keamanan');
Route::post('/keamanan/store_jagamalam', [App\Http\Controllers\KeamananController::class, 'store_jagamalam'])->name('store jagamalam')->middleware('role:superadmin|rj1|wk|divisi keamanan');
Route::get('/keamanan/delete_jagamalam/{id}', [App\Http\Controllers\KeamananController::class, 'delete_jagamalam'])->name('delete jagamalam')->middleware('role:superadmin|rj1|wk|divisi keamanan');
Route::post('/keamanan/store_pulangmalam', [App\Http\Controllers\KeamananController::class, 'store_pulangmalam'])->name('store pulangmalam')->middleware('role:superadmin|rj1|wk|divisi keamanan|santri');
Route::get('/keamanan/pulang-malam', [App\Http\Controllers\KeamananController::class, 'pulang_malam'])->name('pulangmalam keamanan')->middleware('role:superadmin|rj1|wk|divisi keamanan');
Route::post('/keamanan/jobdesk/store', [App\Http\Controllers\KeamananController::class, 'store_jobdesk'])->name('store jobdesk')->middleware('role:superadmin|rj1|wk|divisi keamanan|santri');

// PMB
Route::get('/pmb', [App\Http\Controllers\PmbPublicController::class, 'index'])->name('index pmb');
Route::get('/pmb/registration-successful', [App\Http\Controllers\PmbPublicController::class, 'registration_successful'])->name('registration successful');
Route::post('/pmb/store_maba', [App\Http\Controllers\PmbPublicController::class, 'store_maba'])->name('store maba');
Route::post('/pmb/change_mentor', [App\Http\Controllers\PmbController::class, 'change_mentor_maba'])->name('change mentor maba')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::post('/pmb/store_nilai_maba', [App\Http\Controllers\PmbController::class, 'store_nilai_maba'])->name('store nilai maba')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::get('/pmb/konfigurasi', [App\Http\Controllers\PmbController::class, 'konfigurasi'])->name('konfigurasi pmb')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::post('/pmb/store_konfigurasi', [App\Http\Controllers\PmbController::class, 'store_konfigurasi'])->name('store konfigurasi pmb')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::get('/pmb/panitia', [App\Http\Controllers\PmbController::class, 'view_panitia'])->name('view panitia')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::get('/pmb/delete_panitia/{id}', [App\Http\Controllers\PmbController::class, 'delete_panitia'])->name('delete panitia pmb')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::post('/pmb/store_panitia', [App\Http\Controllers\PmbController::class, 'store_panitia'])->name('store panitia')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::get('/pmb/list_maba', [App\Http\Controllers\PmbController::class, 'view_maba'])->name('view maba')->middleware('role:superadmin|rj1|wk|panitia pmb');
Route::post('/pmb/change_status_maba', [App\Http\Controllers\PmbController::class, 'change_status_maba'])->name('change status maba')->middleware('role:superadmin|rj1|wk|panitia pmb');

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\ReportScheduler;
use App\Models\SpWhatsappContacts;
use App\Models\SpWhatsappPhoneNumbers;
use App\Helpers\CommonHelpers;
use App\Helpers\WaSchedules;
use Illuminate\Support\Facades\DB;

class MsgtoolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generate_bulk()
    {
        $setting = Settings::find(1);

        // check contact wa
        CommonHelpers::checkWaContact();

        // create bulk all mahasiswa aktif
        $getuser = DB::table('v_user_santri')
            ->orderBy('fullname', 'ASC')
            ->get();
        CommonHelpers::createBulk('Bulk Mahasiswa Aktif', $getuser, 'nohp');
        CommonHelpers::createBulk('Bulk Ortu Aktif', $getuser, 'nohp_ortu');

        // create bulk angkatan
        $getangkatan = DB::table('v_user_santri')
            ->select('angkatan')
            ->whereNull('exit_at')
            ->orderBy('angkatan', 'ASC')
            ->groupBy('angkatan')
            ->get();
        foreach ($getangkatan as $ga) {
            $xcga = DB::table('v_user_santri')
                ->where('angkatan', $ga->angkatan)
                ->orderBy('fullname', 'ASC')
                ->get();
            CommonHelpers::createBulk('Bulk Angkatan ' . $ga->angkatan, $xcga, 'nohp');
            CommonHelpers::createBulk('Bulk Ortu ' . $ga->angkatan, $xcga, 'nohp_ortu');
        }
        return redirect()->route('msgtools view contact');
    }

    public function contact()
    {
        $setting = Settings::find(1);
        $getuser = DB::table('v_user_santri')
            ->orderBy('fullname', 'ASC')
            ->get();

        $contact_user = array();
        foreach ($getuser as $u) {
            $nohp = $u->nohp;
            $set_a = false;
            if ($nohp != '' && $nohp != '0') {
                if ($nohp[0] == '0') {
                    $nohp = '62' . substr($nohp, 1);
                }
                $getspa = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                    $query->where('name', 'NOT LIKE', '%Bulk%');
                })->where('phone', $nohp)->where('team_id', $setting->wa_team_id)->first();
                if ($getspa != null) {
                    $contact_user[$u->id]['nohp_pribadi'] = $getspa->phone;
                    $contact_user[$u->id]['pribadi_id'] = $getspa->pid;
                    $contact_user[$u->id]['nama_pribadi'] = '[' . $u->angkatan . '] ' . $getspa->contact->name;
                } else {
                    $set_a = true;
                }
            } else {
                $set_a = true;
            }
            if ($set_a) {
                $contact_user[$u->id]['nohp_pribadi'] = '0';
                $contact_user[$u->id]['pribadi_id'] = '0';
                $contact_user[$u->id]['nama_pribadi'] = '[' . $u->angkatan . '] X ' . $u->fullname;
            }

            $nohp_ortu = $u->nohp_ortu;
            $set_b = false;
            if ($nohp_ortu != '') {
                if ($nohp_ortu[0] == '0') {
                    $nohp_ortu = '62' . substr($nohp_ortu, 1);
                }
                $getspb = SpWhatsappPhoneNumbers::whereHas('contact', function ($query) {
                    $query->where('name', 'NOT LIKE', '%Bulk%');
                })->where('phone', $nohp_ortu)->where('team_id', $setting->wa_team_id)->first();
                if ($getspb != null) {
                    $contact_user[$u->id]['nohp_ortu'] = $getspb->phone;
                    $contact_user[$u->id]['ortu_id'] = $getspb->pid;
                    $contact_user[$u->id]['nama_ortu'] = '[' . $u->angkatan . '] ' . $getspb->contact->name;
                } else {
                    $set_b = true;
                }
            } else {
                $set_b = true;
            }

            if ($set_b) {
                $contact_user[$u->id]['ortu_id'] = '0';
                $contact_user[$u->id]['nohp_ortu'] = '0';
                $contact_user[$u->id]['nama_ortu'] = '[' . $u->angkatan . '] OrtuX ' . $u->fullname;
            }
        }

        $group_user = array();
        $getgroup = SpWhatsappContacts::where('name', 'LIKE', '%Group%')->where('team_id', $setting->wa_team_id)->get();
        foreach ($getgroup as $gg) {
            $xget = SpWhatsappPhoneNumbers::where('pid', $gg->id)->first();
            if ($xget == null) {
                $group_user[$gg->id]['phone'] = '0';
            } else {
                $group_user[$gg->id]['phone'] = $xget->phone;
            }
            $group_user[$gg->id]['group_id'] = $gg->id;
            $group_user[$gg->id]['group_name'] = $gg->name;
        }

        $bulk_user = array();
        $getbulk = SpWhatsappContacts::where('name', 'LIKE', '%Bulk%')->where('team_id', $setting->wa_team_id)->orderBy('id', 'ASC')->get();
        foreach ($getbulk as $gg) {
            $xget = SpWhatsappPhoneNumbers::where('pid', $gg->id)->get();
            if ($xget == null) {
                $bulk_user[$gg->id]['data'] = null;
            } else {
                foreach ($xget as $xg) {
                    $setnohp = $xg->phone;
                    if ($xg->phone[0] == '6' && $xg->phone[1] == '2') {
                        $setnohp = '0' . substr($xg->phone, 2);
                    }
                    $xc_user = DB::table('v_user_santri')
                        ->where('nohp', $setnohp)
                        ->orWhere('nohp_ortu', $setnohp)
                        ->get();
                    if (count($xc_user) > 0) {
                        foreach ($xc_user as $xcu) {
                            $bulk_user[$gg->id]['data'][$xg->id]['phone'] = $xg->phone;
                            $bulk_user[$gg->id]['data'][$xg->id]['name'] = $xcu->fullname;
                        }
                    } else {
                        $bulk_user[$gg->id]['data'][$xg->id]['phone'] = $setnohp;
                        $bulk_user[$gg->id]['data'][$xg->id]['name'] = 'undefined';
                    }
                }
            }
            $bulk_user[$gg->id]['bulk_id'] = $gg->id;
            $bulk_user[$gg->id]['bulk_name'] = $gg->name;
        }

        return view('msgtools.contact', [
            'get_user' => $getuser,
            'contact_user' => $contact_user,
            'group_user' => $group_user,
            'bulk_user' => $bulk_user,
        ]);
    }

    public function report()
    {
        $datax = ReportScheduler::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->get();

        return view('msgtools.report', [
            'datax' => $datax
        ]);
    }

    public function delete_contact(Request $request)
    {
        $team_id = Settings::find(1);
        $check_contact = SpWhatsappContacts::find($request->input('contact_id'));
        if ($check_contact != null) {
            $check_number = SpWhatsappPhoneNumbers::where('team_id', $team_id->wa_team_id)->where('pid', $request->input('contact_id'))->first();
            if ($check_number != null) {
                $check_contact->delete();
                if ($check_number->delete()) {
                    echo json_encode(['status' => true, 'message' => 'Group Whatsapp berhasil dihapus']);
                } else {
                    echo json_encode(['status' => false, 'message' => 'Group Whatsapp gagal dihapus']);
                }
            }
        }
    }

    public function create_group(Request $request)
    {
        $request->validate([
            'group_name' => 'required',
            'group_id' => 'required',
        ]);

        $team_id = Settings::find(1);
        $check_number = SpWhatsappPhoneNumbers::where('team_id', $team_id->wa_team_id)->where('phone', $request->input('group_id'))->get();
        if (count($check_number) == 0) {
            $contact_id = SpWhatsappContacts::create([
                'ids' => uniqid(),
                'team_id' => $team_id->wa_team_id,
                'name' => $request->input('group_name'),
                'status' => 1,
                'changed' => time(),
                'created' => time()
            ]);
            if ($contact_id) {
                $csv = SpWhatsappPhoneNumbers::create([
                    'ids' => uniqid(),
                    'team_id' => $team_id->wa_team_id,
                    'pid' => $contact_id->id,
                    'phone' => $request->input('group_id')
                ]);
            }
        } else {
            $check_number = SpWhatsappContacts::find($check_number->pid);
            $check_number->name = $request->input('group_name');
            $csv = $check_number->save();
        }

        if ($csv) {
            echo json_encode(['status' => true, 'message' => 'Group berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Group gagal disimpan']);
        }
    }

    public function send_wa(Request $request)
    {
        $request->validate([
            'bulk_subject' => 'required',
            'bulk_message' => 'required',
        ]);
        if (WaSchedules::save($request->input('bulk_subject'), $request->input('bulk_message'), $request->input('bulk_id'))) {
            echo json_encode(['status' => true, 'message' => 'Pesan berhasil dikirim']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Pesan gagal dikirim']);
        }
    }
}

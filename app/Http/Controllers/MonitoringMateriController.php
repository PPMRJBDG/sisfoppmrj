<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonitoringMateri;
use App\Models\Santri;
use App\Models\Materi;
use App\Models\Lorong;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MonitoringMateriController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:update monitoring materis')->only(['update']);
    }


    /**
     * Show the list and manage table of monitoringMateris.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list_and_manage()
    {
        $lorongs = Lorong::all();
        // $view_usantri = DB::table('v_user_santri')->orderBy('fullname')->get();
        $users = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->orderBy('fullname', 'asc')->get();
        $monitoringMateris = null; //MonitoringMateri::all();
        $materis = Materi::all();

        return view('monitoringMateri.list_and_manage', ['lorongs' => $lorongs, 'users' => $users, 'materis' => $materis, 'monitoringMateris' => $monitoringMateris]);
    }

    public function materi_santri(Request $request)
    {
        $materis = Materi::all();
        $santri = Santri::find($request->input('santri_id'));
        $data = '';
        foreach ($materis as $materi) {
            if ($materi->for == 'mubalegh' && !$santri->user->hasRole('mubalegh'))
                continue;
            if ($materi->for != 'mubalegh' && $santri->user->hasRole('mubalegh'))
                continue;
            $completedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'complete')->count();
            $partiallyCompletedPages = $santri->monitoringMateris->where('fkMateri_id', $materi->id)->where('status', 'partial')->count();
            $totalPages = $completedPages + ($partiallyCompletedPages / 2);
            $data = $data . '
            <tr>
                <td class="p-2">' . $materi->name . '</td>
                <td class="p-2">' . $totalPages . '/' . $materi->pageNumbers . ' page = ' . number_format((float) $totalPages / $materi->pageNumbers * 100, 2, ".", "") . '%</td>
                <td class="p-2">
                    <a href="' . route('edit monitoring materi', [$materi->id, $santri->id]) . '" class="btn btn-success btn-sm mb-0">Lihat</a>
                </td>
            </tr>';
        }
        return $data;
    }

    /**
     * Show the create form of monitoringMateri.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($materiId, $santriId)
    {

        if(auth()->user()->santri){
            if($santriId != auth()->user()->santri->id)
                return abort(403);
        }
        if (!auth()->user()->santri && !auth()->user()->can('update monitoring materis') && !auth()->user()->hasRole('dewan guru'))
            return abort(403);


        $materi = Materi::find($materiId);
        $santri = Santri::find($santriId);

        $monitoringMateris = MonitoringMateri::where('fkSantri_id', $santriId)->where('fkMateri_id', $materiId)->get();

        return view('monitoringMateri.edit', ['santri' => $santri, 'materi' => $materi, 'monitoringMateris' => $monitoringMateris]);
    }

    /**
     * Insert new monitoringMateri.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        $request->validate([
            'fkSantri_id' => 'required|integer',
            'fkMateri_id' => 'required|integer',
            'pages' => 'required',
            'statusOfPages' => 'required'
        ]);

        $materiId = $request->input('fkMateri_id');
        $santriId = $request->input('fkSantri_id');

        $pages = $request->input('pages');
        $statusOfPages = $request->input('statusOfPages');
        $failedPages = [];

        foreach ($pages as $key => $page) {
            $inserted = MonitoringMateri::updateOrCreate(
                [
                    'fkSantri_id' => $request->input('fkSantri_id'),
                    'fkMateri_id' => $request->input('fkMateri_id'),
                    'page' => $page,
                ],
                [
                    'fkSantri_id' => $request->input('fkSantri_id'),
                    'fkMateri_id' => $request->input('fkMateri_id'),
                    'page' => $page,
                    'status' => $statusOfPages[$key]
                ]
            );

            if (!$inserted)
                $failedPages[] = $page;
        }

        return redirect()->route('edit monitoring materi', [$materiId, $santriId])->with(sizeof($failedPages) == 0 ? 'success' : 'failed', sizeof($failedPages) == 0 ? 'Monitoring materi berhasil diupdate.' : 'Gagal mengupdate monitoring materi di halaman ' . implode(', ', $failedPages));
    }

    public function match_empty_pages(Request $request)
    {
        $users = User::whereHas('santri', function ($query) {
            $query->whereNull('exit_at');
        })->orderBy('fullname', 'asc')->get();
        $materis = Materi::all();

        $santriIds = $request->get('santri_ids');
        $materiId = $request->get('materi_id');
        $status = $request->get('status');

        $santriIds = explode(',', $santriIds);
        $selectedSantris = Santri::whereIn('id', $santriIds)->get();

        $santriMonitoringMateris = MonitoringMateri::where('fkMateri_id', $materiId)->whereIn('fkSantri_id', $santriIds)->get();

        $materi = Materi::where('id', $materiId)->first();

        // detect full pages
        $fullPages = [];

        foreach ($santriMonitoringMateris as $monitoringMateri) {
            if (!isset($fullPages[$monitoringMateri->page]))
                $fullPages[$monitoringMateri->page] = array('complete' => 0, 'partial' => 0, 'blank' => 0);

            $fullPages[$monitoringMateri->page][$monitoringMateri->status] += 1;
        }

        return view('monitoringMateri.match_empty_pages', ['selectedSantris' => $selectedSantris, 'status' => $status, 'santriIds' => $santriIds, 'fullPages' => $fullPages, 'materi' => $materi, 'santriMonitoringMateris' => $santriMonitoringMateris, 'users' => $users, 'materis' => $materis]);
    }
}

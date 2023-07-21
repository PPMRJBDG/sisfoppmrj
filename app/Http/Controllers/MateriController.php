<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;

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
        
        return redirect()->route('materi tm')->with($inserted ? 'success' : 'failed' , $inserted ? 'Materi baru berhasil ditambahkan.' : 'Gagal menambah materi baru.');
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

        if(!$materi)
            return redirect()->route('edit materi', $materiIdToUpdate)->withErrors(['materi_not_found' => 'Can\'t update unexisting Materi.']); 
        
        $materi->name = $request->input('name');
        $materi->pageNumbers = $request->input('pageNumbers');

        $updated = $materi->save();

        if(!$updated)
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
        
        if($materi)
        {            
            $deleted = $materi->delete();

            if(!$deleted)
                return redirect()->route('materi tm')->withErrors(['failed_deleting_materi', 'Gagal menghapus Materi.']);
        }

        return redirect()->route('materi tm')->with('success', 'Berhasil menghapus Materi');
    }
}
<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rsetBarang = Barang::all();
        $rsetBarang = Barang::with('kategori')->get();
        
            $search = $request->query('search');
        if ($search) {
            $rsetBarang = Barang::where('merk', 'like', '%' . $search . '%')
                                    ->orWhere('seri', 'like', '%' . $search . '%')
                                    ->orWhere('spesifikasi', 'like', '%' . $search . '%')
                                    ->orWhere('stok', 'like', '%' . $search . '%')
                                    ->orWhere('kategori_id', 'like', '%' . $search . '%')
                                    ->orWhereHas('kategori', function($query) use ($search) {
                                        $query->where('deskripsi', 'like', '%' . $search . '%');
                                    })
                                    ->paginate(10);
        } else {
            $rsetBarang = Barang::paginate(10);
        }

        return view('v_barang.index', compact('rsetBarang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rsetKategori = Kategori::all();
        return view('v_barang.create', compact('rsetKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'merk'          => 'required',
            'seri'          => 'nullable',
            'spesifikasi'   => 'nullable',
            'stok'          => 'nullable',
            'kategori_id'   => 'required',

        ],[
            'merk.required' => 'Item brand must be filled in',
            'merk.unique' => 'Item brand already exists!',
        ]);

        Barang::create([
            'merk'          => $request->merk,
            'seri'          => $request->seri,
            'spesifikasi'   => $request->spesifikasi,
            'stok'          => $request->stok,
            'kategori_id'   => $request->kategori_id,
        ]);

        return redirect()->route('barang.index')->with(['Success' => 'Successfully saved!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarang = Barang::find($id);
        return view('v_barang.show', compact('rsetBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rsetBarang = Barang::find($id);
        $rsetKategori = Kategori::all(); 
        return view('v_barang.edit', compact('rsetBarang', 'rsetKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'merk'        => 'required',
            'seri'        => 'nullable',
            'spesifikasi' => 'nullable',
            'stok'        => 'nullable',
            'kategori_id' => 'required',
        ], [
            'merk.required' => 'Item brand must be filled in',
            'merk.unique' => 'Item brand already exists!',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->all());

        return redirect()->route('barang.index')->with(['Success' => 'Updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (DB::table('barangmasuk')->where('barang_id', $id)->exists() || DB::table('barangkeluar')->where('barang_id', $id)->exists()){ 
            return redirect()->route('barang.index')->with(['Gagal' => 'Failed to delete, data in Use!']);
        } else {
            $rsetBarang = Barang::find($id);
            $rsetBarang->delete();
            return redirect()->route('barang.index')->with(['Success' => 'Data deleted successfully']);
        }
    }
}

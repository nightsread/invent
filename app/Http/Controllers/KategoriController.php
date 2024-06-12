<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        if ($request->search) {
            $searchTerm = '%' . $request->search . '%';
    
            $rsetKategori = DB::table('kategori')
                ->select('id', 'deskripsi', 'kategori', DB::raw('kategori as kat'))
                ->where('id', 'like', $searchTerm)
                ->orWhere('deskripsi', 'like', $searchTerm)
                ->orWhere('kategori', 'like', $searchTerm)
                ->get();  // Menambahkan get() untuk mengeksekusi query
    
            return view('v_kategori.index', compact('rsetKategori'));
        } else {
            // Jika tidak ada pencarian, Anda mungkin ingin mengembalikan semua data atau data default
            $rsetKategori = DB::table('kategori')->get();
    
            return view('v_kategori.index', compact('rsetKategori'));
        }
    }
        /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('v_kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:100|unique:kategori,deskripsi',
            'kategori' => 'required|in:M,A,BHP,BTHP'
        ]);        

        Kategori::create([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori
        ]);

        return redirect()->route('kategori.index')->with('Success', 'Data saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetKategori = Kategori::find($id);
        return view('v_kategori.show', compact('rsetKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rsetKategori = Kategori::find($id);
        return view('v_kategori.edit', compact('rsetKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);        

        $rsetKategori = Kategori::find($id);

        $rsetKategori->update([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('kategori.index')->with('Success', 'Successfully modified');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (DB::table('barang')->where('kategori_id', $id)->exists()){ 
            return redirect()->route('kategori.index')->with(['Gagal' => 'Data failed to delete']);
        } else {
            $rseKategori = Kategori::find($id);
            $rseKategori->delete();
            return redirect()->route('kategori.index')->with(['Success' => 'Successfully deleted']);
        }
    }
}

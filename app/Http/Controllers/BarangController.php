<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Kategori;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BarangController extends Controller
{
    use ValidatesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil data barang dengan deskripsi kategori
        $query = DB::table('barang')
            ->join('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->select('barang.*', 'kategori.deskripsi as kategori_deskripsi');

        // Jika ada parameter pencarian, tambahkan kondisi pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($query) use ($searchTerm) {
                $query->where('barang.merk', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barang.seri', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barang.spesifikasi', 'like', '%' . $searchTerm . '%')
                    ->orWhere('kategori.deskripsi', 'like', '%' . $searchTerm . '%');
            });
        }

        $rsetBarang = $query->paginate(10); // Menggunakan paginasi dengan 10 item per halaman

        return view('v_barang.index', compact('rsetBarang'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rsetKategori = Kategori::all(); // Mengambil semua kategori
        $aKategori = array(
            'blank' => 'Pilih Kategori',
            'M' => 'Barang Modal',
            'A' => 'Alat',
            'BHP' => 'Bahan Habis Pakai',
            'BTHP' => 'Bahan Tidak Habis Pakai'
        );

        return view('v_barang.create', compact('rsetKategori', 'aKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'kategori_id' => 'required',
            'stok' => 'required|integer',
        ]);

        try {
            DB::beginTransaction(); // <= Starting the transaction

            // Insert a new order history
            DB::table('barang')->insert([
                'merk' => $request->merk,
                'seri' => $request->seri,
                'spesifikasi' => $request->spesifikasi,
                'kategori_id' => $request->kategori_id,
                'stok' => $request->stok,
            ]);

            DB::commit(); // <= Commit the changes
        } catch (\Exception $e) {
            report($e);

            DB::rollBack(); // <= Rollback in case of an exception
        }

        // Redirect to index
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Use eager loading to get the category description along with the Barang
        $rsetBarang = Barang::with('kategori')->find($id);

        // Check if the barang exists
        if (!$rsetBarang) {
            return redirect()->route('barang.index')->with(['error' => 'Barang not found']);
        }

        // Get all categories for the dropdown
        $aKategori = Kategori::pluck('deskripsi', 'id')->all();

        return view('v_barang.show', compact('rsetBarang', 'aKategori'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Mendapatkan data barang yang akan diedit
        $rsetBarang = Barang::find($id);

        // Mendapatkan data kategori untuk dropdown
        $aKategori = Kategori::pluck('deskripsi', 'id')->all();

        return view('v_barang.edit', compact('rsetBarang', 'aKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $this->validate($request, [
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'kategori_id' => 'required',
            'stok' => 'required|integer',
        ]);

        $rsetBarang = Barang::find($id);
        $rsetBarang->update($validatedData);

        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (DB::table('barangmasuk')->where('barang_id', $id)->exists() || DB::table('barangkeluar')->where('barang_id', $id)->exists()) {
            return redirect()->route('barang.index')->with(['Gagal' => 'Gagal dihapus']);
        } else {
            $rsetBarang = Barang::find($id);
            $rsetBarang->delete();
            return redirect()->route('barang.index')->with(['success' => 'Berhasil dihapus']);
        }
    }

    public function getCategories(): JsonResponse
    {
        $categories = Kategori::all(['id', 'deskripsi']);
        return response()->json($categories);
    }
}
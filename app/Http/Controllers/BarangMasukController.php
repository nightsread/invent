<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('barangmasuk')
        ->join('barang', 'barangmasuk.barang_id', '=', 'barang.id')
        ->select('barangmasuk.*', 'barang.merk', 'barang.seri', 'barang.spesifikasi');

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($query) use ($searchTerm) {
                $query->where('barangmasuk.tgl_masuk', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barangmasuk.qty_masuk', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barang.merk', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barang.seri', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barang.spesifikasi', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $barangMasuk = $query->paginate(10);

        return view('v_barangmasuk.index', compact('barangMasuk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rsetBarang = Barang::all();
        return view('v_barangmasuk.create', compact('rsetBarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_masuk'     => 'required|date',
            'qty_masuk'     => 'required|numeric|min:0',
            'barang_id'     => 'required|exists:barang,id',
        ]);

        $tgl_masuk = $request->tgl_masuk;
        $barang_id = $request->barang_id;

        $afterBKeluar = BarangKeluar::where('barang_id', $barang_id)
            ->where('tgl_keluar', '<', $tgl_masuk)
            ->exists();

        if ($afterBKeluar) {
            return redirect()->back()->withInput()->withErrors(['tgl_masuk' => 'The entry date cannot after the exit date!']);
        }

        try {
            DB::beginTransaction();

            DB::table('barangmasuk')->insert([
                'tgl_masuk' => $validatedData['tgl_masuk'],
                'qty_masuk' => $validatedData['qty_masuk'],
                'barang_id' => $validatedData['barang_id'],
            ]);

            DB::commit(); 
        } catch (\Exception $e) {
            report($e);

            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to save the entry.']);
        }
        
        return redirect()->route('barangmasuk.index')->with(['success' => 'Successfully saved!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barangMasuk = BarangMasuk::find($id);
        return view('v_barangmasuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangMasuk = BarangMasuk::find($id);
        $rsetBarang = Barang::all();
        return view('v_barangmasuk.edit', compact('barangMasuk', 'rsetBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'tgl_masuk'     => 'required|date',
            'qty_masuk'     => 'required|numeric|min:0',
            'barang_id'     => 'required|exists:barang,id',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);
        $barang = Barang::findOrFail($validatedData['barang_id']);

        $tgl_masuk = $validatedData['tgl_masuk'];
        $qty_masuk = $validatedData['qty_masuk'];
        $barang_id = $validatedData['barang_id'];

        $afterBKeluar = BarangKeluar::where('barang_id', $barang_id)
            ->where('tgl_keluar', '<', $tgl_masuk)
            ->exists();

        if ($afterBKeluar) {
            return redirect()->back()->withInput()->withErrors(['tgl_masuk' => 'The entry date cannot after the exit date!']);
        }

        try {
            DB::beginTransaction();

            $barangMasuk->update([
                'tgl_masuk' => $tgl_masuk,
                'qty_masuk' => $qty_masuk,
                'barang_id' => $barang_id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update the entry.']);
        }

        return redirect()->route('barangmasuk.index')->with(['success' => 'Successfully modified!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barangMasuk = BarangMasuk::find($id);
        $barangMasuk->delete();
        
        return redirect()->route('barangmasuk.index')->with(['success' => 'Successfully deleted!']);
    }
}

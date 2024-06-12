<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        if ($search) {
            $barangMasuk = BarangMasuk::where('tgl_masuk', 'like', '%' . $search . '%')
                                    ->orWhere('qty_masuk', 'like', '%' . $search . '%')
                                    ->orWhere('barang_id', 'like', '%' . $search . '%')
                                    ->orWhereHas('barang', function($query) use ($search) {
                                        $query->where('merk', 'like', '%' . $search . '%');
                                    })
                                    ->paginate(10);
        } else {
            $barangMasuk = BarangMasuk::paginate(10);
        }

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
        $request->validate([
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

        BarangMasuk::create([
            'tgl_masuk' => $request->tgl_masuk,
            'qty_masuk' => $request->qty_masuk,
            'barang_id' => $request->barang_id,
        ]);

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
        $request->validate([
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

        $barangMasuk = BarangMasuk::find($id);

        $barangMasuk->update([
            'tgl_masuk' => $request->tgl_masuk,
            'qty_masuk' => $request->qty_masuk,
            'barang_id' => $request->barang_id,
        ]);

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

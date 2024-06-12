<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Validator;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        if ($search) {
            $barangKeluar = BarangKeluar::where('tgl_keluar', 'like', '%' . $search . '%')
                                    ->orWhere('qty_keluar', 'like', '%' . $search . '%')
                                    ->orWhere('barang_id', 'like', '%' . $search . '%')
                                    ->orWhereHas('barang', function($query) use ($search) {
                                        $query->where('merk', 'like', '%' . $search . '%');
                                    })
                                    ->paginate(10);
        } else {
            $barangKeluar = BarangKeluar::paginate(10);
        }

        return view('v_barangkeluar.index', compact('barangKeluar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rsetBarang = Barang::all();
        return view('v_barangkeluar.create', compact('rsetBarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_keluar'   => 'required|date',
            'qty_keluar'   => 'required|numeric|min:0',
            'barang_id'    => 'required|exists:barang,id',
        ]);

        $tgl_keluar = $request->tgl_keluar;
        $barang_id = $request->barang_id;

        $beforeBMasuk = BarangMasuk::where('barang_id', $barang_id)
        ->where('tgl_masuk', '>', $tgl_keluar)
        ->exists();
        if ($beforeBMasuk) {
            return redirect()->back()->withInput()->withErrors(['tgl_keluar' => 'The exit date cannot be before the entry date!']);
        }
        
        $barang = Barang::find($request->barang_id);
        if ($request->qty_keluar > $barang->stok) {
            return redirect()->back()->withInput()->withErrors(['qty_keluar' => 'The quantity exceeds existing stock items!']);
        }

        BarangKeluar::create([
            'tgl_keluar' => $request->tgl_keluar,
            'qty_keluar' => $request->qty_keluar,
            'barang_id' => $request->barang_id
        ]);

        return redirect()->route('barangkeluar.index')->with(['success' => 'Successfully saved!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        return view('v_barangkeluar.show', compact('barangKeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $rsetBarang = Barang::all();
        return view('v_barangkeluar.edit', compact('barangKeluar', 'rsetBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tgl_keluar'   => 'required|date',
            'qty_keluar'   => 'required|numeric|min:1',
            'barang_id'    => 'required|exists:barang,id',
        ]);

        $tgl_keluar = $request->tgl_keluar;
        $barang_id = $request->barang_id;

        $beforeBMasuk = BarangMasuk::where('barang_id', $barang_id)
        ->where('tgl_masuk', '>', $tgl_keluar)
        ->exists();
        if ($beforeBMasuk) {
            return redirect()->back()->withInput()->withErrors(['tgl_keluar' => 'The exit date cannot be before the entry date!']);
        }
        
        $barang = Barang::find($request->barang_id);
        if ($request->qty_keluar > $barang->stok) {
            return redirect()->back()->withInput()->withErrors(['qty_keluar' => 'The quantity exceeds existing stock items!']);
        }

        $barangKeluar = BarangKeluar::find($id);

        $barangKeluar->update([
            'tgl_keluar' => $request->tgl_keluar,
            'qty_keluar' => $request->qty_keluar,
            'barang_id' => $request->barang_id,
        ]);

        return redirect()->route('barangkeluar.index')->with(['success' => 'Successfully modified!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $barangKeluar->delete();
        return redirect()->route('barangkeluar.index')->with(['success' => 'Successfully deleted!']);
    }
}

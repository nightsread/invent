@extends('layouts.adm-main')

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Barang Masuk {{ $barangMasuk->id }}</h4>
                        <table class="table">
                            <tr>
                                <td>TANGGAL MASUK</td>
                                <td>: {{ $barangMasuk->tgl_masuk }}</td>
                            </tr>
                            <tr>
                                <td>QTY MASUK</td>
                                <td>: {{ $barangMasuk->qty_masuk }}</td>
                            </tr>
                            <tr>
                                <td>BARANG</td>
                                <td>: {{ $barangMasuk->barang->merk . ' ' . $barangMasuk->barang->seri }}</td>
                            </tr>
                            </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12  text-center">
            <a href="{{ route('barangmasuk.index') }}" class="btn btn-md btn-primary mb-3 mt-3">BACK</a>
        </div>
    </div>
</div>
@endsection
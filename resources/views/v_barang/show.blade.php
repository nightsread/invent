@extends('layouts.adm-main')

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">BARANG {{ $rsetBarang->id }}</h4>
                        <table class="table">
                            <tr>
                                <td>MERK</td>
                                <td>: {{ $rsetBarang->merk }}</td>
                            </tr>
                            <tr>
                                <td>SERI</td>
                                <td>: {{ $rsetBarang->seri }}</td>
                            </tr>
                            <tr>
                                <td>SPESIFIKASI</td>
                                <td>: {{ $rsetBarang->spesifikasi }}</td>
                            </tr>
                            <tr>
                                <td>Stok</td>
                                <td>: {{ $rsetBarang->stok }}</td>
                            </tr>
                            <tr>
                                <td>Kategori</td>
                                <td>: {{ ($rsetBarang->kategori)->deskripsi }}</td>
                            </tr>
                            </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12  text-center">
            <a href="{{ route('barang.index') }}" class="btn btn-md btn-primary mb-3 mt-3">BACK</a>
        </div>
    </div>
</div>
@endsection
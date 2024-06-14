@extends('layouts.adm-main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 justify-content-between">
                        <div class="col-md-6">
                            <a href="{{ route('barang.create') }}" class="btn btn-md btn-success">+ TAMBAH BARANG</a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('barang.index') }}" method="GET">
                            @csrf
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search for ..." value="{{ request()->query('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-secondary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(session('Success'))
                        <div class="alert alert-success">
                            {{ session('Success') }}
                        </div>
                    @endif

                    @if(session('Gagal'))
                        <div class="alert alert-danger">
                            {{ session('Gagal') }}
                        </div>
                    @endif
                    
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>MERK</th>
                            <th>SERI</th>
                            <th>SPESIFIKASI</th>
                            <th>STOK</th>
                            <th>KATEGORI</th>
                            <th style="width: 15%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rsetBarang as $rowbarang)
                            <tr>
                                <td>{{ $rowbarang->id  }}</td>
                                <td>{{ $rowbarang->merk  }}</td>
                                <td>{{ $rowbarang->seri  }}</td>
                                <td>{{ $rowbarang->spesifikasi  }}</td>
                                <td>{{ $rowbarang->stok  }}</td> 
                                <td>{{ $rowbarang->kategori_deskripsi  }}</td>
                                <td class="text-center"> 
                                    <form onsubmit="return confirm('Want to delete this field?');" action="{{ route('barang.destroy', $rowbarang->id) }}" method="POST">
                                        <a href="{{ route('barang.show', $rowbarang->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barang.edit', $rowbarang->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                                
                            </tr>
                        @empty
                            <div class="alert">
                                Item not found
                            </div>
                        @endforelse
                    </tbody>
                    
                </table>
                {{-- {{ $barang->links() }} --}}
                </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@extends('layouts.adm-main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 justify-content-between">
                        <div class="col-md-6">
                            <a href="{{ route('barangmasuk.create') }}" class="btn btn-md btn-success">+ TAMBAH BARANG MASUK</a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('barangmasuk.index') }}" method="GET">
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

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TGL MASUK</th>
                            <th>QTT MASUK</th>
                            <th>BARANG ID</th>
                            <th style="width: 15%">AKSI</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangMasuk as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->tgl_masuk }}</td>
                                <td>{{ $row->qty_masuk }}</td>
                                <td>{{ $row->merk }}</td>
                                <td class="text-center"> 
                                    <form onsubmit="return confirm('Want to delete this field?');" action="{{ route('barangmasuk.destroy', $row->id) }}" method="POST">
                                        <a href="{{ route('barangmasuk.show', $row->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barangmasuk.edit', $row->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <div class="alert">
                                Data Barang Masuk belum tersedia
                            </div>
                        @endforelse
                    </tbody>
                </table>
                {{-- {{ $barangmasuk->links() }} --}}
                </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@extends('layouts.adm-main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 justify-content-between">
                        <div class="col-md-6">
                            <a href="{{ route('barangkeluar.create') }}" class="btn btn-md btn-success">+ TAMBAH BARANG KELUAR</a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('barangkeluar.index') }}" method="GET">
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


                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TGL KELUAR</th>
                            <th>QTT KELUAR</th>
                            <th>BARANG ID</th>
                            <th style="width: 15%">AKSI</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangKeluar as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->tgl_keluar }}</td>
                                <td>{{ $row->qty_keluar }}</td>
                                <td>{{ $row->barang_id }} - {{ $row->barang->merk }}</td>
                                <td class="text-center">
                                    <form onsubmit="return confirm('Want to delete this field?');" action="{{ route('barangkeluar.destroy', $row->id) }}" method="POST">
                                        <a href="{{ route('barangkeluar.show', $row->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barangkeluar.edit', $row->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
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
                {{-- {{ $barangkeluar->links() }} --}}
                </div>
                </div>

            </div>
        </div>
    </div>
@endsection
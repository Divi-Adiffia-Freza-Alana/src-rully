@extends('adminlte::page')

@section('title', 'Tambah Produk - SI Inventory')

@section('content_header')
    <h1>Tambah Produk</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf
                @include('produk._form')
            </form>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Edit Produk - SRC Rully')

@section('content_header')
    <h1>Edit Produk</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('produk.update', $produk) }}" method="POST">
                @csrf
                @method('PUT')
                @include('produk._form')
            </form>
        </div>
    </div>
@stop

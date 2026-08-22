@extends('adminlte::page')

@section('title', 'Tambah Karyawan - SRC Rully')

@section('content_header')
    <h1>Tambah Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf
                @include('karyawan._form')
            </form>
        </div>
    </div>
@stop

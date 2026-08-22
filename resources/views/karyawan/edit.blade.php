@extends('adminlte::page')

@section('title', 'Edit Karyawan - SRC Rully')

@section('content_header')
    <h1>Edit Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('karyawan.update', $karyawan) }}" method="POST">
                @csrf
                @method('PUT')
                @include('karyawan._form')
            </form>
        </div>
    </div>
@stop

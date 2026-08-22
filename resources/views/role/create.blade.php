@extends('adminlte::page')

@section('title', 'Tambah Role - SRC Rully')

@section('content_header')
    <h1>Tambah Role</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('role.store') }}" method="POST">
                @csrf
                @include('role._form')
            </form>
        </div>
    </div>
@stop

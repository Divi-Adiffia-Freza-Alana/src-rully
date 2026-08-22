@extends('adminlte::page')

@section('title', 'Edit Role - SI Inventory')

@section('content_header')
    <h1>Edit Role</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('role.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                @include('role._form')
            </form>
        </div>
    </div>
@stop

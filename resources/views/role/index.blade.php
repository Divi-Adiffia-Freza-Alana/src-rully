@extends('adminlte::page')

@section('title', 'Role & Hak Akses - SI Inventory')

@section('content_header')
    <h1>Role & Hak Akses</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Role</h3>
            @can('role.kelola')
                <a href="{{ route('role.create') }}" class="btn btn-primary btn-sm">+ Tambah Role</a>
            @endcan
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Role</th>
                        <th>Tipe</th>
                        <th>Jumlah Karyawan</th>
                        @can('role.kelola')
                            <th></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>
                                @if ($role->is_system)
                                    <span class="badge badge-secondary">Bawaan Sistem</span>
                                @else
                                    <span class="badge badge-info">Kustom</span>
                                @endif
                            </td>
                            <td>{{ $role->users_count }}</td>
                            @can('role.kelola')
                                <td class="text-nowrap">
                                    <a href="{{ route('role.edit', $role) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @unless ($role->is_system)
                                        <form action="{{ route('role.destroy', $role) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @endunless
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada role.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Karyawan - SI Inventory')

@section('content_header')
    <h1>Karyawan</h1>
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
            <h3 class="card-title">Daftar Karyawan</h3>
            @can('karyawan.kelola')
                <a href="{{ route('karyawan.create') }}" class="btn btn-primary btn-sm">+ Tambah Karyawan</a>
            @endcan
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        @can('karyawan.kelola')
                            <th></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }} @if ($user->id === auth()->id()) <span class="badge badge-info">Anda</span> @endif</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role?->name ?? '-' }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            @can('karyawan.kelola')
                                <td class="text-nowrap">
                                    <a href="{{ route('karyawan.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('karyawan.destroy', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus karyawan {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
@stop

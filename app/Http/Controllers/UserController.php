<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')->orderBy('name')->paginate(15);

        return view('karyawan.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('karyawan.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role_id' => $request->validated('role_id'),
            'password' => $request->validated('password'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $karyawan): View
    {
        $roles = Role::orderBy('name')->get();

        return view('karyawan.edit', ['karyawan' => $karyawan, 'roles' => $roles]);
    }

    public function update(StoreUserRequest $request, User $karyawan): RedirectResponse
    {
        if ($karyawan->id === $request->user()->id && ! $request->boolean('is_active', true)) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role_id' => $request->validated('role_id'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $karyawan->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Request $request, User $karyawan): RedirectResponse
    {
        if ($karyawan->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($karyawan->role?->slug === 'owner' && User::whereHas('role', fn ($q) => $q->where('slug', 'owner'))->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Owner.');
        }

        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}

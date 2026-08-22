<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->get();

        return view('role.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('role.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
            'is_system' => false,
        ]);

        $role->permissions()->sync($request->validated('permissions', []));

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(StoreRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update([
            'name' => $request->validated('name'),
            'slug' => $role->is_system ? $role->slug : Str::slug($request->validated('name')),
        ]);

        $role->permissions()->sync($request->validated('permissions', []));

        return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih dipakai oleh karyawan.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus.');
    }
}

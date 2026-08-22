@php
    $selectedPermissions = old('permissions', ($role ?? null)?->permissions->pluck('id')->all() ?? []);
@endphp

<div class="form-group">
    <label for="name">Nama Role</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name ?? '') }}" {{ ($role->is_system ?? false) ? 'readonly' : '' }} required>
    @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    @if ($role->is_system ?? false)
        <small class="text-muted">Nama role bawaan sistem tidak dapat diubah.</small>
    @endif
</div>

<hr>
<h5>Hak Akses Menu</h5>
@error('permissions') <div class="alert alert-danger">{{ $message }}</div> @enderror

<div class="row">
    @foreach ($permissions as $group => $groupPermissions)
        <div class="col-md-4 mb-3">
            <div class="card card-outline card-primary h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">{{ $group }}</h6>
                </div>
                <div class="card-body py-2">
                    @foreach ($groupPermissions as $permission)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="perm-{{ $permission->id }}"
                                   name="permissions[]" value="{{ $permission->id }}"
                                   @checked(in_array($permission->id, $selectedPermissions))>
                            <label class="custom-control-label" for="perm-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('role.index') }}" class="btn btn-default">Batal</a>

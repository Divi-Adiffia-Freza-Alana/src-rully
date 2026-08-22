<div class="form-group">
    <label for="name">Nama</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $karyawan->name ?? '') }}" required>
    @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $karyawan->email ?? '') }}" required>
    @error('email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="role_id">Role</label>
    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
        <option value="">-- pilih role --</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected(old('role_id', $karyawan->role_id ?? '') == $role->id)>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
    @error('role_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label for="password">Password {{ isset($karyawan) ? '(kosongkan jika tidak ingin diubah)' : '' }}</label>
    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
           {{ isset($karyawan) ? '' : 'required' }}>
    @error('password') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
               @checked(old('is_active', $karyawan->is_active ?? true))
               {{ isset($karyawan) && $karyawan->id === auth()->id() ? 'disabled' : '' }}>
        <label class="custom-control-label" for="is_active">Aktif</label>
    </div>
    @if (isset($karyawan) && $karyawan->id === auth()->id())
        <input type="hidden" name="is_active" value="1">
        <small class="text-muted">Anda tidak dapat menonaktifkan akun sendiri.</small>
    @endif
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('karyawan.index') }}" class="btn btn-default">Batal</a>

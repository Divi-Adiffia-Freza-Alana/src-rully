@extends('layouts.customer')

@section('title', 'Masuk - SRC Rully')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="SRC Rully" style="width: 90px; height: 90px; border-radius: 50%;">
                    </div>
                    <h4 class="card-title text-center mb-4">Masuk ke Akun Anda</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.login.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Belum punya akun? <a href="{{ route('customer.register') }}">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Grosir SRC Rully')</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        body { background: #f4f4f4; }
        .navbar-brand img { border-radius: 50%; }
        .product-card { transition: box-shadow .15s ease; }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }
    </style>
    @stack('css')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('customer.catalog') }}">
                <img src="{{ asset('images/logo.jpeg') }}" alt="SRC Rully" width="36" height="36" class="mr-2">
                <span class="font-weight-bold">SRC Rully</span>
            </a>
            <div class="ml-auto d-flex align-items-center">
                @auth('customer')
                    <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                    </a>
                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-receipt"></i> Pesanan Saya
                    </a>
                    <span class="mr-2 text-muted d-none d-md-inline">Hai, {{ Auth::guard('customer')->user()->name }}</span>
                    <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}" class="btn btn-outline-primary btn-sm mr-2">Masuk</a>
                    <a href="{{ route('customer.register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
</body>
</html>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>@yield('title', 'Dashboard')</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light dark" />

  {{-- Fonts --}}
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">

  {{-- Icons --}}
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  {{-- Overlay Scrollbar --}}
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">

  {{-- AdminLTE --}}
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.css') }}">

  {{-- Custom --}}
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/yayasan.css') }}">

  @stack('css')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary sidebar-open">

<div class="app-wrapper">

  @include('admin.partials.navbar')
  @include('admin.partials.sidebar')

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
  </main>

  @include('admin.partials.footer')

</div>

{{-- Bootstrap (WAJIB untuk dropdown navbar)
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- AdminLTE --}}
<script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>
{{-- <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}

@stack('js')
</body>
</html>

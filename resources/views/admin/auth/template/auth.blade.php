<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication') - Voler Admin</title>

    <link rel="shortcut icon" href="{{ asset('backend/template/assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('backend/template/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/template/assets/css/app.css') }}">

    @stack('styles')
</head>

<body>
    <div id="auth">
        @yield('content')
    </div>

    <script src="{{ asset('backend/template/assets/js/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('backend/template/assets/js/app.js') }}"></script>
    <script src="{{ asset('backend/template/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
@include('layouts.partials.head')
@yield('head')
</head>
<body>
    @include('layouts.partials.navbar')
    @yield('content')
    @include('layouts.partials.footer')
    @yield('footer')
</body>
</html>
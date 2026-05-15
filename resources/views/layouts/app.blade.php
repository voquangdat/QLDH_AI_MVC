<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VoxFootball')</title>

    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/clients/css/mainstyle.css') }}">
    <script src="{{ asset('assets/clients/js/header.js') }}"></script>
    @stack('styles')
</head>

<body>
    <div class="page-wrapper">

        @include('clients.partials.header')

        @yield('content')

        @include('clients.partials.footer')

    </div>

    <script src="{{ asset('assets/clients/js/script.js') }}"></script>
    <script src="{{ asset('assets/clients/js/footer.js') }}"></script>
    <script>
        document.addEventListener('click', function(e) {
            var trigger  = document.getElementById('mini-cart-trigger');
            var wrapper  = document.getElementById('mini-cart-wrapper');
            var dropdown = document.getElementById('mini-cart-dropdown');

            if (!trigger || !dropdown) return;

            if (trigger.contains(e.target)) {
                e.preventDefault();
                dropdown.classList.toggle('open');
            } else if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    </script>
    @stack('scripts')
</body>

</html>

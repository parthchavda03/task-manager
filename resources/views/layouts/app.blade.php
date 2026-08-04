<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskSpace - Premium Task Manager')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    
    <!-- Custom Premium Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>

    @include('layouts.header')

    <!-- Main Content Area -->
    <main class="container mb-5">
        @yield('content')
    </main>

    <!-- Toast Notifications Container -->
    <div id="toast-container"></div>

    <!-- Scripts -->
    <!-- jQuery, Popper, Bootstrap 4 JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <script>
        // Custom elegant Toast utility
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            
            let icon = 'fa-solid fa-circle-check';
            if (type === 'danger') icon = 'fa-solid fa-circle-exclamation';
            if (type === 'warning') icon = 'fa-solid fa-triangle-exclamation';
            
            toast.innerHTML = `
                <i class="${icon}"></i>
                <div class="flex-grow-1">${message}</div>
            `;
            
            container.appendChild(toast);
            
            // Remove after 4 seconds
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.4s ease forwards';
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 3500);
        };

        // Show PHP session messages automatically
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'danger');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast("{{ $error }}", 'danger');
            @endforeach
        @endif
    </script>
    @yield('scripts')
</body>
</html>

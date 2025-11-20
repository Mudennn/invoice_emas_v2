<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet" />
    <title>{{ $title ?? 'Jin jin Jewelleries Manufacturing' }}</title>

    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}

    @include('layouts.styles.index')
    @include('layouts.styles.form')
    @include('layouts.styles.sidebar')
    @include('layouts.styles.topbar')
    @include('layouts.styles.tables')
    @include('layouts.styles.dashboard')
    @include('layouts.styles.profile')
    @include('layouts.styles.print')

    <link href="{{ asset('build/assets/app-DAzV1NZX.css') }}" rel="stylesheet">
</head>

<body>
    <div style="display:flex; margin: 0 auto; width:100%">
        {{-- SIDEBAR --}}
        @include('layouts.components.sidebar')

        <div class="w-100" style="overflow-x: hidden;">
            @include('layouts.components.topbar')
            {{-- TOPBAR --}}
            <div class="content-container">
                @yield('content')

            </div>

        </div>

        <script src="{{ asset('build/assets/app-D2eLhe8d.js') }}" defer></script>

        @include('layouts.scripts.sidebar')
        @include('layouts.scripts.form-handler')
        @include('layouts.scripts.return-balance-calculator')
        @include('layouts.scripts.invoice-form')
        @include('layouts.scripts.credit-note-form')
        @include('layouts.scripts.debit-note-form')
        @include('layouts.scripts.refund-note-form')
        {{-- @include('layouts.scripts.self-billed-form') --}}
        @include('layouts.scripts.other-company-handler')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $('#invoiceTable').DataTable({
                    responsive: false,
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    dom: '<"top"lf>rt<"bottom"ip><"clear">',
                });
            });
        </script>

        @stack('scripts')
        {{-- Alerts timeout --}}
        <script>
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        </script>
    </body>

</html>

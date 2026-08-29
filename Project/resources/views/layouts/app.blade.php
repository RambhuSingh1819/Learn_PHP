<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlowEscalate') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind Play CDN for modern CSS compiled utility classes -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            display: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f0f3ff',
                                100: '#e1e7ff',
                                500: '#435eff',
                                600: '#2d3eff',
                                700: '#222df2',
                            }
                        }
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            .grid-bg {
                background-color: #fafbfc;
                background-image: radial-gradient(rgba(0, 0, 0, 0.02) 1px, transparent 0);
                background-size: 24px 24px;
            }
        </style>

        <!-- Keep Alpine/JS scripts -->
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased grid-bg text-slate-800">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/80">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>

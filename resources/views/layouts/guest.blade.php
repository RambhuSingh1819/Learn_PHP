<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS Play CDN for instant, reliable rendering -->
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
            .grid-bg {
                background-color: #f8fafc;
                background-image: radial-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 0);
                background-size: 20px 20px;
            }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased min-h-screen grid-bg flex flex-col justify-center items-center p-4">
        <div class="w-full sm:max-w-md space-y-6">
            
            <!-- Branding Header -->
            <div class="text-center space-y-1">
                <a href="/" class="inline-block hover:opacity-90 transition">
                    <span class="font-display font-extrabold text-3xl tracking-tight text-slate-900">FlowEscalate</span>
                    <span class="text-[10px] block text-slate-500 font-bold tracking-widest uppercase mt-0.5">Multi-Tenant SaaS</span>
                </a>
            </div>

            <!-- Formal Card Container -->
            <div class="bg-white border border-slate-200/80 p-8 shadow-sm rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

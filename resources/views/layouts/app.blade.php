<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-cloak
    x-data="{ dark: localStorage.getItem('dark') === 'true' }" 
    x-bind:class="{ 'dark': dark }" 
    x-init="$watch('dark', val => localStorage.setItem('dark', val))"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} | @yield('title', 'Project Management Tool')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/3.1.2/flowbite.min.css"  rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/css/fonts.css'])

        <!-- Flaticon -->
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-straight/css/uicons-solid-straight.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-chubby/css/uicons-solid-chubby.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-straight/css/uicons-bold-straight.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-chubby/css/uicons-regular-chubby.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-thin-rounded/css/uicons-thin-rounded.css'>

        <!-- Scripts -->
        @vite(['resources/js/app.js'])

        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- Styles -->
        @asyncSelectStyles
        @livewireStyles
    </head>
    <body class="font-sans antialiased overflow-hidden">
        <div class="min-h-screen bg-gray-200 dark:bg-gray-900 flex overflow-hidden">
            <!-- Sidebar -->
            <div class="h-screen">
                @livewire('components.sidebar')
            </div>

            <!-- Main Content -->
            <div class="w-full h-screen overflow-y-auto">
                <main class="p-4">
                    <!-- Breadcrumbs -->
                    {{ $breadcrumbs ?? '' }}

                    <!-- Alerts -->
                    <x-alert />

                    {{ $slot }}
                </main>
            </div>
        </div>
        
        <!-- Toast -->
        <x-toaster-hub />

        @livewireScripts
        @stack('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    </body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Taps into your systems and monitors and analyzes your application logs in real-time.">
<title>{{ config('app.name', 'Wiretap') }}</title>
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Wiretap" />
<link rel="manifest" href="/site.webmanifest" />
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
  <div class="min-h-screen flex flex-col justify-center items-center px-6 py-6">
    <div class="w-full flex flex-col items-center justify-center max-w-md grow-1">
      <div class="mb-8">
          <flux:brand href="/" class="!h-auto !w-48">
            <x-logo class="!h-auto !w-full" />
          </flux:brand>
      </div>
      <div class="w-full">
          @yield('content')
      </div>
    </div>
    <div class="text-center font-mono text-xs text-gray-500 dark:text-gray-200">
      Made with ❤️ by <a href="https://marceli.to" target="_blank" class="hover:underline hover:underline-offset-2">marceli.to</a>
    </div>
  </div>
  @fluxScripts
</body>
</html>
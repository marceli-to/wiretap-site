<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Taps into your systems and monitors and analyzes your application logs in real-time.">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
	<div class="min-h-screen">
		<!-- Header -->
		<flux:header container class="bg-white min-h-16 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center">
      <flux:brand href="{{ route('logs.dashboard') }}" logo="/img/logo.svg" />
      <flux:spacer />
			{{-- <flux:brand
				href="{{ route('logs.dashboard') }}"
				name="AppLog Dashboard"
				class="text-xl font-semibold"
			/> --}}

			{{-- <flux:navbar class="-mb-px">
				<flux:navbar.item icon="chart-bar" href="{{ route('logs.dashboard') }}" current>
					Dashboard
				</flux:navbar.item>
				<flux:navbar.item icon="document-text" href="{{ route('logs.dashboard') }}">
					Logs
				</flux:navbar.item>
			</flux:navbar>

			

			<flux:navbar class="me-4">
				<flux:navbar.item icon="magnifying-glass" href="#" label="Search" />
				<flux:navbar.item icon="bell" href="#" label="Notifications" />
			</flux:navbar> --}}

			<flux:dropdown position="bottom" align="end">
        <flux:profile avatar="/img/avatar.jpg" />
				<flux:menu>
					{{-- <flux:menu.item icon="user-circle">Profile</flux:menu.item>
					<flux:menu.item icon="cog-6-tooth">Settings</flux:menu.item>
					<flux:menu.separator /> --}}
					<form method="POST" action="{{ route('logout') }}">
						@csrf
						<flux:menu.item
							icon="arrow-right-start-on-rectangle"
							onclick="this.closest('form').submit()"
							type="button"
						>
							Sign out
						</flux:menu.item>
					</form>
				</flux:menu>
			</flux:dropdown>
		</flux:header>

		<!-- Page Content -->
		<main class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				{{ $slot }}
			</div>
		</main>
	</div>

	@fluxScripts
</body>
</html>
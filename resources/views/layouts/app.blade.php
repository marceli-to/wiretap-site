<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Taps into your systems and monitors and analyzes your application logs in real-time.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
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
<script>
    // Configure Pusher Beams Instance ID (you'll need to set this in your .env)
    window.pusherBeamsInstanceId = '{{ config('services.pusher.beams_instance_id') }}';

</script>
@fluxAppearance
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
	<div class="min-h-screen flex flex-col pb-4">
		<!-- Header -->
		<flux:header container class="bg-white min-h-16 lg:min-h-18 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center">
      
      <flux:brand  href="{{ route('logs.dashboard') }}" class="!h-auto !w-40">
        <x-logo class="!h-auto !w-full" />
      </flux:brand>

      <flux:spacer />

      <!-- Notifications Button -->
      <flux:button id="enable-notifications" variant="ghost" size="sm" class="mr-4">
          <flux:icon name="bell" />
          Enable Notifications
      </flux:button>


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
		<main class="py-6 flex-1">
			<div class="max-w-7xl mx-auto px-6 lg:px-8">
				{{ $slot }}
			</div>
		</main>
    <div class="text-center font-mono text-xs text-gray-500 dark:text-gray-200">
      Made with ❤️ by <a href="https://marceli.to" target="_blank" class="hover:underline hover:underline-offset-2">marceli.to</a>
    </div>
	</div>

	@fluxScripts
</body>
</html>
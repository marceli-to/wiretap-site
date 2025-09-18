@extends('layouts.auth')

@section('content')
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Sign in to your account</flux:heading>
            <flux:subheading>Access your wiretap dashboard</flux:subheading>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label for="email">Email</flux:label>
                <flux:input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label for="password">Password</flux:label>
                <flux:input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <flux:error name="password" />
            </flux:field>


            <div class="flex justify-between items-center mt-8">
                <flux:button type="submit" variant="primary" color="zinc" class="w-auto min-w-1/3">
                    Sign in
                </flux:button>

                @if (Route::has('password.request'))
                    <div class="text-center">
                        <flux:link href="{{ route('password.request') }}" class="text-sm">
                            Forgot your password?
                        </flux:link>
                    </div>
                @endif
            </div>
        </form>
    </flux:card>
@endsection
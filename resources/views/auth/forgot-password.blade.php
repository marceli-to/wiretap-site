@extends('layouts.auth')

@section('content')
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Forgot password</flux:heading>
            <flux:subheading>
                Enter your email address and we'll send you a link to reset your password.
            </flux:subheading>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <flux:toast variant="success">
                {{ session('status') }}
            </flux:toast>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

            <div class="flex justify-between items-center mt-8">
                <flux:button type="submit" variant="primary" color="zinc" class="w-auto min-w-1/3">
                    Send reset link
                </flux:button>

                <div class="text-center">
                    <flux:link href="{{ route('login') }}" class="text-sm">
                        Back to login
                    </flux:link>
                </div>
            </div>
        </form>
    </flux:card>
@endsection
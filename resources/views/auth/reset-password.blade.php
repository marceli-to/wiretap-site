@extends('layouts.auth')

@section('content')
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Reset password</flux:heading>
            <flux:subheading>Enter your new password</flux:subheading>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <flux:field>
                <flux:label for="email">Email</flux:label>
                <flux:input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $request->email) }}"
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
                    autocomplete="new-password"
                />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label for="password_confirmation">Confirm Password</flux:label>
                <flux:input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <flux:error name="password_confirmation" />
            </flux:field>

            <flux:button type="submit" variant="primary" color="zinc" class="w-auto min-w-1/3">
                Reset password
            </flux:button>
        </form>
    </flux:card>
@endsection
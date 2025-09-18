@extends('layouts.auth')

@section('content')
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Verify your email</flux:heading>
            <flux:subheading>
                Before continuing, please verify your email address by clicking on the link we just emailed to you.
            </flux:subheading>
        </div>

        @if (session('status') == 'verification-link-sent')
            <flux:toast variant="success">
                A new verification link has been sent to your email address.
            </flux:toast>
        @endif

        <div class="flex justify-between items-center mt-8">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" color="zinc" class="w-auto min-w-1/3">
                    Resend verification email
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="outline" class="w-auto min-w-1/3">
                    Log out
                </flux:button>
            </form>
        </div>
    </flux:card>
@endsection
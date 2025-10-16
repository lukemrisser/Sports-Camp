<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        @if (session('pending_email'))
            {{ __('Thanks for signing up! We\'ve sent a verification email to ') }}<strong>{{ session('pending_email') }}</strong>{{ __('. Please click the link in the email to complete your registration.') }}
        @else
            {{ __('Thanks for signing up! Please check your email for a verification link to complete your registration.') }}
        @endif
    </div>

    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Important Information:</h3>
        <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
            <li>Your account will be created after you verify your email</li>
            <li>The verification link expires in 48 hours</li>
            <li>Check your spam/junk folder if you don't see the email</li>
        </ul>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        <strong>{{ __('Didn\'t receive the email?') }}</strong><br>
        {{ __('Since your account isn\'t created yet, you\'ll need to register again if the link expires.') }}
    </div>

    <div class="mt-4 flex items-center justify-between">
        <a href="{{ route('register') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('Register Again') }}
        </a>

        <a href="{{ route('login') }}"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Go to Login') }}
        </a>
    </div>
</x-guest-layout>

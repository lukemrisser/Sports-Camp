<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter your email address and we\'ll send you a new verification link.') }}
    </div>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $email)" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to login') }}
            </a>

            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('We have sent a verification code to your email address. Enter it below to continue.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <!-- Code -->
        <div>
            <x-input-label for="code" :value="__('Verification code')" />
            <x-text-input id="code" class="block mt-1 w-full text-center tracking-widest text-lg" type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-recaptcha-field action="two_factor" />

        <div class="flex items-center justify-between mt-4">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4">
        @csrf
        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Resend code') }}
        </button>
    </form>
</x-guest-layout>

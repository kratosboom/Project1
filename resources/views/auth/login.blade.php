<x-guest-layout>
    <h1 class="mb-6 text-xl font-extrabold tracking-tight text-white">Masuk</h1>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="accent-primary rounded border-white/20 bg-black/40 text-primary shadow-sm focus:ring-2 focus:ring-primary/50" name="remember">
                <span class="ms-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-end">
            @if (Route::has('password.request'))
                <a class="order-2 text-sm text-gray-400 underline decoration-white/20 underline-offset-2 transition hover:text-white sm:order-1 sm:mr-auto" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="order-1 w-full sm:order-2 sm:w-auto">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

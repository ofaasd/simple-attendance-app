<x-guest-layout>
    <!-- Session Status -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
          <x-auth-session-status class="mb-4" :status="session('status')" />
          <p class="login-box-msg">Sign in to start your session</p>
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group mb-3">
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <!-- Password -->
            <div class="input-group mb-3">
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="btn btn-primary btn-block">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
           </form>
        </div>
    </div>
</x-guest-layout>

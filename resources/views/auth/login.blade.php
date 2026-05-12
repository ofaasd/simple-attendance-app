<x-guest-layout>
    <!-- Session Status -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <img src="{{ asset('/img/logo_kampi.png') }}" alt="Logo Kampi" class="h-32 w-auto" style="height:140px !important;">
        </div>
        <div class="card-body">
          @if (session('status'))
              <div class="alert alert-success mb-4">
                  {{ session('status') }}
              </div>
          @endif
          <p class="login-box-msg">Silahakn Login untuk masuk ke <br /> <b style="font-size: 18px;">Sistem Informasi Monitoring dan Evaluasi SPPG</b></p>
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group mb-3">
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            @error('email')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            <!-- Password -->
            <div class="input-group mb-3">
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @error('password')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
               

                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Log in') }}
                </button>
            </div>
           </form>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var togglePassword = document.getElementById('togglePassword');
        var passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
</x-guest-layout>

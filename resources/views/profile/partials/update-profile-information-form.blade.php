<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="row">
        <div class="col-md-4 text-center">
            <div class="form-group">
                <img src="{{ $user->profile_photo_path ? asset($user->profile_photo_path) : asset('dist/img/user2-160x160.jpg') }}" alt="Profile Photo" class="img-fluid img-circle mb-3" style="max-width: 180px; object-fit: cover;">
            </div>
            <div class="form-group">
                <label for="photo">{{ __('Photo') }}</label>
                <input id="photo" name="photo" type="file" class="form-control-file">
                @error('photo')
                    <span class="text-danger d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nik">{{ __('NIK') }}</label>
                <input id="nik" name="nik" type="text" class="form-control {{ $errors->has('nik') ? 'is-invalid' : '' }}" value="{{ old('nik', $user->nik) }}" autocomplete="nik">
                @error('nik')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning">
                    <p class="mb-2">{{ __('Your email address is unverified.') }}</p>
                    <button form="send-verification" class="btn btn-sm btn-secondary">{{ __('Click here to re-send the verification email.') }}</button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2 alert alert-success">{{ __('A new verification link has been sent to your email address.') }}</div>
                    @endif
                </div>
            @endif

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                @if (session('status') === 'profile-updated')
                    <span class="text-success ml-3">{{ __('Saved.') }}</span>
                @endif
            </div>
        </div>
    </div>
</form>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

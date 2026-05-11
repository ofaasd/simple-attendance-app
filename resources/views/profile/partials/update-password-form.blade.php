<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="update_password_current_password">{{ __('Current Password') }}</label>
        <input id="update_password_current_password" name="current_password" type="password" class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" autocomplete="current-password">
        @if ($errors->updatePassword->has('current_password'))
            <span class="text-danger">{{ $errors->updatePassword->first('current_password') }}</span>
        @endif
    </div>

    <div class="form-group">
        <label for="update_password_password">{{ __('New Password') }}</label>
        <input id="update_password_password" name="password" type="password" class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" autocomplete="new-password">
        @if ($errors->updatePassword->has('password'))
            <span class="text-danger">{{ $errors->updatePassword->first('password') }}</span>
        @endif
    </div>

    <div class="form-group">
        <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" autocomplete="new-password">
        @if ($errors->updatePassword->has('password_confirmation'))
            <span class="text-danger">{{ $errors->updatePassword->first('password_confirmation') }}</span>
        @endif
    </div>

    <div class="form-group mt-3">
        <button type="submit" class="btn btn-info">{{ __('Save') }}</button>
        @if (session('status') === 'password-updated')
            <span class="text-success ml-3">{{ __('Saved.') }}</span>
        @endif
    </div>
</form>

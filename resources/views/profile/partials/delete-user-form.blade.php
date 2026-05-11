<p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmUserDeletionModal">
    {{ __('Delete Account') }}
</button>

<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Delete Account') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>

                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" class="form-control {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" placeholder="{{ __('Password') }}">
                        @if ($errors->userDeletion->has('password'))
                            <span class="text-danger">{{ $errors->userDeletion->first('password') }}</span>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

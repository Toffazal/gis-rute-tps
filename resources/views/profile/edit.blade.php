@php
    $template = auth()->user()->role === 'user' ? 'user.template' : 'admin.template';
@endphp

@extends($template)


@section('content')

    <div class="white-box">
        <h4 class="box-title">{{ __('Profile Information') }}</h4>

        <div class="basic-form">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                        value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">{{ __('Username') }}</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username"
                        value="{{ old('username', $user->username) }}" required>
                    @error('username')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email Address') }}</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email"
                        value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-warning small mb-1">
                                {{ __('Your email address is unverified.') }}
                                <button type="submit" form="send-verification"
                                    class="btn btn-link btn-sm p-0 align-baseline">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="text-success small">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    @if (session('status') === 'profile-updated')
                        <span class="text-success small ms-2">{{ __('Saved.') }}</span>
                    @endif
                </div>
            </form>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">
                @csrf
            </form>
        </div>
    </div>


    <div class="white-box">
        <h4 class="box-title">{{ __('Update Password') }}</h4>

        <div class="basic-form">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="current_password">{{ __('Current Password') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control"
                                autocomplete="current-password">
                            <button type="button"
                                class="btn btn-outline-secondary btn-sm toggle-password float-end position-relative"
                                style="top: -32px; right: 5px;" data-target="current_password">
                                👁
                            </button>
                            @if ($errors->updatePassword->has('current_password'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('current_password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="password">{{ __('New Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control"
                                autocomplete="new-password">
                            <button type="button"
                                class="btn btn-outline-secondary btn-sm toggle-password float-end position-relative"
                                style="top: -32px; right: 5px;" data-target="password">
                                👁
                            </button>
                            @if ($errors->updatePassword->has('password'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="form-control" autocomplete="new-password">
                            <button type="button"
                                class="btn btn-outline-secondary btn-sm toggle-password float-end position-relative"
                                style="top: -32px; right: 5px;" data-target="password_confirmation">
                                👁
                            </button>
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>







                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    @if (session('status') === 'password-updated')
                        <span class="text-success small ms-2">{{ __('Password updated successfully.') }}</span>
                    @endif
                </div>
            </form>
        </div>
    </div>


    {{-- Script Show/Hide Password --}}
    @push('scripts')
        <script>
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerText = '🙈';
                    } else {
                        input.type = 'password';
                        this.innerText = '👁';
                    }
                });
            });
        </script>
    @endpush


@endsection

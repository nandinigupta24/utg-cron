@extends('layouts.login')
@section('title', 'Login')
@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper auth-page">
        <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
            <div class="row w-100">
                <div class="col-lg-4 mx-auto">
                    
                    <div class="auto-form-wrapper">
                        <h3>{{ __('Reset Password') }}</h3>
                        <hr/>
                        <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Reset Password') }}">
                            @csrf
                             <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group">
                                <label class="label">{{ __('E-Mail Address') }}</label>
                                <div class="input-group">
                                    <input placeholder="Email" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>

                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="label">{{ __('Password') }}</label>
                                <div class="input-group">
                                    <input placeholder="Password" id="password" type="password" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>

                                     @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="label">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <input placeholder="Password" id="password_confirm" type="password" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="password_confirmation" value="{{ old('password_confirmation') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary submit-btn btn-block" type="submit">{{ __('Reset Password') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>

@endsection

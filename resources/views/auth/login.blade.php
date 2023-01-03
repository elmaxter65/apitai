@extends('layouts.app')

@section('content')

<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="auth-wrapper auth-basic px-2">
                <div class="auth-inner my-2">
                    <!-- Login basic -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="brand-logo">
                                <img src="{{asset('uikit/logo.png')}}" alt="logo">
                            </div>

                            <h4 class="card-title mb-1">Iniciar Sesi&oacute;n</h4>
                            <p class="card-text mb-2">Por favor ingresa tus credenciales para continuar.</p>

                            <form class="auth-login-form mt-2" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-1">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="john@example.com" value="{{ old('email') }}" aria-describedby="email" tabindex="1" autofocus />
                                    @error('email')
                                        <span id="email-error" class="invalid-feedback error" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-1">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>

                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}">
                                                <small>¿Has olvidado tu contrase&ntilde;a?</small>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="input-group input-group-merge form-password-toggle">
                                        <input type="password" name="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100" tabindex="4">Ingresar</button>
                            </form>

                            {{-- <p class="text-center mt-2">
                                <span>New on our platform?</span>
                                <a href="auth-register-basic.html">
                                    <span>Create an account</span>
                                </a>
                            </p>

                            <div class="divider my-2">
                                <div class="divider-text">or</div>
                            </div>

                            <div class="auth-footer-btn d-flex justify-content-center">
                                <a href="#" class="btn btn-facebook">
                                    <i data-feather="facebook"></i>
                                </a>
                                <a href="#" class="btn btn-twitter white">
                                    <i data-feather="twitter"></i>
                                </a>
                                <a href="#" class="btn btn-google">
                                    <i data-feather="mail"></i>
                                </a>
                                <a href="#" class="btn btn-github">
                                    <i data-feather="github"></i>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                    <!-- /Login basic -->
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->

@endsection
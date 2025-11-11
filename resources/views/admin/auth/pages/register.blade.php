@extends('admin.auth.template.auth')
@section('title', 'Register')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-12 mx-auto">
                <div class="card pt-4">
                    <div class="card-body">
                        <div class="text-center mb-5">
                            <img src="{{ asset('backend/template/assets/images/favicon.svg') }}" height="48" class="mb-4">
                            <h3>Sign Up</h3>
                            <p>Please fill the form to register.</p>
                        </div>
                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('postRegister') }}" method="POST" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                            value="{{ old('first_name') }}">
                                    </div>
                                    @error('first_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                            value="{{ old('last_name') }}">
                                    </div>
                                    @error('last_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" id="username" name="username" class="form-control"
                                            value="{{ old('username') }}" required>
                                    </div>
                                    @error('username')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            value="{{ old('email') }}" required>
                                    </div>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" name="password" class="form-control" required>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control" required>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <a href="{{ route('login') }}">Have an account? Login</a>
                                <button class="btn btn-primary">Register</button>
                            </div>
                        </form>

                        <div class="divider">
                            <div class="divider-text">OR</div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-block mb-2 btn-primary"><i data-feather="facebook"></i>
                                    Facebook</button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-block mb-2 btn-secondary"><i data-feather="github"></i>
                                    Github</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

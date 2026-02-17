@extends('layouts.public')
@section('content')
    <div class="min-vh-100 p-3">
        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
            <div class="form-section p-3">
                <h1 class="text-center">Welcome to Apex Racing</h1>
                <p class="text-center">Create your account before accessing Apex</p>

                <form action="{{ route('user.creation') }}" method="POST">

                    @csrf
                    <div class="row g-3">
                        <div class="mb-3">
                            <div class="col-lg-12">
                                <label for="full_names" class="form-label">Full name</label>
                                <input type="text" name="full_names" id="full_names" class="form-control">
                            </div>
                            @error('full_names')
                                <div class="alert alert-danger mt-3 fs-6" style="font-size: 10px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="col-lg-12">
                                <label for="email_address" class="form-label">Email Address</label>
                                <input type="email" name="email_address" id="email_address" class="form-control">
                                @error('email_address')
                                    <div class="alert alert-danger mt-3 fs-6" style="font-size: 10px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-lg-12">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control">
                                @error('phone_number')
                                    <div class="alert alert-danger mt-3 fs-6" style="font-size: 10px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                                    @error('password')
                                        <div class="alert alert-danger mt-3 fs-6" style="font-size: 10px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                                    @error('confirm_password')
                                        <div class="alert alert-danger mt-3 fs-6" style="font-size: 10px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-content-center justify-content-center">
                            <button class="btn btn-primary">Create Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh -->
    </div>
@endsection

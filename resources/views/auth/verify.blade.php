@extends('layouts.public')
@section('content')
    <div class="min-vh-100 p-3">
        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
            <div class="form-section p-3">
                <h1 class="text-center">Verify your account.</h1>
                <p class="text-center">We need to confirm your account before you access Apex. We have sent a verification code to your number.</p>

                <form method="POST" id="otp-verification">

                    @csrf
                    <div class="row g-3">
                        <div class="mb-3">
                            <div class="col-lg-12 d-flex align-content-center justify-content-center">
                                <div id="otp-inputs">
                                    <form id="otp-verification">
                                        @csrf
                                        <div class="mb-4">
                                            <input type="text" name="otp-code" class="otp-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required>
                                        </div>
                                        <div class="d-flex align-content-center justify-content-center flex-column">
                                            <button class="btn btn-primary" type="submit">Verify Account</button>
                
                                        </div>
                                    </form>
                                    <p class="text-center fs-3">Didnt get the code? <a id="resend-button">Resend.</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Waste no more time arguing what a good man should be, be one. - Marcus Aurelius -->
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const form = document.getElementById('otp-verification');
            const resendOTPButton = document.getElementById('resend-button');

            async function sendData(){
                const formData = new FormData(form);
                try {
                    const response = await fetch('{{ route('verify.otp') }}', {
                        method: "POST",
                        body: formData,
                    });

                    server_response = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(`${server_response.message}`);
                    }

                    
                    if(response.status == 200){
                        toastr.success('OTP Verified successfully');
                        setTimeout(() => {
                            window.location.href="/";
                        }, 2000);
                    }
                } catch (error) {
                    toastr.error(error.message);
                }
            }

            async function requestNewOTPCode() {
                try {
                    const response = await fetch('{{ route('resend.otp') }}');
                    server_response = await response.json();

                    toastr.success(server_response.message);
                } catch (error) {
                    console.error('Error in requesting code: ', error);
                    toastr.success(error.message);
                }
            }

            form.addEventListener('submit', function(e){
                e.preventDefault();

                sendData();
            });

            resendOTPButton.addEventListener('click', function(event){
                event.preventDefault();
                form.reset();
                requestNewOTPCode();
            });
        });
    </script>
@endsection

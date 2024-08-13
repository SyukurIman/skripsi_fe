@extends('layouts.app')

@section('content')

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="wrap">
                    <div class="img" style="background-image: url(images/bg-1.jpg);"></div>
                    <div class="login-wrap p-4 p-md-5">
                  <div class="d-flex">
                      <div class="w-100">
                          <h3 class="mb-4">Sign In</h3>
                      </div>
                            <div class="w-100">
                                <p class="social-media d-flex justify-content-end">
                                    <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                                    <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-twitter"></span></a>
                                </p>
                            </div>
                  </div>
                        <form class="signin-form">
                      <div class="form-group mt-3">
                          <input type="text" class="form-control" name="email" id="email" required>
                          <label class="form-control-placeholder" for="email">Email</label>
                      </div>
                <div class="form-group">
                  <input id="password-field" name="password" type="password" class="form-control" required>
                  <label class="form-control-placeholder" for="password">Password</label>
                  <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                <div class="form-group">
                    <button type="button" class="form-control btn btn-primary rounded submit px-3" id="btn_login">Sign In</button>
                </div>
                <div class="form-group d-md-flex">
                    <div class="w-50 text-left">
                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                  <input type="checkbox" checked>
                                  <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="w-50 text-md-right">
                                    <a href="#">Forgot Password</a>
                                </div>
                </div>
              </form>
              <p class="text-center">Not a member? <a data-toggle="tab" href="#signup">Sign Up</a></p>
            </div>
          </div>
            </div>
        </div>
    </div>
</section>

{{-- <div class="container mx-auto">
    <div class="flex flex-col items-center justify-center h-screen px-4 py-6">
        <div class="text_html items-center justify-center">
            <h1 class="text-4xl font-bold mb-4">Welcome to Video Call App</h1>
            <div id="cameraCheck"></div>
        </div>


        <div class="container flex login">
            <div class="right">
                <img src="img/logo-login.png" alt="" width="1024" height="500">

            </div>
            <div class="left">
                <form class="my-4 flex flex-col form-container w-full p-5">
                    <h3 class="title_page">Login Page</h3>
                    @csrf
                    <div class="flex flex-col">
                        <label for="email" class="my-2 px-2 w-full">Email</label>
                        <input type="text" name="email" id="email" placeholder="Masukkan Email Anda" class="my-2 p-2 border border-gray-300 rounded-md w-full">

                        <label for="password" class="my-2 px-2 w-full">Password</label>
                        <input type="password" name="password" id="password" placeholder="Masukkan Password Anda" class="my-2 p-2 border border-gray-300 rounded-md w-full">
                    </div>

                    <button type="button" id="btn_login" class="p-2 mt-4 bg-blue-500 text-white rounded-md w-full">Login Video Call</button>
                </form>
            </div>
        </div>


        @if (session('error'))
            <div class="mt-4 p-2 bg-red-500 text-white rounded-md">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div> --}}

<script>
    $(document).ready(async function() {
        $('#btn_login').on('click',async function () {
            const response = await fetch('/api/v1/auth/login', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: $('#email').val(),
                    password: $('#password-field').val()
                })
            });

            const result = await response.json();
            console.log(result)
            if (result.status == 'Berhasil') {
                window.localStorage.setItem('token', result.token)
                window.location.href = '/chatormeet';
            } else {
                alert('Login Gagal')
            }
        });
    });

    async function cek_login() {
        // console.log(window.localStorage.getItem('token'))
        if (window.localStorage.getItem('token') != null) {
            const response = await fetch('/api/v1/image_profile', {
                method: 'GET',

                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer '+window.localStorage.getItem('token')
                },
            });

            const result = await response.json();
            if (result.status == "Berhasil") {
                window.location.href = '/meeting';
            } else {
                window.localStorage.removeItem('token')
            }
        }
    }
    cek_login()
</script>
@endsection

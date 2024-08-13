<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Admin</title>

    <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>

    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
          <div class="col-12">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
              <div class="container-fluid pe-0">
                <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.html">
                  PS Admin
                </a>
                <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon mt-2">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                  </span>
                </button>
                <div class="collapse navbar-collapse" id="navigation">
                  <ul class="navbar-nav mx-auto ms-xl-auto me-xl-7" >
                    
                    {{-- <li class="nav-item">
                      <a class="nav-link me-2" href="../pages/sign-in.html">
                        <i class="fas fa-key opacity-6 text-dark me-1"></i>
                        Sign In
                      </a>
                    </li> --}}
                  </ul>
                  <li class="nav-item d-flex align-items-center">
                    <a class="btn btn-round btn-sm mb-0 btn-outline-primary me-2" target="_blank" href="">Log In</a>
                  </li>
                </div>
              </div>
            </nav>
            <!-- End Navbar -->
          </div>
        </div>
    </div>

    <main class="main-content  mt-0">
      <section>
        <div class="page-header min-vh-100">
          <div class="container">
            <div class="row">
              <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                <div class="card card-plain mt-7">
                  <div class="card-header pb-0 text-left bg-transparent">
                      <h3 class="font-weight-bolder text-info text-gradient">Welcome back</h3>
                      <p class="mb-0">Enter your email and password to sign in</p>
                  </div>
                  <div class="card-body">
                      <form method="POST" action="{{ route('login_admin') }}" class="log-in" autocomplete="off">
                          @csrf`
                          <label for="email">Email:</label>
                          <div class="mb-3">
                              <input 
                                  placeholder="Email" 
                                  type="text" name="email" 
                                  id="email" autocomplete="off"
                                  onfocus="this.placeholder = ''"
                                  :value="old('email')"
                                  onblur="this.placeholder = 'email'"
                                  required 
                                  class="form-control">
                          </div>
                          
                          <label for="password">Password:</label>
                          <div class="mb-3">
                              <input 
                                  placeholder="Password" type="password" 
                                  name="password" id="password" 
                                  autocomplete="off"
                                  onfocus="this.placeholder = ''"
                                  onblur="this.placeholder = 'Password'"
                                  required
                                  autocomplete="current-password" 
                                  class="form-control">
                          </div>

                          <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                              <label class="form-check-label" for="rememberMe">Remember me</label>
                          </div>
                          
                      
                          <div class="text-center">
                              <button type="submit" class="btn btn-primary w-100 mt-4 mb-0">Log in</button>
                          </div>
                      </form>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                  <div
                    class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6"
                    style="background-image:url('{{ env('APP_URL') }}assets/img/curved-images/curved6.jpg')">
                  </div>
                </div>
              </div>
            </div>
          </div>
        
        </div>
      </section>
    </main>

    <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
    <footer class="footer py-3 position-sticky z-index-sticky bottom-0 blur shadow">
      <div class="container">
        <div class="row">
          <div class="col-8 mx-auto text-center mt-1">
            <p class="mb-0 text-secondary">
              Copyright © <script>
                document.write(new Date().getFullYear())
              </script> Pertemanan Sejiwa
            </p>
          </div>
        </div>
      </div>
    </footer>

  
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <!--   Core JS Files   -->
  <script src="{{  asset('assets/js/core/popper.min.js')}}"></script>
  <script src="{{  asset('assets/js/core/bootstrap.min.js')}}"></script>
  <script src="{{  asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{  asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{  asset('assets/js/soft-ui-dashboard.min.js?v=1.0.7')}}"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</body>
</html>
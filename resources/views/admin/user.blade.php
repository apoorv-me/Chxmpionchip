
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Admin Profile
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="{{ asset('assets/css/material-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet" />
</head>

<body class="dark-edition">
  <div class="wrapper ">
    @include('admin.includes.sidebar')
    <div class="main-panel">
      <!-- Navbar -->
      @include('admin.includes.header')
      <!-- End Navbar --> 
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Profile</h4>
                </div>
                <div class="card-body">
                  <form method="post" action="{{route('profile')}}">
                    @csrf
                    <input type="hidden" name="id" value="{{$admin->id}}">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Name</label>
                          <input type="text" class="form-control" name="name" value="{{$admin->name}}">
                        </div>

                        @if ($errors->has('name'))
                          <span class="help-block font-red-mint">
                            <strong>{{ $errors->first('name') }}</strong>
                          </span>
                        @endif
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Email</label>
                          <input type="text" class="form-control" name="email" value="{{$admin->email}}" disabled>
                        </div>
                      </div>

                    </div>
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                          {{ session()->get('message') }}
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary pull-right">Update Profile</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Change Password</h4>
                </div>
                
                <div class="card-body">
                  <form method="post" action="{{route('change-password')}}">
                    @csrf
                    <input type="hidden" name="id" value="{{$admin->id}}">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Password</label>
                          <input type="password" name="password" class="form-control">
                        </div>

                        @if ($errors->has('password'))
                          <span class="help-block font-red-mint">
                            <strong>{{ $errors->first('password') }}</strong>
                          </span>
                        @endif

                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Confirm Password</label>
                          <input type="password" name="confirm_password" class="form-control">
                        </div>

                        @if ($errors->has('confirm_password'))
                          <span class="help-block font-red-mint">
                            <strong>{{ $errors->first('confirm_password') }}</strong>
                          </span>
                        @endif

                      </div>

                    </div>
                    @if(session()->has('msg'))
                        <div class="alert alert-success">
                          {{ session()->get('msg') }}
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary pull-right">Update Password</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>

            </div>
            <!-- <div class="col-md-4">
              <div class="card card-profile">
                <div class="card-avatar">
                  <a href="#pablo">
                    <img class="img" src="../assets/img/faces/marc.jpg" />
                  </a>
                </div>
                <div class="card-body">
                  <h6 class="card-category">CEO / Co-Founder</h6>
                  <h4 class="card-title">Alec Thompson</h4>
                  <p class="card-description">
                    Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...
                  </p>
                  <a href="#pablo" class="btn btn-primary btn-round">Follow</a>
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </div>
      @include('admin.includes.footer')
  <!--   Core JS Files   -->
  <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
  <script src="https://unpkg.com/default-passive-events"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="{{ asset('assets/js/plugins/chartist.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('assets/js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.0') }}"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="{{ asset('assets/demo/demo.js') }}"></script>
  <script src="{{ asset('assets/js/admin/core.js') }}"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Add Promo Code
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
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Wallet Management</h4>
                  <a href="{{ route('promoCode')}}" class="btn btn-primary">Back</a>
                </div>
                <div class="card-body">
                  <form method="post" action="{{route('promoCodeProcess')}}">
                    @csrf

                    <div class="row">
                      <div class="col-md-2">
                        <label class="bmd-label-floating">Promo Code Name</label>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">

                          <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>

                        @if ($errors->has('name'))
                        <span class="help-block font-red-mint">
                          <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                      </div>

                      <div class="col-md-2">
                        <label class="bmd-label-floating">Chips</label>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">

                          <input type="number" class="form-control" name="chips" value="{{ old('chips') }}">
                        </div>

                        @if ($errors->has('chips'))
                        <span class="help-block font-red-mint">
                          <strong>{{ $errors->first('chips') }}</strong>
                        </span>
                        @endif
                      </div>

                      <div class="col-md-2">
                        <label class="bmd-label-floating">Valid Till</label>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">

                          <input type="date" class="form-control" name="valid_till" value="{{ old('valid_till') }}">
                        </div>

                        @if ($errors->has('valid_till'))
                        <span class="help-block font-red-mint">
                          <strong>{{ $errors->first('valid_till') }}</strong>
                        </span>
                        @endif
                      </div>


                    </div>



                    @if(session()->has('message'))
                    <div class="alert alert-success">
                      {{ session()->get('message') }}
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary pull-right">Save</button>

                </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include('admin.includes.footer')
      <!--   Core JS Files   -->
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      <input type="hidden" id="base-url" value="{{ url('/') }}">
      <input type="hidden" id="send-notify" value="{{ route('sendNotification') }}">
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
      <script type="text/javascript" src=" https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>

      <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

      <script src="{{ asset('assets/js/admin/sweetalert.js') }}"></script>
      <script>
        $(document).ready(function() {
          var table = $('#example').DataTable({});
          $('[data-toggle="tooltip"]').tooltip();

        });

        function sendNotification(id) {

          var token = $('#token').val();
          var request = $('#send-notify').val().trim();

          swal({
              title: "Are you sure?",
              text: "You want to send this notification To User's devices!",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                $.ajax({
                  url: request,
                  type: "POST",
                  data: 'id=' + id + '&_token=' + token,
                  success: function(response) {
                    if (response.success === true) {
                      swal("Good job!", "Notification Sent Successfully", "success").then(() => {
                        location.reload();
                      })
                    }
                  }
                });
              } else {
                swal("This Notification is not send to user's devices");
              }
            });

        }
      </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Contact Us
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="{{ asset('assets/css/material-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet" />
  <style>
    
  </style>
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
                  <h4 class="card-title ">Contact Us</h4>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                {{$dataTable->table()}}
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @include('admin.includes.footer')
      <!-- modal start added by sarmistha -->
      <div id="demo-modal" class="modall">
          <div class="modall__content">
            <h3>Reply</h3>
            <form method="post"  action="{{ route('replyContact')}}">
                @csrf
                <input class="form-control" type="hidden" name="contact_id" id="contact_id">
                <textarea name="description" id="description" placeholder="Add your reply" required></textarea>
                <button class="btn btn-primary" type="submit">Save</button>

                @if($errors->has('description'))
                      <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert">Close</button><strong>{{ $errors->first('description') }}</strong> 
                      </div>
                @endif 
            </form>

              <a href="#" class="modall__close">&times;</a>
          </div>
      </div>
      <!-- modal end by sarmistha -->


  <!--   Core JS Files   -->
  <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
  <input type="hidden"  id="base-url" value="{{ url('/') }}">
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

  {{$dataTable->scripts()}}
  <script src="{{ asset('assets/js/admin/sweetalert.js') }}"></script>
  <script>
    
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});

function addReply(id){
    $('#contact_id').val(id);
    // $('#myModal').modal('show')
}
</script>
@if (count($errors) > 0)
    <script>
        // $( document ).ready(function() {
        //     $('#myModal').modal('show');
        // });
    </script>
@endif

@if(session()->has('message'))
    <script>
        $( document ).ready(function() {
            swal("Good job!", "You have added your reply successfully", "success");
        });
    </script>
@endif



</body>

</html>
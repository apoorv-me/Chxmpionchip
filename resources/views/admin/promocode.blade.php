<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Promo Code
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
                  <p> <a href="{{ route('addPromoCode')}}" class="btn btn-primary">Add Promo Code</a></p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        
                        <th>
                          Name
                        </th>
                        <th>
                          Chips
                        </th>
                        <th>
                          Valid Till
                        </th>
                        <th>
                          Expire on
                        </th>
                        <th>
                          Send
                        </th>
                        <th>
                          Delete
                        </th>

                      </thead>

                      <tbody>
                        @if($promoCodes)
            
                        @foreach($promoCodes as $promoCode)
                        <tr>
                          <td>
                            {{$promoCode->name}}
                          </td>
                          <td>
                            {{$promoCode->chips}}
                          </td>
                          <td>{{ \Carbon\Carbon::parse($promoCode->valid_till)->format('m/d/Y')}}
                          </td>
                          <td>{{ \Carbon\Carbon::parse($promoCode->valid_till)->diffForHumans()}}
                          </td>
                          <td>
                             <i class="fa fa-envelope" aria-hidden="true" id="<?php echo $promoCode->id; ?>" onclick="sendPromoCode('<?php echo $promoCode->name; ?>')"></i> 
                            <!-- <button class="btn btn-info" id="<?php echo $promoCode->id; ?>" onclick="sendPromoCode('<?php echo $promoCode->name; ?>')">Mail</button> -->
                          </td>
                          <td> 
                          <i class="fa fa-trash" aria-hidden="true" id="<?php echo $promoCode->id; ?>" onclick="deletePromoCode('<?php echo $promoCode->id; ?>')"></i>
                          <!-- <button class="btn btn-danger" id="<?php echo $promoCode->id; ?>" onclick="deletePromoCode('<?php echo $promoCode->id; ?>')">Delete</button> -->
                          </td>
                        </tr>
                        
                        @endforeach
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @include('admin.includes.footer')
      <!--   Core JS Files   -->
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      <input type="hidden" id="base-url" value="{{ url('/') }}">
      <input type="hidden" id="delete-promo-code" value="{{ route('promoCodeDeleteProcess') }}">
      <input type="hidden" id="send-promo-code" value="{{ route('promoCodeSendProcess') }}">
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

        function deletePromoCode(id) {

          var token = $('#token').val();
          var request = $('#delete-promo-code').val().trim();

          swal({
              title: "Are you sure?",
              text: "You want to Delete this Promo Code ! Once deleted you are unable to recover  this",
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
                      swal("Good job!", "Delete Successfully", "success").then(() => {
                        location.reload();
                      })
                    }
                  }
                });
              } else {
                swal("Promo Code is Safe");
              }
            });

        }

        // Send Mail for Promo Code

        function sendPromoCode(PromoCode){
          var token = $('#token').val();
          var request = $('#send-promo-code').val().trim();

          swal({
              title: "Are you sure?",
              text: "You want to Send Promo Code to the Users.",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                $.ajax({
                  url: request,
                  type: "POST",
                  data: 'promoCode=' + PromoCode + '&_token=' + token,
                  success: function(response) {
                    if (response.success === true) {
                      swal("Good job!",response.message, "success").then(() => {
                        location.reload();
                      })
                    }
                  }
                });
              } else {
                swal("This Promo Code is not sent to the users.");
              }
            });
        }
      </script>
</body>

</html>
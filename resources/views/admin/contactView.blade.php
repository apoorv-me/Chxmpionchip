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
  <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" />
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
            <div class="col-md-12 contact_view">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Contact Us</h4>
                  <a href="{{ route('contactUs') }}" class="btn btn-primary">Back</a>
                </div>
                <div class="card-body">
                <!-- comment start -->
              @if($contactMsg)
              <div id="comments" class="comments-area">

                <ol class="comment-list">
                  <li id="comment-1" class="comment even thread-even depth-1 parent">
                    <article id="div-comment-1" class="comment-body">
                      <footer class="comment-meta">
                        <div class="comment-author vcard">
                          <b class="fn"><a href="#" rel="external nofollow ugc" class="url">{{$contactMsg->name}}</a></b> <span class="says">says:</span> </div><!-- .comment-author -->

                        <div class="comment-metadata">
                          <a href="http://cave-theme.local/hello-world/#comment-1">
                            <time datetime="{{ $contactMsg->created_at->diffForHumans() }}">
                            {{ $contactMsg->created_at->diffForHumans() }} </time>
                          </a>
                        </div><!-- .comment-metadata -->

                      </footer><!-- .comment-meta -->

                      <div class="comment-content">
                        <p>{{$contactMsg->description}}.</p>
                      </div><!-- .comment-content -->
                    </article><!-- .comment-body -->
                    @if($replyMsgs)
                    <ol class="children">
                     @foreach($replyMsgs as $replyMsg)
                      <li id="comment-{{$replyMsg->id}}" class="comment odd alt depth-2">
                        <article id="div-comment-{{$replyMsg->id}}" class="comment-body">
                          <footer class="comment-meta">
                            <div class="comment-author vcard">
                              <b class="fn"><a href="#" rel="external nofollow ugc" class="url">Admin</a></b> <span class="says">says:</span> </div><!-- .comment-author -->

                            <div class="comment-metadata">
                              <a href="#">
                                <time datetime="{{ $replyMsg->created_at->diffForHumans() }}">
                                {{ $replyMsg->created_at->diffForHumans() }} </time>
                              </a>
                            </div><!-- .comment-metadata -->
                          </footer><!-- .comment-meta -->

                          <div class="comment-content">
                            <p>{{$replyMsg->description}}</p>
                          </div><!-- .comment-content -->
                        </article><!-- .comment-body -->
                      </li><!-- #comment-## -->
                      
                    
                     @endforeach
                     </ol><!-- .children -->
                    @endif
                    
                  </li><!-- #comment-## -->
                </ol><!-- .comment-list -->

                </div>
              @endif
                <!-- comment end -->

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @include('admin.includes.footer')
      
  <!--Modal -->
  


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
  <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.0') }}"></script>>
  <script type="text/javascript" src=" https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>

  <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
  <script src="{{ asset('assets/js/admin/sweetalert.js') }}"></script>
  
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  
</body>

</html>
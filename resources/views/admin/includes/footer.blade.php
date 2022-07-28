      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright float-right" id="date">
            , made with <i class="material-icons">favorite</i> by
            <a href="#">{{env('APP_NAME')}}</a>Admin.
          </div>
        </div>
      </footer>

      <script>
        const x = new Date().getFullYear();
        let date = document.getElementById('date');
        date.innerHTML = '&copy; ' + x + date.innerHTML;
      </script>
    </div>
  </div>
  <!-- <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
      <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
      </a>
      <ul class="dropdown-menu">
        <li class="header-title"> Sidebar Filters</li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger active-color">
            <div class="badge-colors ml-auto mr-auto">
              <span class="badge filter badge-purple active" data-color="purple"></span>
              <span class="badge filter badge-azure" data-color="azure"></span>
              <span class="badge filter badge-green" data-color="green"></span>
              <span class="badge filter badge-warning" data-color="orange"></span>
              <span class="badge filter badge-danger" data-color="danger"></span>
            </div>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="header-title">Images</li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
          
            <img src="{{ asset('assets/img/sidebar-1.jpg') }}" alt="">
          </a>
        </li>
        <li class="active">
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="{{ asset('assets/img/sidebar-2.jpg') }}" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="{{ asset('assets/img/sidebar-3.jpg') }}" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="{{ asset('assets/img/sidebar-4.jpg') }}" alt="">
          </a>
        </li>
      </ul>
    </div>
  </div> -->






  <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?ver=5.8.2' id='jquery-min-js'></script>

  <script type="text/javascript">

jQuery(document).ready(function(){

  jQuery(".sidebar .sidebar-wrapper ul li span.material-icons").click(function() {
    jQuery(this).toggleClass("on");
    jQuery(".sidebar .sidebar-wrapper ul li .submenu").slideToggle();
});


});

</script>



<div class="sidebar" data-color="purple" data-background-color="black" data-image="../assets/img/sidebar-2.jpg">
        <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
        -->
      <div class="logo"><a href="#" class="simple-text logo-normal">
        {{env('APP_NAME')}}
        </a></div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('user') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user') }}">
              <i class="material-icons">person</i>
              <p>Profile</p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('list') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('list') }}">
              <i class="material-icons">content_paste</i>
              <p>User List</p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('contentManagement') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('contentManagement') }}">
              <i class="material-icons">content_paste</i>
              <p>Content Management</p>
            </a>
          </li>
          
          <!-- <li class="nav-item {{ request()->routeIs('notification') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('notification') }}">
              <i class="material-icons">content_paste</i>
              <p>Notification Management</p>
            </a>
          </li> -->

          <li class="nav-item {{ request()->routeIs('promoCode') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('promoCode') }}">
              <i class="material-icons">content_paste</i>
              <p>Promo Code Management</p>
            </a>
          </li>

          <li class="nav-item {{ request()->routeIs('walletManagement') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('walletManagement') }}">
              <i class="material-icons">content_paste</i>
              <p>User Wallet</p>
            </a>
          </li>

          <li class="nav-item {{ request()->routeIs('adminWalletSC') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('adminWalletSC') }}">
              <i class="material-icons">content_paste</i>
              <p>Admin Wallet & SC</p>
            </a>
          </li>

          <li class="nav-item {{ request()->routeIs('contactUs') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('contactUs') }}">
              <i class="material-icons">person</i>
              <p>Contact Us</p>
            </a>
          </li>

        </ul>
      </div>
    </div>




  
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{ asset('/img/logo_kampi.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">SI-KAMPI <b>MONEV</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ Auth::user()->profile_photo_path ? asset(Auth::user()->profile_photo_path) : asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @role('employee')
          <li class="nav-item">
            <a href="{{route('dashboard_employee')}}" class="nav-link {{(Route::currentRouteName() == "dashboard_employee")?"active":""}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          @role('employee|hr')
          <li class="nav-item">
            <a href="{{route('attendance')}}" class="nav-link {{(Route::currentRouteName() == "attendance")?"active":""}}" >
              <i class="nav-icon fas fa-pen-square"></i>
              <p>
                Attendance
              </p>
            </a>
          </li>
          @endrole
          <li class="nav-item {{ request()->routeIs('item') || request()->routeIs('item.*') ||  request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') ? 'menu-open' : '' }}">
            <a href="javascript:void(0)" class="nav-link {{ request()->routeIs('item') || request()->routeIs('item.*') || request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Master Data
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('uom')}}" class="nav-link {{ request()->routeIs('uom*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>UOM</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('vendor')}}" class="nav-link {{ request()->routeIs('vendor*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vendor</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('item')}}" class="nav-link {{ request()->routeIs('item') || request()->routeIs('item.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Bahan Pokok</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('kategori')}}" class="nav-link {{ request()->routeIs('kategori*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Kategori</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('penerima_manfaat.index')}}" class="nav-link {{ request()->routeIs('penerima_manfaat*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Penerima Manfaat</p>
                </a>
              </li>
            </ul>
          </li> 
          <li class="nav-item {{ request()->routeIs('item_menu') || request()->routeIs('item_menu.*') || request()->routeIs('item_vendor*') || request()->routeIs('purchase_order*') || request()->routeIs('cash_in*') || request()->routeIs('cash_out*') || request()->routeIs('cashflow*') || request()->routeIs('distribusi_menu*') ? 'menu-open' : '' }}">
            <a href="javascript:void(0)" class="nav-link {{ request()->routeIs('item_menu') || request()->routeIs('item_menu.*') || request()->routeIs('item_vendor*') || request()->routeIs('purchase_order*') || request()->routeIs('cash_in*') || request()->routeIs('cash_out*') || request()->routeIs('cashflow*') || request()->routeIs('distribusi_menu*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>
                SPPG
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('item_vendor')}}" class="nav-link {{ request()->routeIs('item_vendor*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Harga Vendor Item</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('item_menu')}}" class="nav-link {{ request()->routeIs('item_menu') || request()->routeIs('item_menu.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Menu Maker</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('purchase_order')}}" class="nav-link {{ request()->routeIs('purchase_order*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Purchase Order</p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="{{route('distribusi_menu.index')}}" class="nav-link {{ request()->routeIs('distribusi_menu*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Distribusi Menu</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item {{ request()->routeIs('item') || request()->routeIs('item.*') ||  request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') ? 'menu-open' : '' }}">
            <a href="javascript:void(0)" class="nav-link {{ request()->routeIs('item') || request()->routeIs('item.*') || request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Cashflow
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('cash_in')}}" class="nav-link {{ request()->routeIs('cash_in*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cash In</p>
                </a>
              </li>
                  <li class="nav-item">
                <a href="{{route('cash_out')}}" class="nav-link {{ request()->routeIs('cash_out*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cash Out</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('cashflow')}}" class="nav-link {{ request()->routeIs('cashflow*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Detail Cashflow</p>
                </a>
              </li>
            </ul>
          </li>
          @endrole
          @role('hr')
          <li class="nav-item">
            <a href="{{route('dashboard')}}" class="nav-link {{(Route::currentRouteName() == "dashboard")?"active":""}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('user')}}" class="nav-link {{(Route::currentRouteName() == "user")?"active":""}}" >
              <i class="nav-icon fas fa-user"></i>
              <p>
                Employee
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('working')}}" class="nav-link {{(Route::currentRouteName() == "working")?"active":""}}" >
              <i class="nav-icon fas fa-clock"></i>
              <p>
                Working Hour
              </p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('sppg*') || request()->routeIs('item') || request()->routeIs('item.*') || request()->routeIs('item_menu') || request()->routeIs('item_menu.*') || request()->routeIs('item_vendor*') || request()->routeIs('purchase_order*') || request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') || request()->routeIs('cash_in*') || request()->routeIs('cash_out*') || request()->routeIs('cashflow*') || request()->routeIs('distribusi_menu*') ? 'menu-open' : '' }}">
            <a href="javascript:void(0)" class="nav-link {{ request()->routeIs('sppg*') || request()->routeIs('item') || request()->routeIs('item.*') || request()->routeIs('item_menu') || request()->routeIs('item_menu.*') || request()->routeIs('item_vendor*') || request()->routeIs('purchase_order*') || request()->routeIs('kategori*') || request()->routeIs('uom*') || request()->routeIs('vendor*') || request()->routeIs('penerima_manfaat*') || request()->routeIs('cash_in*') || request()->routeIs('cash_out*') || request()->routeIs('cashflow*') || request()->routeIs('distribusi_menu*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>
                SPPG
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('sppg')}}" class="nav-link {{ request()->routeIs('sppg*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data SPPG</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('item')}}" class="nav-link {{ request()->routeIs('item') || request()->routeIs('item.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Items</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('item_menu')}}" class="nav-link {{ request()->routeIs('item_menu') || request()->routeIs('item_menu.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Menu</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('item_vendor')}}" class="nav-link {{ request()->routeIs('item_vendor*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Harga Vendor Item</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('purchase_order')}}" class="nav-link {{ request()->routeIs('purchase_order*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Purchase Order</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('kategori')}}" class="nav-link {{ request()->routeIs('kategori*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Kategori</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('uom')}}" class="nav-link {{ request()->routeIs('uom*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>UOM</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('vendor')}}" class="nav-link {{ request()->routeIs('vendor*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vendor</p>
                </a>
              </li>              <li class="nav-item">
                <a href="{{route('cash_in')}}" class="nav-link {{ request()->routeIs('cash_in*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cash In</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('cash_out')}}" class="nav-link {{ request()->routeIs('cash_out*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cash Out</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('cashflow')}}" class="nav-link {{ request()->routeIs('cashflow*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cashflow</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('penerima_manfaat.index')}}" class="nav-link {{ request()->routeIs('penerima_manfaat*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Penerima Manfaat</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('distribusi_menu.index')}}" class="nav-link {{ request()->routeIs('distribusi_menu*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Distribusi Menu</p>
                </a>
              </li>
            </ul>
          </li>
          @endrole
          @role('akuntan')
          <li class="nav-item">
            <a href="{{route('purchase_order')}}" class="nav-link {{ request()->routeIs('purchase_order*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>
                Purchase Order
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('laporan_bahan_baku.index') }}" class="nav-link {{ request()->routeIs('laporan_bahan_baku.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                Laporan Bahan Baku Maker
              </p>
            </a>
          </li>
          @endrole
          
          @role('hr')
          <li class="nav-item">
            <a href="{{route('attendance_report')}}" class="nav-link {{(Route::currentRouteName() == "attendance_report")?"active":""}}" >
              <i class="nav-icon fas fa-chart-area"></i>
              <p>
                Attendance Report
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('attendance_log')}}" class="nav-link {{(Route::currentRouteName() == "attendance_log")?"active":""}}" >
              <i class="nav-icon fas fa-sticky-note"></i>
              <p>
                Attendance Log
              </p>
            </a>
          </li>
          @endrole
          <li class="nav-item">
            <a href="{{ route('profile.edit') }}" class="nav-link {{ (Route::currentRouteName() == 'profile.edit') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>
                Profile
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link logout" >
              <i class="nav-icon fas fa-key"></i>
              <p>
                Logout
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
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
          @endrole
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

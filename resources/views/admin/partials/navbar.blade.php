<nav class="app-header navbar navbar-expand bg-body shadow-sm">
  <div class="container-fluid">

    <!-- LEFT -->
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list fs-5"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-block">
        <a href="#" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- RIGHT -->
    <ul class="navbar-nav ms-auto align-items-center">
      
      <!-- FULLSCREEN -->
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="fullscreen" href="#">
          <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
          <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
        </a>
      </li>

      <!-- USER -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
          <img src="{{ asset('adminlte/dist/assets/img/user2-160x160.jpg') }}"
               class="user-image rounded-circle shadow" />
          <span class="d-none d-md-inline">
            {{ auth()->user()->username
 ?? 'User' }}
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

          <li class="user-header text-bg-primary">
            <img src="{{ asset('adminlte/dist/assets/img/user2-160x160.jpg') }}"
                 class="rounded-circle shadow" />
            <p>
              {{ auth()->user()->username
 ?? 'User' }}
              <small>Member since {{ now()->year }}</small>
            </p>
          </li>

          <li class="user-body">
            <div class="row text-center">
              <div class="col-4"><a href="#">Followers</a></div>
              <div class="col-4"><a href="#">Sales</a></div>
              <div class="col-4"><a href="#">Friends</a></div>
            </div>
          </li>

          <li class="user-footer d-flex justify-content-between px-3">
            <a href="#" class="btn btn-default btn-flat">Profile</a>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button class="btn btn-default btn-flat text-danger">
                Logout
              </button>
            </form>
          </li>

        </ul>
      </li>

    </ul>
  </div>
</nav>

<aside class="app-sidebar sidebar-yayasan shadow">

    {{-- BRAND --}}
    <div class="sidebar-brand d-flex align-items-center px-4 py-4">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center">
            <div class="bg-primary rounded-3 p-2 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="bi bi-fingerprint text-white"></i>
            </div>
            <span class="brand-text fw-bold text-dark" style="letter-spacing: 0.5px; font-size: 0.9rem;">SMK 1 TAMANAN</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" role="menu">

                {{-- DASHBOARD --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- MASTER USER --}}
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Master User</p>
                    </a>
                </li>

                {{-- PROFILE --}}
                <li class="nav-item">
                    <a href="{{ route('admin.profiles.index') }}"
                       class="nav-link {{ request()->routeIs('admin.profiles.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Profile</p>
                    </a>
                </li>

                {{-- FACE DATA --}}
                <li class="nav-item">
                    <a href="{{ route('admin.face-data.index') }}"
                       class="nav-link {{ request()->routeIs('admin.face-data.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-camera"></i>
                        <p>Face User</p>
                    </a>
                </li>
            
                <li class="nav-item">
    <a href="{{ route('admin.classes.index') }}"
       class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-building"></i>
        <p>Kelas</p>
    </a>
</li>

  <li class="nav-item">
    <a href="{{ route('admin.subjects.index') }}"
       class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-book"></i>
        <p>Mapel</p>
    </a>
</li>



{{-- JADWAL --}}
<li class="nav-item">
    <a href="{{ route('admin.schedules.index') }}"
       class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-calendar-week"></i>
        <p>Jadwal</p>
    </a>
</li>



                {{-- ATTENDANCE --}}
                <li class="nav-item">
                    <a href="{{ route('admin.attendance.index') }}"
                       class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>Attendance</p>
                    </a>
                </li>

                {{-- SETTINGS --}}
                <li class="nav-item">
                    <a href="{{ route('admin.school.index') }}"
                       class="nav-link {{ request()->routeIs('admin.school.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Pengaturan</p>
                    </a>
                </li>

                {{-- LOGOUT --}}
                <li class="nav-item mt-auto">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                                class="nav-link w-100 text-start btn btn-link text-danger">
                            <i class="nav-icon bi bi-box-arrow-right"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>

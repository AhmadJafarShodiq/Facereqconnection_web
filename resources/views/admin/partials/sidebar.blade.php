<aside class="app-sidebar sidebar-yayasan shadow">

    {{-- BRAND --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <span class="brand-text fw-semibold">ADMIN SMK 1 Tamanan</span>
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
                        <p>Face Data</p>
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

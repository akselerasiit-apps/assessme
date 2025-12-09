<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}">
                <img src="https://via.placeholder.com/110x32/206bc4/ffffff?text=COBIT" height="32" alt="COBIT Assessment" class="navbar-brand-image">
            </a>
        </h1>
        
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=206bc4&color=fff)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('profile') }}" class="dropdown-item">Profile</a>
                    <a href="{{ route('settings') }}" class="dropdown-item">Settings</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>
                
                @canany(['view assessments', 'create assessments'])
                <li class="nav-item dropdown {{ request()->is('assessments*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-assessments" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-clipboard-check"></i>
                        </span>
                        <span class="nav-link-title">Assessments</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('assessments*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('view assessments')
                                <a class="dropdown-item {{ request()->routeIs('assessments.index') ? 'active' : '' }}" href="{{ route('assessments.index') }}">
                                    All Assessments
                                </a>
                                @endcan
                                @can('create assessments')
                                <a class="dropdown-item {{ request()->routeIs('assessments.create') ? 'active' : '' }}" href="{{ route('assessments.create') }}">
                                    Create Assessment
                                </a>
                                @endcan
                                <a class="dropdown-item" href="{{ route('assessments.my') }}">
                                    My Assessments
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endcanany
                
                @can('view reports')
                <li class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-chart-bar"></i>
                        </span>
                        <span class="nav-link-title">Reports</span>
                    </a>
                </li>
                @endcan
                
                @hasanyrole('Super Admin|Admin')
                <li class="nav-item dropdown {{ request()->is('admin*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-admin" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="nav-link-title">Administration</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('admin*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item" href="{{ route('admin.users') }}">
                                    <i class="ti ti-users me-2"></i>Users
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.roles') }}">
                                    <i class="ti ti-shield-lock me-2"></i>Roles & Permissions
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.audit-logs') }}">
                                    <i class="ti ti-file-search me-2"></i>Audit Logs
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                    <i class="ti ti-adjustments me-2"></i>System Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endhasanyrole
            </ul>
        </div>
    </div>
</aside>

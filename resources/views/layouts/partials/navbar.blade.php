<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <!-- Brand/Logo -->
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span class="navbar-brand-text" style="font-size: 1.25rem; font-weight: 600; color: #206bc4;">COBIT Assessment</span>
            </a>
        </h1>
        
        <!-- Mobile toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Horizontal Menu -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <!-- Dashboard -->
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Assessments -->
                    @if(auth()->user()->hasAnyRole(['Viewer', 'Asesi', 'Manager', 'Assessor', 'Admin', 'Super Admin']))
                    <li class="nav-item dropdown {{ request()->is('assessments*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-assessments" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-clipboard-check"></i>
                            </span>
                            <span class="nav-link-title">Assessments</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item {{ request()->routeIs('assessments.index') ? 'active' : '' }}" href="{{ route('assessments.index') }}">
                                <i class="ti ti-list me-2"></i>All Assessments
                            </a>
                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Assessor']))
                            <a class="dropdown-item {{ request()->routeIs('assessments.create') ? 'active' : '' }}" href="{{ route('assessments.create') }}">
                                <i class="ti ti-plus me-2"></i>Create Assessment
                            </a>
                            @endif
                            @if(!auth()->user()->hasAnyRole(['Viewer', 'Asesi']))
                            <a class="dropdown-item {{ request()->routeIs('assessments.my') ? 'active' : '' }}" href="{{ route('assessments.my') }}">
                                <i class="ti ti-user-check me-2"></i>My Assessments
                            </a>
                            @endif
                        </div>
                    </li>
                    @endif
                    
                    <!-- Reports -->
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
                    
                    <!-- Master Data -->
                    @role('Super Admin')
                    <li class="nav-item dropdown {{ request()->is('master-data*') || request()->is('admin/users*') || request()->is('admin/roles*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-master-data" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-database"></i>
                            </span>
                            <span class="nav-link-title">Master Data</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item {{ request()->routeIs('master-data.companies*') ? 'active' : '' }}" href="{{ route('master-data.companies.index') }}">
                                <i class="ti ti-building me-2"></i>Companies
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('master-data.design-factors*') ? 'active' : '' }}" href="{{ route('master-data.design-factors.index') }}">
                                <i class="ti ti-puzzle me-2"></i>Design Factors
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('master-data.gamo-objectives*') ? 'active' : '' }}" href="{{ route('master-data.gamo-objectives.index') }}">
                                <i class="ti ti-target me-2"></i>GAMO Objectives
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="ti ti-users me-2"></i>Users
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.roles') ? 'active' : '' }}" href="{{ route('admin.roles') }}">
                                <i class="ti ti-shield-lock me-2"></i>Roles & Permissions
                            </a>
                        </div>
                    </li>
                    @endrole
                    
                    <!-- Audit Logs -->
                    @role('Super Admin')
                    <li class="nav-item {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.audit-logs') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-file-search"></i>
                            </span>
                            <span class="nav-link-title">Audit Logs</span>
                        </a>
                    </li>
                    @endrole
                    
                    <!-- System Settings -->
                    @role('Super Admin')
                    <li class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.settings') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-adjustments"></i>
                            </span>
                            <span class="nav-link-title">System Settings</span>
                        </a>
                    </li>
                    @endrole
                </ul>
            </div>
        </div>
        
        <!-- Right side items -->
        <div class="navbar-nav flex-row order-md-last">
            <!-- Notifications -->
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                    <i class="ti ti-bell"></i>
                    <span class="badge bg-red"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notifications</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col text-truncate">
                                        <div class="text-reset d-block">No new notifications</div>
                                        <div class="d-block text-muted text-truncate mt-n1">
                                            All caught up!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="nav-item dropdown me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-label="Show notifications">
                    <i class="ti ti-bell icon" style="font-size: 1.5rem;"></i>
                    <span class="badge bg-red notification-badge" id="notification-count" style="display: none;"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow dropdown-menu-card" style="width: 400px; max-height: 500px; overflow-y: auto;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Notifications</h3>
                            <a href="#" class="btn btn-sm btn-link" id="mark-all-read">Mark all as read</a>
                        </div>
                        <div class="list-group list-group-flush" id="notification-list">
                            <div class="list-group-item text-center text-muted py-5">
                                <i class="ti ti-bell-off icon mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">No notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User menu -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=206bc4&color=fff)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('profile.index') }}" class="dropdown-item">
                        <i class="ti ti-user me-2"></i>Profile
                    </a>
                    <a href="{{ route('profile.settings') }}" class="dropdown-item">
                        <i class="ti ti-settings me-2"></i>Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ti ti-logout me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

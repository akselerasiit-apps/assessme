<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
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
                    <a href="{{ route('profile') }}" class="dropdown-item">
                        <i class="ti ti-user me-2"></i>Profile
                    </a>
                    <a href="{{ route('settings') }}" class="dropdown-item">
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

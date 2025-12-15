@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Admin</div>
                <h2 class="page-title">System Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-cards">
            <!-- Application Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Application Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="#">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Application Name</label>
                                <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? config('app.name') }}" readonly>
                                <small class="form-hint">Defined in .env file (APP_NAME)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Application URL</label>
                                <input type="url" name="app_url" class="form-control" value="{{ $settings['app_url'] ?? config('app.url') }}" readonly>
                                <small class="form-hint">Defined in .env file (APP_URL)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Environment</label>
                                <input type="text" class="form-control" value="{{ config('app.env') }}" readonly>
                                <small class="form-hint">Defined in .env file (APP_ENV)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Debug Mode</label>
                                <div>
                                    <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    <small class="form-hint d-block">Defined in .env file (APP_DEBUG)</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mail Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Mail Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="#">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Mail Driver</label>
                                <input type="text" class="form-control" value="{{ config('mail.default') }}" readonly>
                                <small class="form-hint">Defined in .env file (MAIL_MAILER)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mail Host</label>
                                <input type="text" class="form-control" value="{{ config('mail.mailers.smtp.host') }}" readonly>
                                <small class="form-hint">Defined in .env file (MAIL_HOST)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mail Port</label>
                                <input type="text" class="form-control" value="{{ config('mail.mailers.smtp.port') }}" readonly>
                                <small class="form-hint">Defined in .env file (MAIL_PORT)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mail From Address</label>
                                <input type="email" class="form-control" value="{{ $settings['mail_from'] ?? config('mail.from.address') }}" readonly>
                                <small class="form-hint">Defined in .env file (MAIL_FROM_ADDRESS)</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Database Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Database Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Connection</div>
                                <div class="datagrid-content">{{ config('database.default') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Driver</div>
                                <div class="datagrid-content">{{ config('database.connections.' . config('database.default') . '.driver') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Host</div>
                                <div class="datagrid-content">{{ config('database.connections.' . config('database.default') . '.host') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Port</div>
                                <div class="datagrid-content">{{ config('database.connections.' . config('database.default') . '.port') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Database</div>
                                <div class="datagrid-content">{{ config('database.connections.' . config('database.default') . '.database') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Laravel Version</div>
                                <div class="datagrid-content">{{ app()->version() }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">PHP Version</div>
                                <div class="datagrid-content">{{ PHP_VERSION }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Timezone</div>
                                <div class="datagrid-content">{{ config('app.timezone') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Locale</div>
                                <div class="datagrid-content">{{ config('app.locale') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache & Storage -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cache & Storage Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-blue text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-10" /><path d="M3 7l9 6l9 -6" /></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    Application Cache
                                                </div>
                                                <div class="text-muted">
                                                    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="clearCache('config')">
                                                        Clear Config Cache
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-green text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    Route Cache
                                                </div>
                                                <div class="text-muted">
                                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="clearCache('route')">
                                                        Clear Route Cache
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-yellow text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    View Cache
                                                </div>
                                                <div class="text-muted">
                                                    <button type="button" class="btn btn-sm btn-warning mt-2" onclick="clearCache('view')">
                                                        Clear View Cache
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-red text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    All Cache
                                                </div>
                                                <div class="text-muted">
                                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearCache('all')">
                                                        Clear All Cache
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <h4 class="alert-title">Note</h4>
            <div class="text-muted">Most settings are read-only and defined in your .env file. To change these settings, please edit the .env file directly and restart the application.</div>
        </div>
    </div>
</div>

<script>
function clearCache(type) {
    if (!confirm(`Are you sure you want to clear ${type} cache?`)) {
        return;
    }
    
    alert(`This feature will be implemented to clear ${type} cache via artisan commands.`);
    // You can implement AJAX calls to routes that run artisan commands
    // Example: axios.post('/admin/settings/clear-cache', { type: type })
}
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Admin</div>
                <h2 class="page-title">Audit Logs</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <form method="GET" class="row g-2 w-100">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search action or module..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="module" class="form-select">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Record ID</th>
                            <th>IP Address</th>
                            <th class="w-1">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <div>{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        <span class="avatar avatar-sm me-2">{{ strtoupper(substr($log->user?->name ?? 'S', 0, 2)) }}</span>
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">{{ $log->user?->name ?? 'System' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ $log->module }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $log->action === 'created' ? 'success' : 
                                        ($log->action === 'updated' ? 'info' : 
                                        ($log->action === 'deleted' ? 'danger' : 'secondary')) 
                                    }}-lt">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>{{ $log->entity_id ?? '-' }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-ghost-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logDetailModal"
                                            onclick="showLogDetail({{ json_encode($log) }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No audit logs found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal modal-blur fade" id="logDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Date & Time</div>
                        <div class="datagrid-content" id="detail_datetime"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">User</div>
                        <div class="datagrid-content" id="detail_user"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Module</div>
                        <div class="datagrid-content" id="detail_module"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Action</div>
                        <div class="datagrid-content" id="detail_action"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Record ID</div>
                        <div class="datagrid-content" id="detail_record_id"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">IP Address</div>
                        <div class="datagrid-content" id="detail_ip"></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">User Agent</div>
                        <div class="datagrid-content" id="detail_user_agent"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4>Changes</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-muted">Old Values</h5>
                            <pre id="detail_old_values" class="bg-secondary-lt p-3 rounded" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">New Values</h5>
                            <pre id="detail_new_values" class="bg-info-lt p-3 rounded" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetail(log) {
    document.getElementById('detail_datetime').textContent = new Date(log.created_at).toLocaleString();
    document.getElementById('detail_user').textContent = log.user ? log.user.name : 'System';
    document.getElementById('detail_module').textContent = log.module;
    document.getElementById('detail_action').textContent = log.action.charAt(0).toUpperCase() + log.action.slice(1);
    document.getElementById('detail_record_id').textContent = log.record_id || '-';
    document.getElementById('detail_ip').textContent = log.ip_address || '-';
    document.getElementById('detail_user_agent').textContent = log.user_agent || '-';
    
    // Parse and display old/new values
    try {
        const oldValues = log.old_values ? JSON.parse(log.old_values) : null;
        const newValues = log.new_values ? JSON.parse(log.new_values) : null;
        
        document.getElementById('detail_old_values').textContent = oldValues ? JSON.stringify(oldValues, null, 2) : 'No data';
        document.getElementById('detail_new_values').textContent = newValues ? JSON.stringify(newValues, null, 2) : 'No data';
    } catch (e) {
        document.getElementById('detail_old_values').textContent = log.old_values || 'No data';
        document.getElementById('detail_new_values').textContent = log.new_values || 'No data';
    }
}
</script>
@endsection

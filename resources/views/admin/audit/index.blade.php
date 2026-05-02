@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Audit Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Audit Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Audit Trail</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterPanel">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                    <a href="{{ route('admin.audit.export', request()->query()) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="collapse {{ request()->hasAny(['user_id', 'action_type', 'model_type', 'start_date', 'end_date']) ? 'show' : '' }}" id="filterPanel">
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.audit.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Action Type</label>
                                    <select name="action_type" class="form-control">
                                        <option value="">All Actions</option>
                                        @foreach($actionTypes as $type)
                                            <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $type)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Model Type</label>
                                    <select name="model_type" class="form-control">
                                        <option value="">All Models</option>
                                        @foreach($modelTypes as $type)
                                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="input-group">
                                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Timestamp</th>
                                <th style="width: 120px;">User</th>
                                <th style="width: 120px;">Action Type</th>
                                <th>Description</th>
                                <th style="width: 100px;">Model</th>
                                <th style="width: 100px;">IP Address</th>
                                <th style="width: 80px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="badge badge-info">{{ $log->user->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($log->action_type) {
                                                'created' => 'success',
                                                'updated' => 'warning',
                                                'deleted' => 'danger',
                                                'login' => 'info',
                                                'logout' => 'secondary',
                                                'workflow_transition' => 'primary',
                                                'payment' => 'success',
                                                'document_upload' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ ucwords(str_replace('_', ' ', $log->action_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $log->description }}</small>
                                    </td>
                                    <td>
                                        @if($log->model_type)
                                            <small class="text-muted">
                                                {{ class_basename($log->model_type) }}
                                                @if($log->model_id)
                                                    #{{ $log->model_id }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        @if($log->old_values || $log->new_values)
                                            <button type="button" class="btn btn-xs btn-outline-primary" 
                                                    data-toggle="modal" 
                                                    data-target="#detailModal{{ $log->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Audit Log Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="row">
                                                                <dt class="col-sm-3">Timestamp:</dt>
                                                                <dd class="col-sm-9">{{ $log->created_at->format('Y-m-d H:i:s') }}</dd>

                                                                <dt class="col-sm-3">User:</dt>
                                                                <dd class="col-sm-9">{{ $log->user ? $log->user->name : 'System' }}</dd>

                                                                <dt class="col-sm-3">Action:</dt>
                                                                <dd class="col-sm-9">{{ ucwords(str_replace('_', ' ', $log->action_type)) }}</dd>

                                                                <dt class="col-sm-3">Description:</dt>
                                                                <dd class="col-sm-9">{{ $log->description }}</dd>

                                                                @if($log->old_values)
                                                                    <dt class="col-sm-3">Old Values:</dt>
                                                                    <dd class="col-sm-9">
                                                                        <pre class="bg-light p-2">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                                    </dd>
                                                                @endif

                                                                @if($log->new_values)
                                                                    <dt class="col-sm-3">New Values:</dt>
                                                                    <dd class="col-sm-9">
                                                                        <pre class="bg-light p-2">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                                    </dd>
                                                                @endif

                                                                <dt class="col-sm-3">IP Address:</dt>
                                                                <dd class="col-sm-9">{{ $log->ip_address }}</dd>

                                                                <dt class="col-sm-3">User Agent:</dt>
                                                                <dd class="col-sm-9"><small>{{ $log->user_agent }}</small></dd>
                                                            </dl>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No audit logs found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> About Audit Logs</h5>
            <p class="mb-0">
                Audit logs are immutable records of all significant actions in the system. 
                They cannot be modified or deleted to ensure data integrity and compliance. 
                Logs are retained for 5 years as per policy.
            </p>
        </div>
    </div>
</section>
@endsection

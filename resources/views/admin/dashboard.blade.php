@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-xl col-md-6">
        <div class="card h-100 p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size:0.75rem;">Total Users</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 2 -->
    <div class="col-xl col-md-6">
        <div class="card h-100 p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="bi bi-chat-left-text-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size:0.75rem;">Total Chats</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_conversations']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 3 -->
    <div class="col-xl col-md-6">
        <div class="card h-100 p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="bi bi-chat-dots-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size:0.75rem;">Total Messages</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_messages']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 4 -->
    <div class="col-xl col-md-6">
        <div class="card h-100 p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-cpu-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size:0.75rem;">Active LLM Keys</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['active_keys']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 5 -->
    <div class="col-xl col-md-6">
        <div class="card h-100 p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-danger-subtle text-danger rounded-3">
                    <i class="bi bi-lightning-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size:0.75rem;">Est. Tokens Used</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['estimated_tokens']) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Quick Actions Panel -->
    <div class="col-lg-12">
        <div class="card border-0 p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick SaaS Actions</h5>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('admin.providers.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Config AI Provider
                </a>
                <a href="{{ route('admin.models.create') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Create Model Config
                </a>
                <a href="{{ route('admin.routing.index') }}" class="btn btn-outline-info d-flex align-items-center gap-2">
                    <i class="bi bi-shuffle"></i> Model Routing Rules
                </a>
                <a href="{{ route('admin.kb.create') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-upload"></i> Upload Knowledge Base
                </a>
                <a href="{{ route('admin.settings') }}?tab=general" class="btn btn-outline-dark d-flex align-items-center gap-2">
                    <i class="bi bi-sliders"></i> System Configurations
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Security Posture & Safeguards Panel -->
    <div class="col-lg-12">
        <div class="card border-0 p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-danger me-2"></i>Enterprise Security Posture & Safeguards</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">2FA REQUIREMENT</div>
                            <div class="fw-bold mt-1">{{ $securityHealth['two_factor_enabled'] ? 'Enabled' : 'Optional / Off' }}</div>
                        </div>
                        <span class="badge {{ $securityHealth['two_factor_enabled'] ? 'bg-success' : 'bg-secondary' }}">
                            {{ $securityHealth['two_factor_enabled'] ? 'SECURE' : 'DEFAULT' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">ADMIN IP ALLOWLIST</div>
                            <div class="fw-bold mt-1">{{ $securityHealth['ip_allowlist_enabled'] ? 'Active Allowlist' : 'Any IP Allowed' }}</div>
                        </div>
                        <span class="badge {{ $securityHealth['ip_allowlist_enabled'] ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $securityHealth['ip_allowlist_enabled'] ? 'RESTRICTED' : 'OPEN' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">SESSION TIMEOUT</div>
                            <div class="fw-bold mt-1">{{ $securityHealth['session_timeout'] }} mins</div>
                        </div>
                        <span class="badge bg-primary">ENFORCED</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">MAINTENANCE MODE</div>
                            <div class="fw-bold mt-1">{{ $securityHealth['maintenance_mode'] ? 'Active (Maintenance)' : 'Normal Operations' }}</div>
                        </div>
                        <span class="badge {{ $securityHealth['maintenance_mode'] ? 'bg-danger' : 'bg-success' }}">
                            {{ $securityHealth['maintenance_mode'] ? 'MAINTENANCE' : 'ONLINE' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Recent Users List -->
    <div class="col-lg-6">
        <div class="card border-0 h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Recent SaaS Registrations</h5>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border:0;">
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td style="border:0; padding:12px 0;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <div class="text-muted" style="font-size:0.8rem;">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end" style="border:0; padding:12px 0;">
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">No recent registrations.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active User Activity -->
    <div class="col-lg-6">
        <div class="card border-0 h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-activity text-success me-2"></i>Top Activity Logs</h5>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-decoration-none">Audit Trail</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border:0;">
                    <tbody>
                        @forelse($userStats as $user)
                            <tr>
                                <td style="border:0; padding:12px 0;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                            <i class="bi bi-chat-text-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <div class="text-muted" style="font-size:0.8rem;">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end" style="border:0; padding:12px 0;">
                                    <span class="fw-bold text-success" style="font-size:0.9rem;">
                                        {{ $user->conversations_count }} chats
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">No user activity recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-12">
        <div class="card border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Recent Security & Audit Trail</h5>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-link text-decoration-none">View All Diffs</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border:0;">
                    <thead class="text-muted text-uppercase" style="font-size:0.75rem;">
                        <tr>
                            <th style="border:0;">Timestamp</th>
                            <th style="border:0;">Admin / User</th>
                            <th style="border:0;">Action</th>
                            <th style="border:0;">Target</th>
                            <th style="border:0;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td style="border:0; padding:12px 0;" class="text-muted">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td style="border:0; padding:12px 0;">
                                    <div class="fw-semibold">{{ $log->user ? $log->user->name : 'System / Guest' }}</div>
                                    <div class="text-muted" style="font-size:0.8rem;">{{ $log->user ? $log->user->role : '-' }}</div>
                                </td>
                                <td style="border:0; padding:12px 0;">
                                    <span class="badge bg-dark-subtle text-dark fw-semibold">{{ $log->action }}</span>
                                </td>
                                <td style="border:0; padding:12px 0;">
                                    <span class="text-muted">{{ $log->model_type ? class_basename($log->model_type) . " #{$log->model_id}" : '-' }}</span>
                                </td>
                                <td style="border:0; padding:12px 0;">
                                    <code>{{ $log->ip_address ?: '-' }}</code>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No security audit logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

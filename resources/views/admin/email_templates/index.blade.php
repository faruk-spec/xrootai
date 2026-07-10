@extends('layouts.admin')

@section('title', 'Transactional Email Templates')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.email-config.index') }}">Email Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Email Templates</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 p-4 shadow-sm mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-file-earmark-code-fill fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">Email Template Management</h4>
                        <p class="text-muted small mb-0">Customize database-driven HTML email templates, subject lines, and dynamic placeholder substitutions.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.email-templates.index') }}" class="d-flex gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search templates..." value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                            @endif
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.email-templates.seed') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm fw-semibold d-flex align-items-center gap-1" title="Seed / Restore Default System Templates">
                            <i class="bi bi-arrow-clockwise"></i> Restore Default Templates
                        </button>
                    </form>
                    <a href="{{ route('admin.email-config.index') }}" class="btn btn-outline-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-gear-fill"></i> SMTP Settings
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="width: 32%;">Template Name & Description</th>
                            <th class="py-3" style="width: 25%;">Subject Line</th>
                            <th class="py-3" style="width: 20%;">Supported Placeholders</th>
                            <th class="py-3 text-center" style="width: 10%;">Status</th>
                            <th class="pe-4 py-3 text-end" style="width: 13%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($templates as $template)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $template->name }}</div>
                                    <div class="text-muted small mb-1">{{ $template->description }}</div>
                                    <code class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">{{ $template->slug }}</code>
                                </td>
                                <td class="py-3">
                                    <div class="fw-medium text-dark">{{ $template->subject }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(is_array($template->available_variables))
                                            @foreach(array_keys($template->available_variables) as $varKey)
                                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">@php echo '{{ ' . e($varKey) . ' }}'; @endphp</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Standard variables</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <form method="POST" action="{{ route('admin.email-templates.toggle', $template) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $template->is_active ? 'btn-success-subtle text-success border-success border-opacity-25' : 'btn-secondary-subtle text-secondary border' }} rounded-pill px-3 py-1 fw-semibold" title="Click to toggle status">
                                            <i class="bi {{ $template->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1"></i>
                                            {{ $template->is_active ? 'Active' : 'Disabled' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.email-templates.preview', $template) }}" target="_blank" class="btn btn-outline-secondary" title="Live Preview">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#testModal-{{ $template->id }}" title="Send Test Email">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                        <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-outline-primary" title="Edit Template">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>

                                    <!-- Test Send Modal -->
                                    <div class="modal fade text-start" id="testModal-{{ $template->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 p-3">
                                                <div class="modal-header border-bottom-0">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-send-fill text-info me-2"></i>Send Test Email</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.email-templates.test', $template) }}">
                                                    @csrf
                                                    <div class="modal-body py-2">
                                                        <p class="text-muted small">You are about to send a test rendering of <strong>{{ $template->name }}</strong> using dummy sample variables and your currently active SMTP configuration.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small">Recipient Email Address</label>
                                                            <input type="email" name="test_email" class="form-control" value="{{ Auth::user()->email }}" required placeholder="your.email@example.com">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-secondary px-3 py-2" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-info text-white fw-bold px-4 py-2">
                                                            <i class="bi bi-send-check me-1"></i> Send Test Now
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No email templates found matching your criteria.<br><br>
                                    <form method="POST" action="{{ route('admin.email-templates.seed') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary fw-semibold px-4 py-2">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Seed / Restore Default Templates
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($templates->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

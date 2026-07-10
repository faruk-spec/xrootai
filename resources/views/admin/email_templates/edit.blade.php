@extends('layouts.admin')

@section('title', 'Edit Email Template: ' . $emailTemplate->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.email-config.index') }}">Email Settings</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.email-templates.index') }}">Email Templates</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
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
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following errors:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Main Editor Left Column -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1">Edit Template Details</h5>
                                <p class="text-muted small mb-0">Modifying template: <code class="badge bg-secondary-subtle text-secondary">{{ $emailTemplate->slug }}</code></p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.email-templates.preview', $emailTemplate) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                    <i class="bi bi-eye-fill"></i> Live Preview
                                </a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Template Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $emailTemplate->name) }}" required maxlength="150">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Subject Line <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope-check text-muted"></i></span>
                                <input type="text" name="subject" id="input_subject" class="form-control focus-track" value="{{ old('subject', $emailTemplate->subject) }}" required maxlength="255">
                            </div>
                            <div class="form-text">Supports placeholders like <code>{{ '{{app_name}}' }}</code> and <code>{{ '{{user_name}}' }}</code>.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>HTML Email Body <span class="text-danger">*</span></span>
                                <span class="badge bg-info-subtle text-info fw-normal">Rich HTML / Blade Syntax Supported</span>
                            </label>
                            <textarea name="body_html" id="input_body_html" class="form-control font-monospace focus-track" rows="18" required style="font-size: 0.88rem; background-color: #1e293b; color: #f8fafc; border-radius: 12px; padding: 18px; line-height: 1.5;">{{ old('body_html', $emailTemplate->body_html) }}</textarea>
                            <div class="form-text">Enter clean inline-styled HTML. Use double curly braces for dynamic variables.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Plain Text Version (Optional Fallback)</span>
                                <span class="badge bg-secondary-subtle text-secondary fw-normal">For Email Clients Without HTML</span>
                            </label>
                            <textarea name="body_text" id="input_body_text" class="form-control font-monospace focus-track" rows="6" style="font-size: 0.88rem;">{{ old('body_text', $emailTemplate->body_text) }}</textarea>
                            <div class="form-text">If left blank, the system will automatically strip HTML tags from your HTML body when sending to plaintext clients.</div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Variables & Options -->
                <div class="col-lg-4">
                    <!-- Status & Save Card -->
                    <div class="card border-0 shadow-sm p-4 mb-4 position-sticky" style="top:90px;">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Status & Save</h6>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-1" for="is_active">Enable Template</label>
                                <div class="form-text small">When disabled, the system will fall back to static built-in Blade views.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Description & Usage Notes</label>
                            <textarea name="description" class="form-control small" rows="3">{{ old('description', $emailTemplate->description) }}</textarea>
                        </div>

                        <div class="d-flex flex-column gap-2 border-top pt-3">
                            <button type="submit" class="btn btn-primary fw-bold py-2 d-flex justify-content-center align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Template Changes
                            </button>
                            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary py-2 text-center">
                                Cancel & Return
                            </a>
                        </div>
                    </div>

                    <!-- Available Placeholders Card -->
                    <div class="card border-0 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-uppercase text-muted small mb-0">Supported Placeholders</h6>
                            <span class="badge bg-primary-subtle text-primary">Click to Insert</span>
                        </div>
                        <p class="text-muted small mb-3">Click any variable below to insert it directly where your cursor is located in the Subject line or HTML/Text body:</p>

                        <div class="d-flex flex-column gap-2">
                            @if(is_array($emailTemplate->available_variables))
                                @foreach($emailTemplate->available_variables as $varKey => $varDesc)
                                    <div class="border rounded-3 p-2 bg-light d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary font-monospace fw-bold insert-var-btn" data-variable="{{ '{{'.$varKey.'}}' }}" title="Click to insert {{ '{{'.$varKey.'}}' }}">
                                                {{ '{{'.$varKey.'}}' }}
                                            </button>
                                            <div class="text-muted small mt-1">{{ $varDesc }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Universal System Variables -->
                            <div class="border rounded-3 p-2 bg-light mt-2">
                                <div class="fw-bold text-dark small mb-1">Universal Placeholders:</div>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-monospace insert-var-btn" data-variable="{{ '{{app_name}}' }}">{{ '{{app_name}}' }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-monospace insert-var-btn" data-variable="{{ '{{app_url}}' }}">{{ '{{app_url}}' }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-monospace insert-var-btn" data-variable="{{ '{{current_year}}' }}">{{ '{{current_year}}' }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastFocusedElement = document.getElementById('input_body_html');

    document.querySelectorAll('.focus-track').forEach(function(el) {
        el.addEventListener('focus', function() {
            lastFocusedElement = this;
        });
        el.addEventListener('click', function() {
            lastFocusedElement = this;
        });
    });

    document.querySelectorAll('.insert-var-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const varText = this.getAttribute('data-variable');
            if (!lastFocusedElement) return;

            if (lastFocusedElement.selectionStart !== undefined && lastFocusedElement.selectionEnd !== undefined) {
                const startPos = lastFocusedElement.selectionStart;
                const endPos = lastFocusedElement.selectionEnd;
                const value = lastFocusedElement.value;
                
                lastFocusedElement.value = value.substring(0, startPos) + varText + value.substring(endPos, value.length);
                lastFocusedElement.selectionStart = startPos + varText.length;
                lastFocusedElement.selectionEnd = startPos + varText.length;
                lastFocusedElement.focus();
            } else {
                lastFocusedElement.value += varText;
                lastFocusedElement.focus();
            }
        });
    });
});
</script>
@endsection

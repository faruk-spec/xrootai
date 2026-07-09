@extends('layouts.admin')

@section('title', 'Add Routing Rule')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.routing.index') }}">AI Routing Rules</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Rule</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 650px;">
    <h4 class="fw-bold mb-4">Add Routing Rule</h4>

    <form action="{{ route('admin.routing.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium">Rule Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Code Assistance Router" required>
            <div class="form-text">Enter a clear descriptive name for this rule.</div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Trigger Type</label>
                <select name="trigger_type" class="form-select" required>
                    <option value="pattern">Regex Pattern Match</option>
                    <option value="plan">SaaS User Plan</option>
                    <option value="fallback">System Fallback</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Trigger Pattern / Keyword</label>
                <input type="text" name="pattern" class="form-control" placeholder="e.g. write a code, build, refactor">
                <div class="form-text">Omit or set empty for fallback triggers.</div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label fw-medium">Target AI Model</label>
                <select name="target_model_id" class="form-select" required>
                    <option value="">Select Target Model...</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}">
                            {{ $model->name }} ({{ strtoupper($model->provider->slug) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Rule Priority (Score)</label>
                <input type="number" name="priority" class="form-control" value="0" required>
                <div class="form-text">Higher numbers run first.</div>
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                <label class="form-check-label fw-semibold" for="isActive">Enable Rule</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Rule
            </button>
            <a href="{{ route('admin.routing.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

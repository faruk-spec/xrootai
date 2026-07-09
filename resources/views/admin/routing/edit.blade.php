@extends('layouts.admin')

@section('title', 'Edit Routing Rule')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.routing.index') }}">AI Routing Rules</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Rule</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 650px;">
    <h4 class="fw-bold mb-4">Edit Routing Rule: {{ $routing->name }}</h4>

    <form action="{{ route('admin.routing.update', $routing->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-medium">Rule Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $routing->name) }}" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Trigger Type</label>
                <select name="trigger_type" class="form-select" required>
                    <option value="pattern" {{ old('trigger_type', $routing->trigger_type) === 'pattern' ? 'selected' : '' }}>Regex Pattern Match</option>
                    <option value="plan" {{ old('trigger_type', $routing->trigger_type) === 'plan' ? 'selected' : '' }}>SaaS User Plan</option>
                    <option value="fallback" {{ old('trigger_type', $routing->trigger_type) === 'fallback' ? 'selected' : '' }}>System Fallback</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Trigger Pattern / Keyword</label>
                <input type="text" name="pattern" class="form-control" value="{{ old('pattern', $routing->pattern) }}">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label fw-medium">Target AI Model</label>
                <select name="target_model_id" class="form-select" required>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}" {{ old('target_model_id', $routing->target_model_id) == $model->id ? 'selected' : '' }}>
                            {{ $model->name }} ({{ strtoupper($model->provider->slug) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Rule Priority (Score)</label>
                <input type="number" name="priority" class="form-control" value="{{ old('priority', $routing->priority) }}" required>
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $routing->is_active ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Enable Rule</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.routing.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

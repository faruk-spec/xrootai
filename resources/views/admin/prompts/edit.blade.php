@extends('layouts.admin')

@section('title', 'Edit Prompt Template')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.prompts.index') }}">Prompt Templates</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Template</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 800px;">
    <h4 class="fw-bold mb-4">Edit Prompt Template: {{ $prompt->name }}</h4>

    <form action="{{ route('admin.prompts.update', $prompt->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-medium">Template Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $prompt->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Description</label>
            <input type="text" name="description" class="form-control" value="{{ old('description', $prompt->description) }}">
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Template Variables (comma separated)</label>
            <input type="text" name="variables" class="form-control" value="{{ old('variables', is_array($prompt->variables) ? implode(', ', $prompt->variables) : '') }}" placeholder="e.g. language, code_block">
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Prompt Content</label>
            <textarea name="content" class="form-control" rows="8" style="font-family:monospace;" required>{{ old('content', $prompt->content) }}</textarea>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $prompt->is_active ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Enable Template</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.prompts.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

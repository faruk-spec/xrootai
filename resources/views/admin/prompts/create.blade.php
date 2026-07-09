@extends('layouts.admin')

@section('title', 'Add Prompt Template')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.prompts.index') }}">Prompt Templates</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Template</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 800px;">
    <h4 class="fw-bold mb-4">Add Prompt Template</h4>

    <form action="{{ route('admin.prompts.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium">Template Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Code Review Helper" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Description</label>
            <input type="text" name="description" class="form-control" placeholder="e.g. Guide for debugging user code syntax and performance.">
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Template Variables (comma separated)</label>
            <input type="text" name="variables" class="form-control" placeholder="e.g. language, code_block, error_message">
            <div class="form-text">List variables which can be dynamically populated in prompts.</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Prompt Content</label>
            <textarea name="content" class="form-control" rows="8" style="font-family:monospace;" placeholder="You are a senior {language} code reviewer. Inspect the following snippet: {code_block}..." required></textarea>
            <div class="form-text">Place variables inside curly braces (e.g. <code>{language}</code>).</div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                <label class="form-check-label fw-semibold" for="isActive">Enable Template</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Template
            </button>
            <a href="{{ route('admin.prompts.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

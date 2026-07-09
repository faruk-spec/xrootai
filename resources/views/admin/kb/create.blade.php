@extends('layouts.admin')

@section('title', 'Add Knowledge Source')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.kb.index') }}">Knowledge Base</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Source</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 650px;">
    <h4 class="fw-bold mb-4">Add RAG Knowledge Source</h4>

    <form action="{{ route('admin.kb.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium">Source Label Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Corporate FAQ Wiki" required>
            <div class="form-text">Choose a descriptive name for internal index classification.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Source Type</label>
            <select name="type" class="form-select" required>
                <option value="url">Website URL (Web Crawler)</option>
                <option value="faq">FAQ dataset (Questions & Answers)</option>
                <option value="file">Local File Path (PDF, TXT, DOCX)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Source Path / URL Endpoint / File Location</label>
            <input type="text" name="source_path" class="form-control" placeholder="e.g. https://docs.xrootai.com or C:/Data/Wiki.txt" required>
            <div class="form-text">Enter the HTTP url endpoint, FAQ payload string, or absolute system file path.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-cloud-arrow-up-fill"></i> Upload & Index
            </button>
            <a href="{{ route('admin.kb.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

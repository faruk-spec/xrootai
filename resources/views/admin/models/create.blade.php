@extends('layouts.admin')

@section('title', 'Add AI Model')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.models.index') }}">AI Models</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Model</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 700px;">
    <h4 class="fw-bold mb-4">Add AI Model Configuration</h4>

    <form action="{{ route('admin.models.store') }}" method="POST">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">AI Provider</label>
                <select name="provider_id" class="form-select @error('provider_id') is-invalid @enderror" required>
                    <option value="">Select Provider...</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                            {{ $provider->name }}
                        </option>
                    @endforeach
                </select>
                @error('provider_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-medium">Model Type</label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="chat" {{ old('type') === 'chat' ? 'selected' : '' }}>Chat / Text Generation</option>
                    <option value="embedding" {{ old('type') === 'embedding' ? 'selected' : '' }}>Embeddings Search</option>
                    <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Image Generation</option>
                    <option value="speech" {{ old('type') === 'speech' ? 'selected' : '' }}>Speech Audio Conversion</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Model Name (Display Label)</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Claude 3.5 Sonnet" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-medium">API Model Identifier</label>
                <input type="text" name="model_identifier" class="form-control @error('model_identifier') is-invalid @enderror" value="{{ old('model_identifier') }}" placeholder="e.g. claude-3-5-sonnet-20241022" required>
                @error('model_identifier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Context Window (Tokens)</label>
                <input type="number" name="context_window" class="form-control @error('context_window') is-invalid @enderror" value="{{ old('context_window', 16384) }}" required>
                @error('context_window')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-medium">Max Output Tokens</label>
                <input type="number" name="max_tokens" class="form-control @error('max_tokens') is-invalid @enderror" value="{{ old('max_tokens', 4096) }}" required>
                @error('max_tokens')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Cost Input Per Million Tokens ($)</label>
                <input type="number" step="0.0001" name="cost_per_million_input" class="form-control @error('cost_per_million_input') is-invalid @enderror" value="{{ old('cost_per_million_input', 0.0) }}" required>
                @error('cost_per_million_input')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-medium">Cost Output Per Million Tokens ($)</label>
                <input type="number" step="0.0001" name="cost_per_million_output" class="form-control @error('cost_per_million_output') is-invalid @enderror" value="{{ old('cost_per_million_output', 0.0) }}" required>
                @error('cost_per_million_output')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Model Capabilities</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="capabilities[vision]" id="capVision" {{ old('capabilities.vision') ? 'checked' : '' }}>
                        <label class="form-check-label" for="capVision">Vision (Image Input / OCR)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="capabilities[function_calling]" id="capTools" {{ old('capabilities.function_calling') ? 'checked' : '' }}>
                        <label class="form-check-label" for="capTools">Function Calling (Tools/API Execution)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="capabilities[image_gen]" id="capImage" {{ old('capabilities.image_gen') ? 'checked' : '' }}>
                        <label class="form-check-label" for="capImage">Image Generation</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="capabilities[audio]" id="capAudio" {{ old('capabilities.audio') ? 'checked' : '' }}>
                        <label class="form-check-label" for="capAudio">Audio/Speech capabilities</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                <label class="form-check-label fw-semibold" for="isActive">Enable Model</label>
            </div>
            <div class="form-text">If disabled, the model won't appear in user selection panels or dynamic routing rules.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Model
            </button>
            <a href="{{ route('admin.models.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection

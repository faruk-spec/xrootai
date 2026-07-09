@extends('layouts.admin')

@section('title', 'Conversation Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.conversations.index') }}">Conversations</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Logs</li>
@endsection

@section('content')
<div class="card border-0 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">{{ $conversation->title }}</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">UUID: {{ $conversation->uuid }} | Model: <strong class="text-secondary">{{ $conversation->model }}</strong></p>
        </div>
        <a href="{{ route('admin.conversations.index') }}" class="btn btn-light border">Back to List</a>
    </div>
</div>

<div class="row g-4">
    <!-- Messages Stream -->
    <div class="col-lg-8">
        <div class="card border-0 p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Dialogue Inspection</h5>
            
            <div class="d-flex flex-column gap-3" style="max-height: 600px; overflow-y: auto; padding-right: 10px;">
                @forelse($conversation->messages as $msg)
                    <div class="p-3 rounded-3 {{ $msg->role === 'user' ? 'bg-light border-start border-primary border-4 text-dark' : 'bg-primary-subtle border-start border-success border-4 text-dark' }}" style="border-radius:12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom border-light">
                            <span class="fw-bold text-uppercase" style="font-size:0.75rem;">
                                @if($msg->role === 'user')
                                    <i class="bi bi-person-fill text-primary"></i> User: {{ $conversation->user->name ?? 'Guest' }}
                                @else
                                    <i class="bi bi-robot text-success"></i> Assistant / AI
                                @endif
                            </span>
                            <span class="text-muted" style="font-size:0.7rem;">{{ $msg->created_at->format('M d, H:i:s') }}</span>
                        </div>
                        <div style="font-size:0.9rem; white-space: pre-wrap;">{{ $msg->content }}</div>
                        
                        @if($msg->metadata)
                            <div class="mt-2 border-top pt-1 text-muted" style="font-size:0.75rem;">
                                <strong>Tokens:</strong> {{ data_get($msg->metadata, 'tokens', 'N/A') }} | 
                                <strong>Latency:</strong> {{ data_get($msg->metadata, 'time_elapsed', 'N/A') }}s | 
                                <strong>Actual Model:</strong> {{ data_get($msg->metadata, 'model_used', 'N/A') }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-5">No messages in this conversation.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Session & User Meta -->
    <div class="col-lg-4">
        <div class="card border-0 p-4">
            <h5 class="fw-bold mb-3">Session Metadata</h5>
            
            <div class="mb-3">
                <label class="text-muted fw-semibold" style="font-size:0.75rem;">SaaS User</label>
                <div class="fw-medium">
                    @if($conversation->user)
                        <div class="fw-bold text-primary">{{ $conversation->user->name }}</div>
                        <div class="text-muted small">{{ $conversation->user->email }}</div>
                    @else
                        <span class="text-muted">Anonymous Guest</span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="text-muted fw-semibold" style="font-size:0.75rem;">Chat Created</label>
                <div class="fw-medium">{{ $conversation->created_at->format('M d, Y H:i:s') }}</div>
            </div>

            <div class="mb-3">
                <label class="text-muted fw-semibold" style="font-size:0.75rem;">Last Active</label>
                <div class="fw-medium">{{ $conversation->updated_at->format('M d, Y H:i:s') }}</div>
            </div>

            <div class="border-top pt-3 mt-4">
                <form action="{{ route('admin.conversations.delete', $conversation->id) }}" method="POST" onsubmit="return confirm('Permanently delete this chat log?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-trash"></i> Delete Conversation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Knowledge Sources')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Knowledge Base</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Knowledge Directory</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Manage websites, manuals, and FAQ datasets compiled for dynamic RAG and context retrieval.</p>
        </div>
        <a href="{{ route('admin.kb.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-cloud-upload-fill"></i> Upload Source
        </a>
    </div>

    <!-- Filters -->
    <form action="{{ route('admin.kb.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or source..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="url" {{ request('type') === 'url' ? 'selected' : '' }}>Websites</option>
                <option value="file" {{ request('type') === 'file' ? 'selected' : '' }}>Files (PDF/TXT)</option>
                <option value="faq" {{ request('type') === 'faq' ? 'selected' : '' }}>FAQ Datasets</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Source Name</th>
                    <th>Type</th>
                    <th>Reference Path</th>
                    <th>Status</th>
                    <th>Last Synced</th>
                    <th>Indexing Action</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                Chunks: {{ data_get($item->metadata, 'chunks_indexed', 0) }} / Embedding: {{ data_get($item->metadata, 'embedding_model', 'N/A') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($item->type) }}</span>
                        </td>
                        <td style="font-family:monospace; font-size:0.8rem; max-width:250px; overflow:hidden; text-overflow:ellipsis;">
                            {{ $item->source_path }}
                        </td>
                        <td>
                            @if($item->status === 'indexed')
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i> Indexed</span>
                            @elseif($item->status === 'indexing')
                                <span class="badge bg-primary-subtle text-primary"><span class="spinner-border spinner-border-sm" role="status" style="width:10px; height:10px;"></span> Syncing</span>
                            @elseif($item->status === 'failed')
                                <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill"></i> Failed</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock-fill"></i> Pending</span>
                            @endif
                        </td>
                        <td>
                            {{ $item->last_synced_at ? $item->last_synced_at->format('M d, Y H:i') : 'Never' }}
                        </td>
                        <td>
                            <form action="{{ route('admin.kb.sync', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info py-1 px-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-arrow-repeat"></i> Sync Chunks
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.kb.delete', $item->id) }}" method="POST" onsubmit="return confirm('Remove this knowledge source from indexing? This will delete associated vector storage embeddings.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No knowledge base sources registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection

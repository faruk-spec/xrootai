<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Preview: {{ $emailTemplate->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { background-color: #1e293b; border-bottom: 1px solid #334155; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; }
        .viewport-container { flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 40px 20px; overflow-y: auto; }
        .iframe-wrapper { background: #ffffff; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden; transition: width 0.3s ease; width: 100%; max-width: 720px; height: 750px; display: flex; flex-direction: column; }
        .iframe-wrapper.mobile { max-width: 390px; height: 800px; border: 12px solid #334155; border-radius: 36px; }
        .email-header { background: #f8fafc; color: #334155; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; font-size: 0.88rem; }
        .email-header strong { color: #0f172a; }
        iframe { width: 100%; flex: 1; border: none; }
        .dummy-panel { background: #1e293b; border-top: 1px solid #334155; padding: 16px 24px; font-size: 0.82rem; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back to Editor
            </a>
            <div>
                <h6 class="fw-bold mb-0">{{ $emailTemplate->name }}</h6>
                <span class="text-muted small">Slug: {{ $emailTemplate->slug }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-primary active" id="btn-desktop" onclick="setViewport('desktop')">
                    <i class="bi bi-pc-display me-1"></i> Desktop (720px)
                </button>
                <button type="button" class="btn btn-outline-light" id="btn-mobile" onclick="setViewport('mobile')">
                    <i class="bi bi-phone me-1"></i> Mobile (390px)
                </button>
            </div>

            <div class="btn-group btn-group-sm ms-3" role="group">
                <button type="button" class="btn btn-info text-white" onclick="showTab('html')" id="tab-html">Visual Render</button>
                <button type="button" class="btn btn-outline-info" onclick="showTab('text')" id="tab-text">Plain Text</button>
                <button type="button" class="btn btn-outline-info" onclick="showTab('code')" id="tab-code">Raw Code</button>
            </div>
        </div>
    </div>

    <div class="viewport-container">
        <!-- Visual HTML Render -->
        <div class="iframe-wrapper" id="view-html">
            <div class="email-header">
                <div><strong>Subject:</strong> {{ $rendered['subject'] }}</div>
                <div class="text-muted mt-1 small"><strong>From:</strong> {{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;</div>
            </div>
            <iframe srcdoc="{{ htmlspecialchars($rendered['body_html']) }}"></iframe>
        </div>

        <!-- Plain Text View -->
        <div class="iframe-wrapper p-4 bg-white text-dark font-monospace d-none overflow-auto" id="view-text" style="white-space: pre-wrap; font-size: 0.9rem;">
<strong>Subject:</strong> {{ $rendered['subject'] }}

----------------------------------------------------------------------

{{ $rendered['body_text'] }}
        </div>

        <!-- Raw HTML Code View -->
        <div class="iframe-wrapper p-4 d-none overflow-auto" id="view-code" style="background-color: #1e293b; color: #38bdf8; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap;">
{{ $rendered['body_html'] }}
        </div>
    </div>

    <div class="dummy-panel">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="fw-bold text-light me-2"><i class="bi bi-sliders me-1"></i>Active Mock Data Substitution:</span>
                @foreach($dummyData as $k => $v)
                    @if(!in_array($k, ['app_url', 'current_year']))
                        <span class="badge bg-dark border border-secondary text-light font-monospace me-1 mb-1">@php echo '{{ ' . e($k) . ' }}'; @endphp = "{{ Str::limit($v, 20) }}"</span>
                    @endif
                @endforeach
            </div>
            <span class="text-muted">Changes saved in the editor take effect immediately upon reload.</span>
        </div>
    </div>

    <script>
        function setViewport(mode) {
            const wrapper = document.getElementById('view-html');
            const btnDesktop = document.getElementById('btn-desktop');
            const btnMobile = document.getElementById('btn-mobile');

            if (mode === 'mobile') {
                wrapper.classList.add('mobile');
                btnMobile.classList.remove('btn-outline-light');
                btnMobile.classList.add('btn-primary', 'active');
                btnDesktop.classList.remove('btn-primary', 'active');
                btnDesktop.classList.add('btn-outline-light');
            } else {
                wrapper.classList.remove('mobile');
                btnDesktop.classList.remove('btn-outline-light');
                btnDesktop.classList.add('btn-primary', 'active');
                btnMobile.classList.remove('btn-primary', 'active');
                btnMobile.classList.add('btn-outline-light');
            }
        }

        function showTab(tab) {
            document.getElementById('view-html').classList.add('d-none');
            document.getElementById('view-text').classList.add('d-none');
            document.getElementById('view-code').classList.add('d-none');

            document.getElementById('tab-html').className = 'btn btn-outline-info';
            document.getElementById('tab-text').className = 'btn btn-outline-info';
            document.getElementById('tab-code').className = 'btn btn-outline-info';

            document.getElementById('view-' + tab).classList.remove('d-none');
            document.getElementById('tab-' + tab).className = 'btn btn-info text-white';
        }
    </script>
</body>
</html>

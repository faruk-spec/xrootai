@extends('layouts.admin')

@section('title', 'System Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">System Settings</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Navigation Menu -->
    <div class="col-lg-3">
        <div class="card border-0 p-3 shadow-sm position-sticky" style="top:90px;">
            <h6 class="fw-bold text-muted text-uppercase mb-3 px-2" style="font-size:0.75rem;">SaaS Configurations</h6>
            <div class="d-flex flex-column gap-1">
                <a href="?tab=general" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'general' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-sliders"></i> General Settings
                </a>
                <a href="?tab=plans" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'plans' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-shield-lock-fill"></i> User Plans & Limits
                </a>
                <a href="?tab=behavior" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'behavior' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-gear-fill"></i> Model Parameters
                </a>
                <a href="?tab=prompt" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'prompt' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-chat-left-quote-fill"></i> System Prompts
                </a>
                <a href="?tab=kb" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'kb' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-database-fill-gear"></i> RAG Configurations
                </a>
                <a href="?tab=conv" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'conv' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-chat-left-dots-fill"></i> Conversation Config
                </a>
                <a href="?tab=handoff" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'handoff' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-headset"></i> Human Handoff
                </a>
                <a href="?tab=ux" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'ux' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-window-sidebar"></i> User Experience (UX)
                </a>
                <a href="?tab=lang" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'lang' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-translate"></i> Language Settings
                </a>
                <a href="?tab=notif" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'notif' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-bell-fill"></i> Alerts & Notifications
                </a>
                <a href="?tab=security" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'security' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-safe-fill"></i> Security & Moderation
                </a>
                <a href="?tab=roles" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'roles' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-person-fill-lock"></i> Roles & Permissions
                </a>
                <a href="?tab=privacy" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'privacy' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-eye-slash-fill"></i> Data & Privacy
                </a>
                <a href="?tab=integrations" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'integrations' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-puzzle-fill"></i> Integrations
                </a>
                <a href="?tab=billing" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'billing' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-credit-card-fill"></i> Billing & Budget
                </a>
                <a href="?tab=analytics" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'analytics' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-bar-chart-fill"></i> Analytics Simulation
                </a>
                <a href="?tab=moderation" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'moderation' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Content Moderation
                </a>
                <a href="?tab=logging" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'logging' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-journal-text"></i> System Logs Config
                </a>
                <a href="?tab=backup" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'backup' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-cloud-arrow-down-fill"></i> Backup & Recovery
                </a>
                <a href="?tab=developer" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'developer' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-code-slash"></i> Developer Settings
                </a>
                <a href="?tab=branding" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'branding' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-palette-fill"></i> Appearance & Branding
                </a>
                <a href="?tab=toggle" class="btn btn-sm text-start py-2 px-3 border-0 d-flex align-items-center gap-2 {{ $tab === 'toggle' ? 'btn-primary text-white' : 'btn-light' }}">
                    <i class="bi bi-toggle-on"></i> Feature Toggles
                </a>
            </div>
        </div>
    </div>

    <!-- Right Settings Input Card -->
    <div class="col-lg-9">
        <div class="card border-0 p-4 shadow-sm">
            <!-- 1. GENERAL SETTINGS -->
            @if($tab === 'general')
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_group" value="general">
                    <h5 class="fw-bold mb-4"><i class="bi bi-sliders text-primary me-2"></i>General Settings</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chatbot Display Name</label>
                        <input type="text" name="general_chatbot_name" value="{{ $settings['general_chatbot_name'] }}" class="form-control" required>
                        <div class="form-text">Your AI chatbot name displayed in chat headers and titles.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chatbot Description</label>
                        <input type="text" name="general_chatbot_description" value="{{ $settings['general_chatbot_description'] }}" class="form-control">
                        <div class="form-text">A brief description outlining chatbot capabilities.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chatbot Logo / Brand Image</label>
                            @if(!empty($settings['general_chatbot_logo']))
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ $settings['general_chatbot_logo'] }}" alt="Logo" style="height: 48px; border-radius: 8px; border: 1px solid #dee2e6; object-fit: contain;">
                                    <span class="text-muted small">Current Logo</span>
                                </div>
                            @endif
                            <input type="file" name="general_chatbot_logo" class="form-control" accept="image/*">
                            <div class="form-text">Recommended size: 128x128px (PNG, JPG, SVG, WebP).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Site Icon / Favicon</label>
                            @if(!empty($settings['general_site_icon']))
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ $settings['general_site_icon'] }}" alt="Favicon" style="height: 32px; border-radius: 4px; border: 1px solid #dee2e6; object-fit: contain;">
                                    <span class="text-muted small">Current Favicon</span>
                                </div>
                            @endif
                            <input type="file" name="general_site_icon" class="form-control" accept="image/*,.ico">
                            <div class="form-text">Recommended: 32x32px or 64x64px (ICO, PNG).</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Locale Language</label>
                            <select name="general_default_language" class="form-select">
                                <option value="en" {{ $settings['general_default_language'] === 'en' ? 'selected' : '' }}>English (en)</option>
                                <option value="es" {{ $settings['general_default_language'] === 'es' ? 'selected' : '' }}>Spanish (es)</option>
                                <option value="fr" {{ $settings['general_default_language'] === 'fr' ? 'selected' : '' }}>French (fr)</option>
                                <option value="de" {{ $settings['general_default_language'] === 'de' ? 'selected' : '' }}>German (de)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">System Timezone</label>
                            <input type="text" name="general_timezone" value="{{ $settings['general_timezone'] }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Global Welcome message</label>
                        <textarea name="general_welcome_message" class="form-control" rows="2" required>{{ $settings['general_welcome_message'] }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="general_enable_chatbot" class="form-check-input" id="enableChat" {{ $settings['general_enable_chatbot'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="enableChat">Enable Chat Interface</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="general_maintenance_mode" class="form-check-input" id="maintMode" {{ $settings['general_maintenance_mode'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-danger" for="maintMode">Enable Maintenance Mode</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save General Configurations
                    </button>
                </form>
            @endif

            <!-- 2. USER PLANS & LIMITS -->
            @if($tab === 'plans')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="plans">
                    <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill text-primary me-2"></i>User Plans & Limits</h5>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Guest / Anonymous Limits</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Daily Chat Session Limit</label>
                            <input type="number" name="plans_guest_chat_limit" value="{{ $settings['plans_guest_chat_limit'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Max Messages per Session</label>
                            <input type="number" name="plans_guest_messages_per_session" value="{{ $settings['plans_guest_messages_per_session'] }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="plans_guest_file_upload" class="form-check-input" id="guestFile" {{ $settings['plans_guest_file_upload'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="guestFile">Allow Guest File Uploads</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="plans_guest_image_gen" class="form-check-input" id="guestImage" {{ $settings['plans_guest_image_gen'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="guestImage">Allow Guest Image Generation</label>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2 pt-3">Free Tier Limits</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Daily Message Limit</label>
                            <input type="number" name="plans_free_message_limit" value="{{ $settings['plans_free_message_limit'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Monthly Token Quota</label>
                            <input type="number" name="plans_free_token_limit" value="{{ $settings['plans_free_token_limit'] }}" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2 pt-3">Pro Tier Limits</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Daily Chats (-1 for unlimited)</label>
                            <input type="number" name="plans_pro_chat_limit" value="{{ $settings['plans_pro_chat_limit'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Larger Context Window (tokens)</label>
                            <input type="number" name="plans_pro_context_window" value="{{ $settings['plans_pro_context_window'] }}" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Plan Configurations
                    </button>
                </form>
            @endif

            <!-- 3. AI BEHAVIOR / MODEL PARAMETERS -->
            @if($tab === 'behavior')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="behavior">
                    <h5 class="fw-bold mb-4"><i class="bi bi-gear-fill text-primary me-2"></i>AI Behavior & Parameters</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Global Temperature (0.0 - 2.0)</label>
                            <input type="number" step="0.1" name="behavior_temperature" value="{{ $settings['behavior_temperature'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Top P (0.0 - 1.0)</label>
                            <input type="number" step="0.1" name="behavior_top_p" value="{{ $settings['behavior_top_p'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Output Response length</label>
                            <input type="number" name="behavior_max_response_length" value="{{ $settings['behavior_max_response_length'] }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4 pt-3">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Conversation Memory Options</h6>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="behavior_conversation_memory" class="form-check-input" id="behMem" {{ $settings['behavior_conversation_memory'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="behMem">Enable Chat History Memory Context</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="behavior_remember_username" class="form-check-input" id="behUser" {{ $settings['behavior_remember_username'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="behUser">Remember User Name</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="behavior_remember_preferences" class="form-check-input" id="behPref" {{ $settings['behavior_remember_preferences'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="behPref">Remember User Preferences</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Model Parameters
                    </button>
                </form>
            @endif

            <!-- 4. SYSTEM PROMPT -->
            @if($tab === 'prompt')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="prompt">
                    <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-quote-fill text-primary me-2"></i>System Prompts Editor</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Global Base System Prompt (Fallback)</label>
                        <textarea name="prompt_default" class="form-control" rows="5" style="font-family:monospace;" required>{{ $settings['prompt_default'] }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Primary Role Definition</label>
                        <textarea name="prompt_role_definition" class="form-control" rows="3">{{ $settings['prompt_role_definition'] }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Business Constraints & Guidelines</label>
                        <textarea name="prompt_business_rules" class="form-control" rows="3">{{ $settings['prompt_business_rules'] }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Prompt Rules
                    </button>
                </form>
            @endif

            <!-- 5. KNOWLEDGE BASE RAG SETTINGS -->
            @if($tab === 'kb')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="kb">
                    <h5 class="fw-bold mb-4"><i class="bi bi-database-fill-gear text-primary me-2"></i>RAG Embeddings & Chunking Config</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Embedding Model Endpoint</label>
                            <input type="text" name="kb_embedding_model" value="{{ $settings['kb_embedding_model'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Search Match Threshold (0.0 - 1.0)</label>
                            <input type="number" step="0.1" name="kb_search_threshold" value="{{ $settings['kb_search_threshold'] }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Chunk Size (Words / Characters)</label>
                            <input type="number" name="kb_chunk_size" value="{{ $settings['kb_chunk_size'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Chunk Overlap</label>
                            <input type="number" name="kb_chunk_overlap" value="{{ $settings['kb_chunk_overlap'] }}" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save RAG Settings
                    </button>
                </form>
            @endif

            <!-- 6. CONVERSATION CONFIG -->
            @if($tab === 'conv')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="conv">
                    <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Conversation Configurations</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Session Inactivity Timeout (minutes)</label>
                            <input type="number" name="conv_session_timeout" value="{{ $settings['conv_session_timeout'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Max Messages per Conversation Thread</label>
                            <input type="number" name="conv_max_length" value="{{ $settings['conv_max_length'] }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="conv_store_history" class="form-check-input" id="storeHistory" {{ $settings['conv_store_history'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="storeHistory">Permanently Store Message History in DB</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="conv_auto_summary" class="form-check-input" id="autoSum" {{ $settings['conv_auto_summary'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="autoSum">Auto Generate Conversation Titles using LLM summary</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Conversation Settings
                    </button>
                </form>
            @endif

            <!-- 7. HUMAN HANDOFF -->
            @if($tab === 'handoff')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="handoff">
                    <h5 class="fw-bold mb-4"><i class="bi bi-headset text-primary me-2"></i>Human Handoff Configurations</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="handoff_enable" class="form-check-input" id="enableHandoff" {{ $settings['handoff_enable'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="enableHandoff">Enable Live Agent Handoff Integration</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Escalation Triggers / Keywords (comma separated)</label>
                        <input type="text" name="handoff_keywords" value="{{ $settings['handoff_keywords'] }}" class="form-control">
                        <div class="form-text">Chats containing these phrases will instantly pause LLM responses and alert support agents.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Support Desk Alert Email</label>
                            <input type="email" name="handoff_support_team" value="{{ $settings['handoff_support_team'] }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Operational Hours</label>
                            <input type="text" name="handoff_business_hours" value="{{ $settings['handoff_business_hours'] }}" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Handoff Configurations
                    </button>
                </form>
            @endif

            <!-- 8. USER EXPERIENCE (UX) -->
            @if($tab === 'ux')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="ux">
                    <h5 class="fw-bold mb-4"><i class="bi bi-window-sidebar text-primary me-2"></i>User Experience (UX) Settings</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Widget Default CSS Position</label>
                            <input type="text" name="ux_widget_position" value="{{ $settings['ux_widget_position'] }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Widget Default Size</label>
                            <input type="text" name="ux_widget_size" value="{{ $settings['ux_widget_size'] }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Suggested Questions (One per line)</label>
                        <textarea name="ux_suggested_questions" class="form-control" rows="4">{{ $settings['ux_suggested_questions'] }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="ux_typing_indicator" class="form-check-input" id="typeInd" {{ $settings['ux_typing_indicator'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="typeInd">Show dynamic "typing..." indicators during API streaming</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="ux_feedback_thumbs" class="form-check-input" id="feedThumbs" {{ $settings['ux_feedback_thumbs'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="feedThumbs">Enable User Upvote/Downvote feedback icons on answers</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save UX Settings
                    </button>
                </form>
            @endif

            <!-- 9. LANGUAGE SETTINGS -->
            @if($tab === 'lang')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="lang">
                    <h5 class="fw-bold mb-4"><i class="bi bi-translate text-primary me-2"></i>Languages & Translations</h5>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Supported Locales (comma separated)</label>
                        <input type="text" name="lang_supported" value="{{ $settings['lang_supported'] }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Fallback Locale</label>
                        <input type="text" name="lang_fallback" value="{{ $settings['lang_fallback'] }}" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="lang_auto_detect" class="form-check-input" id="langDetect" {{ $settings['lang_auto_detect'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="langDetect">Auto-detect user browser locales</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="lang_rtl_support" class="form-check-input" id="rtlSup" {{ $settings['lang_rtl_support'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="rtlSup">Enable Right-to-Left (RTL) stylesheets</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Language Configurations
                    </button>
                </form>
            @endif

            <!-- 10. ALERTS & NOTIFICATIONS -->
            @if($tab === 'notif')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="notif">
                    <h5 class="fw-bold mb-4"><i class="bi bi-bell-fill text-primary me-2"></i>Alerts & Notifications</h5>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Admin Triggers</h6>
                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="notif_admin_ai_failures" class="form-check-input" id="notifFail" {{ $settings['notif_admin_ai_failures'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifFail">Notify on API network call connection failures</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="notif_admin_security_events" class="form-check-input" id="notifSec" {{ $settings['notif_admin_security_events'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifSec">Notify on prompt injection attempts</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Slack Alert Webhook URL</label>
                        <input type="url" name="notif_slack_webhook" value="{{ $settings['notif_slack_webhook'] }}" class="form-control" placeholder="https://hooks.slack.com/services/...">
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Notification Settings
                    </button>
                </form>
            @endif

            <!-- 11. SECURITY & MODERATION -->
            @if($tab === 'security')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="security">
                    <h5 class="fw-bold mb-4"><i class="bi bi-safe-fill text-primary me-2"></i>Security & Moderation</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">IP Whitelist (comma separated)</label>
                        <input type="text" name="security_ip_whitelist" value="{{ $settings['security_ip_whitelist'] }}" class="form-control" placeholder="127.0.0.1, 192.168.1.1">
                    </div>

                    <div class="mb-4 pt-2">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="security_prompt_injection" class="form-check-input" id="secInject" {{ $settings['security_prompt_injection'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="secInject">Activate Prompt Injection Filtering Protection</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="security_jailbreak_protection" class="form-check-input" id="secJail" {{ $settings['security_jailbreak_protection'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="secJail">Activate Jailbreak Protections</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="security_sensitive_data" class="form-check-input" id="secSens" {{ $settings['security_sensitive_data'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="secSens">Redact Sensitive PII (Social IDs, Credit Cards) before LLM submission</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Security Settings
                    </button>
                </form>
            @endif

            <!-- 12. ROLES & PERMISSIONS -->
            @if($tab === 'roles')
                <h5 class="fw-bold mb-3"><i class="bi bi-person-fill-lock text-primary me-2"></i>Roles & Permissions Matrix</h5>
                <p class="text-muted small mb-4">Sync feature scopes mapping permissions to roles.</p>
                
                <form action="{{ route('admin.settings.permissions') }}" method="POST">
                    @csrf
                    <div class="table-responsive mb-4">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Permissions Scope</th>
                                    @foreach($roles as $role)
                                        <th class="text-center">{{ $role->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $perm)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" style="font-size:0.85rem;">{{ str_replace('-', ' ', $perm->name) }}</div>
                                            <div class="text-muted" style="font-size:0.75rem;">{{ $perm->description }}</div>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="roles[{{ $role->id }}][]" value="{{ $perm->id }}" 
                                                    {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}
                                                    {{ $role->name === 'Super Admin' ? 'disabled checked' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Save Permission Matrix
                    </button>
                </form>
            @endif

            <!-- 13. DATA & PRIVACY -->
            @if($tab === 'privacy')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="privacy">
                    <h5 class="fw-bold mb-4"><i class="bi bi-eye-slash-fill text-primary me-2"></i>Data Retention & GDPR</h5>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Message Logs retention (days)</label>
                        <input type="number" name="privacy_retention_days" value="{{ $settings['privacy_retention_days'] }}" class="form-control" required>
                        <div class="form-text">Conversations older than this will be pruned from vectors and DB records.</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="privacy_gdpr_compliance" class="form-check-input" id="privGdpr" {{ $settings['privacy_gdpr_compliance'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="privGdpr">Enable GDPR right-to-forget user actions</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="privacy_consent_banner" class="form-check-input" id="privCookie" {{ $settings['privacy_consent_banner'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="privCookie">Show cookie/privacy consent banners on page load</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Privacy Settings
                    </button>
                </form>
            @endif

            <!-- 14. INTEGRATIONS -->
            @if($tab === 'integrations')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="integrations">
                    <h5 class="fw-bold mb-4"><i class="bi bi-puzzle-fill text-primary me-2"></i>Integrations Config</h5>

                    <div class="mb-3">
                        <label class="form-label fw-medium">SSO Authentication Providers</label>
                        <input type="text" name="integrations_auth_providers" value="{{ $settings['integrations_auth_providers'] }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Customer CRM mapping endpoints</label>
                        <input type="text" name="integrations_comm_channels" value="{{ $settings['integrations_comm_channels'] }}" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Active Payment Gateways</label>
                        <input type="text" name="integrations_payments" value="{{ $settings['integrations_payments'] }}" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Integrations
                    </button>
                </form>
            @endif

            <!-- 15. BILLING & BUDGETS -->
            @if($tab === 'billing')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="billing">
                    <h5 class="fw-bold mb-4"><i class="bi bi-credit-card-fill text-primary me-2"></i>Billing & Budget Limits</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Global Cost Budget Limit ($)</label>
                            <input type="number" step="0.1" name="billing_cost_budget" value="{{ $settings['billing_cost_budget'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Standard Value Added Tax Rate (%)</label>
                            <input type="number" step="0.1" name="billing_taxes" value="{{ $settings['billing_taxes'] }}" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Billing Parameters
                    </button>
                </form>
            @endif

            <!-- 16. ANALYTICS SIMULATION -->
            @if($tab === 'analytics')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="analytics">
                    <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Analytics Configurations</h5>
                    <div class="alert alert-light border small text-muted mb-4">Modify analytical indices simulated on the main dashboard graphs.</div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Response Speed baseline (seconds)</label>
                            <input type="number" step="0.1" name="analytics_avg_response_time" value="{{ $settings['analytics_avg_response_time'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Average LLM Accuracy Baseline (%)</label>
                            <input type="number" step="0.1" name="analytics_ai_accuracy" value="{{ $settings['analytics_ai_accuracy'] }}" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Analytics Config
                    </button>
                </form>
            @endif

            <!-- 17. CONTENT MODERATION -->
            @if($tab === 'moderation')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="moderation">
                    <h5 class="fw-bold mb-4"><i class="bi bi-exclamation-triangle-fill text-primary me-2"></i>Content Moderation</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="moderation_profanity_filter" class="form-check-input" id="modProf" {{ $settings['moderation_profanity_filter'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="modProf">Filter Profanity & Block Bad Words</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="moderation_hate_speech" class="form-check-input" id="modHate" {{ $settings['moderation_hate_speech'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="modHate">Filter Hate Speech & Violence Content</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Blocked Keywords (comma separated)</label>
                        <textarea name="moderation_blocked_keywords" class="form-control" rows="3">{{ $settings['moderation_blocked_keywords'] }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Moderation Settings
                    </button>
                </form>
            @endif

            <!-- 18. SYSTEM LOGS CONFIG -->
            @if($tab === 'logging')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="logging">
                    <h5 class="fw-bold mb-4"><i class="bi bi-journal-text text-primary me-2"></i>System Logging Configuration</h5>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="logging_system" class="form-check-input" id="logSys" {{ $settings['logging_system'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="logSys">Log system actions & admin settings updates</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="logging_ai_request" class="form-check-input" id="logPayload" {{ $settings['logging_ai_request'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="logPayload">Log AI completion request JSON payloads</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Logging Settings
                    </button>
                </form>
            @endif

            <!-- 19. BACKUP & RECOVERY -->
            @if($tab === 'backup')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="backup">
                    <h5 class="fw-bold mb-4"><i class="bi bi-cloud-arrow-down-fill text-primary me-2"></i>Backup & Recovery</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Backup Cron Frequency</label>
                            <input type="text" name="backup_frequency" value="{{ $settings['backup_frequency'] }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Max Retained local backups count</label>
                            <input type="number" name="backup_retention_count" value="{{ $settings['backup_retention_count'] }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="backup_auto" class="form-check-input" id="backupAuto" {{ $settings['backup_auto'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="backupAuto">Enable automatic database backups</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Backup Config
                    </button>
                </form>
            @endif

            <!-- 20. DEVELOPER SETTINGS -->
            @if($tab === 'developer')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="developer">
                    <h5 class="fw-bold mb-4"><i class="bi bi-code-slash text-primary me-2"></i>Developer Configurations</h5>

                    <div class="mb-3">
                        <label class="form-label fw-medium">API Rate Limiting (requests/minute)</label>
                        <input type="number" name="developer_rate_limits" value="{{ $settings['developer_rate_limits'] }}" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="developer_api_playground" class="form-check-input" id="devPlay" {{ $settings['developer_api_playground'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="devPlay">Activate developers playground interface</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="developer_debug_mode" class="form-check-input" id="devDebug" {{ $settings['developer_debug_mode'] ? 'checked' : '' }}>
                            <label class="form-check-label text-danger fw-semibold" for="devDebug">Enable System Debug Mode (Verbose logs)</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Developer Settings
                    </button>
                </form>
            @endif

            <!-- 21. APPEARANCE & BRANDING -->
            @if($tab === 'branding')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="branding">
                    <h5 class="fw-bold mb-4"><i class="bi bi-palette-fill text-primary me-2"></i>Appearance & Branding</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">System Footer Copyright Text</label>
                        <input type="text" name="branding_footer_text" value="{{ $settings['branding_footer_text'] }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Custom CNAME Domain Mapping</label>
                        <input type="text" name="branding_custom_domain" value="{{ $settings['branding_custom_domain'] }}" class="form-control" placeholder="chat.yourdomain.com">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Custom CSS Code Overrides</label>
                        <textarea name="branding_custom_css" class="form-control" rows="4" style="font-family:monospace;">{{ $settings['branding_custom_css'] }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Branding
                    </button>
                </form>
            @endif

            <!-- 22. FEATURE TOGGLES -->
            @if($tab === 'toggle')
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_group" value="toggle">
                    <h5 class="fw-bold mb-4"><i class="bi bi-toggle-on text-primary me-2"></i>Global Feature Toggles</h5>
                    <div class="alert alert-light border small text-muted mb-4">Toggle core software components dynamically. When disabled, access routes will trigger 404/403 blocks.</div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_vision" class="form-check-input" id="tVision" {{ $settings['toggle_vision'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tVision">Vision AI Analysis</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_images" class="form-check-input" id="tImages" {{ $settings['toggle_images'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tImages">Image Generation (DALL-E/Flux)</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_file_upload" class="form-check-input" id="tFiles" {{ $settings['toggle_file_upload'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tFiles">Chat Document Uploads</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="toggle_knowledge_base" class="form-check-input" id="tKb" {{ $settings['toggle_knowledge_base'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tKb">RAG Index Context Fetching</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_human_handoff" class="form-check-input" id="tHandoff" {{ $settings['toggle_human_handoff'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tHandoff">Live Support Handoffs</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_feedback" class="form-check-input" id="tFeedback" {{ $settings['toggle_feedback'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tFeedback">User Responses Rating</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="toggle_analytics" class="form-check-input" id="tAnalytics" {{ $settings['toggle_analytics'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tAnalytics">Analytical Monitoring</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="toggle_api_access" class="form-check-input" id="tApi" {{ $settings['toggle_api_access'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="tApi">Dynamic Developers API Key generation</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Save Feature Toggles
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

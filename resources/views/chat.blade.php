<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XrootAI Chat</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <link rel="stylesheet" href="{{ asset('css/github-dark.min.css') }}">
    
    <!-- Scripts -->
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/markdown-it.min.js') }}"></script>
    <script src="{{ asset('js/highlight.min.js') }}"></script>
    
    <style>
        /* Ambient glows local to chat workspace */
        .glow-sphere {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            z-index: -1;
            opacity: 0.35;
            pointer-events: none;
        }
        .glow-1 {
            top: 20%;
            left: 25%;
            width: 350px;
            height: 350px;
            background: rgba(126, 182, 255, 0.3);
        }
        .glow-2 {
            bottom: 20%;
            right: 20%;
            width: 400px;
            height: 400px;
            background: rgba(168, 224, 99, 0.25);
        }

        /* Custom Scrollbar Styles for a premium UI */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .dark-mode ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }
        .dark-mode ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
        }
        .dark-mode * {
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        /* Responsive Layout grid */
        .workspace {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }

        @media (max-width: 768px) {
            .workspace {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                width: 280px; /* Explicit width on mobile */
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: block !important;
            }
        }

        /* Sidebar styling */
        .sidebar {
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--clay-card-border);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            padding: 20px;
            gap: 16px;
        }

        /* Chat view pane */
        .chat-pane {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .chat-header {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            border-bottom: 1px solid var(--clay-card-border);
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            scroll-behavior: smooth;
        }

        .chat-input-area {
            padding: 20px 24px;
            border-top: 1px solid var(--clay-card-border);
            backdrop-filter: blur(10px);
        }

        /* Message bubbles */
        .message-bubble {
            max-width: 80%;
            margin-bottom: 4px;
            line-height: 1.6;
        }

        .message-user {
            align-self: flex-end;
            background: var(--accent-blue);
            color: white;
            box-shadow: var(--accent-blue-shadow);
            border-radius: 24px 24px 4px 24px;
        }

        .message-assistant {
            align-self: flex-start;
            background: var(--clay-card-bg);
            color: var(--text-primary);
            box-shadow: var(--clay-outer-shadow);
            border: 1px solid var(--clay-card-border);
            border-radius: 24px 24px 24px 4px;
        }

        /* Markdown lists & paragraphs spacing */
        .message-assistant p {
            margin-bottom: 12px;
        }
        .message-assistant p:last-child {
            margin-bottom: 0;
        }
        .message-assistant ul, .message-assistant ol {
            margin-left: 20px;
            margin-bottom: 12px;
        }
        .message-assistant pre {
            margin: 12px 0;
            border-radius: 12px;
            overflow: hidden;
        }
        .message-assistant code {
            font-family: Consolas, Monaco, monospace;
            font-size: 0.9rem;
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 6px;
            border-radius: 6px;
        }
        .dark-mode .message-assistant code {
            background: rgba(255, 255, 255, 0.08);
        }
        .message-assistant pre code {
            display: block;
            padding: 16px;
            overflow-x: auto;
            background: #1e1e2e;
            color: #cdd6f4;
        }

        /* Inset list active indicator */
        .history-item {
            padding: 12px 16px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--text-primary);
            border: 1px solid transparent;
        }
        .history-item:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateX(3px);
        }
        .dark-mode .history-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .history-item.active {
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
        }

        /* Modals (Frosted Glass Clay style) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 18, 29, 0.4);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-card {
            width: 100%;
            max-width: 550px;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Attachment chips */
        .attach-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            padding: 6px 12px;
            border-radius: 14px;
            font-size: 0.85rem;
            box-shadow: var(--clay-outer-shadow);
        }

        /* Auto-expandable textarea logic helper */
        .chat-textarea {
            resize: none;
            max-height: 200px;
        }

        /* Premium Table Styles */
        .message-assistant table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 16px 0;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
        }
        .message-assistant th, .message-assistant td {
            padding: 12px 16px;
            text-align: left;
        }
        .message-assistant th {
            background: rgba(0, 0, 0, 0.03);
            font-weight: 600;
            border-bottom: 1px solid var(--clay-card-border);
            color: var(--text-primary);
        }
        .dark-mode .message-assistant th {
            background: rgba(255, 255, 255, 0.03);
        }
        .message-assistant td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }
        .dark-mode .message-assistant td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.1);
        }
        .message-assistant tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body x-data="chatApp()" x-init="initApp()" :class="{ 'dark-mode': darkMode }">
    <div class="glow-sphere glow-1"></div>
    <div class="glow-sphere glow-2"></div>

    <div class="workspace">
        
        <!-- Sidebar overlay for Mobile view -->
        <div class="sidebar-overlay" x-show="sidebarOpen" @click="sidebarOpen = false" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:40;"></div>

        <!-- Sidebar Container (Phase 2 Component) -->
        <aside class="sidebar" :class="{ 'open': sidebarOpen }">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <a href="{{ route('chat') }}" class="app-brand">
                    <div class="app-brand-icon">X</div>
                    <span>XrootAI</span>
                </a>
                <button @click="sidebarOpen = false" class="clay-btn clay-btn-secondary" style="border-radius:50%; width:36px; height:36px; padding:0; display: flex; align-items: center; justify-content: center; @media(min-width:769px){display:none;}">
                    ✕
                </button>
            </div>

            <!-- New Chat Puffed Button -->
            <form action="{{ route('chats.store') }}" method="POST">
                @csrf
                <input type="hidden" name="model" :value="activeModel">
                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%;">
                    <span>+ New Chat</span>
                </button>
            </form>

            <!-- Search bar -->
            <input type="text" class="clay-inset clay-input" placeholder="Search conversations..." x-model="searchQuery" style="width:100%;">

            <!-- Conversations list (Grouped by date) -->
            <div style="flex-grow: 1; overflow-y: auto; margin-top: 10px; padding-right: 5px;">
                <template x-for="group in groupedConversations()" :key="group.label">
                    <div style="margin-bottom: 20px;">
                        <!-- Group Header divider -->
                        <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; padding-left: 8px; letter-spacing: 0.05em;" x-text="group.label"></div>
                        
                        <template x-for="chat in group.items" :key="chat.uuid">
                            <div class="history-item" :class="{ 'active': activeUuid === chat.uuid }">
                                <a :href="'/chats/' + chat.uuid" style="text-decoration:none; color:inherit; flex-grow:1; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; font-size:0.95rem; font-weight: 500;">
                                    <span x-text="chat.title"></span>
                                </a>
                                
                                <div style="display: flex; gap: 4px;">
                                    <!-- Pin toggle -->
                                    <button @click.prevent="togglePin(chat)" class="clay-btn" style="background:none; border:none; padding:4px; font-size:0.85rem; cursor:pointer;" :title="chat.pinned_at ? 'Unpin' : 'Pin'">
                                        <span x-text="chat.pinned_at ? '📌' : '📍'"></span>
                                    </button>
                                    <!-- Delete button -->
                                    <button @click.prevent="deleteChat(chat)" class="clay-btn" style="background:none; border:none; padding:4px; font-size:0.85rem; cursor:pointer;" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- User footer menu -->
            <div class="clay-card" style="padding: 12px 16px; border-radius: 20px; display: flex; align-items: center; justify-content: space-between; margin-top: auto; flex-shrink: 0; box-shadow: var(--clay-outer-shadow);">
                @auth
                    <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; overflow:hidden;">
                            <div style="display: flex; align-items: center; gap: 10px; overflow:hidden; flex-grow: 1;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #4a88ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size: 0.9rem; flex-grow: 1;">
                                    <div style="font-weight: 600; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 4px; flex-shrink: 0;">
                                <button @click="settingsModalOpen = true" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;" title="Settings">
                                    ⚙️
                                </button>
                                <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline-flex;">
                                    @csrf
                                    <button type="submit" class="clay-btn" style="background:none; border:none; padding:4px; font-size:1.1rem; cursor:pointer;" title="Logout">
                                        🚪
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
                @guest
                    <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); text-align: center; font-weight: 600;">Guest Mode (5 messages limit)</div>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('login') }}" class="clay-btn clay-btn-secondary" style="flex: 1; font-size: 0.82rem; padding: 8px 10px; border-radius: 12px; text-decoration: none; text-align: center;">Login</a>
                            <a href="{{ route('register') }}" class="clay-btn clay-btn-primary" style="flex: 1; font-size: 0.82rem; padding: 8px 10px; border-radius: 12px; text-decoration: none; text-align: center;">Register</a>
                        </div>
                    </div>
                @endguest
            </div>
        </aside>

        <!-- Main Chat Pane -->
        <main class="chat-pane">
            
            <!-- Header -->
            <header class="chat-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button @click="sidebarOpen = true" class="clay-btn clay-btn-secondary" style="border-radius: 12px; height:40px; padding: 0 12px; @media(min-width:769px){display:none;}">
                        ☰
                    </button>
                    
                    <!-- Active model indicator -->
                    <div style="font-weight: 600; font-size: 1.05rem;">
                        <span x-text="getModelName(activeModel)"></span>
                    </div>
                </div>

                <!-- Model selection dropdown -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <select class="clay-inset" x-model="activeModel" style="padding: 8px 16px; border-radius: 16px; font-weight: 500; font-size: 0.9rem;">
                        <template x-for="model in availableModels" :key="model.id">
                            <option :value="model.id" x-text="model.name" :selected="model.id === activeModel"></option>
                        </template>
                    </select>

                    <button @click="settingsModalOpen = true" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width:40px; height:40px; padding:0; display:flex; align-items:center; justify-content:center;">
                        ⚙️
                    </button>
                </div>
            </header>

            <!-- Chat messages box (Phase 5 + Phase 6 attachments) -->
            <div class="chat-messages" id="chat-messages-container" x-ref="messageContainer">
                
                <!-- If no conversation is active -->
                <template x-if="messages.length === 0">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; max-width: 500px; margin: 0 auto; gap: 20px;">
                        <div style="font-size: 4rem;">🔮</div>
                        <h2 style="font-weight: 700; font-size: 1.8rem; background: linear-gradient(135deg, #4a88ff 0%, #56ab2f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">What can I help you build today?</h2>
                        <p style="color: var(--text-muted); line-height: 1.6;">XrootAI handles multiple AI providers, code block syntax highlighting, file attachments, and complete chat persistence in a soft clay design.</p>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 10px;">
                            <button @click="prompt = 'Write a classic binary search algorithm in PHP'; $nextTick(() => sendMessage())" class="clay-btn clay-btn-secondary" style="border-radius:16px; font-size:0.85rem;">
                                "PHP Binary Search"
                            </button>
                            <button @click="prompt = 'Explain how Server-Sent Events (SSE) stream text to the browser'; $nextTick(() => sendMessage())" class="clay-btn clay-btn-secondary" style="border-radius:16px; font-size:0.85rem;">
                                "Explain SSE streams"
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Render active messages list -->
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="clay-card message-bubble" :class="msg.role === 'user' ? 'message-user' : 'message-assistant'">
                        <!-- Attachments preview row with visual thumbnails -->
                        <template x-if="msg.attachments && msg.attachments.length > 0">
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                                <template x-for="file in msg.attachments" :key="file.id">
                                    <a :href="'/storage/' + file.file_path" target="_blank" class="attach-pill" style="opacity: 0.95; padding: 6px 12px; display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                                        <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                            <img :src="'/storage/' + file.file_path" style="width: 28px; height: 28px; object-fit: cover; border-radius: 6px;" />
                                        </template>
                                        <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                            <span style="font-size: 1rem;" x-text="getFileIcon(file.mime_type)"></span>
                                        </template>
                                        <span x-text="file.file_name" style="font-weight: 500; font-size: 0.82rem; text-decoration: underline;"></span>
                                    </a>
                                </template>
                            </div>
                        </template>

                        <!-- Body text (markdown parsed) -->
                        <div x-html="renderMessageContent(msg.content)"></div>
                    </div>
                </template>

                <!-- Streaming active assistant bubble -->
                <template x-if="isStreaming && activeStreamText.length > 0">
                    <div class="clay-card message-bubble message-assistant">
                        <div x-html="renderMessageContent(activeStreamText)"></div>
                    </div>
                </template>

                <!-- Typing indicator -->
                <template x-if="isStreaming && activeStreamText.length === 0">
                    <div class="clay-card message-bubble message-assistant" style="padding: 16px 24px; display: flex; align-items: center; gap: 6px; border-radius: 20px;">
                        <span style="font-size:0.9rem; color:var(--text-muted);">Thinking</span>
                        <div style="display:flex; gap:4px; margin-top:2px;">
                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--text-muted); animation: bounce 1.4s infinite ease-in-out both;"></span>
                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--text-muted); animation: bounce 1.4s infinite ease-in-out both; animation-delay: 0.2s;"></span>
                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--text-muted); animation: bounce 1.4s infinite ease-in-out both; animation-delay: 0.4s;"></span>
                        </div>
                    </div>
                </template>
                
                <div x-ref="bottomScrollAnchor"></div>
            </div>

            <!-- Input Tray -->
            <footer class="chat-input-area">
                
                <!-- Upload status & preview bar with rich previews -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;" x-show="attachments.length > 0 || isUploading">
                    <template x-for="(file, idx) in attachments" :key="idx">
                        <div class="attach-pill" style="padding: 6px 12px; display: flex; align-items: center; gap: 8px;">
                            <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                <img :src="'/storage/' + file.file_path" style="width: 24px; height: 24px; object-fit: cover; border-radius: 4px;" />
                            </template>
                            <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                <span style="font-size: 0.95rem;" x-text="getFileIcon(file.mime_type)"></span>
                            </template>
                            <span x-text="file.file_name" style="font-weight: 500; font-size: 0.82rem;"></span>
                            <button @click="removeAttachment(idx)" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-weight:bold; margin-left:4px; font-size: 0.85rem;" title="Remove attachment">✕</button>
                        </div>
                    </template>
                    <div x-show="isUploading" class="attach-pill" style="border-style:dashed;">
                        ⏳ Uploading...
                    </div>
                </div>

                <!-- Input form bubble -->
                <div class="clay-card" style="padding: 10px 16px; border-radius: 24px; display: flex; align-items: flex-end; gap: 12px;">
                    
                    <!-- Paperclip upload button -->
                    <div style="position: relative;">
                        <button @click="$refs.fileInput.click()" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 42px; height: 42px; padding:0; display:flex; align-items:center; justify-content:center;" title="Attach File">
                            📎
                        </button>
                        <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" style="display: none;">
                    </div>

                    <!-- Main auto-expanding text input -->
                    <textarea 
                        class="clay-inset chat-textarea" 
                        placeholder="Ask XrootAI anything..." 
                        x-model="prompt"
                        @keydown.enter.prevent="if(!isStreaming) sendMessage()"
                        x-ref="promptInput"
                        rows="1"
                        style="flex-grow: 1; border: none; background: transparent; box-shadow: none; padding: 10px 0; font-size: 1.05rem;"
                    ></textarea>

                    <!-- Send button -->
                    <button 
                        @click="sendMessage()" 
                        class="clay-btn clay-btn-primary" 
                        style="border-radius: 50%; width: 44px; height: 44px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink: 0;"
                        :disabled="isStreaming || (!prompt.trim() && attachments.length === 0)"
                    >
                        ➔
                    </button>
                </div>
            </footer>
        </main>

        <!-- Settings Modal (Frosted Clay style) -->
        <div class="modal-overlay" x-show="settingsModalOpen" x-transition.opacity style="display: none;">
            <div class="clay-card modal-card" @click.away="settingsModalOpen = false">
                <div style="display:flex; justify-content:between; align-items:center; margin-bottom: 24px; border-bottom: 1px solid var(--clay-card-border); padding-bottom:12px;">
                    <h2 style="font-weight: 700; font-size: 1.4rem;">App Settings</h2>
                    <button @click="settingsModalOpen = false" class="clay-btn clay-btn-secondary" style="border-radius:50%; width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center; margin-left:auto;">
                        ✕
                    </button>
                </div>

                <!-- Theme / Prompt settings form -->
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="clay-input-group">
                        <label class="clay-input-label">Theme Mode</label>
                        <select name="theme" class="clay-inset" x-model="tempTheme" @change="darkMode = (tempTheme === 'dark' || (tempTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)); localStorage.setItem('darkMode', darkMode)">
                            <option value="system">System Default</option>
                            <option value="light">Light Mode</option>
                            <option value="dark">Dark Mode</option>
                        </select>
                    </div>

                    <div class="clay-input-group">
                        <label class="clay-input-label">Default Model</label>
                        <select name="default_model" class="clay-inset">
                            <template x-for="model in availableModels" :key="model.id">
                                <option :value="model.id" x-text="model.name" :selected="model.id === defaultModel"></option>
                            </template>
                        </select>
                    </div>

                    <div class="clay-input-group">
                        <label class="clay-input-label">System Instructions</label>
                        <textarea name="system_prompt" class="clay-inset" rows="3" style="resize:none;">{{ $settings->system_prompt }}</textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; margin-top: 10px;">
                        <button type="submit" class="clay-btn clay-btn-primary">
                            Save Settings
                        </button>
                    </div>
                </form>

                <!-- API Keys section -->
                <div style="margin-top: 32px; border-top: 1px solid var(--clay-card-border); padding-top: 24px;">
                    <h3 style="font-weight: 700; font-size: 1.15rem; margin-bottom: 8px;">Provider API Keys</h3>
                    <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom: 20px;">Keys are encrypted locally before storing and are never exposed to the client.</p>

                    <form action="{{ route('settings.keys') }}" method="POST">
                        @csrf
                        
                        <div class="clay-input-group">
                            <label class="clay-input-label">OpenAI Key</label>
                            <input type="password" name="keys[openai]" class="clay-inset clay-input" placeholder="{{ empty($apiKeys['openai']) ? 'sk-proj-...' : '•••••••• (Saved - enter new to change)' }}">
                        </div>

                        <div class="clay-input-group">
                            <label class="clay-input-label">Google Gemini Key</label>
                            <input type="password" name="keys[gemini]" class="clay-inset clay-input" placeholder="{{ empty($apiKeys['gemini']) ? 'AIzaSy...' : '•••••••• (Saved - enter new to change)' }}">
                        </div>

                        <div class="clay-input-group">
                            <label class="clay-input-label">Anthropic Claude Key</label>
                            <input type="password" name="keys[claude]" class="clay-inset clay-input" placeholder="{{ empty($apiKeys['claude']) ? 'sk-ant-...' : '•••••••• (Saved - enter new to change)' }}">
                        </div>

                        <div class="clay-input-group">
                            <label class="clay-input-label">DeepSeek Key</label>
                            <input type="password" name="keys[deepseek]" class="clay-inset clay-input" placeholder="{{ empty($apiKeys['deepseek']) ? 'sk-...' : '•••••••• (Saved - enter new to change)' }}">
                        </div>

                        <div class="clay-input-group">
                            <label class="clay-input-label">Ollama Host URL</label>
                            <input type="text" name="keys[ollama]" class="clay-inset clay-input" placeholder="http://localhost:11434" value="{{ $apiKeys['ollama'] ?? '' }}">
                        </div>

                        <div style="display:flex; justify-content:flex-end; margin-top: 10px;">
                            <button type="submit" class="clay-btn clay-btn-primary">
                                Save API Keys
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script variables mapped from Blade to client Alpine logic -->
    <script>
        function chatApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: false,
                settingsModalOpen: false,
                
                // Active chat states
                activeUuid: @json($activeConversation ? $activeConversation->uuid : null),
                activeModel: @json($activeConversation ? $activeConversation->model : ($settings->default_model ?? 'mock')),
                defaultModel: @json($settings->default_model ?? 'mock'),
                tempTheme: @json($settings->theme ?? 'system'),
                
                availableModels: @json($models),
                conversationsList: @json($conversations),
                
                messages: @json($messages),
                prompt: '',
                isStreaming: false,
                activeStreamText: '',
                
                // Attachments
                attachments: [],
                isUploading: false,
                searchQuery: '',

                initApp() {
                    // Automatically scroll to bottom on start if we have messages
                    this.scrollToBottom();

                    // Auto-adjust height of textarea input
                    const textarea = this.$refs.promptInput;
                    if (textarea) {
                        textarea.addEventListener('input', () => {
                            textarea.style.height = 'auto';
                            textarea.style.height = (textarea.scrollHeight) + 'px';
                        });
                    }

                    // Toggle system theme if system option is picked
                    if (this.tempTheme === 'system') {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    // Check for pending prompt and attachments (captured during new chat creation redirect)
                    const pendingPrompt = sessionStorage.getItem('pending_prompt');
                    const pendingAttachments = sessionStorage.getItem('pending_attachments');

                    if (pendingPrompt && this.activeUuid) {
                        this.prompt = pendingPrompt;
                        if (pendingAttachments) {
                            try {
                                this.attachments = JSON.parse(pendingAttachments);
                            } catch (e) {
                                console.error('Parse pending attachments failed:', e);
                            }
                        }
                        sessionStorage.removeItem('pending_prompt');
                        sessionStorage.removeItem('pending_attachments');
                        
                        this.$nextTick(() => {
                            this.sendMessage();
                        });
                    }
                },

                filteredConversations() {
                    if (!this.searchQuery) return this.conversationsList;
                    return this.conversationsList.filter(chat => 
                        chat.title.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },

                groupedConversations() {
                    const list = this.filteredConversations();
                    const groups = {
                        pinned: [],
                        today: [],
                        yesterday: [],
                        week: [],
                        older: []
                    };

                    const now = new Date();
                    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const startOfYesterday = new Date(startOfToday.getTime() - 24 * 60 * 60 * 1000);
                    const startOfWeek = new Date(startOfToday.getTime() - 7 * 24 * 60 * 60 * 1000);

                    list.forEach(chat => {
                        if (chat.pinned_at) {
                            groups.pinned.push(chat);
                            return;
                        }

                        const date = new Date(chat.updated_at);
                        if (date >= startOfToday) {
                            groups.today.push(chat);
                        } else if (date >= startOfYesterday) {
                            groups.yesterday.push(chat);
                        } else if (date >= startOfWeek) {
                            groups.week.push(chat);
                        } else {
                            groups.older.push(chat);
                        }
                    });

                    return [
                        { label: 'Pinned Chats', items: groups.pinned },
                        { label: 'Today', items: groups.today },
                        { label: 'Yesterday', items: groups.yesterday },
                        { label: 'Previous 7 Days', items: groups.week },
                        { label: 'Older', items: groups.older }
                    ].filter(g => g.items.length > 0);
                },

                getFileIcon(mimeType) {
                    if (!mimeType) return '📄';
                    if (mimeType.includes('pdf')) return '📕';
                    if (mimeType.includes('msword') || mimeType.includes('officedocument')) return '📘';
                    if (mimeType.includes('excel') || mimeType.includes('sheet')) return '📗';
                    if (mimeType.includes('zip') || mimeType.includes('compressed') || mimeType.includes('tar')) return '📦';
                    if (mimeType.includes('text/') || mimeType.includes('json') || mimeType.includes('javascript') || mimeType.includes('markdown')) return '📝';
                    if (mimeType.startsWith('audio/')) return '🎵';
                    if (mimeType.startsWith('video/')) return '🎥';
                    return '📄';
                },

                getModelName(modelId) {
                    const model = this.availableModels.find(m => m.id === modelId);
                    return model ? model.name : modelId;
                },

                renderMessageContent(content) {
                    if (!content) return '';
                    // Initialize markdown-it with highlight.js support
                    const md = window.markdownit({
                        html: false,
                        linkify: true,
                        typographer: true,
                        highlight: function (str, lang) {
                            if (lang && hljs.getLanguage(lang)) {
                                try {
                                    return '<pre class="hljs"><code>' +
                                           hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                                           '</code></pre>';
                                } catch (__) {}
                            }
                            return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>';
                        }
                    });
                    return md.render(content);
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                async handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.isUploading = true;
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("attachments.upload") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            this.attachments.push({
                                file_path: result.file_path,
                                file_name: result.file_name,
                                mime_type: result.mime_type,
                                file_size: result.file_size
                            });
                        }
                    } catch (error) {
                        console.error('Upload failed:', error);
                    } finally {
                        this.isUploading = false;
                        event.target.value = ''; // Reset input
                    }
                },

                removeAttachment(index) {
                    this.attachments.splice(index, 1);
                },

                async sendMessage() {
                    const cleanPrompt = this.prompt.trim();
                    if (!cleanPrompt && this.attachments.length === 0) return;
                    if (this.isStreaming) return;

                    // If no conversation exists yet, first create one
                    if (!this.activeUuid) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("chats.store") }}';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        const modelInput = document.createElement('input');
                        modelInput.type = 'hidden';
                        modelInput.name = 'model';
                        modelInput.value = this.activeModel;
                        form.appendChild(modelInput);

                        document.body.appendChild(form);
                        
                        // Capture prompt and attachments to local session before redirecting
                        sessionStorage.setItem('pending_prompt', this.prompt);
                        sessionStorage.setItem('pending_attachments', JSON.stringify(this.attachments));
                        
                        form.submit();
                        return;
                    }

                    // Append user message to active state instantly
                    const userMsgObj = {
                        role: 'user',
                        content: cleanPrompt,
                        attachments: [...this.attachments]
                    };
                    this.messages.push(userMsgObj);
                    
                    // Clear inputs and start streaming mode
                    const currentPrompt = this.prompt;
                    const currentAttachments = [...this.attachments];
                    this.prompt = '';
                    this.attachments = [];
                    
                    // Reset textarea size
                    const textarea = this.$refs.promptInput;
                    if (textarea) textarea.style.height = 'auto';

                    this.isStreaming = true;
                    this.activeStreamText = '';
                    this.scrollToBottom();

                    // Establish POST fetch body stream connection
                    try {
                        const response = await fetch(`/chats/${this.activeUuid}/stream`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt: currentPrompt,
                                attachments: currentAttachments
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Server returned error ' + response.status);
                        }

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        let buffer = '';

                        while (true) {
                            const { value, done } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            
                            // Process SSE line format
                            const lines = buffer.split('\n');
                            // Keep the last line if it is incomplete
                            buffer = lines.pop();

                            for (const line of lines) {
                                const trimmed = line.trim();
                                if (trimmed.startsWith('data: ')) {
                                    const dataStr = trimmed.substring(6);
                                    if (dataStr === '[DONE]') {
                                        break;
                                    } else {
                                        try {
                                            const parsed = JSON.parse(dataStr);
                                            this.activeStreamText += parsed.text;
                                            this.scrollToBottom();
                                        } catch (e) {}
                                    }
                                }
                            }
                        }

                        // Save streamed response fully to messages stack
                        this.messages.push({
                            role: 'assistant',
                            content: this.activeStreamText
                        });
                        this.isStreaming = false;
                        this.activeStreamText = '';
                        
                        // Update sidebar conversation list time / refresh items
                        this.refreshConversationsList();

                    } catch (err) {
                        console.error('SSE streaming error:', err);
                        this.messages.push({
                            role: 'assistant',
                            content: this.activeStreamText + '\n\n[Error: Connection lost or stream timed out.]'
                        });
                        this.isStreaming = false;
                        this.activeStreamText = '';
                    }
                },

                async togglePin(chat) {
                    try {
                        const response = await fetch(`/chats/${chat.uuid}/pin`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            chat.pinned_at = data.pinned ? new Date().toISOString() : null;
                            this.sortConversationsList();
                        }
                    } catch (e) {
                        console.error('Pin failed:', e);
                    }
                },

                async deleteChat(chat) {
                    if (!confirm('Are you sure you want to delete this conversation?')) return;

                    try {
                        const response = await fetch(`/chats/${chat.uuid}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.conversationsList = this.conversationsList.filter(c => c.uuid !== chat.uuid);
                            if (this.activeUuid === chat.uuid) {
                                window.location.href = '{{ route("chat") }}';
                            }
                        }
                    } catch (e) {
                        console.error('Delete failed:', e);
                    }
                },

                sortConversationsList() {
                    this.conversationsList.sort((a, b) => {
                        if (a.pinned_at && !b.pinned_at) return -1;
                        if (!a.pinned_at && b.pinned_at) return 1;
                        
                        // Sort pinned chats by pinned_at desc, unpinned by updated_at desc
                        if (a.pinned_at && b.pinned_at) {
                            return new Date(b.pinned_at) - new Date(a.pinned_at);
                        }
                        return new Date(b.updated_at) - new Date(a.updated_at);
                    });
                },

                async refreshConversationsList() {
                    try {
                        const response = await fetch('{{ route("chat") }}');
                        const html = await response.text();
                        
                        // Simple parser to extract list JSON if we wanted, or we just manually update the active item's update time
                        const current = this.conversationsList.find(c => c.uuid === this.activeUuid);
                        if (current) {
                            current.updated_at = new Date().toISOString();
                            this.sortConversationsList();
                        }
                    } catch (e) {
                        console.error('Refresh failed:', e);
                    }
                }
            }
        }

    </script>
</body>
</html>

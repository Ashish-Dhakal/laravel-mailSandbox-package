@extends('mail-sandbox::layout')

@section('content')
    <!-- Toolbar -->
    <div class="h-12 border-b border-gray-100 flex items-center px-4 sticky top-0 bg-white z-10">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mail-sandbox.index') }}" class="p-2 hover:bg-gray-100 rounded transition-colors group"
                title="Back to Inbox">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
        </div>
    </div>

    <div class="px-8 py-6">
        <!-- Subject -->
        <div class="mb-6 flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-normal text-gray-800">{{ $email->subject ?? '(No Subject)' }}</h1>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">Inbox</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-400">#{{ $email->id }}</span>
            </div>
        </div>

        <!-- Meta -->
        <div class="flex items-start mb-8">
            <div
                class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-lg flex-shrink-0">
                {{ strtoupper(substr($email->from, 0, 1)) }}
            </div>
            <div class="ml-4 flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-900">{{ explode('<', $email->from)[0] }}</span>
                        <span class="text-sm text-gray-500 ml-1"><{{ $email->from }}></span>
                    </div>
                    <div class="text-xs text-gray-500 flex items-center space-x-3">
                        <span>{{ $email->created_at->format('M d, Y, H:i') }}
                            ({{ $email->created_at->diffForHumans() }})</span>
                        <button class="hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                </path>
                            </svg>
                        </button>
                        <button class="hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    to {{ $email->to }}
                    @if($email->cc)
                    <div class="text-xs text-gray-400 mt-0.5">Cc: {{ $email->cc }}</div> @endif
                    @if($email->bcc)
                    <div class="text-xs text-gray-400 mt-0.5">Bcc: {{ $email->bcc }}</div> @endif
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="mb-4 flex space-x-4 border-b border-gray-100">
            <button onclick="switchTab('html')" id="tab-btn-html"
                class="px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 transition-all">HTML
                Version</button>
            <button onclick="switchTab('text')" id="tab-btn-text"
                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all">Plain
                Text</button>
            @if(!empty($email->attachments))
                <button onclick="switchTab('attachments')" id="tab-btn-attachments"
                    class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all">
                    Attachments ({{ count($email->attachments) }})
                </button>
            @endif
        </div>

        <!-- Body Panel -->
        <div id="panel-html" class="bg-white min-h-[500px] border border-gray-100 rounded-lg overflow-hidden">
            @if($email->body_html || $email->body_text)
                <iframe id="email-frame" 
                        src="{{ route('mail-sandbox.content', $email->id) }}" 
                        class="w-full min-h-[600px] border-none shadow-inner"
                        onload="resizeIframe(this)">
                </iframe>
            @else
                <div class="flex items-center justify-center h-64 text-gray-400 italic">
                    No content available for this message.
                </div>
            @endif
        </div>

        <div id="panel-text" class="hidden">
            <pre
                class="p-6 bg-gray-50 rounded-xl overflow-auto text-sm text-gray-700 font-mono leading-relaxed">{{ $email->body_text ?? 'No plain text content available.' }}</pre>
        </div>

        @if(!empty($email->attachments))
            <div id="panel-attachments" class="hidden space-y-6">
                <!-- Attachment Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($email->attachments as $index => $attachment)
                        @php
                            $isImage = str_starts_with($attachment['content_type'] ?? '', 'image/');
                        @endphp

                        <div class="group border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all bg-white flex flex-col">
                            <!-- Preview Area -->
                            <div class="h-32 bg-gray-50 flex items-center justify-center relative overflow-hidden border-b border-gray-100">
                                @if($isImage)
                                    <img src="{{ route('mail-sandbox.download', [$email->id, $index]) }}"
                                        alt="{{ $attachment['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="p-6 bg-blue-50 text-blue-600 rounded-full">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Hover Overlay for Download -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 flex items-center justify-center transition-all">
                                    <a href="{{ route('mail-sandbox.download', [$email->id, $index]) }}"
                                        target="_blank"
                                        class="p-2 bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all text-gray-700 hover:text-blue-600"
                                        title="Download / Open">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Footer Info -->
                            <div class="p-3 flex items-center justify-between">
                                <div class="min-w-0 pr-2">
                                    <div class="text-xs font-semibold text-gray-900 truncate" title="{{ $attachment['name'] }}">
                                        {{ $attachment['name'] }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">
                                        {{ round(($attachment['size'] ?? 0) / 1024, 1) }} KB
                                    </div>
                                </div>
                                <a href="{{ route('mail-sandbox.download', [$email->id, $index]) }}"
                                    class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function switchTab(tab) {
            // Panels
            document.getElementById('panel-html').classList.add('hidden');
            document.getElementById('panel-text').classList.add('hidden');
            if (document.getElementById('panel-attachments')) {
                document.getElementById('panel-attachments').classList.add('hidden');
            }

            // Buttons
            const buttons = ['html', 'text', 'attachments'];
            buttons.forEach(b => {
                const btn = document.getElementById('tab-btn-' + b);
                if (btn) {
                    btn.classList.remove('border-blue-600', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });

            // Show active
            document.getElementById('panel-' + tab).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-btn-' + tab);
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-blue-600', 'text-blue-600');
        }

        function resizeIframe(obj) {
            setTimeout(() => {
                obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
            }, 100);
        }
    </script>
@endsection
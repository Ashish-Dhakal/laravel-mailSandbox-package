<!-- Toolbar -->
<div class="h-12 border-b border-gray-100 flex items-center px-4 sticky top-0 bg-white z-10">
    <div class="flex items-center space-x-2">
        <button onclick="window.location.reload()"
            class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600" title="Refresh">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
        </button>
        <div class="h-6 w-px bg-gray-200"></div>
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2">Inbox</span>
    </div>

    <div class="ml-auto flex items-center space-x-2">
        <div class="text-[12px] text-gray-500 font-medium">
            @if($emails->total() > 0)
                {{ $emails->firstItem() }}-{{ $emails->lastItem() }} of {{ $emails->total() }}
            @else
                0 of 0
            @endif
        </div>
        <div class="flex items-center">
            @if($emails->onFirstPage())
                <span class="p-1 px-1.5 text-gray-300 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @else
                <a href="{{ $emails->previousPageUrl() }}"
                    class="p-1 px-1.5 hover:bg-gray-100 rounded text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif

            @if($emails->hasMorePages())
                <a href="{{ $emails->nextPageUrl() }}"
                    class="p-1 px-1.5 hover:bg-gray-100 rounded text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="p-1 px-1.5 text-gray-300 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Email List -->
<div class="flex flex-col">
    @forelse($emails as $email)
        <div onclick="window.location='{{ route('mail-sandbox.show', $email->id) }}'"
            class="flex items-center px-4 py-2 border-b border-gray-50 hover:shadow-md hover:z-10 bg-white cursor-pointer group transition-all">


            <div class="w-48 flex-shrink-0 font-semibold text-gray-900 truncate pr-4">
                {{ explode('@', $email->from)[0] }}
            </div>

            <div class="flex-1 flex min-w-0 pr-4">
                <span class="font-semibold text-gray-900 truncate mr-2">{{ $email->subject ?? '(No Subject)' }}</span>
                <span class="text-gray-500 truncate">-
                    {{ Str::limit(strip_tags($email->body_html ?: $email->body_text), 100) }}</span>
            </div>

            <div class="flex-shrink-0 text-xs font-semibold text-gray-500">
                {{ $email->created_at->isToday() ? $email->created_at->format('H:i') : $email->created_at->format('M d') }}
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                </path>
            </svg>
            <p class="text-lg">No emails found</p>
        </div>
    @endforelse
</div>
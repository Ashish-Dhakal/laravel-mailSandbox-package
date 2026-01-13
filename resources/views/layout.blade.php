<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Sandbox - Inbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        gmail: {
                            bg: '#f6f8fc',
                            sidebar: '#f6f8fc',
                            hover: '#eaf1fb',
                            active: '#d3e3fd',
                            border: '#f1f3f4',
                            primary: '#0b57d0'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-item-active {
            background-color: #d3e3fd;
            color: #041e49;
            font-weight: 600;
        }

        .sidebar-item:hover:not(.sidebar-item-active) {
            background-color: #eaf1fb;
        }
    </style>
</head>

<body class="h-full overflow-hidden flex flex-col bg-gmail-bg">

    <!-- Header -->
    <header class="h-16 flex items-center px-4 bg-gmail-bg flex-shrink-0">
        <div class="flex items-center w-64">
            <a href="{{ route('mail-sandbox.index') }}" class="flex items-center space-x-2">
                <img src="{{ route('mail-sandbox.logo') }}" alt="Mail Sandbox" class="h-14 w-auto">
            </a>
        </div>

        <div class="flex-1 max-w-3xl mx-4">
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search mail"
                    class="block w-full pl-10 pr-10 py-3 bg-gmail-hover border-transparent rounded-lg leading-5 text-gray-900 placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-1 focus:ring-gray-200 focus:border-transparent transition-all sm:text-sm">
                <div id="searchLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                    <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="ml-auto flex items-center space-x-2">
            <button onclick="openModal('clearInboxModal')"
                class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                Clear Inbox
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 flex flex-col px-4 py-2 flex-shrink-0 bg-gmail-bg">
            <nav class="space-y-1">
                <a href="{{ route('mail-sandbox.index') }}"
                    class="sidebar-item {{ Route::is('mail-sandbox.index') ? 'sidebar-item-active' : '' }} flex items-center justify-between px-4 py-2 rounded-r-full transition-colors mr-2">
                    <div class="flex items-center space-x-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <span>Inbox</span>
                    </div>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main
            class="flex-1 flex flex-col bg-white rounded-2xl mb-4 mr-4 shadow-sm overflow-hidden border border-gmail-border">
            <div class="flex-1 overflow-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Modal Background -->
    <div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-30 hidden z-40 transition-opacity"></div>

    <!-- Clear Inbox Modal -->
    <div id="clearInboxModal"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white rounded-xl shadow-2xl z-50 hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Clear Inbox?</h3>
            <p class="text-sm text-gray-600 mb-6">This will permanently delete all captured email messages. This action
                cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal('clearInboxModal')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Cancel
                </button>
                <form action="{{ route('mail-sandbox.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                        Clear All
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div id="successToast"
            class="fixed bottom-8 left-8 flex items-center space-x-3 bg-gray-900 text-white px-6 py-4 rounded-lg shadow-2xl z-50 transform transition-transform duration-500 translate-y-24">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('successToast');
                toast.classList.remove('translate-y-24');
                toast.classList.add('translate-y-0');
                setTimeout(() => {
                    toast.classList.remove('translate-y-0');
                    toast.classList.add('translate-y-24');
                }, 4000);
            }, 100);
        </script>
    @endif

    <script>
        function openModal(id) {
            document.getElementById('modalOverlay').classList.remove('hidden');
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById('modalOverlay').classList.add('hidden');
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</body>

</html>
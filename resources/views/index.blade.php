@extends('mail-sandbox::layout')

@section('content')
    <div id="email-list-container">
        @include('mail-sandbox::partials.email-list')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const searchLoading = document.getElementById('searchLoading');
            const container = document.getElementById('email-list-container');
            let debounceTimer;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                searchLoading.classList.remove('hidden');

                debounceTimer = setTimeout(() => {
                    const query = searchInput.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            container.innerHTML = html;
                            searchLoading.classList.add('hidden');
                            // Update the URL without reloading the page
                            window.history.pushState({}, '', url);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            searchLoading.classList.add('hidden');
                        });
                }, 300);
            });
        });
    </script>
@endsection
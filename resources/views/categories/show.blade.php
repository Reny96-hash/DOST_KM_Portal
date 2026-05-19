@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $name }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-folder-open text-primary"></i> {{ $name }}</h2>
            <div>
                @if (auth()->user()->isAdmin())
                    <form method="POST" action="{{ url('/admin/documents/bulk-delete') }}" id="bulk-delete-form"
                        class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids" id="bulk-delete-ids" value="">
                        <button type="submit" id="bulk-delete-btn" class="btn btn-danger btn-sm me-2" disabled>
                            <i class="fas fa-trash-alt"></i> Delete Selected
                        </button>
                        <span class="me-2">
                            <input type="checkbox" id="select-all"> <label for="select-all" class="small">Select
                                All</label>
                        </span>
                    </form>
                @endif

                <!-- Create Dropdown -->
                <div class="dropdown d-inline-block me-2">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-plus"></i> Create
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('content.create') }}?type=article"><i
                                    class="fas fa-newspaper"></i> Article</a></li>
                        <li><a class="dropdown-item" href="{{ route('content.create') }}?type=file"><i
                                    class="fas fa-upload"></i> File</a></li>
                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addLinkModal"><i
                                    class="fas fa-link"></i> Link</a></li>
                    </ul>
                </div>

                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i
                        class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <!-- Tabs, Sorting & Search (all in one row) -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link {{ $type == 'all' ? 'active' : '' }}"
                        href="{{ route('category.show', ['name' => $name, 'type' => 'all', 'search' => request('search'), 'sort' => request('sort')]) }}">All</a>
                </li>
                <li class="nav-item"><a class="nav-link {{ $type == 'article' ? 'active' : '' }}"
                        href="{{ route('category.show', ['name' => $name, 'type' => 'article', 'search' => request('search'), 'sort' => request('sort')]) }}">Articles</a>
                </li>
                <li class="nav-item"><a class="nav-link {{ $type == 'file' ? 'active' : '' }}"
                        href="{{ route('category.show', ['name' => $name, 'type' => 'file', 'search' => request('search'), 'sort' => request('sort')]) }}">Files</a>
                </li>
                <li class="nav-item"><a class="nav-link {{ $type == 'link' ? 'active' : '' }}"
                        href="{{ route('category.show', ['name' => $name, 'type' => 'link', 'search' => request('search'), 'sort' => request('sort')]) }}">Links</a>
                </li>
                <li class="nav-item"><a class="nav-link {{ $type == 'question' ? 'active' : '' }}"
                        href="{{ route('category.show', ['name' => $name, 'type' => 'question', 'search' => request('search'), 'sort' => request('sort')]) }}">Questions</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <!-- Autocomplete Search (relative wrapper prevents overlap) -->
                <div style="position: relative; width: 260px;">
                    <div class="input-group input-group-sm">
                        <input type="text" id="category-search" class="form-control"
                            placeholder="Search in this category..." autocomplete="off" value="{{ request('search') }}">
                        <button class="btn btn-primary btn-sm" id="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                    <div id="search-suggestions" class="list-group"
                        style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; display: none;">
                    </div>
                </div>
                <!-- Sorting Buttons -->
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('category.show', array_merge(['name' => $name, 'type' => $type, 'search' => request('search')], ['sort' => 'newest'])) }}"
                        class="btn btn-outline-secondary {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">Newest</a>
                    <a href="{{ route('category.show', array_merge(['name' => $name, 'type' => $type, 'search' => request('search')], ['sort' => 'oldest'])) }}"
                        class="btn btn-outline-secondary {{ request('sort') == 'oldest' ? 'active' : '' }}">Oldest</a>
                </div>
            </div>
        </div>

        <!-- Cards Grid -->
        @if ($documents->count())
            <div class="row">
                @foreach ($documents as $doc)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm position-relative">
                            @if (auth()->user()->isAdmin())
                                <div class="position-absolute top-0 start-0 mt-2 ms-2" style="z-index: 5;">
                                    <input type="checkbox" class="doc-select" value="{{ $doc->doc_id }}">
                                </div>
                            @endif
                            <div class="card-body"
                                style="padding-top: 0.75rem; @if (auth()->user()->isAdmin()) padding-left: 2.2rem; @endif">
                                <h6 class="card-title">{{ Str::limit($doc->doc_title, 50) }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($doc->doc_description, 80) }}</p>
                                @if ($doc->is_question)
                                    <div class="small text-muted mb-2">
                                        <i class="far fa-comment"></i> {{ $doc->allComments->count() }} answers
                                    </div>
                                @endif
                                <div class="mt-2">
                                    @if ($doc->content_type == 'link')
                                        <a href="{{ $doc->content_rich }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary" title="Open">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('document.show', $doc->doc_id) }}"
                                            class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer text-muted small">
                                <i class="fas fa-user"></i> {{ $doc->user->user_first_name }} •
                                {{ $doc->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $documents->links() }}
            </div>
        @else
            <div class="alert alert-info">No {{ $type == 'question' ? 'questions' : 'documents' }} in this category.</div>
        @endif
    </div>

    <!-- Modal for adding a link -->
    @include('modals.add-link')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        @if (auth()->user()->isAdmin())
            // Bulk delete logic
            $(document).ready(function() {
                let deleteBtn = $('#bulk-delete-btn');
                let idsInput = $('#bulk-delete-ids');
                let selectAll = $('#select-all');

                function updateDeleteButton() {
                    let selected = $('.doc-select:checked').length;
                    deleteBtn.prop('disabled', selected === 0);
                    idsInput.val($('.doc-select:checked').map(function() {
                        return $(this).val();
                    }).get().join(','));
                }

                $(document).on('change', '.doc-select', updateDeleteButton);

                selectAll.on('change', function() {
                    $('.doc-select').prop('checked', $(this).prop('checked'));
                    updateDeleteButton();
                });

                $('#bulk-delete-form').on('submit', function(e) {
                    let ids = idsInput.val();
                    if (ids === '') {
                        e.preventDefault();
                        alert('No documents selected.');
                    } else if (!confirm('Delete selected documents? This cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
        @endif

        // Autocomplete search within category (identical to dashboard, but category-specific)
        let searchInput = $('#category-search');
        let suggestions = $('#search-suggestions');
        let timeout = null;
        let categoryName = "{{ $name }}";

        function submitSearch() {
            let query = searchInput.val();
            let url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }

        $('#search-btn').on('click', submitSearch);
        searchInput.on('keypress', function(e) {
            if (e.which == 13) submitSearch();
        });

        searchInput.on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                suggestions.hide();
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                $.ajax({
                    url: '/search/autocomplete/category/' + encodeURIComponent(categoryName),
                    data: {
                        q: query
                    },
                    success: function(data) {
                        suggestions.empty();
                        if (data.length) {
                            $.each(data, function(i, item) {
                                suggestions.append(
                                    `<a href="/document/${item.doc_id}" class="list-group-item list-group-item-action">${item.doc_title}</a>`
                                );
                            });
                            suggestions.show();
                        } else {
                            suggestions.hide();
                        }
                    }
                });
            }, 300);
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#category-search, #search-suggestions').length) suggestions.hide();
        });
    </script>
@endsection

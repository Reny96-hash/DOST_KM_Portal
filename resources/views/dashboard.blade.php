@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3">

        <!-- Welcome Bar -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold">Welcome, {{ auth()->user()->user_first_name }}
                                {{ auth()->user()->user_last_name }}</span>
                            <span class="text-muted ms-2 small">Clearance: {{ auth()->user()->security_clearance }}</span>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <div>
                                <a href="{{ route('upload.form') }}" class="btn btn-sm btn-primary me-1"><i
                                        class="fas fa-upload"></i> Upload</a>
                                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary"><i
                                        class="fas fa-users"></i> Users</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-2 mb-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center py-2">
                    <div class="card-body p-2">
                        <i class="fas fa-file-alt text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ $approvedDocumentsCount ?? 0 }}</h4>
                        <small class="text-muted">Approved Docs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center py-2">
                    <div class="card-body p-2">
                        <i class="fas fa-clock text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ $pendingDocumentsCount ?? 0 }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center py-2">
                    <div class="card-body p-2">
                        <i class="fas fa-image text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ $imagesCount ?? 0 }}</h4>
                        <small class="text-muted">Images</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center py-2">
                    <div class="card-body p-2">
                        <i class="fas fa-user-folder text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ $myDocumentsCount ?? 0 }}</h4>
                        <small class="text-muted">My Documents</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin/KM Champion extra stats -->
        @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm text-center py-2">
                        <div class="card-body p-2">
                            <i class="fas fa-users text-secondary fa-lg"></i>
                            <h4 class="mb-0 mt-1 fw-bold">{{ $totalUsers ?? 0 }}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm text-center py-2">
                        <div class="card-body p-2">
                            <i class="fas fa-star text-secondary fa-lg"></i>
                            <h4 class="mb-0 mt-1 fw-bold">{{ $kmChampionCount ?? 0 }}</h4>
                            <small class="text-muted">KM Champions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm text-center py-2">
                        <div class="card-body p-2">
                            <i class="fas fa-shield-alt text-secondary fa-lg"></i>
                            <h4 class="mb-0 mt-1 fw-bold">{{ $adminCount ?? 0 }}</h4>
                            <small class="text-muted">Admins</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm text-center py-2">
                        <div class="card-body p-2">
                            <i class="fas fa-user text-secondary fa-lg"></i>
                            <h4 class="mb-0 mt-1 fw-bold">{{ $staffCount ?? 0 }}</h4>
                            <small class="text-muted">Staff</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recently Added & Most Viewed -->
        <div class="row mb-3 g-2">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 border-0"><small class="fw-semibold text-muted">RECENTLY
                            ADDED</small></div>
                    <div class="card-body p-2">
                        @if (isset($recentDocuments) && $recentDocuments->count())
                            @foreach ($recentDocuments as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-alt text-secondary fa-sm"></i>
                                        <span class="small">{{ Str::limit($doc->doc_title, 35) }}</span>
                                    </div>
                                    <small class="text-muted">{{ $doc->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small text-center py-2 mb-0">No approved documents yet</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 border-0"><small class="fw-semibold text-muted">MOST
                            VIEWED</small></div>
                    <div class="card-body p-2">
                        @if (isset($mostViewedDocuments) && $mostViewedDocuments->count())
                            @foreach ($mostViewedDocuments as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-chart-simple text-secondary fa-sm"></i>
                                        <span class="small">{{ Str::limit($doc->doc_title, 35) }}</span>
                                    </div>
                                    <small class="text-muted">{{ $doc->view_count }} views</small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small text-center py-2 mb-0">No view data yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Repository with Dynamic Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 border-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="fw-semibold text-muted">DOCUMENT REPOSITORY (Approved Only)</small>
                            <div class="d-flex gap-2 align-items-center">
                                <select id="sort-select" class="form-select form-select-sm" style="width:130px">
                                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                                        Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                    </option>
                                </select>
                                <input type="text" id="search-input" class="form-control form-control-sm"
                                    placeholder="Search..." style="width:180px" value="{{ request('search') }}">
                                <select id="category-select" class="form-select form-select-sm" style="width:130px">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <button id="reset-btn" class="btn btn-sm btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="document-table-container">
                        @include('partials.document-table', ['paginatedDocuments' => $paginatedDocuments])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and AJAX script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchDocuments(page = 1) {
            let search = $('#search-input').val();
            let category = $('#category-select').val();
            let sort = $('#sort-select').val();

            $.ajax({
                url: "{{ route('documents.fetch') }}",
                type: "GET",
                data: {
                    search: search,
                    category: category,
                    sort: sort,
                    page: page
                },
                success: function(response) {
                    $('#document-table-container').html(response.html);
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }
            });
        }

        $(document).ready(function() {
            $('#search-input').on('keyup', function() {
                fetchDocuments();
            });
            $('#category-select').on('change', function() {
                fetchDocuments();
            });
            $('#sort-select').on('change', function() {
                fetchDocuments();
            });
            $('#reset-btn').on('click', function() {
                $('#search-input').val('');
                $('#category-select').val('');
                $('#sort-select').val('newest');
                fetchDocuments();
            });

            // Handle pagination clicks (dynamic)
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URL(url).searchParams.get('page');
                if (page) fetchDocuments(page);
            });
        });
    </script>
@endsection

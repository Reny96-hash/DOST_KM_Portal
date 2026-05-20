@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <!-- Welcome, Search & Create (inline) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0">Welcome, {{ auth()->user()->user_first_name }}</h5>
                            <small class="text-muted">{{ auth()->user()->user_role }} | Clearance:
                                {{ auth()->user()->security_clearance }}</small>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" id="global-search" class="form-control form-control-sm"
                                    placeholder="Search documents..." autocomplete="off">
                                <button class="btn btn-primary btn-sm" id="global-search-btn"><i
                                        class="fas fa-search"></i></button>
                            </div>
                            <div id="search-suggestions" class="list-group mt-1"
                                style="position: absolute; z-index: 1000; width: calc(100% - 45px); display: none;"></div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recently Added & Most Viewed Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">RECENTLY ADDED</div>
                        <div class="card-body p-2">
                            @forelse($recentDocuments ?? [] as $doc)
                                <a href="{{ route('document.show', $doc->doc_id) }}" class="text-decoration-none text-dark">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <div><i class="fas fa-file-alt text-secondary me-1"></i>
                                            {{ Str::limit($doc->doc_title, 40) }}</div>
                                        <small class="text-muted">{{ $doc->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted small text-center py-2 mb-0">No approved documents yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">MOST VIEWED</div>
                        <div class="card-body p-2">
                            @forelse($mostViewedDocuments ?? [] as $doc)
                                <a href="{{ route('document.show', $doc->doc_id) }}" class="text-decoration-none text-dark">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <div>
                                            <i class="fas fa-chart-simple text-secondary me-1"></i>
                                            {{ Str::limit($doc->doc_title, 40) }}
                                        </div>
                                        <small class="text-muted">{{ $doc->view_count }} views</small>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted small text-center py-2 mb-0">No view data yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts (only for Admin and KM Champion) -->
            @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
                <div class="row mb-4 g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">Documents by Category</div>
                            <div class="card-body">
                                <canvas id="categoryChart" style="height: 180px;"></canvas>
                                <div id="categoryNoData" class="text-muted text-center mt-2" style="display: none;">No data
                                    yet. Add approved documents with categories.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">Documents by Security Clearance</div>
                            <div class="card-body">
                                <canvas id="clearanceChart" style="height: 180px;"></canvas>
                                <div id="clearanceNoData" class="text-muted text-center mt-2" style="display: none;">No data
                                    yet.</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Staff: their own documents (paginated table) -->
            <!-- Staff Dashboard -->
            @if (!auth()->user()->isAdmin() && !auth()->user()->isKmChampion())
                <!-- Personal Document Summary -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-2">
                            <div class="card-body p-2">
                                <i class="fas fa-file-alt text-secondary fa-lg"></i>
                                <h4 class="mb-0 mt-1 fw-bold">{{ $myDocsSummary['drafts'] ?? 0 }}</h4>
                                <small class="text-muted">Drafts</small>
                                <br><a href="{{ route('documents.my-uploads', ['type' => 'all', 'status' => 'draft']) }}"
                                    class="small">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-2">
                            <div class="card-body p-2">
                                <i class="fas fa-clock text-secondary fa-lg"></i>
                                <h4 class="mb-0 mt-1 fw-bold">{{ $myDocsSummary['pending'] ?? 0 }}</h4>
                                <small class="text-muted">Pending Review</small>
                                <br><a href="{{ route('documents.my-uploads', ['type' => 'all', 'status' => 'pending']) }}"
                                    class="small">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-2">
                            <div class="card-body p-2">
                                <i class="fas fa-check-circle text-secondary fa-lg"></i>
                                <h4 class="mb-0 mt-1 fw-bold">{{ $myDocsSummary['approved'] ?? 0 }}</h4>
                                <small class="text-muted">Approved</small>
                                <br><a
                                    href="{{ route('documents.my-uploads', ['type' => 'all', 'status' => 'approved']) }}"
                                    class="small">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm text-center py-2">
                            <div class="card-body p-2">
                                <i class="fas fa-times-circle text-secondary fa-lg"></i>
                                <h4 class="mb-0 mt-1 fw-bold">{{ $myDocsSummary['rejected'] ?? 0 }}</h4>
                                <small class="text-muted">Rejected</small>
                                <br><a
                                    href="{{ route('documents.my-uploads', ['type' => 'all', 'status' => 'rejected']) }}"
                                    class="small">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity on Your Documents -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">Recent Activity on Your Documents</div>
                    <div class="card-body p-0">
                        @if ($activities->count())
                            <div class="list-group list-group-flush">
                                @foreach ($activities as $activity)
                                    <a href="{{ route('document.show', $activity->doc_id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            @if ($activity->type == 'comment')
                                                <i class="fas fa-comment text-info me-3 fa-fw"></i>
                                            @elseif($activity->type == 'like')
                                                <i class="fas fa-thumbs-up text-success me-3 fa-fw"></i>
                                            @else
                                                <i class="fas fa-info-circle text-secondary me-3 fa-fw"></i>
                                            @endif
                                            <div class="flex-grow-1">
                                                <strong>{{ $activity->doc_title }}</strong><br>
                                                <small>{{ $activity->message }}</small>
                                            </div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3 mb-0">No recent activity on your documents.</p>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal for adding a link -->
    @include('modals.add-link')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Autocomplete search
        let searchInput = $('#global-search');
        let suggestions = $('#search-suggestions');
        let timeout = null;
        searchInput.on('keyup', function() {
            let query = $(this).val();
            if (query.length < 2) {
                suggestions.hide();
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('search.autocomplete') }}",
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
            if (!$(e.target).closest('#global-search, #search-suggestions').length) suggestions.hide();
        });
    </script>

    @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const catLabels = @json($categoryStats->keys());
            const catData = @json($categoryStats->values());
            const clearanceLabels = @json($clearanceStats->keys());
            const clearanceData = @json($clearanceStats->values());

            if (catLabels.length && catData.length) {
                new Chart(document.getElementById('categoryChart'), {
                    type: 'bar',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            label: 'Documents',
                            data: catData,
                            backgroundColor: '#1d799d'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            } else {
                document.getElementById('categoryChart').style.display = 'none';
                document.getElementById('categoryNoData').style.display = 'block';
            }

            if (clearanceLabels.length && clearanceData.length) {
                new Chart(document.getElementById('clearanceChart'), {
                    type: 'pie',
                    data: {
                        labels: clearanceLabels,
                        datasets: [{
                            data: clearanceData,
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            } else {
                document.getElementById('clearanceChart').style.display = 'none';
                document.getElementById('clearanceNoData').style.display = 'block';
            }
        </script>
    @endif
@endsection

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
                                <a href="{{ route('upload.form') }}" class="btn btn-sm btn-primary me-1">
                                    <i class="fas fa-upload"></i> Upload
                                </a>
                                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-users"></i> Users
                                </a>
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
                        <small class="text-muted">Approved Documents</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center py-2">
                    <div class="card-body p-2">
                        <i class="fas fa-clock text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ $pendingDocumentsCount ?? 0 }}</h4>
                        <small class="text-muted">Pending Approval</small>
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
                        <i class="fas fa-tags text-secondary fa-lg"></i>
                        <h4 class="mb-0 mt-1 fw-bold">{{ count($categories) }}</h4>
                        <small class="text-muted">Categories</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <!-- Second Row Stats - Only for Admin/KM Champion -->
        @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm text-center py-2">
                        <div class="card-body p-2">
                            <i class="fas fa-users text-secondary fa-lg"></i>
                            <h4 class="mb-0 mt-1 fw-bold">{{ $totalUsers ?? 1 }}</h4>
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
                    <div class="card-header bg-white py-2 border-0">
                        <small class="fw-semibold text-muted">RECENTLY ADDED</small>
                    </div>
                    <div class="card-body p-2">
                        @if (isset($recentDocuments) && $recentDocuments->count() > 0)
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
                    <div class="card-header bg-white py-2 border-0">
                        <small class="fw-semibold text-muted">MOST VIEWED</small>
                    </div>
                    <div class="card-body p-2">
                        @if (isset($mostViewedDocuments) && $mostViewedDocuments->count() > 0)
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

        <!-- Document Repository Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 border-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="fw-semibold text-muted">DOCUMENT REPOSITORY (Approved Only)</small>
                            <form method="GET" action="{{ route('search') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search..." value="{{ request('search') }}" style="width: 180px;">
                                <select name="category" class="form-select form-select-sm" style="width: 130px;">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                                @if (request('search') || request('category'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if (isset($paginatedDocuments) && $paginatedDocuments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Title</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Classification</th>
                                            <th>Date</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginatedDocuments as $doc)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="fw-medium">{{ $doc->doc_title }}</span>
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($doc->doc_description, 55) }}</small>
                                                </td>
                                                <td class="align-middle">{{ strtoupper($doc->doc_file_type) }}</td>
                                                <td class="align-middle">{{ $doc->doc_category }}</td>
                                                <td class="align-middle">{{ $doc->security_clearance }}</td>
                                                <td class="align-middle">{{ $doc->created_at->format('Y-m-d') }}</td>
                                                <td class="align-middle text-end pe-3">
                                                    @if (in_array($doc->doc_file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                                        <button type="button" class="btn btn-sm btn-outline-info me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#previewModal{{ $doc->doc_id }}"
                                                            title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="previewModal{{ $doc->doc_id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h6 class="modal-title">{{ $doc->doc_title }}</h6>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ route('preview', $doc->doc_id) }}"
                                                                            class="img-fluid"
                                                                            alt="{{ $doc->doc_title }}">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ route('download', $doc->doc_id) }}"
                                                                            class="btn btn-sm btn-primary">Download</a>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($doc->doc_file_type == 'pdf')
                                                        <a href="{{ route('download', $doc->doc_id) }}"
                                                            class="btn btn-sm btn-outline-info me-1" title="Download PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('download', $doc->doc_id) }}"
                                                        class="btn btn-sm btn-outline-secondary" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-2 border-top">
                                {{ $paginatedDocuments->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-2"></i>
                                <p class="text-muted small mb-0">No approved documents yet.</p>
                                <a href="{{ route('upload.form') }}" class="btn btn-sm btn-primary mt-2">Upload
                                    Document</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $name }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fas fa-folder-open text-primary"></i> {{ $name }}</h2>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i> Create in this category
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item"
                            href="{{ route('content.create') }}?type=article&category={{ $name }}"><i
                                class="fas fa-newspaper"></i> Article</a></li>
                    <li><a class="dropdown-item"
                            href="{{ route('content.create') }}?type=file&category={{ $name }}"><i
                                class="fas fa-upload"></i> File</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addLinkModal"
                            data-category="{{ $name }}"><i class="fas fa-link"></i> Link</a></li>
                </ul>
            </div>
        </div>
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link {{ $type == 'all' ? 'active' : '' }}"
                    href="{{ route('category.show', ['name' => $name, 'type' => 'all']) }}">All</a></li>
            <li class="nav-item"><a class="nav-link {{ $type == 'article' ? 'active' : '' }}"
                    href="{{ route('category.show', ['name' => $name, 'type' => 'article']) }}">Articles</a></li>
            <li class="nav-item"><a class="nav-link {{ $type == 'file' ? 'active' : '' }}"
                    href="{{ route('category.show', ['name' => $name, 'type' => 'file']) }}">Files</a></li>
            <li class="nav-item"><a class="nav-link {{ $type == 'link' ? 'active' : '' }}"
                    href="{{ route('category.show', ['name' => $name, 'type' => 'link']) }}">Links</a></li>
            <li class="nav-item"><a class="nav-link {{ $type == 'question' ? 'active' : '' }}"
                    href="{{ route('category.show', ['name' => $name, 'type' => 'question']) }}">Questions</a></li>
        </ul>

        @if ($documents->count())
            <div class="row">
                @foreach ($documents as $doc)
                    <div class="col-md-4 col-lg-3 mb-4">
                        @if ($doc->content_type == 'link')
                            <!-- Link card – no click navigation, only copy button -->
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($doc->doc_title, 50) }}</h6>
                                    <p class="card-text text-muted small">{{ Str::limit($doc->doc_description, 80) }}</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary copy-link-btn"
                                            data-link="{{ $doc->content_rich }}"><i class="fas fa-copy"></i> Copy
                                            Link</button>
                                    </div>
                                </div>
                                <div class="card-footer text-muted small">
                                    <i class="fas fa-user"></i> {{ $doc->user->user_first_name }} •
                                    {{ $doc->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @else
                            <!-- Article / File / Question – whole card clickable -->
                            <a href="{{ route('document.show', $doc->doc_id) }}" class="text-decoration-none text-dark">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        @if ($doc->is_question)
                                            <div class="small text-muted mb-2">
                                                <i class="far fa-comment"></i> {{ $doc->allComments->count() }} answers
                                            </div>
                                        @endif
                                        <h6 class="card-title">{{ Str::limit($doc->doc_title, 50) }}</h6>
                                        <p class="card-text text-muted small">{{ Str::limit($doc->doc_description, 80) }}
                                        </p>
                                    </div>
                                    <div class="card-footer text-muted small">
                                        <i class="fas fa-user"></i> {{ $doc->user->user_first_name }} •
                                        {{ $doc->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @endif
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

    <!-- Copy Link Modal -->
    <div class="modal fade" id="copyLinkModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="linkToCopy" class="form-control" readonly>
                    <button class="btn btn-primary mt-2 w-100" id="copyLinkBtn">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.copy-link-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // prevent any parent click
                const link = this.dataset.link;
                document.getElementById('linkToCopy').value = link;
                new bootstrap.Modal(document.getElementById('copyLinkModal')).show();
            });
        });
        document.getElementById('copyLinkBtn')?.addEventListener('click', function() {
            const input = document.getElementById('linkToCopy');
            input.select();
            document.execCommand('copy');
            alert('Link copied to clipboard!');
            bootstrap.Modal.getInstance(document.getElementById('copyLinkModal')).hide();
        });
    </script>
@endsection

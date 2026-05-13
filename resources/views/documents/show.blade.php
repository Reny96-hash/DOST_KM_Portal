@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('category.show', $document->doc_category) }}">{{ $document->doc_category }}</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($document->doc_title, 50) }}</li>
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $document->doc_title }}</h4>
                <div>
                    @if ($document->content_type == 'link')
                        <button class="btn btn-sm btn-outline-secondary copy-link-btn"
                            data-link="{{ $document->content_rich }}"><i class="fas fa-copy"></i> Copy Link</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $document->doc_description }}</p>
                <hr>
                @if ($document->content_type == 'article')
                    <div class="article-content">
                        {!! $document->content_rich !!}
                    </div>
                    <div class="mt-3">
                        <small>Posted by {{ $document->user->user_first_name }} on
                            {{ $document->created_at->format('F d, Y') }}</small>
                    </div>
                @elseif($document->content_type == 'file')
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-4x text-secondary mb-3"></i>
                        <p><strong>{{ $document->doc_file_name }}</strong> ({{ round($document->doc_file_size, 2) }} KB)
                        </p>
                        <a href="{{ route('download', $document->doc_id) }}" class="btn btn-primary"><i
                                class="fas fa-download"></i> Download File</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-link fa-4x text-secondary mb-3"></i>
                        <p><strong>External Link</strong></p>
                        <a href="{{ $document->content_rich }}" target="_blank" class="btn btn-primary">Open Link</a>
                        <button class="btn btn-outline-secondary copy-link-btn"
                            data-link="{{ $document->content_rich }}">Copy Link</button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Comments Section (same as before, but ensure clearance checks) -->
        <div class="card mt-4">
            <div class="card-header">Comments & Answers</div>
            <div class="card-body">
                @foreach ($document->comments as $comment)
                    @include('partials.comment', ['comment' => $comment])
                @endforeach
                <form action="{{ route('comment.store', $document->doc_id) }}" method="POST" class="mt-3">
                    @csrf
                    <textarea name="comment_text" class="form-control" rows="2" placeholder="Write a comment..."></textarea>
                    <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
                </form>
            </div>
        </div>
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
            btn.addEventListener('click', function() {
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

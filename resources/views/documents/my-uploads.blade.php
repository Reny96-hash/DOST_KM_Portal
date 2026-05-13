@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">My Uploads</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">My Uploads</h3>
            <div class="dropdown">
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
        </div>
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type', 'all') == 'all' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'all']) }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'article' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'article']) }}">Articles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'file' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'file']) }}">Files</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'link' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'link']) }}">Links</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'question' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'question']) }}">Questions</a>
            </li>
        </ul>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($documents->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>{{ $doc->doc_title }}</td>
                                        <td>{{ $doc->doc_category }}</td>
                                        <td>{{ ucfirst($doc->content_type) }}</td>
                                        <td><span class="badge bg-secondary">{{ $doc->doc_status }}</span></td>
                                        <td>
                                            @if ($doc->doc_status == 'draft')
                                                {{-- Draft: only Edit and Delete (no View, no Submit) --}}
                                                @if (auth()->user()->isAdmin() || $doc->user_id == auth()->id())
                                                    <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                        class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <form method="POST"
                                                        action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                        style="display:inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Delete?')">Delete</button>
                                                    </form>
                                                @endif
                                            @else
                                                {{-- Non-draft: View always --}}
                                                <a href="{{ route('document.show', $doc->doc_id) }}"
                                                    class="btn btn-sm btn-primary">View</a>

                                                {{-- Submit button (only if pending/draft? Not needed, but keep original logic) --}}
                                                @if ($doc->doc_status == 'pending')
                                                    {{-- Optional: You may add a "Submit" for pending? Usually not. Keep as is. --}}
                                                @endif

                                                {{-- Edit button for admin or owner of non-approved documents --}}
                                                @if (auth()->user()->isAdmin() || ($doc->user_id == auth()->id() && $doc->approval_status != 'approved'))
                                                    <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                        class="btn btn-sm btn-outline-primary">Edit</a>
                                                @endif

                                                {{-- Delete button for admin or owner of non-approved documents --}}
                                                @if (auth()->user()->isAdmin() || ($doc->user_id == auth()->id() && $doc->approval_status != 'approved'))
                                                    <form method="POST"
                                                        action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                        style="display:inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Delete?')">Delete</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-top">
                        {{ $documents->links() }}
                    </div>
                @else
                    <p class="text-muted text-center py-3">No documents found.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

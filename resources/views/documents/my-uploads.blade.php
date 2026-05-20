@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'My Uploads']]])

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>My Uploads</h3>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i> Create
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('content.create') }}?type=article"><i
                                class="fas fa-newspaper"></i> Article</a></li>
                    <li><a class="dropdown-item" href="{{ route('content.create') }}?type=file"><i class="fas fa-file"></i>
                            File</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addLinkModal"><i
                                class="fas fa-link"></i> Link</a></li>
                </ul>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type', 'all') == 'all' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'all', 'sort' => request('sort'), 'direction' => request('direction')]) }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'article' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'article', 'sort' => request('sort'), 'direction' => request('direction')]) }}">Articles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'file' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'file', 'sort' => request('sort'), 'direction' => request('direction')]) }}">Files</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'link' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'link', 'sort' => request('sort'), 'direction' => request('direction')]) }}">Links</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->get('type') == 'question' ? 'active' : '' }}"
                    href="{{ route('documents.my-uploads', ['type' => 'question', 'sort' => request('sort'), 'direction' => request('direction')]) }}">Questions</a>
            </li>
        </ul>

        <!-- Page-specific search -->
        <div class="mb-3">
            <input type="text" id="table-search" class="form-control" placeholder="Search within this page...">
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($documents->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><a href="{{ route('documents.my-uploads', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark">Title @if (request('sort') == 'title')
                                                <i
                                                    class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th><a href="{{ route('documents.my-uploads', array_merge(request()->query(), ['sort' => 'category', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark">Category @if (request('sort') == 'category')
                                                <i
                                                    class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Type</th>
                                    <th>Security Clearance</th>

                                    <th><a href="{{ route('documents.my-uploads', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark">Status @if (request('sort') == 'status')
                                                <i
                                                    class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th><a href="{{ route('documents.my-uploads', array_merge(request()->query(), ['sort' => 'date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark">Date @if (request('sort') == 'date')
                                                <i
                                                    class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a></th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>{{ $doc->doc_title }}</td>
                                        <td>{{ $doc->doc_category }}</td>
                                        <td>{{ ucfirst($doc->content_type) }}</td>
                                        <td>{{ $doc->security_clearance }}</td>
                                        <td>
                                            @if ($doc->approval_status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                                @if ($doc->rejection_reason)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($doc->rejection_reason, 30) }}</small>
                                                @endif
                                            @elseif($doc->doc_status == 'published')
                                                <span class="badge bg-success">Published</span>
                                            @elseif($doc->doc_status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($doc->approval_status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending Review</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($doc->doc_status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>

                                        <td class="text-nowrap text-center">
                                            @if ($doc->doc_status == 'draft')
                                                {{-- Draft: only Edit and Delete for owner --}}
                                                @if (auth()->user()->isAdmin() || $doc->user_id == auth()->id())
                                                    <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i></a>
                                                    <form method="POST"
                                                        action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                        style="display:inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Delete" onclick="return confirm('Delete?')"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            @else
                                                {{-- Non‑draft: View always --}}
                                                <a href="{{ route('document.show', $doc->doc_id) }}"
                                                    class="btn btn-sm btn-outline-info" title="View"><i
                                                        class="fas fa-eye"></i></a>
                                                @if (auth()->user()->isAdmin() || ($doc->user_id == auth()->id() && $doc->approval_status != 'approved'))
                                                    <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i></a>
                                                    <form method="POST"
                                                        action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                        style="display:inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Delete" onclick="return confirm('Delete?')"><i
                                                                class="fas fa-trash"></i></button>
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

    <script>
        document.getElementById('table-search')?.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.table tbody tr');
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
    @include('modals.add-link')
@endsection

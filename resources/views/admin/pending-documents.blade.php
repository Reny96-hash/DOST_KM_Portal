@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'Pending Approvals']]])

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Pending Document Approvals</h2>
            <form method="GET" action="{{ route('admin.documents.pending') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search by title or uploader..." value="{{ request('search') }}" style="width: 250px;">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.documents.pending') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($pendingDocuments->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><a href="{{ route('admin.documents.pending', array_merge(request()->query(), ['sort' => 'doc_title', 'dir' => request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-dark text-decoration-none">Title @if (request('sort') == 'doc_title')
                                                <i class="fas fa-sort-{{ request('dir') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Uploaded By</th>
                                    <th><a href="{{ route('admin.documents.pending', array_merge(request()->query(), ['sort' => 'doc_category', 'dir' => request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-dark text-decoration-none">Category @if (request('sort') == 'doc_category')
                                                <i class="fas fa-sort-{{ request('dir') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Classification</th>
                                    <th><a href="{{ route('admin.documents.pending', array_merge(request()->query(), ['sort' => 'created_at', 'dir' => request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-dark text-decoration-none">Submitted @if (request('sort') == 'created_at')
                                                <i class="fas fa-sort-{{ request('dir') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a></th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingDocuments as $doc)
                                    <tr>
                                        <td>{{ $doc->doc_title }}<br><small
                                                class="text-muted">{{ Str::limit($doc->doc_description, 60) }}</small></td>
                                        <td>{{ $doc->user->user_first_name ?? '' }}
                                            {{ $doc->user->user_last_name ?? '' }}<br><small>{{ $doc->user->user_email ?? '' }}</small>
                                        </td>
                                        <td>{{ $doc->doc_category }}</td>
                                        <td><span class="badge bg-secondary">{{ $doc->security_clearance ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('document.show', $doc->doc_id) }}"
                                                class="btn btn-sm btn-outline-secondary" target="_blank" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.documents.approve', $doc->doc_id) }}"
                                                style="display:inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Approve" onclick="return confirm('Approve this document?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Reject"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $doc->doc_id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $doc->doc_id }}" tabindex="-1">
                                                <div class="modal-dialog modal-sm">
                                                    <form method="POST"
                                                        action="{{ route('admin.documents.reject', $doc->doc_id) }}">
                                                        @csrf
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title">Reject Document</h6>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label class="form-label">Reason for rejection <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea name="admin_comment" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-sm btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger">Reject</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-top">
                        {{ $pendingDocuments->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-muted mb-2"></i>
                        <p class="mb-0">No pending documents for approval.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

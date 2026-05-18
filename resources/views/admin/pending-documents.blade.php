@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-2">
            <h6 class="mb-0"><i class="fas fa-clock"></i> Pending Document Approvals</h6>
            <small class="text-muted">Documents waiting for your review</small>
        </div>
        <div class="card-body p-0">
            @if ($pendingDocuments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Title</th>
                                <th>Uploaded By</th>
                                <th>Category</th>
                                <th>Classification</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingDocuments as $doc)
                                <tr>
                                    <td class="align-middle">
                                        <div class="fw-semibold">{{ $doc->doc_title }}</div>
                                        <small class="text-muted">{{ Str::limit($doc->doc_description, 60) }}</small>
                                    </td>
                                    <td class="align-middle">
                                        {{ $doc->user->user_first_name }} {{ $doc->user->user_last_name }}<br>
                                        <small class="text-muted">{{ $doc->user->user_email }}</small>
                                    </td>
                                    <td class="align-middle">{{ $doc->doc_category }}</td>
                                    <td class="align-middle"><span class="badge">{{ $doc->security_clearance }}</span></td>
                                    <td class="align-middle">{{ $doc->created_at->format('Y-m-d') }}</td>
                                    <td class="align-middle text-end">
                                        <form method="POST" action="{{ route('admin.documents.approve', $doc->doc_id) }}"
                                            style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                onclick="return confirm('Approve this document?')">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $doc->doc_id }}">
                                            <i class="fas fa-times"></i> Reject
                                        </button>

                                        <!-- Reject Modal -->
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
                    <p class="text-muted mb-0">No pending documents for approval.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

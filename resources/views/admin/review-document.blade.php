@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Pending Document Approvals</h2>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($pendingDocuments->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Uploaded By</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Security Clearance</th>
                                    <th>Submitted</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingDocuments as $doc)
                                    <tr>
                                        <td>
                                            <strong>{{ $doc->doc_title }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($doc->doc_description, 60) }}</small>
                                        </td>
                                        <td>{{ $doc->user->user_first_name }} {{ $doc->user->user_last_name }}<br>
                                            <small>{{ $doc->user->user_email }}</small>
                                        </td>
                                        <td>{{ $doc->doc_category }}</td>
                                        <td>{{ ucfirst($doc->content_type) }}</td>
                                        <td><span class="badge bg-info">{{ $doc->security_clearance }}</span></td>
                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <!-- Approve button (direct submit) -->
                                            <form method="POST"
                                                action="{{ route('admin.documents.approve', $doc->doc_id) }}"
                                                style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Approve this document?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>

                                            <!-- Reject button (opens modal) -->
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $doc->doc_id }}">
                                                <i class="fas fa-times"></i> Reject
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

    <script>
        // Optional: Ensure that reject button does not submit without comment (HTML5 required already handles)
        document.querySelectorAll('form[action*="reject"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const comment = this.querySelector('textarea[name="admin_comment"]');
                if (comment && !comment.value.trim()) {
                    e.preventDefault();
                    alert('Please provide a rejection reason.');
                }
            });
        });
    </script>
@endsection

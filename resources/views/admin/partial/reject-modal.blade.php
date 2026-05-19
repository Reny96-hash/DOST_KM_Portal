<div class="modal fade" id="rejectModal{{ $doc->doc_id }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" action="{{ route('admin.documents.reject', $doc->doc_id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Reject Document</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea name="admin_comment" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

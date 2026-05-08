@if ($paginatedDocuments->count() > 0)
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
                            <div class="fw-medium">{{ $doc->doc_title }}</div>
                            <small class="text-muted">{{ Str::limit($doc->doc_description, 55) }}</small>
                        </td>
                        <td class="align-middle">{{ strtoupper($doc->doc_file_type) }}</td>
                        <td class="align-middle">{{ $doc->doc_category }}</td>
                        <td class="align-middle">{{ $doc->security_clearance }}</td>
                        <td class="align-middle">{{ $doc->created_at->format('Y-m-d') }}</td>
                        <td class="align-middle text-end pe-3">
                            @if (in_array($doc->doc_file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                <button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                    data-bs-target="#previewModal{{ $doc->doc_id }}" title="Preview"><i
                                        class="fas fa-eye"></i></button>
                                <!-- Modal -->
                                <div class="modal fade" id="previewModal{{ $doc->doc_id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title">{{ $doc->doc_title }}</h6>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ route('images.show', $doc->doc_id) }}" class="img-fluid"
                                                    alt="{{ $doc->doc_title }}">
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ route('download', $doc->doc_id) }}"
                                                    class="btn btn-sm btn-primary">Download</a>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($doc->doc_file_type == 'pdf')
                                <a href="{{ route('preview', $doc->doc_id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info me-1" title="Preview PDF"><i
                                        class="fas fa-file-pdf"></i></a>
                            @endif
                            <a href="{{ route('download', $doc->doc_id) }}" class="btn btn-sm btn-outline-secondary"
                                title="Download"><i class="fas fa-download"></i></a>
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
        <p class="text-muted small mb-0">No approved documents found.</p>
    </div>
@endif

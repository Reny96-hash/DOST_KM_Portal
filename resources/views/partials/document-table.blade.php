@if (count($paginatedDocuments) > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" id="document-table">
            <thead class="table-light">
                <tr>
                    @if (auth()->user()->isAdmin())
                        <th class="text-center" width="40"><input type="checkbox" id="select-all"></th>
                    @endif
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
                        @if (auth()->user()->isAdmin())
                            <td class="text-center"><input type="checkbox" class="doc-select"
                                    value="{{ $doc->doc_id }}"></td>
                        @endif
                        <td class="ps-3">
                            <div class="fw-medium">{{ $doc->doc_title }}</div>
                            <small class="text-muted">{{ Str::limit($doc->doc_description, 55) }}</small>
                        </td>
                        <td class="align-middle">{{ strtoupper($doc->doc_file_type) }}</td>
                        <td class="align-middle">{{ $doc->doc_category }}</td>
                        <td class="align-middle">{{ $doc->security_clearance }}</td>
                        <td class="align-middle">{{ $doc->created_at->format('Y-m-d') }}</td>
                        <td class="align-middle text-end pe-3">
                            <!-- your existing action buttons (preview, download, delete) -->
                            @if (in_array($doc->doc_file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                <button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                    data-bs-target="#previewModal{{ $doc->doc_id }}" title="Preview"><i
                                        class="fas fa-eye"></i></button>
                                <!-- modal here -->
                            @elseif($doc->doc_file_type == 'pdf')
                                <a href="{{ route('preview', $doc->doc_id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info me-1" title="Preview PDF"><i
                                        class="fas fa-file-pdf"></i></a>
                            @endif
                            <a href="{{ route('download', $doc->doc_id) }}" class="btn btn-sm btn-outline-secondary"
                                title="Download"><i class="fas fa-download"></i></a>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('documents.destroy', $doc->doc_id) }}"
                                    style="display: inline-block; margin-left: 5px;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                        onclick="return confirm('Delete this document?')"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            @endif
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

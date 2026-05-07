@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-folder-open"></i> My Uploads</h6>
                <small class="text-muted">Documents you have uploaded</small>
            </div>
            <div class="card-body p-0">
                @if ($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Classification</th>
                                    <th>Status</th>
                                    <th>Review Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $doc->doc_title }}</div>
                                            <small class="text-muted">{{ Str::limit($doc->doc_description, 50) }}</small>
                                        </td>
                                        <td class="align-middle">{{ $doc->doc_category }}</td>
                                        <td class="align-middle"><span class="badge">{{ $doc->security_clearance }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($doc->doc_status == 'published')
                                                <span class="badge bg-success">Published</span>
                                            @elseif($doc->doc_status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($doc->doc_status) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($doc->approval_status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($doc->approval_status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending Review</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                                @if ($doc->rejection_reason)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($doc->rejection_reason, 30) }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $doc->created_at->format('Y-m-d') }}</td>
                                        <td class="align-middle text-end">
                                            @if (auth()->user()->isAdmin())
                                                <!-- Admin can edit/delete any document -->
                                                <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete" onclick="return confirm('Delete this document?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @elseif($doc->approval_status !== 'approved')
                                                <!-- Regular users can edit/delete only pending/rejected -->
                                                <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('documents.destroy', $doc->doc_id) }}"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete" onclick="return confirm('Delete this document?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Locked</span>
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
                    <div class="text-center py-4">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">You haven't uploaded any documents yet.</p>
                        <a href="{{ route('upload.form') }}" class="btn btn-sm btn-primary mt-2">Upload Your First
                            Document</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

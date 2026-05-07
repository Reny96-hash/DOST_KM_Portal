@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0"><i class="fas fa-edit"></i> Edit Document</h6>
                    <small class="text-muted">Update document metadata or replace the file</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.update', $document->doc_id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="doc_title" class="form-control" value="{{ $document->doc_title }}"
                                required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="doc_description" rows="3" class="form-control">{{ $document->doc_description }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="doc_category" class="form-select" required>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ $document->doc_category == $cat ? 'selected' : '' }}>{{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Security Classification -->
                            <div class="col-md-6">
                                <label class="form-label">Security Classification <span class="text-danger">*</span></label>
                                <select name="security_clearance" class="form-select" required>
                                    <option value="Public"
                                        {{ $document->security_clearance == 'Public' ? 'selected' : '' }}>Public</option>
                                    <option value="Internal"
                                        {{ $document->security_clearance == 'Internal' ? 'selected' : '' }}>Internal
                                    </option>
                                    <option value="Confidential"
                                        {{ $document->security_clearance == 'Confidential' ? 'selected' : '' }}>Confidential
                                    </option>
                                    <option value="Secret"
                                        {{ $document->security_clearance == 'Secret' ? 'selected' : '' }}>Secret</option>
                                    <option value="Top Secret"
                                        {{ $document->security_clearance == 'Top Secret' ? 'selected' : '' }}>Top Secret
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Current File Info -->
                        <div class="mb-3 p-2 bg-light rounded">
                            <label class="form-label fw-semibold">Current File</label>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span>{{ $document->doc_file_name }}</span>
                                <small class="text-muted">({{ strtoupper($document->doc_file_type) }},
                                    {{ $document->doc_file_size }} KB)</small>
                            </div>
                        </div>

                        <!-- Replace File Upload -->
                        <div class="mb-3">
                            <label class="form-label">Replace File <span class="text-muted">(Optional)</span></label>
                            <input type="file" name="document" class="form-control">
                            <small class="text-muted">Leave empty to keep the current file. Allowed: PDF, DOC, DOCX, XLSX,
                                JPG, JPEG, PNG. Max: 10MB</small>
                        </div>

                        <!-- Warning for re-approval -->
                        <div class="alert alert-warning py-2">
                            <small><i class="fas fa-exclamation-triangle"></i> Note: Any changes will reset approval status.
                                Document will need to be re-approved.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('documents.my-uploads') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

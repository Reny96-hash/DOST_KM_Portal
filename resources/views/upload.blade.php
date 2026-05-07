@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0"><i class="fas fa-upload"></i> Upload Document</h6>
                    <small class="text-muted">All uploads require approval before becoming visible to others</small>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
                                <input type="text" name="author" class="form-control"
                                    placeholder="Document author name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control"
                                    placeholder="e.g., IT Division">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Brief description of the document..."></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Security Classification <span class="text-danger">*</span></label>
                                <select name="security_clearance" class="form-select" required>
                                    <option value="Public">Public - Visible to all authenticated users</option>
                                    <option value="Internal">Internal - DOST staff only</option>
                                    <option value="Confidential">Confidential - Restricted access</option>
                                    <option value="Secret">Secret - Highly restricted</option>
                                    <option value="Top Secret">Top Secret - Very limited access</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">File <span class="text-danger">*</span></label>
                                <input type="file" name="document" class="form-control" required>
                                <small class="text-muted">Allowed: PDF, DOC, DOCX, XLSX, JPG, JPEG, PNG. Max: 10MB</small>
                            </div>
                        </div>

                        <!-- Approval Notice -->
                        <div class="alert alert-info py-2 mb-3">
                            <i class="fas fa-info-circle"></i>
                            <small>After upload, your document will be reviewed by an administrator or KM Champion before it
                                becomes visible to others. You will be notified once approved.</small>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

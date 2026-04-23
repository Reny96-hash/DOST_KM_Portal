@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card card-box p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #000847;"></i>
                    <h3 style="color: #000847; font-size: 1.2rem; margin-top: 10px;">Upload Document</h3>
                    <p class="text-muted" style="font-size: 0.7rem;">Share knowledge with the organization</p>
                </div>

                <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">TITLE</label>
                        <input type="text" name="title" class="form-control" style="font-size: 0.8rem;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">DESCRIPTION</label>
                        <textarea name="description" rows="3" class="form-control" style="font-size: 0.8rem;"
                            placeholder="Brief description of the document..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">CATEGORY</label>
                        <select name="category" class="form-select" style="font-size: 0.8rem;" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">SECURITY
                            CLASSIFICATION</label>
                        <select name="security_clearance" class="form-select" style="font-size: 0.8rem;" required>
                            <option value="Public">Public</option>
                            <option value="Internal">Internal</option>
                            <option value="Confidential">Confidential</option>
                            <option value="Secret">Secret</option>
                            <option value="Top Secret">Top Secret</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">FILE</label>
                        <input type="file" name="document" class="form-control" style="font-size: 0.8rem;" required>
                        <small class="text-muted" style="font-size: 0.65rem;">Allowed: PDF, DOC, DOCX, XLSX. Max:
                            10MB</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary flex-grow-1"
                            style="font-size: 0.75rem;">Cancel</a>
                        <button type="submit" class="btn"
                            style="background-color: #000847; color: white; font-size: 0.75rem; flex-grow: 1;">
                            <i class="fas fa-upload"></i> Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

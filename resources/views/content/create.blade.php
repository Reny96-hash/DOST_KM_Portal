@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">{{ request()->get('type') == 'article' ? 'Create Article' : 'Upload File' }}</div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('content.store') }}" enctype="multipart/form-data" id="contentForm">
                    @csrf
                    <input type="hidden" name="content_type" value="{{ request()->get('type') }}">

                    <div class="mb-3">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="2" class="form-control" required>{{ old('description') }}</textarea>
                    </div>

                    @if (request()->get('type') == 'article')
                        <div class="mb-3">
                            <label>Content</label>
                            <div id="editor-container" style="height: 300px;"></div>
                            <textarea name="content_rich" id="content_rich_input" style="display:none;"></textarea>
                            <small class="text-muted">Required only when publishing (not for draft).</small>
                        </div>
                    @else
                        <div class="mb-3">
                            <label>Upload File</label>
                            <input type="file" name="document" class="form-control">
                            <small class="text-muted">Required only when publishing (not for draft).</small>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ isset($selectedCategory) && $selectedCategory == $cat ? 'selected' : '' }}>
                                        {{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Security Clearance <span class="text-danger">*</span></label>
                            <select name="security_clearance" class="form-select" required>
                                <option value="Public">Public</option>
                                <option value="Internal">Internal</option>
                                <option value="Confidential">Confidential</option>
                                <option value="Secret">Secret</option>
                                <option value="Top Secret">Top Secret</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Additional Attachments (optional)</label>
                        <input type="file" name="attachments[]" multiple class="form-control">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="save_draft" value="1" class="btn btn-secondary">Save as
                            Draft</button>
                        <button type="submit" class="btn btn-primary">Publish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quill CSS & JS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (request()->get('type') == 'article')
                var quill = new Quill('#editor-container', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{
                                header: [1, 2, 3, false]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                align: []
                            }],
                            ['blockquote', 'code-block'],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Write your article here...'
                });
                quill.on('text-change', function() {
                    document.getElementById('content_rich_input').value = quill.root.innerHTML;
                });
                // Initial sync
                document.getElementById('content_rich_input').value = quill.root.innerHTML;
                // Final sync before submit
                document.querySelector('form').addEventListener('submit', function() {
                    document.getElementById('content_rich_input').value = quill.root.innerHTML;
                });
            @endif
        });
    </script>
@endsection

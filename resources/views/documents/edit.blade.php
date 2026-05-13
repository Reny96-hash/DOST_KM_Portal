@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Edit {{ ucfirst($document->content_type) }}</div>
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

                <form method="POST" action="{{ route('documents.update', $document->doc_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="doc_title" class="form-control" value="{{ $document->doc_title }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="doc_description" rows="2" class="form-control" required>{{ $document->doc_description }}</textarea>
                    </div>

                    @if ($document->content_type == 'article')
                        <div class="mb-3">
                            <label>Content</label>
                            <div id="editor-container" style="height: 300px;">{!! $document->content_rich !!}</div>
                            <textarea name="content_rich" id="content_rich_input" style="display:none;">{{ $document->content_rich }}</textarea>
                            <small class="text-muted">Required only when publishing (not for draft).</small>
                        </div>

                        <!-- Quill CSS & JS -->
                        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
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

                                // Set initial content
                                quill.root.innerHTML = document.querySelector('#editor-container').innerHTML;

                                // Sync to hidden textarea on change
                                quill.on('text-change', function() {
                                    document.getElementById('content_rich_input').value = quill.root.innerHTML;
                                });

                                // Final sync on form submit
                                document.querySelector('form').addEventListener('submit', function() {
                                    document.getElementById('content_rich_input').value = quill.root.innerHTML;
                                });
                            });
                        </script>
                    @elseif($document->content_type == 'file')
                        <div class="mb-3">
                            <label>Current File</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-file-alt"></i> {{ $document->doc_file_name }}
                                ({{ strtoupper($document->doc_file_type) }}, {{ round($document->doc_file_size, 2) }} KB)
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Replace File (optional)</label>
                            <input type="file" name="document" class="form-control">
                            <small class="text-muted">Leave empty to keep the current file. Max: 10MB</small>
                        </div>
                    @elseif($document->content_type == 'link')
                        <div class="mb-3">
                            <label>URL</label>
                            <input type="url" name="url" class="form-control" value="{{ $document->content_rich }}"
                                required>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Category</label>
                            <select name="doc_category" class="form-select" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ $document->doc_category == $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Security Clearance</label>
                            <select name="security_clearance" class="form-select" required>
                                <option value="Public" {{ $document->security_clearance == 'Public' ? 'selected' : '' }}>
                                    Public</option>
                                <option value="Internal"
                                    {{ $document->security_clearance == 'Internal' ? 'selected' : '' }}>Internal</option>
                                <option value="Confidential"
                                    {{ $document->security_clearance == 'Confidential' ? 'selected' : '' }}>Confidential
                                </option>
                                <option value="Secret" {{ $document->security_clearance == 'Secret' ? 'selected' : '' }}>
                                    Secret</option>
                                <option value="Top Secret"
                                    {{ $document->security_clearance == 'Top Secret' ? 'selected' : '' }}>Top Secret
                                </option>
                            </select>
                        </div>
                    </div>

                    @if ($document->content_type == 'article')
                        <div class="alert alert-warning">
                            <small><i class="fas fa-exclamation-triangle"></i> Any change will reset approval status. The
                                document will need to be re‑approved.</small>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('documents.my-uploads') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

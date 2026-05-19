@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'Ask a Question']]])

    <div class="container">
        <div class="card">
            <div class="card-header">Ask a Question</div>
            <div class="card-body">
                <form method="POST" action="{{ route('question.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Details (Rich Text)</label>
                        <textarea name="content_rich" id="editor"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Category</label>
                            <select name="category" class="form-select" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Security Clearance</label>
                            <select name="security_clearance" class="form-select" required>
                                <option>Public</option>
                                <option>Internal</option>
                                <option>Confidential</option>
                                <option>Secret</option>
                                <option>Top Secret</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Question</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#editor'));
    </script>
@endsection

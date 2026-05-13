@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Manage Categories</h3>

        <!-- Add Category Form -->
        <div class="card mb-4">
            <div class="card-header">Add New Category</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="cat_name" class="form-control" placeholder="Category name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="cat_description" class="form-control"
                                placeholder="Description (optional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Add Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Categories -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $cat)
                            <tr>
                                <td>{{ $cat->cat_id }}</td>
                                <td>{{ $cat->cat_name }}</td>
                                <td>{{ $cat->cat_description ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $cat->cat_id }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->cat_id) }}"
                                        style="display:inline-block" onsubmit="return confirm('Delete this category?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $cat->cat_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.categories.update', $cat->cat_id) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label>Name</label>
                                                    <input type="text" name="cat_name" class="form-control"
                                                        value="{{ $cat->cat_name }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Description</label>
                                                    <textarea name="cat_description" class="form-control">{{ $cat->cat_description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $categories->links() }}
    </div>
@endsection

<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('content.link') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add External Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>URL</label>
                        <input type="url" name="url" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" rows="2" class="form-control" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @php $cats = \App\Models\Category::pluck('cat_name'); @endphp
                                @foreach ($cats as $cat)
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Link</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Pre-fill category if the button that opened modal has data-category attribute
    document.querySelectorAll('[data-bs-target="#addLinkModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const cat = this.dataset.category;
            if (cat) {
                setTimeout(() => {
                    const select = document.querySelector(
                        '#addLinkModal select[name="category"]');
                    if (select) select.value = cat;
                }, 100);
            }
        });
    });
</script>

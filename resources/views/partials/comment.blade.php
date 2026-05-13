<div class="comment mb-2 p-2 border rounded">
    <div class="d-flex justify-content-between">
        <strong>{{ $comment->user->user_first_name ?? 'Anonymous' }}</strong>
        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
    </div>
    <p class="mb-2">{{ $comment->comment_text }}</p>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary like-comment" data-id="{{ $comment->comment_id }}">
            <i class="fas fa-thumbs-up"></i> <span class="likes-count">{{ $comment->likes }}</span>
        </button>
        <button class="btn btn-sm btn-link reply-toggle"
            data-target="reply-form-{{ $comment->comment_id }}">Reply</button>
    </div>

    <!-- Reply form (hidden initially) -->
    <div id="reply-form-{{ $comment->comment_id }}" class="mt-2" style="display: none;">
        <form action="{{ route('comment.store', $comment->doc_id) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_comment_id" value="{{ $comment->comment_id }}">
            <textarea name="comment_text" rows="2" class="form-control form-control-sm" placeholder="Write a reply..."></textarea>
            <button type="submit" class="btn btn-sm btn-primary mt-1">Post Reply</button>
        </form>
    </div>

    <!-- Child comments (recursive) -->
    @if ($comment->replies->count())
        <div class="ml-4 mt-2">
            @foreach ($comment->replies as $reply)
                @include('partials.comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>

<script>
    // Toggle reply form visibility
    document.querySelectorAll('.reply-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const form = document.getElementById(targetId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    });

    // Like comment via AJAX
    document.querySelectorAll('.like-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.id;
            const likesSpan = this.querySelector('.likes-count');
            fetch(`/comment/${commentId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    likesSpan.textContent = data.likes;
                });
        });
    });
</script>

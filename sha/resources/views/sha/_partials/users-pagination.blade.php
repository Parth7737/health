@if ($users->hasPages())
    <div class="pagination">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
@endif

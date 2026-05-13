<div class="short-text" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    {{ $truncatedText }}
</div>
<div class="full-text" style="max-width: 250px; word-wrap: break-word; display: none;">
    {{ $fullText }}
</div>
@if(\Illuminate\Support\Str::wordCount($fullText) > 5)
    <a href="javascript:;" class="toggle-text">Show More</a>
@endif
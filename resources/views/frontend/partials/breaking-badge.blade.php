@if(isset($post) && $post->isBreakingActive())
    @php($breakingVariant = $breakingVariant ?? '')
    <span class="ku-breaking-badge {{ $breakingVariant }}">
        <span class="ku-breaking-dot"></span>Breaking
    </span>
@endif

@props(['authorId'])

@php
  $displayName  = get_the_author_meta('display_name', $authorId);
  $bio          = get_the_author_meta('description', $authorId);
  $niceName     = get_the_author_meta('user_nicename', $authorId);
  $archiveUrl   = get_author_posts_url($authorId);
  $avatarUrl    = get_avatar_url($authorId, ['size' => 64]);

  // Derive initials from display name
  $nameParts  = explode(' ', trim($displayName));
  $firstName  = $nameParts[0] ?? '';
  $lastName   = count($nameParts) > 1 ? end($nameParts) : '';
  $initials   = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
@endphp

<aside class="author-card" aria-label="About the author">
  <div class="author-card__inner">

    <div class="author-card__avatar-wrap">
      @if ($avatarUrl)
        <img
          src="{{ $avatarUrl }}"
          alt="{{ esc_attr($displayName) }}"
          class="author-avatar author-avatar--lg"
          width="64"
          height="64"
          loading="lazy"
          decoding="async"
        >
      @else
        <span class="author-avatar author-avatar--initials author-avatar--lg" aria-hidden="true">
          {{ $initials }}
        </span>
      @endif
    </div>

    <div class="author-card__body">

      <p class="author-card__label">Written by</p>

      <h3 class="author-card__name font-sans">
        <a href="{{ $archiveUrl }}" class="author-card__name-link">{{ $displayName }}</a>
      </h3>

      @if ($bio)
        <p class="author-card__bio">{{ $bio }}</p>
      @endif

      <a href="{{ $archiveUrl }}" class="author-card__archive-link">
        All articles by {{ $firstName }} &rarr;
      </a>

    </div>{{-- /.author-card__body --}}

  </div>{{-- /.author-card__inner --}}
</aside>

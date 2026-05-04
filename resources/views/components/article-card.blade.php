@props([
  'post',
  'layout'      => 'vertical',
  'showSummary' => true,
  'useSerif'    => true,
  'accentTag'   => false,
])

@php
  $postId      = $post->ID;
  $title       = $post->post_title;
  $permalink   = get_permalink($post);
  $thumbUrl    = get_post_thumbnail_url($postId, 'large');
  $date        = get_the_date('M j', $post);
  $readMins    = get_post_meta($postId, '_read_minutes', true);
  $eyebrowRaw  = get_post_meta($postId, '_eyebrow', true);
  $summaryRaw  = get_post_meta($postId, '_summary', true);
  $authorName  = get_the_author_meta('display_name', $post->post_author);
  $avatarUrl   = get_avatar_url($post->post_author, ['size' => 24]);
  $excerpt     = get_the_excerpt($post);

  // Eyebrow: meta field or first category name
  if ($eyebrowRaw) {
    $eyebrow = $eyebrowRaw;
  } else {
    $cats = get_the_category($postId);
    $eyebrow = !empty($cats) ? $cats[0]->name : '';
  }

  // Dek: summary meta or excerpt fallback
  $dek = $summaryRaw ?: $excerpt;

  // Author initials fallback
  $nameParts = explode(' ', trim($authorName));
  $initials  = strtoupper(
    substr($nameParts[0] ?? '', 0, 1) . substr(end($nameParts), 0, 1)
  );

  // CSS modifier based on layout
  $cardClass = match ($layout) {
    'horizontal' => 'article-card article-card--horizontal',
    'minimal'    => 'article-card article-card--minimal',
    'text'       => 'article-card article-card--text',
    default      => 'article-card',
  };

  $titleClass = 'article-card__title' . ($useSerif ? ' font-serif' : ' font-sans');
@endphp

<article class="{{ $cardClass }}">

  @if ($layout !== 'text' && $thumbUrl)
    <a href="{{ $permalink }}" class="article-card__media-link" tabindex="-1" aria-hidden="true">
      <figure class="article-card__figure">
        <img
          src="{{ $thumbUrl }}"
          alt="{{ esc_attr($title) }}"
          class="article-card__img"
          loading="lazy"
          decoding="async"
        >
      </figure>
    </a>
  @endif

  <div class="article-card__body">

    @if ($eyebrow)
      <div class="eyebrow{{ $accentTag ? ' eyebrow--accent' : '' }}">{{ $eyebrow }}</div>
    @endif

    <h3 class="{{ $titleClass }}">
      <a href="{{ $permalink }}" class="article-card__title-link">{{ $title }}</a>
    </h3>

    @if ($showSummary && $dek && $layout !== 'minimal')
      <p class="article-card__dek">{{ $dek }}</p>
    @endif

    <footer class="article-card__meta">

      <div class="article-card__author">
        @if ($avatarUrl)
          <img
            src="{{ $avatarUrl }}"
            alt="{{ esc_attr($authorName) }}"
            class="author-avatar author-avatar--sm"
            width="24"
            height="24"
            loading="lazy"
            decoding="async"
          >
        @else
          <span class="author-avatar author-avatar--initials author-avatar--sm" aria-hidden="true">{{ $initials }}</span>
        @endif
        <span class="article-card__author-name">{{ $authorName }}</span>
      </div>

      <div class="article-card__byline">
        @if ($date)
          <time class="article-card__date" datetime="{{ get_the_date('Y-m-d', $post) }}">{{ $date }}</time>
        @endif
        @if ($readMins)
          <span class="article-card__read-time" aria-label="{{ $readMins }} minute read">{{ $readMins }} min</span>
        @endif
      </div>

    </footer>

  </div>{{-- /.article-card__body --}}

</article>

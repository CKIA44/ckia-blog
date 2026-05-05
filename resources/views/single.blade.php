@extends('layouts.app')

@section('content')
  {{--
    Single article / post page.
    Data from App\View\Composers\SingleComposer.
    Variables: $post (WP_Post), $author (array), $related (array of WP_Post), $tocSections (array)
  --}}

  @while(have_posts())

  @php
    the_post();
    $post          = get_post();
    $authorId      = $post->post_author;
    $authorName    = get_the_author_meta('display_name', $authorId);
    $authorRole    = get_the_author_meta('user_title', $authorId) ?: 'Contributing writer';
    $authorBio     = get_the_author_meta('description', $authorId);
    $authorNice    = get_the_author_meta('user_nicename', $authorId);
    $authorInitials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $authorName), 0, 2)));
    $eyebrow       = get_post_meta($post->ID, '_eyebrow', true);
    $summary       = get_post_meta($post->ID, '_summary', true) ?: get_the_excerpt();
    $readMin       = get_post_meta($post->ID, '_read_minutes', true) ?: '5';
    $heroImage     = get_the_post_thumbnail_url($post->ID, 'full');
    $postTags      = wp_get_post_tags($post->ID);
    $category      = get_the_category($post->ID)[0] ?? null;
    $related       = $related ?? [];
  @endphp

  {{-- Reading progress bar --}}
  <x-reading-progress />

  {{-- Article hero --}}
  <header class="article-hero">
    <div class="article-hero__inner">
      <nav class="article-hero__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ home_url('/') }}">CKIA</a>
        <span class="article-hero__breadcrumb-sep" aria-hidden="true">/</span>
        @if ($category)
          <a href="{{ get_category_link($category->term_id) }}">{{ $category->name }}</a>
          <span class="article-hero__breadcrumb-sep" aria-hidden="true">/</span>
        @endif
        <span aria-current="page">Article</span>
      </nav>

      @if ($eyebrow)
        <x-eyebrow color="seafoam">{{ $eyebrow }}</x-eyebrow>
      @endif

      <h1 class="article-hero__title">{{ get_the_title() }}</h1>

      @if ($summary)
        <p class="article-hero__dek">{{ $summary }}</p>
      @endif

      <div class="article-hero__byline">
        <span class="author-avatar" style="width:44px;height:44px;font-size:16px" aria-hidden="true">
          {{ $authorInitials }}
        </span>
        <div>
          <div class="article-hero__author-name">{{ $authorName }}</div>
          <div class="article-hero__author-meta">
            {{ $authorRole }}
            · <time datetime="{{ get_the_date('Y-m-d') }}">{{ get_the_date('F j, Y') }}</time>
            · {{ $readMin }} min read
          </div>
        </div>
        <div class="article-hero__actions">
          <button class="article-action-btn" type="button" aria-label="Save article">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            Save
          </button>
          <button class="article-action-btn" type="button" onclick="navigator.share ? navigator.share({title: document.title, url: location.href}) : null" aria-label="Share article">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
              <line x1="8.6" y1="13.5" x2="15.4" y2="17.5"/><line x1="15.4" y1="6.5" x2="8.6" y2="10.5"/>
            </svg>
            Share
          </button>
        </div>
      </div>
    </div>
  </header>

  {{-- Hero image --}}
  @if ($heroImage)
    <div class="article-hero-image">
      <img
        src="{{ $heroImage }}"
        alt="{{ esc_attr(get_the_title()) }}"
        loading="eager"
      >
    </div>
  @endif

  {{-- Three-column body: TOC · content · sidebar --}}
  <div class="article-layout">

    {{-- Left: Table of Contents (hidden on mobile/tablet) --}}
    <aside class="article-toc" aria-label="Table of contents">
      <nav class="article-toc__nav" id="article-toc">
        <div class="article-toc__heading" aria-hidden="true">In this article</div>
        {{-- TOC links injected by JS from article h2 headings --}}
      </nav>
    </aside>

    {{-- Center: article body --}}
    <article class="article-body" id="article-body">
      {!! apply_filters('the_content', get_the_content()) !!}

      {{-- Editorial standards note --}}
      <p style="margin-top:32px;padding:20px 24px;background:var(--surface-2);border-radius:8px;font-family:var(--font-sans);font-size:14px;line-height:1.6;color:var(--ink-500)">
        <strong style="color:var(--ink-700)">How we review:</strong>
        CKIA reviewers sail at full price under our own names. We don't accept press fares.
        Read our <a href="#" style="color:var(--deep-ocean)">full editorial standards</a>.
      </p>
    </article>

    {{-- Right: author + tags sidebar --}}
    <aside class="article-sidebar" aria-label="Article sidebar">
      <x-author-card :authorId="(int) $authorId" />

      @if (count($postTags) > 0)
        <div>
          <x-eyebrow>Tags</x-eyebrow>
          <div class="tags-list">
            @foreach ($postTags as $tag)
              <a href="{{ get_tag_link($tag->term_id) }}" class="tag">{{ $tag->name }}</a>
            @endforeach
          </div>
        </div>
      @endif
    </aside>
  </div>

  {{-- Related articles --}}
  @if (count($related) > 0)
    <section class="related-articles" aria-labelledby="related-heading">
      <div class="related-articles__header">
        <x-eyebrow>Keep reading</x-eyebrow>
        <h2 id="related-heading" class="section-heading">
          More from {{ $category ? $category->name : 'CKIA' }}
        </h2>
      </div>
      <div class="related-articles__grid">
        @foreach ($related as $relPost)
          <x-article-card :post="$relPost" />
        @endforeach
      </div>
    </section>
  @endif

  @endwhile

@endsection

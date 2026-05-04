@extends('layouts.app')

@section('content')
  {{--
    Category archive page.
    Data from App\View\Composers\CategoryComposer.
    Variables: $category (WP_Term), $posts (array of WP_Post), $allTags (array of strings)
    URL params: tag, sort, view
  --}}

  @php
    $activeTag  = request()->get('tag', '');
    $sort       = request()->get('sort', 'newest');
    $view       = request()->get('view', 'grid');
    $catCount   = $category ? $category->count : count($posts);
    $catLabel   = $category ? $category->name : 'Articles';
    $catDesc    = $category ? $category->description : '';
    $catSlug    = $category ? $category->slug : '';
    $featured   = $posts[0] ?? null;
    $restPosts  = array_slice($posts, 1);
  @endphp

  {{-- Category hero --}}
  <section class="category-hero">
    <div class="category-hero__inner">
      <nav class="category-hero__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ home_url('/') }}">CKIA</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $catLabel }}</span>
      </nav>
      <div class="category-hero__grid">
        <div>
          <x-eyebrow>Section · {{ $catCount }} articles</x-eyebrow>
          <h1 class="category-hero__title">{{ $catLabel }}</h1>
        </div>
        @if ($catDesc)
          <p class="category-hero__desc">{{ $catDesc }}</p>
        @endif
      </div>
    </div>
  </section>

  {{-- Sticky filter bar --}}
  <div class="filter-bar" id="filter-bar" role="search" aria-label="Filter articles">
    <div class="filter-bar__inner">
      <div class="filter-bar__tags">
        <span class="filter-bar__label" aria-hidden="true">Filter</span>
        <a
          href="?sort={{ $sort }}&view={{ $view }}"
          class="filter-chip{{ !$activeTag ? ' is-active' : '' }}"
          aria-pressed="{{ !$activeTag ? 'true' : 'false' }}"
        >All</a>
        @foreach ($allTags as $tag)
          <a
            href="?tag={{ urlencode($tag) }}&sort={{ $sort }}&view={{ $view }}"
            class="filter-chip{{ $activeTag === $tag ? ' is-active' : '' }}"
            aria-pressed="{{ $activeTag === $tag ? 'true' : 'false' }}"
          >{{ $tag }}</a>
        @endforeach
      </div>

      <div class="filter-bar__controls">
        <div class="filter-sort">
          <label for="sort-select" class="filter-sort__label">Sort</label>
          <select
            id="sort-select"
            class="filter-sort__select"
            onchange="window.location.search = new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value}).toString().replace(/^/, '?')"
          >
            <option value="newest"{{ $sort === 'newest' ? ' selected' : '' }}>Newest first</option>
            <option value="oldest"{{ $sort === 'oldest' ? ' selected' : '' }}>Oldest first</option>
            <option value="longest"{{ $sort === 'longest' ? ' selected' : '' }}>Longest read</option>
            <option value="shortest"{{ $sort === 'shortest' ? ' selected' : '' }}>Shortest read</option>
          </select>
        </div>

        <div class="view-toggle" role="group" aria-label="View mode">
          <a
            href="?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
            class="view-toggle__btn{{ $view === 'grid' ? ' is-active' : '' }}"
            aria-label="Grid view"
            aria-pressed="{{ $view === 'grid' ? 'true' : 'false' }}"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <rect x="3" y="3" width="7" height="7" rx="1"/>
              <rect x="14" y="3" width="7" height="7" rx="1"/>
              <rect x="3" y="14" width="7" height="7" rx="1"/>
              <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
          </a>
          <a
            href="?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
            class="view-toggle__btn{{ $view === 'list' ? ' is-active' : '' }}"
            aria-label="List view"
            aria-pressed="{{ $view === 'list' ? 'true' : 'false' }}"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
              <line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/>
              <circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Articles --}}
  <section style="max-width:var(--container-wide);margin-inline:auto;padding:48px var(--space-6) 0" aria-label="Articles">

    {{-- Editor's pick featured card --}}
    @if ($featured)
      <article class="featured-category-card">
        <a href="{{ get_permalink($featured) }}" class="featured-category-card__image-link" tabindex="-1" aria-hidden="true">
          <div class="featured-category-card__image">
            <img
              src="{{ get_the_post_thumbnail_url($featured->ID, 'large') ?: 'https://placehold.co/800x640' }}"
              alt="{{ esc_attr($featured->post_title) }}"
              loading="eager"
            >
          </div>
        </a>
        <div class="featured-category-card__body">
          <x-eyebrow color="seafoam">
            Editor's pick · {{ get_post_meta($featured->ID, '_eyebrow', true) ?: strtoupper($catLabel) }}
          </x-eyebrow>
          <h2 class="featured-category-card__title">
            <a href="{{ get_permalink($featured) }}" style="color:inherit;text-decoration:none">
              {{ $featured->post_title }}
            </a>
          </h2>
          <p style="font-family:var(--font-sans);font-size:17px;line-height:1.55;color:var(--ink-500);margin:0">
            {{ get_post_meta($featured->ID, '_summary', true) ?: get_the_excerpt($featured) }}
          </p>
          <div style="display:flex;gap:12px;align-items:center;margin-top:8px;font-family:var(--font-sans);font-size:13px;color:var(--ink-400)">
            @php $authorName = get_the_author_meta('display_name', $featured->post_author); @endphp
            @php $authorInitials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $authorName), 0, 2))); @endphp
            <span class="author-avatar" style="width:28px;height:28px;font-size:11px" aria-hidden="true">{{ $authorInitials }}</span>
            <span style="color:var(--ink-500);font-weight:500">{{ $authorName }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ get_the_date('Y-m-d', $featured) }}">{{ get_the_date('F j, Y', $featured) }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ get_post_meta($featured->ID, '_read_minutes', true) ?: '5' }} min read</span>
          </div>
        </div>
      </article>
    @endif

    {{-- Grid or list of remaining articles --}}
    @if ($view === 'list')
      <div role="list" aria-label="Article list">
        @foreach ($restPosts as $post)
          <x-article-card :post="$post" layout="horizontal" />
        @endforeach
      </div>
    @else
      <div class="category-grid" role="list" aria-label="Article grid">
        @foreach ($restPosts as $post)
          <div role="listitem">
            <x-article-card :post="$post" />
          </div>
        @endforeach
      </div>
    @endif

    {{-- Pagination --}}
    <nav class="pagination" aria-label="Page navigation">
      {!! paginate_links([
        'prev_text' => '←',
        'next_text' => '→',
        'before_page_number' => '<span class="pagination__btn">',
        'after_page_number'  => '</span>',
      ]) !!}
    </nav>

  </section>

@endsection

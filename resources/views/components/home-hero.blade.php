@props(['article', 'treatment' => 'fullbleed', 'useSerif' => true])

@php
  $postId      = $article->ID;
  $title       = $article->post_title;
  $permalink   = get_permalink($article);
  $thumbUrl    = get_post_thumbnail_url($postId, 'full');
  $eyebrowRaw  = get_post_meta($postId, '_eyebrow', true);
  $summary     = get_post_meta($postId, '_summary', true) ?: get_the_excerpt($article);

  // Eyebrow: meta field or "FEATURED · " + first category name
  if ($eyebrowRaw) {
    $eyebrow = $eyebrowRaw;
  } else {
    $cats    = get_the_category($postId);
    $catName = !empty($cats) ? $cats[0]->name : '';
    $eyebrow = $catName ? 'FEATURED · ' . strtoupper($catName) : 'FEATURED';
  }

  // Category archive link for the secondary CTA
  $cats       = get_the_category($postId);
  $catLink    = !empty($cats) ? get_category_link($cats[0]->term_id) : home_url('/');
  $catLabel   = !empty($cats) ? 'All ' . $cats[0]->name : 'All articles';

  $titleClass = 'home-hero__title' . ($useSerif ? ' font-serif' : ' font-sans');
@endphp

@if ($treatment === 'fullbleed')

  <section
    class="home-hero home-hero--fullbleed"
    aria-label="Featured article"
  >
    @if ($thumbUrl)
      <figure class="home-hero__bg-figure" aria-hidden="true">
        <img
          src="{{ $thumbUrl }}"
          alt=""
          class="home-hero__bg-img"
          loading="eager"
          decoding="async"
          fetchpriority="high"
        >
      </figure>
    @endif

    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="home-hero__content">
      <div class="home-hero__content-inner">

        @if ($eyebrow)
          <div class="eyebrow eyebrow--fog">{{ $eyebrow }}</div>
        @endif

        <h1 class="{{ $titleClass }} home-hero__title--fog">
          <a href="{{ $permalink }}" class="home-hero__title-link">{{ $title }}</a>
        </h1>

        @if ($summary)
          <p class="home-hero__dek home-hero__dek--fog">{{ $summary }}</p>
        @endif

        <div class="home-hero__actions">
          <a href="{{ $permalink }}" class="btn btn--primary-fog">Read the review</a>
          <a href="{{ $catLink }}" class="btn btn--outline-fog">{{ $catLabel }}</a>
        </div>

      </div>
    </div>
  </section>

@elseif ($treatment === 'split')

  <section
    class="home-hero home-hero--split"
    aria-label="Featured article"
  >
    <div class="home-hero__split-content">

      @if ($eyebrow)
        <div class="eyebrow">{{ $eyebrow }}</div>
      @endif

      <h1 class="{{ $titleClass }}">
        <a href="{{ $permalink }}" class="home-hero__title-link">{{ $title }}</a>
      </h1>

      @if ($summary)
        <p class="home-hero__dek">{{ $summary }}</p>
      @endif

      <div class="home-hero__actions">
        <a href="{{ $permalink }}" class="btn btn--primary">Read the review</a>
        <a href="{{ $catLink }}" class="btn btn--outline">{{ $catLabel }}</a>
      </div>

    </div>

    @if ($thumbUrl)
      <figure class="home-hero__split-figure">
        <img
          src="{{ $thumbUrl }}"
          alt="{{ esc_attr($title) }}"
          class="home-hero__split-img"
          loading="eager"
          decoding="async"
          fetchpriority="high"
        >
      </figure>
    @endif
  </section>

@elseif ($treatment === 'typographic')

  <section
    class="home-hero home-hero--typographic"
    aria-label="Featured article"
  >
    <div class="home-hero__typographic-content">

      @if ($eyebrow)
        <div class="eyebrow eyebrow--centered">{{ $eyebrow }}</div>
      @endif

      <h1 class="{{ $titleClass }} home-hero__title--centered">
        <a href="{{ $permalink }}" class="home-hero__title-link">{{ $title }}</a>
      </h1>

      @if ($summary)
        <p class="home-hero__dek home-hero__dek--centered">{{ $summary }}</p>
      @endif

      <div class="home-hero__actions home-hero__actions--centered">
        <a href="{{ $permalink }}" class="btn btn--primary">Read the review</a>
        <a href="{{ $catLink }}" class="btn btn--outline">{{ $catLabel }}</a>
      </div>

    </div>

    @if ($thumbUrl)
      <figure class="home-hero__typographic-figure">
        <img
          src="{{ $thumbUrl }}"
          alt="{{ esc_attr($title) }}"
          class="home-hero__typographic-img"
          loading="eager"
          decoding="async"
          fetchpriority="high"
        >
      </figure>
    @endif
  </section>

@endif

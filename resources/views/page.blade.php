@extends('layouts.app')

@section('content')

  @while(have_posts())

  @php
    the_post();
    $post      = get_post();
    $heroImage = get_the_post_thumbnail_url($post->ID, 'full');
    $summary   = get_post_meta($post->ID, '_summary', true) ?: get_the_excerpt();
    $eyebrow   = get_post_meta($post->ID, '_eyebrow', true);
  @endphp

  {{-- Reading progress bar --}}
  <x-reading-progress />

  {{-- Page hero --}}
  <header class="article-hero">
    <div class="article-hero__inner">
      <nav class="article-hero__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ home_url('/') }}">CKIA</a>
        <span class="article-hero__breadcrumb-sep" aria-hidden="true">/</span>
        <span aria-current="page">{{ get_the_title() }}</span>
      </nav>

      @if ($eyebrow)
        <x-eyebrow color="seafoam">{{ $eyebrow }}</x-eyebrow>
      @endif

      <h1 class="article-hero__title">{{ get_the_title() }}</h1>

      @if ($summary)
        <p class="article-hero__dek">{{ $summary }}</p>
      @endif
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

  {{-- Two-column body: TOC · content --}}
  <div class="article-layout article-layout--page">

    {{-- Left: Table of Contents --}}
    <aside class="article-toc" aria-label="Table of contents">
      <nav class="article-toc__nav" id="article-toc">
        <div class="article-toc__heading" aria-hidden="true">In this article</div>
        {{-- TOC links injected by JS from h2 headings --}}
      </nav>
    </aside>

    {{-- Center: page body --}}
    <article class="article-body" id="article-body">
      {!! apply_filters('the_content', get_the_content()) !!}
    </article>

  </div>

  @endwhile

@endsection

@extends('layouts.app')

@section('content')
  {{--
    Homepage — mirrors the CKIA Redesign prototype.
    Data is passed from App\View\Composers\HomepageComposer.
    Variables: $featured, $reviews, $tips, $destinations, $popular
  --}}

  {{-- Hero: pulls the first "featured" post --}}
  @if ($featured)
    @php
      $heroTreatment = get_post_meta($featured->ID, '_hero_treatment', true) ?: 'fullbleed';
    @endphp
    <x-home-hero :article="$featured" :treatment="$heroTreatment" />
  @endif

  {{-- Featured grid: mix of all categories --}}
  <section class="feature-grid" aria-labelledby="feature-grid-title">
    <div class="section-header">
      <div class="section-header__title">
        <x-eyebrow>This week on CKIA</x-eyebrow>
        <h2 id="feature-grid-title" class="section-heading">What we sailed, what we'd book</h2>
      </div>
      <a href="/reviews" class="section-link">All articles →</a>
    </div>
    <div class="feature-grid__grid">
      @foreach (array_slice($allRecent, 0, 6) as $post)
        <x-article-card :post="$post" />
      @endforeach
    </div>
  </section>

  {{-- Tips magazine row --}}
  @if (count($tips) > 0)
    <section class="magazine-row" aria-labelledby="tips-section-title">
      <div class="section-header">
        <div class="section-header__title">
          <x-eyebrow>Tips · Practical guidance</x-eyebrow>
          <h2 id="tips-section-title" class="section-heading">The kind of thing your travel agent won't tell you</h2>
        </div>
      </div>
      <div class="magazine-row__grid">
        <x-article-card :post="$tips[0]" />
        <div class="magazine-row__stack">
          @foreach (array_slice($tips, 1, 3) as $post)
            <x-article-card :post="$post" layout="text" :showSummary="true" />
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- Destinations rail --}}
  @if (count($destinations) > 0)
    <section class="destinations-rail" aria-labelledby="destinations-section-title">
      <div class="destinations-rail__inner">
        <div class="section-header">
          <div class="section-header__title">
            <x-eyebrow color="ink-500">Destinations</x-eyebrow>
            <h2 id="destinations-section-title" class="section-heading">Where the ports actually deliver</h2>
          </div>
          <a href="/destinations" class="section-link">All destinations →</a>
        </div>
        <div class="destinations-rail__grid">
          @foreach (array_slice($destinations, 0, 3) as $post)
            <x-article-card :post="$post" :accentTag="true" />
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- Most read / popular --}}
  @if (count($popular) > 0)
    <section class="popular-rail" aria-labelledby="popular-section-title">
      <div class="popular-rail__inner">
        <div class="popular-rail__intro">
          <x-eyebrow>Most read</x-eyebrow>
          <h2 id="popular-section-title" class="section-heading" style="margin:8px 0 16px">What readers came back to this month</h2>
          <p style="font-family:var(--font-sans);font-size:15px;line-height:1.6;color:var(--ink-500);margin:0">
            Ranked by complete reads — not clicks. The articles people stayed with from headline to last paragraph.
          </p>
        </div>
        <ol class="popular-rail__list" aria-label="Most read articles">
          @foreach (array_slice($popular, 0, 5) as $i => $post)
            <li class="popular-rail__item">
              <a href="{{ get_permalink($post) }}" class="popular-rail__item-link" style="display:grid;grid-template-columns:48px 1fr;gap:var(--space-5);width:100%;text-decoration:none;color:inherit">
                <span class="popular-rail__num" aria-hidden="true">0{{ $i + 1 }}</span>
                <div>
                  <div class="eyebrow eyebrow--seafoam" style="margin-bottom:6px">
                    {{ get_post_meta($post->ID, '_eyebrow', true) ?: strtoupper(get_the_category($post->ID)[0]->name ?? '') }}
                  </div>
                  <h3 class="popular-rail__item-title">{{ $post->post_title }}</h3>
                  <div style="font-family:var(--font-sans);font-size:13px;color:var(--ink-400);margin-top:4px">
                    {{ get_the_author_meta('display_name', $post->post_author) }}
                    · {{ get_the_date('M j', $post) }}
                    · {{ get_post_meta($post->ID, '_read_minutes', true) ?: '5' }} min
                  </div>
                </div>
              </a>
            </li>
          @endforeach
        </ol>
      </div>
    </section>
  @endif

  {{-- Voyana CTA --}}
  <x-voyana-cta prominence="medium" />

  {{-- Newsletter --}}
  <x-newsletter />

@endsection

<header class="site-header" id="site-header">
  <div class="site-header__inner">

    <a class="site-header__logo" href="{{ home_url('/') }}" aria-label="CKIA — Cruising Know It All, go to homepage">
      @php
        $ckia_logo = function_exists('get_field') ? get_field('site_logo', 'option') : null;
      @endphp
      <img
        src="{{ $ckia_logo['url'] ?? get_template_directory_uri() . '/public/images/ckia-mark-nav.png' }}"
        alt="{{ $ckia_logo['alt'] ?? 'CKIA' }}"
        class="site-header__logo-img"
        width="{{ $ckia_logo['width'] ?? 80 }}"
        height="{{ $ckia_logo['height'] ?? 32 }}"
      >
    </a>

    <nav class="site-nav" id="site-nav" aria-label="Primary navigation">
      @php
        wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'container'      => false,
          'items_wrap'     => '<ul class="site-nav__list" role="list">%3$s</ul>',
          'walker'         => new \App\CkiaNavWalker(),
          'fallback_cb'    => false,
        ]);
      @endphp
    </nav>

    <div class="site-header__actions">

      <button
        class="site-header__search-btn"
        type="button"
        aria-label="Toggle search"
        aria-expanded="false"
        aria-controls="site-search-bar"
        data-search-toggle
      >
        <svg class="site-header__search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" width="20" height="20">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <span class="sr-only">Search</span>
      </button>

      @php $voyana_url = function_exists('get_field') ? get_field('voyana_url', 'option') : ''; @endphp
      <a href="{{ $voyana_url ?: '#' }}" class="btn btn--medium"{{ $voyana_url ? ' target="_blank" rel="noopener"' : '' }}>Get Voyana</a>

    </div>

    <button
      class="site-header__menu-btn"
      type="button"
      aria-label="Open menu"
      aria-expanded="false"
      aria-controls="site-nav"
      data-menu-toggle
    >
      <span class="site-header__menu-bar" aria-hidden="true"></span>
      <span class="site-header__menu-bar" aria-hidden="true"></span>
      <span class="site-header__menu-bar" aria-hidden="true"></span>
      <span class="sr-only">Menu</span>
    </button>

  </div>{{-- /.site-header__inner --}}
</header>

<div
  class="site-search-bar"
  id="site-search-bar"
  role="search"
  aria-label="Site search"
  hidden
>
  <div class="site-search-bar__inner">
    <form class="site-search-bar__form" method="get" action="{{ home_url('/') }}">
      <label for="site-search-input" class="sr-only">Search CKIA</label>
      <input
        id="site-search-input"
        class="site-search-bar__input"
        type="search"
        name="s"
        placeholder="Search reviews, tips, destinations…"
        autocomplete="off"
      >
      <button class="btn btn--primary site-search-bar__submit" type="submit">Search</button>
    </form>

    <button
      class="site-search-bar__close"
      type="button"
      aria-label="Close search"
      data-search-toggle
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" width="20" height="20">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
      <span class="sr-only">Close</span>
    </button>
  </div>
</div>

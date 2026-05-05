<footer class="site-footer">
  <div class="site-footer__inner">

    <div class="site-footer__grid">

      {{-- Column 1: Brand --}}
      <div class="site-footer__col site-footer__col--brand">
        @php
          $ckia_logo = function_exists('get_field') ? get_field('site_logo', 'option') : null;
        @endphp
        <a href="{{ home_url('/') }}" class="site-footer__logo-link" aria-label="CKIA homepage">
          <img
            src="{{ $ckia_logo['url'] ?? get_template_directory_uri() . '/public/images/ckia-mark-nav.png' }}"
            alt="{{ $ckia_logo['alt'] ?? 'CKIA' }}"
            class="site-footer__logo"
            width="{{ $ckia_logo['width'] ?? 80 }}"
            height="{{ $ckia_logo['height'] ?? 32 }}"
          >
        </a>
        <p class="site-footer__tagline">
          An editorial product of Voyana. We sail the ships, file the receipts, and tell readers what we'd actually book.
        </p>
      </div>

      {{-- Column 2: Read --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="Editorial sections">
        <h3 class="site-footer__col-heading">Read</h3>
        @php
          wp_nav_menu([
            'theme_location' => 'footer_read',
            'container'      => false,
            'items_wrap'     => '<ul class="site-footer__nav-list" role="list">%3$s</ul>',
            'walker'         => new \App\CkiaFooterNavWalker(),
            'fallback_cb'    => false,
          ]);
        @endphp
      </nav>

      {{-- Column 3: Voyana --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="Voyana product links">
        <h3 class="site-footer__col-heading">Voyana</h3>
        @php
          wp_nav_menu([
            'theme_location' => 'footer_voyana',
            'container'      => false,
            'items_wrap'     => '<ul class="site-footer__nav-list" role="list">%3$s</ul>',
            'walker'         => new \App\CkiaFooterNavWalker(),
            'fallback_cb'    => false,
          ]);
        @endphp
      </nav>

      {{-- Column 4: About --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="About CKIA">
        <h3 class="site-footer__col-heading">About</h3>
        @php
          wp_nav_menu([
            'theme_location' => 'footer_about',
            'container'      => false,
            'items_wrap'     => '<ul class="site-footer__nav-list" role="list">%3$s</ul>',
            'walker'         => new \App\CkiaFooterNavWalker(),
            'fallback_cb'    => false,
          ]);
        @endphp
      </nav>

    </div>{{-- /.site-footer__grid --}}

    <div class="site-footer__bottom">
      <p class="site-footer__legal">
        &copy; {{ date('Y') }} Voyana, Inc. CKIA is an editorial product.
      </p>
      <p class="site-footer__style-note">Sentence-case, always.</p>
    </div>

  </div>{{-- /.site-footer__inner --}}
</footer>

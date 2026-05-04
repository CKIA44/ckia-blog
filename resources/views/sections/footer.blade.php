<footer class="site-footer">
  <div class="site-footer__inner">

    <div class="site-footer__grid">

      {{-- Column 1: Brand --}}
      <div class="site-footer__col site-footer__col--brand">
        <a href="{{ home_url('/') }}" class="site-footer__logo-link" aria-label="CKIA homepage">
          <img
            src="{{ get_template_directory_uri() }}/images/ckia-mark-nav.png"
            alt="CKIA"
            class="site-footer__logo"
            width="80"
            height="32"
          >
        </a>
        <p class="site-footer__tagline">
          An editorial product of Voyana. We sail the ships, file the receipts, and tell readers what we'd actually book.
        </p>
      </div>

      {{-- Column 2: Read --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="Editorial sections">
        <h3 class="site-footer__col-heading">Read</h3>
        <ul class="site-footer__nav-list" role="list">
          <li><a href="/reviews" class="site-footer__nav-link">Reviews</a></li>
          <li><a href="/tips" class="site-footer__nav-link">Tips</a></li>
          <li><a href="/destinations" class="site-footer__nav-link">Destinations</a></li>
          <li><a href="/lines" class="site-footer__nav-link">Cruise lines</a></li>
          <li><a href="/newsletter" class="site-footer__nav-link">Newsletter</a></li>
        </ul>
      </nav>

      {{-- Column 3: Voyana --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="Voyana product links">
        <h3 class="site-footer__col-heading">Voyana</h3>
        <ul class="site-footer__nav-list" role="list">
          <li><a href="#" class="site-footer__nav-link">Plan a cruise</a></li>
          <li><a href="#" class="site-footer__nav-link">Track prices</a></li>
          <li><a href="#" class="site-footer__nav-link">Flight monitor</a></li>
          <li><a href="#" class="site-footer__nav-link">About Voyana</a></li>
        </ul>
      </nav>

      {{-- Column 4: About --}}
      <nav class="site-footer__col site-footer__col--nav" aria-label="About CKIA">
        <h3 class="site-footer__col-heading">About</h3>
        <ul class="site-footer__nav-list" role="list">
          <li><a href="#" class="site-footer__nav-link">Editorial standards</a></li>
          <li><a href="#" class="site-footer__nav-link">How we review</a></li>
          <li><a href="#" class="site-footer__nav-link">Press</a></li>
          <li><a href="#" class="site-footer__nav-link">Careers</a></li>
          <li><a href="#" class="site-footer__nav-link">Contact</a></li>
        </ul>
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

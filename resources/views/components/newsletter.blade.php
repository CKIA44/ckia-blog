<section class="newsletter-section" aria-labelledby="newsletter-heading">
  <div class="newsletter-section__inner">

    <div class="newsletter-section__content">
      <h2 class="newsletter-section__heading font-serif" id="newsletter-heading">
        The CKIA dispatch
      </h2>
      <p class="newsletter-section__dek">
        Ship reviews, fare intelligence, and destination picks — delivered to your inbox every two weeks. No fluff, no sponsored filler.
      </p>
    </div>

    <div class="newsletter-section__form-wrap">

      @if (isset($_GET['subscribed']) && $_GET['subscribed'] === '1')

        <div class="newsletter-section__success" role="status" aria-live="polite">
          <div class="newsletter-section__success-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" aria-hidden="true" focusable="false">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <p class="newsletter-section__success-text">
            You're in. Watch your inbox for the next dispatch.
          </p>
        </div>

      @else

        <form
          class="newsletter-section__form"
          method="post"
          action="{{ admin_url('admin-post.php') }}"
          novalidate
        >
          {!! wp_nonce_field('newsletter_subscribe', '_wpnonce', true, false) !!}
          <input type="hidden" name="action" value="newsletter_subscribe">

          <div class="newsletter-section__field-group">
            <label class="newsletter-section__label" for="newsletter-email">
              Email address
            </label>
            <div class="newsletter-section__input-row">
              <input
                class="newsletter-section__input"
                id="newsletter-email"
                type="email"
                name="newsletter_email"
                placeholder="you@example.com"
                autocomplete="email"
                required
                aria-required="true"
                aria-describedby="newsletter-privacy"
              >
              <button class="btn btn--primary newsletter-section__submit" type="submit">
                Subscribe
              </button>
            </div>
          </div>

          <p class="newsletter-section__privacy" id="newsletter-privacy">
            No spam. Unsubscribe any time. Read our <a href="#" class="newsletter-section__privacy-link">privacy policy</a>.
          </p>
        </form>

      @endif

    </div>{{-- /.newsletter-section__form-wrap --}}

  </div>{{-- /.newsletter-section__inner --}}
</section>

@props(['prominence' => 'medium'])

@if ($prominence === 'subtle')
  {{-- Render nothing for subtle prominence --}}
@else

  @php
    $innerClass = match ($prominence) {
      'strong' => 'voyana-cta__inner voyana-cta__inner--strong',
      default  => 'voyana-cta__inner voyana-cta__inner--medium',
    };
  @endphp

  <aside class="voyana-cta" aria-label="Voyana — plan and track your cruise">
    <div class="{{ $innerClass }}">

      <div class="voyana-cta__header">
        <div class="voyana-cta__icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" class="voyana-cta__icon-svg" width="40" height="40" aria-hidden="true" focusable="false">
            <rect width="40" height="40" rx="8" fill="currentColor" class="voyana-cta__icon-bg"/>
            <path d="M10 28 L20 12 L30 28" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            <circle cx="20" cy="12" r="2" fill="white"/>
          </svg>
        </div>

        <div class="voyana-cta__heading-group">
          <h2 class="voyana-cta__heading font-sans">Track any cruise in Voyana</h2>
          <p class="voyana-cta__dek">
            Save itineraries, monitor prices, and get fare-drop alerts — so you book at the right moment. Free to use.
          </p>
        </div>
      </div>

      <div class="voyana-cta__actions">
        <a href="#" class="btn {{ $prominence === 'strong' ? 'btn--primary-fog' : 'btn--primary' }}">
          Open Voyana &rarr;
        </a>
        <a href="#" class="btn {{ $prominence === 'strong' ? 'btn--outline-fog' : 'btn--outline' }}">
          Take the tour
        </a>
      </div>

      <ul class="voyana-cta__features" role="list">

        <li class="voyana-cta__feature">
          <div class="voyana-cta__feature-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true" focusable="false">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="voyana-cta__feature-body">
            <strong class="voyana-cta__feature-title font-sans">Planner</strong>
            <span class="voyana-cta__feature-desc">Build and compare full cruise itineraries side-by-side.</span>
          </div>
        </li>

        <li class="voyana-cta__feature">
          <div class="voyana-cta__feature-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true" focusable="false">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <div class="voyana-cta__feature-body">
            <strong class="voyana-cta__feature-title font-sans">Tracker</strong>
            <span class="voyana-cta__feature-desc">Watch cabin prices in real time and get notified on drops.</span>
          </div>
        </li>

        <li class="voyana-cta__feature">
          <div class="voyana-cta__feature-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true" focusable="false">
              <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9z"/>
            </svg>
          </div>
          <div class="voyana-cta__feature-body">
            <strong class="voyana-cta__feature-title font-sans">Flights</strong>
            <span class="voyana-cta__feature-desc">Monitor positioning flights and set fare alerts for embarkation cities.</span>
          </div>
        </li>

      </ul>

    </div>{{-- /.voyana-cta__inner --}}
  </aside>

@endif

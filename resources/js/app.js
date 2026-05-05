/**
 * CKIA Theme — Main JavaScript
 * Handles: sticky header, search toggle, reading progress, TOC, mobile nav
 */

// ── Sticky header scroll behavior ─────────────────────────────────────────────
(function initStickyHeader() {
  const header = document.getElementById('site-header');
  if (!header) return;

  let ticking = false;

  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(() => {
        header.classList.toggle('scrolled', window.scrollY > 8);
        ticking = false;
      });
      ticking = true;
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ── Search toggle ──────────────────────────────────────────────────────────────
(function initSearch() {
  const searchBar   = document.getElementById('site-search-bar');
  const searchInput = searchBar?.querySelector('input[type="search"]');
  const toggleBtns  = document.querySelectorAll('[data-search-toggle]');
  const searchBtn   = document.querySelector('[data-search-toggle][aria-expanded]');

  if (!searchBar || !toggleBtns.length) return;

  function openSearch() {
    searchBar.hidden = false;
    searchBar.classList.add('is-open');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'true');
    requestAnimationFrame(() => searchInput?.focus());
  }

  function closeSearch() {
    searchBar.hidden = true;
    searchBar.classList.remove('is-open');
    if (searchBtn) searchBtn.setAttribute('aria-expanded', 'false');
  }

  toggleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      searchBar.hidden ? openSearch() : closeSearch();
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !searchBar.hidden) closeSearch();
  });
})();

// ── Mobile nav toggle ──────────────────────────────────────────────────────────
(function initMobileNav() {
  const menuBtn = document.querySelector('[data-menu-toggle]');
  const nav     = document.getElementById('site-nav');

  if (!menuBtn || !nav) return;

  menuBtn.addEventListener('click', () => {
    const isOpen = menuBtn.getAttribute('aria-expanded') === 'true';
    menuBtn.setAttribute('aria-expanded', String(!isOpen));
    nav.classList.toggle('is-open', !isOpen);
    menuBtn.setAttribute('aria-label', isOpen ? 'Open menu' : 'Close menu');
  });
})();

// ── Reading progress bar ───────────────────────────────────────────────────────
(function initReadingProgress() {
  const bar = document.querySelector('.reading-progress__bar');
  if (!bar) return;

  let ticking = false;

  function update() {
    const h   = document.documentElement;
    const max = h.scrollHeight - h.clientHeight;
    const pct = max > 0 ? Math.min(100, (h.scrollTop / max) * 100) : 0;
    bar.style.width = pct + '%';
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(update);
      ticking = true;
    }
  }, { passive: true });
})();

// ── Article TOC: build from headings + active section tracking ─────────────────
(function initTOC() {
  const tocNav  = document.getElementById('article-toc');
  const body    = document.getElementById('article-body');
  if (!tocNav || !body) return;

  const headings = Array.from(body.querySelectorAll('h2'));
  if (!headings.length) return;

  headings.forEach((h, i) => {
    if (!h.id) h.id = 'section-' + i;
    h.style.scrollMarginTop = '80px';
  });

  headings.forEach(h => {
    const link = document.createElement('a');
    link.href = '#' + h.id;
    link.className = 'article-toc__link';
    link.textContent = h.textContent.trim();
    tocNav.appendChild(link);
  });

  const links = Array.from(tocNav.querySelectorAll('.article-toc__link'));

  function setActive(id) {
    links.forEach(l => l.classList.toggle('is-active', l.getAttribute('href') === '#' + id));
  }

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries
          .filter(e => e.isIntersecting)
          .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible.length) setActive(visible[0].target.id);
      },
      { rootMargin: '-80px 0px -60% 0px', threshold: 0 }
    );
    headings.forEach(h => observer.observe(h));
  } else {
    function updateActive() {
      let current = headings[0]?.id;
      headings.forEach(h => {
        if (h.getBoundingClientRect().top <= 120) current = h.id;
      });
      if (current) setActive(current);
    }
    window.addEventListener('scroll', updateActive, { passive: true });
    updateActive();
  }
})();

// ── Category filter: sort select ───────────────────────────────────────────────
(function initFilterSort() {
  const select = document.getElementById('sort-select');
  if (!select) return;

  select.addEventListener('change', () => {
    const params = new URLSearchParams(window.location.search);
    params.set('sort', select.value);
    window.location.search = params.toString();
  });
})();

// ── Newsletter: show success state if URL param present ────────────────────────
(function initNewsletter() {
  if (new URLSearchParams(window.location.search).get('subscribed') !== '1') return;
  const form    = document.querySelector('.newsletter__form-fields');
  const success = document.querySelector('.newsletter__success');
  if (form) form.hidden = true;
  if (success) success.hidden = false;
})();

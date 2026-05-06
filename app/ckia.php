<?php

/**
 * CKIA-specific functionality: newsletter handler, ACF fields, post meta helpers.
 */

namespace App;

// ── Nav walkers ───────────────────────────────────────────────────────────────

class CkiaNavWalker extends \Walker_Nav_Menu
{
    private const CHEVRON = '<svg class="site-nav__chevron" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="6 9 12 15 18 9"/></svg>';

    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '<ul class="sub-menu site-nav__dropdown" role="list">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '</ul>';
    }

    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
    {
        $classes      = (array) ($data_object->classes ?? []);
        $is_active    = in_array('current-menu-item', $classes, true)
                     || in_array('current-menu-ancestor', $classes, true);
        $has_children = in_array('menu-item-has-children', $classes, true);

        if ($depth === 0) {
            $item_class = 'menu-item site-nav__item'
                        . ($has_children ? ' menu-item-has-children site-nav__item--has-dropdown' : '')
                        . ($is_active    ? ' current-menu-item site-nav__item--active'            : '');
            $link_class = 'site-nav__link' . ($is_active ? ' site-nav__link--active' : '');
            $aria       = $is_active ? ' aria-current="page"' : '';
            if ($has_children) {
                $aria .= ' aria-haspopup="true" aria-expanded="false"';
            }
            $output .= '<li class="' . esc_attr($item_class) . '">'
                     . '<a href="' . esc_url($data_object->url) . '" class="' . esc_attr($link_class) . '"' . $aria . '>'
                     . wp_kses_post($data_object->title)
                     . ($has_children ? self::CHEVRON : '')
                     . '</a>';
        } else {
            $link_class = 'site-nav__dropdown-link' . ($is_active ? ' site-nav__dropdown-link--active' : '');
            $aria       = $is_active ? ' aria-current="page"' : '';
            $output .= '<li>'
                     . '<a href="' . esc_url($data_object->url) . '" class="' . esc_attr($link_class) . '"' . $aria . '>'
                     . wp_kses_post($data_object->title)
                     . '</a>';
        }
    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null)
    {
        $output .= '</li>';
    }
}

class CkiaFooterNavWalker extends \Walker_Nav_Menu
{
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
    {
        $output .= '<li>'
                 . '<a href="' . esc_url($data_object->url) . '" class="site-footer__nav-link">'
                 . wp_kses_post($data_object->title)
                 . '</a>';
    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null)
    {
        $output .= '</li>';
    }
}

// ── Dropdown nav: inline CSS (no Vite build required) ────────────────────────

add_action('wp_head', function () {
    echo '<style id="ckia-nav-dropdown">
.site-nav__list > .menu-item{position:relative}
.site-nav__list > .menu-item-has-children > a{display:inline-flex;align-items:center;gap:4px}
.site-nav__list > .menu-item-has-children > a::after{
  content:"";display:inline-block;flex-shrink:0;
  width:10px;height:10px;margin-left:2px;
  background:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%230F4C6B\' stroke-width=\'2.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpolyline points=\'6 9 12 15 18 9\'/%3E%3C/svg%3E") center/contain no-repeat;
  transition:transform 120ms cubic-bezier(.2,.8,.2,1)
}
.site-nav__list > .menu-item-has-children:hover > a::after,
.site-nav__list > .menu-item-has-children.is-open > a::after,
.site-nav__list > .menu-item-has-children:focus-within > a::after{transform:rotate(180deg)}
.site-nav__list > .menu-item-has-children > .sub-menu{
  list-style:none;margin:0;padding:8px 0;
  position:absolute;top:calc(100% + 10px);left:50%;
  transform:translateX(-50%) translateY(-6px);
  min-width:210px;background:#fff;
  border:1px solid #D8E4EC;border-radius:8px;
  box-shadow:0 8px 24px rgba(15,76,107,.09);
  opacity:0;visibility:hidden;pointer-events:none;
  transition:opacity 120ms ease,visibility 120ms ease,transform 120ms ease;
  z-index:200
}
.site-nav__list > .menu-item-has-children:hover > .sub-menu,
.site-nav__list > .menu-item-has-children.is-open > .sub-menu,
.site-nav__list > .menu-item-has-children:focus-within > .sub-menu{
  opacity:1;visibility:visible;pointer-events:auto;
  transform:translateX(-50%) translateY(0)
}
.site-nav__list .sub-menu a{
  display:block;padding:9px 16px;
  font-family:inherit;font-size:14px;font-weight:400;
  color:#0F4C6B;text-decoration:none;white-space:nowrap;
  transition:background 100ms ease,color 100ms ease
}
.site-nav__list .sub-menu a:hover{background:#F5F9FC;color:#0A2F42}
.site-nav__list .sub-menu .current-menu-item > a{color:#0F4C6B;font-weight:500}
.site-nav__list .sub-menu .menu-item-has-children > .sub-menu{
  left:100%;top:0;transform:none;
  opacity:0;visibility:hidden;pointer-events:none
}
.site-nav__list .sub-menu .menu-item-has-children:hover > .sub-menu,
.site-nav__list .sub-menu .menu-item-has-children:focus-within > .sub-menu{
  opacity:1;visibility:visible;pointer-events:auto
}
@media(max-width:640px){
  .site-nav__list > .menu-item-has-children > a::after{display:none}
  .site-nav__list > .menu-item-has-children{flex-direction:column;align-items:flex-start}
  .site-nav__list > .menu-item-has-children > .sub-menu{
    position:static;transform:none;
    opacity:1;visibility:visible;pointer-events:auto;
    box-shadow:none;border:none;
    border-left:2px solid #D8E4EC;border-radius:0;
    padding:4px 0;margin-left:12px;background:transparent
  }
  .site-nav__list .sub-menu a{padding:6px 12px;white-space:normal}
}
</style>' . "\n";
}, 20);

// ── Dropdown nav: inline JS (no Vite build required) ─────────────────────────

add_action('wp_footer', function () {
    echo '<script id="ckia-nav-dropdown-js">
(function(){
  var nav=document.querySelector(".site-nav__list");
  if(!nav)return;
  var items=nav.querySelectorAll(":scope > .menu-item-has-children");
  if(!items.length)return;
  function close(el){
    el.classList.remove("is-open");
    var a=el.querySelector(":scope > a");
    if(a)a.setAttribute("aria-expanded","false");
  }
  function open(el){
    el.classList.add("is-open");
    var a=el.querySelector(":scope > a");
    if(a)a.setAttribute("aria-expanded","true");
  }
  function closeAll(skip){items.forEach(function(el){if(el!==skip)close(el);});}
  items.forEach(function(item){
    var link=item.querySelector(":scope > a");
    if(!link)return;
    var hoverTimer=null;
    item.addEventListener("mouseenter",function(){
      clearTimeout(hoverTimer);
      closeAll(item);
      open(item);
    });
    item.addEventListener("mouseleave",function(){
      hoverTimer=setTimeout(function(){close(item);},200);
    });
    link.addEventListener("click",function(e){
      if(window.matchMedia("(hover:hover)").matches)return;
      var isOpen=item.classList.contains("is-open");
      closeAll(item);
      if(!isOpen){e.preventDefault();open(item);}
    });
    link.addEventListener("keydown",function(e){
      if(e.key==="Enter"||e.key===" "){
        var isOpen=item.classList.contains("is-open");
        e.preventDefault();closeAll(item);
        isOpen?close(item):open(item);
      }
      if(e.key==="Escape"){close(item);link.focus();}
      if(e.key==="ArrowDown"){
        e.preventDefault();open(item);
        var first=item.querySelector(".sub-menu a");
        if(first)first.focus();
      }
    });
  });
  document.addEventListener("keydown",function(e){if(e.key==="Escape")closeAll(null);});
  document.addEventListener("pointerdown",function(e){
    if(!e.target.closest(".site-nav__list > .menu-item-has-children"))closeAll(null);
  },true);
})();
</script>' . "\n";
}, 20);

// ── Font Awesome (for nav menu icons) ────────────────────────────────────────

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', [], '4.7.0');
});

// ── Newsletter form handler ───────────────────────────────────────────────────

add_action('admin_post_newsletter_subscribe', function () {
    check_admin_referer('newsletter_subscribe');

    $email = sanitize_email($_POST['newsletter_email'] ?? '');

    if (is_email($email)) {
        // Store subscriber — replace with your actual newsletter provider
        // e.g. Mailchimp, Beehiiv, ConvertKit, or a simple WP option
        $subscribers = get_option('ckia_newsletter_subscribers', []);
        if (! in_array($email, $subscribers, true)) {
            $subscribers[] = $email;
            update_option('ckia_newsletter_subscribers', $subscribers);
        }

        wp_redirect(add_query_arg('subscribed', '1', wp_get_referer() ?: home_url('/')));
    } else {
        wp_redirect(add_query_arg('subscribed', 'error', wp_get_referer() ?: home_url('/')));
    }

    exit;
});

// Also handle for non-logged-in users
add_action('admin_post_nopriv_newsletter_subscribe', function () {
    do_action('admin_post_newsletter_subscribe');
});


// ── ACF field groups (requires ACF Pro or free ACF) ───────────────────────────

add_action('acf/init', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    // Article fields
    acf_add_local_field_group([
        'key'      => 'group_ckia_article',
        'title'    => 'CKIA Article Options',
        'fields'   => [
            [
                'key'          => 'field_ckia_featured',
                'label'        => 'Featured article',
                'name'         => '_featured',
                'type'         => 'true_false',
                'instructions' => 'Surfaces this post as the homepage hero.',
                'ui'           => 1,
            ],
            [
                'key'          => 'field_ckia_hero_treatment',
                'label'        => 'Homepage hero treatment',
                'name'         => '_hero_treatment',
                'type'         => 'select',
                'choices'      => [
                    'fullbleed'   => 'Full bleed photo',
                    'split'       => 'Split (text left, photo right)',
                    'typographic' => 'Typographic (large type + photo below)',
                ],
                'default_value' => 'fullbleed',
                'conditional_logic' => [
                    [['field' => 'field_ckia_featured', 'operator' => '==', 'value' => '1']],
                ],
            ],
            [
                'key'          => 'field_ckia_eyebrow',
                'label'        => 'Eyebrow label',
                'name'         => '_eyebrow',
                'type'         => 'text',
                'instructions' => 'Short uppercase label, e.g. "REVIEWS · CABINS". Leave blank to auto-generate from category.',
                'placeholder'  => 'REVIEWS · CABINS',
            ],
            [
                'key'          => 'field_ckia_summary',
                'label'        => 'Article summary (dek)',
                'name'         => '_summary',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => '1–2 sentence summary shown on cards and below the article title. Falls back to excerpt.',
            ],
            [
                'key'          => 'field_ckia_read_minutes',
                'label'        => 'Reading time (minutes)',
                'name'         => '_read_minutes',
                'type'         => 'number',
                'min'          => 1,
                'max'          => 60,
                'default_value' => 5,
                'instructions' => 'Estimated reading time in minutes.',
            ],
        ],
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'post']],
        ],
        'menu_order' => 0,
        'position'   => 'side',
        'style'      => 'default',
    ]);

    // Author extras
    acf_add_local_field_group([
        'key'    => 'group_ckia_author',
        'title'  => 'CKIA Author Details',
        'fields' => [
            [
                'key'   => 'field_ckia_author_role',
                'label' => 'Role / title',
                'name'  => 'user_title',
                'type'  => 'text',
            ],
        ],
        'location' => [
            [['param' => 'user_form', 'operator' => '==', 'value' => 'all']],
        ],
    ]);
});


// ── Voyana cross-promo prominence (Theme Options page) ────────────────────────

add_action('acf/init', function () {
    if (! function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title' => 'CKIA Settings',
        'menu_title' => 'CKIA Settings',
        'menu_slug'  => 'ckia-settings',
        'capability' => 'edit_theme_options',
        'redirect'   => false,
    ]);

    // Site Identity
    acf_add_local_field_group([
        'key'    => 'group_ckia_identity',
        'title'  => 'Site Identity',
        'fields' => [
            [
                'key'           => 'field_ckia_site_logo',
                'label'         => 'Site logo',
                'name'          => 'site_logo',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'instructions'  => 'Recommended: PNG with transparency, at least 160 × 64 px. Leave blank to use the default CKIA mark.',
            ],
            [
                'key'          => 'field_ckia_site_tagline',
                'label'        => 'Site tagline',
                'name'         => 'site_tagline',
                'type'         => 'text',
                'placeholder'  => 'Honest cruising advice.',
                'instructions' => 'Short phrase shown in the footer and meta description.',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']]],
        'menu_order' => 0,
    ]);

    // Social Links
    acf_add_local_field_group([
        'key'    => 'group_ckia_social',
        'title'  => 'Social Links',
        'fields' => [
            [
                'key'         => 'field_ckia_social_instagram',
                'label'       => 'Instagram URL',
                'name'        => 'social_instagram',
                'type'        => 'url',
                'placeholder' => 'https://instagram.com/ckia',
            ],
            [
                'key'         => 'field_ckia_social_twitter',
                'label'       => 'Twitter / X URL',
                'name'        => 'social_twitter',
                'type'        => 'url',
                'placeholder' => 'https://x.com/ckia',
            ],
            [
                'key'         => 'field_ckia_social_youtube',
                'label'       => 'YouTube URL',
                'name'        => 'social_youtube',
                'type'        => 'url',
                'placeholder' => 'https://youtube.com/@ckia',
            ],
            [
                'key'         => 'field_ckia_social_facebook',
                'label'       => 'Facebook URL',
                'name'        => 'social_facebook',
                'type'        => 'url',
                'placeholder' => 'https://facebook.com/ckia',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']]],
        'menu_order' => 10,
    ]);

    // Newsletter Block
    acf_add_local_field_group([
        'key'    => 'group_ckia_newsletter_copy',
        'title'  => 'Newsletter Block',
        'fields' => [
            [
                'key'          => 'field_ckia_newsletter_heading',
                'label'        => 'Heading',
                'name'         => 'newsletter_heading',
                'type'         => 'text',
                'placeholder'  => 'Get the honest truth about cruising.',
                'instructions' => 'Main headline for the newsletter signup section.',
            ],
            [
                'key'         => 'field_ckia_newsletter_subheading',
                'label'       => 'Subheading',
                'name'        => 'newsletter_subheading',
                'type'        => 'textarea',
                'rows'        => 2,
                'placeholder' => 'No fluff, no sponsored puff pieces — just real advice from real cruisers.',
            ],
            [
                'key'           => 'field_ckia_newsletter_button',
                'label'         => 'Button label',
                'name'          => 'newsletter_button_label',
                'type'          => 'text',
                'placeholder'   => 'Subscribe free',
                'default_value' => 'Subscribe free',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']]],
        'menu_order' => 20,
    ]);

    // Footer
    acf_add_local_field_group([
        'key'    => 'group_ckia_footer',
        'title'  => 'Footer',
        'fields' => [
            [
                'key'         => 'field_ckia_footer_tagline',
                'label'       => 'Footer tagline',
                'name'        => 'footer_tagline',
                'type'        => 'text',
                'placeholder' => 'Honest cruising advice since 2024.',
            ],
            [
                'key'          => 'field_ckia_footer_copyright',
                'label'        => 'Copyright line',
                'name'         => 'footer_copyright',
                'type'         => 'text',
                'placeholder'  => '© 2025 CKIA. All rights reserved.',
                'instructions' => 'Leave blank to auto-generate "© [year] CKIA."',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']]],
        'menu_order' => 30,
    ]);

    // Voyana Integration
    acf_add_local_field_group([
        'key'    => 'group_ckia_voyana',
        'title'  => 'Voyana Integration',
        'fields' => [
            [
                'key'           => 'field_voyana_prominence',
                'label'         => 'Voyana CTA prominence',
                'name'          => 'voyana_prominence',
                'type'          => 'select',
                'choices'       => [
                    'subtle' => 'Subtle (hidden)',
                    'medium' => 'Medium (sky background)',
                    'strong' => 'Strong (deep ocean)',
                ],
                'default_value' => 'medium',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']]],
        'menu_order' => 40,
    ]);
});

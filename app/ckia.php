<?php

/**
 * CKIA-specific functionality: newsletter handler, ACF fields, post meta helpers.
 */

namespace App;

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

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

    acf_add_local_field_group([
        'key'    => 'group_ckia_theme_options',
        'title'  => 'Theme Options',
        'fields' => [
            [
                'key'     => 'field_voyana_prominence',
                'label'   => 'Voyana CTA prominence',
                'name'    => 'voyana_prominence',
                'type'    => 'select',
                'choices' => [
                    'subtle' => 'Subtle (hidden)',
                    'medium' => 'Medium (sky background)',
                    'strong' => 'Strong (deep ocean)',
                ],
                'default_value' => 'medium',
            ],
        ],
        'location' => [
            [['param' => 'options_page', 'operator' => '==', 'value' => 'ckia-settings']],
        ],
    ]);
});

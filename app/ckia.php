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


// ── Voyana cross-promo prominence via ACF Pro (if available) ──────────────────

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


// ── Native WordPress theme settings page (no ACF Pro required) ────────────────

add_action('admin_menu', function () {
    add_menu_page(
        'CKIA Settings',
        'CKIA Settings',
        'edit_theme_options',
        'ckia-theme-settings',
        'App\ckia_settings_page',
        'dashicons-anchor',
        61
    );
});

add_action('admin_init', function () {
    register_setting('ckia_theme_options', 'ckia_options', [
        'sanitize_callback' => 'App\ckia_sanitize_options',
    ]);

    // Social links
    add_settings_section('ckia_social', 'Social Links', '__return_false', 'ckia-theme-settings');
    foreach ([
        'social_twitter'   => 'Twitter / X URL',
        'social_instagram' => 'Instagram URL',
        'social_youtube'   => 'YouTube URL',
        'social_facebook'  => 'Facebook URL',
    ] as $key => $label) {
        add_settings_field($key, $label, function () use ($key) {
            $opts = get_option('ckia_options', []);
            printf(
                '<input type="url" name="ckia_options[%s]" value="%s" class="regular-text" placeholder="https://">',
                esc_attr($key), esc_attr($opts[$key] ?? '')
            );
        }, 'ckia-theme-settings', 'ckia_social');
    }

    // Homepage
    add_settings_section('ckia_homepage', 'Homepage', '__return_false', 'ckia-theme-settings');
    add_settings_field('hero_treatment_default', 'Default hero treatment', function () {
        $opts = get_option('ckia_options', []);
        $val  = $opts['hero_treatment_default'] ?? 'fullbleed';
        foreach (['fullbleed' => 'Full bleed photo', 'split' => 'Split (text | photo)', 'typographic' => 'Typographic'] as $k => $l) {
            printf(
                '<label style="margin-right:16px"><input type="radio" name="ckia_options[hero_treatment_default]" value="%s"%s> %s</label>',
                esc_attr($k), checked($val, $k, false), esc_html($l)
            );
        }
    }, 'ckia-theme-settings', 'ckia_homepage');

    // Voyana CTA
    add_settings_section('ckia_voyana', 'Voyana CTA', '__return_false', 'ckia-theme-settings');
    add_settings_field('voyana_prominence', 'Prominence', function () {
        $opts = get_option('ckia_options', []);
        $val  = $opts['voyana_prominence'] ?? 'medium';
        foreach (['subtle' => 'Subtle (hidden)', 'medium' => 'Medium (sky)', 'strong' => 'Strong (deep ocean)'] as $k => $l) {
            printf(
                '<label style="margin-right:16px"><input type="radio" name="ckia_options[voyana_prominence]" value="%s"%s> %s</label>',
                esc_attr($k), checked($val, $k, false), esc_html($l)
            );
        }
    }, 'ckia-theme-settings', 'ckia_voyana');

    // Footer
    add_settings_section('ckia_footer', 'Footer', '__return_false', 'ckia-theme-settings');
    foreach ([
        'footer_tagline'   => ['Footer tagline',  'The world\'s most trusted cruise resource'],
        'footer_copyright' => ['Copyright text',  'Cruising Know It All. All rights reserved.'],
    ] as $key => [$label, $placeholder]) {
        add_settings_field($key, $label, function () use ($key, $placeholder) {
            $opts = get_option('ckia_options', []);
            printf(
                '<input type="text" name="ckia_options[%s]" value="%s" class="regular-text" placeholder="%s">',
                esc_attr($key), esc_attr($opts[$key] ?? ''), esc_attr($placeholder)
            );
        }, 'ckia-theme-settings', 'ckia_footer');
    }

    // Newsletter
    add_settings_section('ckia_newsletter', 'Newsletter', '__return_false', 'ckia-theme-settings');
    foreach ([
        'newsletter_heading'     => ['Heading',     'Never miss a sailing'],
        'newsletter_description' => ['Description', 'Cruise news, reviews and deals — straight to your inbox.'],
    ] as $key => [$label, $placeholder]) {
        add_settings_field($key, $label, function () use ($key, $placeholder) {
            $opts = get_option('ckia_options', []);
            printf(
                '<input type="text" name="ckia_options[%s]" value="%s" class="large-text" placeholder="%s">',
                esc_attr($key), esc_attr($opts[$key] ?? ''), esc_attr($placeholder)
            );
        }, 'ckia-theme-settings', 'ckia_newsletter');
    }
});

function ckia_sanitize_options(array $input): array
{
    $clean = [];
    foreach (['social_twitter', 'social_instagram', 'social_youtube', 'social_facebook'] as $key) {
        $clean[$key] = esc_url_raw($input[$key] ?? '');
    }
    foreach (['footer_tagline', 'footer_copyright', 'newsletter_heading', 'newsletter_description'] as $key) {
        $clean[$key] = sanitize_text_field($input[$key] ?? '');
    }
    $clean['hero_treatment_default'] = in_array($input['hero_treatment_default'] ?? '', ['fullbleed', 'split', 'typographic'], true)
        ? $input['hero_treatment_default'] : 'fullbleed';
    $clean['voyana_prominence'] = in_array($input['voyana_prominence'] ?? '', ['subtle', 'medium', 'strong'], true)
        ? $input['voyana_prominence'] : 'medium';
    return $clean;
}

function ckia_settings_page(): void
{
    if (! current_user_can('edit_theme_options')) return;
    ?>
    <div class="wrap">
        <h1>⚓ CKIA Theme Settings</h1>
        <?php settings_errors('ckia_options'); ?>
        <form method="post" action="options.php">
            <?php settings_fields('ckia_theme_options'); ?>

            <h2>🔗 Social Links</h2>
            <table class="form-table" role="presentation">
                <?php do_settings_fields('ckia-theme-settings', 'ckia_social'); ?>
            </table>

            <h2>🏠 Homepage</h2>
            <table class="form-table" role="presentation">
                <?php do_settings_fields('ckia-theme-settings', 'ckia_homepage'); ?>
            </table>

            <h2>🚢 Voyana CTA</h2>
            <table class="form-table" role="presentation">
                <?php do_settings_fields('ckia-theme-settings', 'ckia_voyana'); ?>
            </table>

            <h2>🦶 Footer</h2>
            <table class="form-table" role="presentation">
                <?php do_settings_fields('ckia-theme-settings', 'ckia_footer'); ?>
            </table>

            <h2>📧 Newsletter</h2>
            <table class="form-table" role="presentation">
                <?php do_settings_fields('ckia-theme-settings', 'ckia_newsletter'); ?>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <hr>
        <h2>Newsletter Subscribers</h2>
        <?php
        $subs = get_option('ckia_newsletter_subscribers', []);
        if ($subs) {
            echo '<p><strong>' . count($subs) . ' subscriber(s)</strong> via the on-site form:</p>';
            echo '<textarea rows="6" class="large-text" readonly>' . esc_textarea(implode("\n", $subs)) . '</textarea>';
            echo '<p class="description">Copy these into Mailchimp, Beehiiv, etc.</p>';
        } else {
            echo '<p>No subscribers collected yet.</p>';
        }
        ?>
    </div>
    <?php
}

function ckia_option(string $key, string $default = ''): string
{
    return get_option('ckia_options', [])[$key] ?? $default;
}

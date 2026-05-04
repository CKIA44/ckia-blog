<?php

use App\Providers\ThemeServiceProvider;
use Roots\Acorn\Application;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Acorn Storage Path
|--------------------------------------------------------------------------
|
| Point Acorn's cache/storage at wp-content/uploads/acorn which is
| guaranteed to be writable on any WordPress install, including
| managed / cPanel shared hosting environments.
|
*/

if (! defined('ACORN_STORAGE_PATH')) {
    define('ACORN_STORAGE_PATH', WP_CONTENT_DIR . '/uploads/acorn');
}

/*
|--------------------------------------------------------------------------
| Acorn Error Handler Guard
|--------------------------------------------------------------------------
|
| Prevent "headers already sent" warnings (from plugins that output early
| when WP_DEBUG is on) from cascading into a fatal via Acorn's error handler.
| The warning is always a symptom of an earlier problem, never the root cause.
|
*/

add_filter('acorn/throw_error_exception', function ($throw, $e) {
    if (str_contains($e->getMessage(), 'Cannot modify header information')) {
        return false;
    }
    return $throw;
}, 10, 2);

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        ThemeServiceProvider::class,
    ])
    ->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters', 'ckia'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'ckia'), $file)
            );
        }
    });

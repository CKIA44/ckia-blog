<?php

// ── PHP-level error log — uncomment to debug ─────────────────────────────────
// ini_set('error_log', __DIR__ . '/php-errors.log');
// error_reporting(E_ALL);
// ─────────────────────────────────────────────────────────────────────────────

use App\Providers\ThemeServiceProvider;
use Roots\Acorn\Application;

// ── Diagnostics — uncomment entire block to debug ────────────────────────────
// $_ckia_log = __DIR__ . '/boot-test.log';
// $_ckia_step = function (string $msg) use ($_ckia_log) {
//     file_put_contents($_ckia_log, date('c') . " $msg\n", FILE_APPEND);
// };
// set_error_handler(function ($severity, $message, $file, $line) use ($_ckia_log) {
//     file_put_contents($_ckia_log, date('c') . " PHP error($severity): $message in $file:$line\n", FILE_APPEND);
//     return false;
// });
// register_shutdown_function(function () use ($_ckia_log) {
//     $e = error_get_last();
//     if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
//         file_put_contents($_ckia_log, date('c') . " FATAL: {$e['message']} in {$e['file']}:{$e['line']}\n", FILE_APPEND);
//     }
// });
// set_exception_handler(function ($e) use ($_ckia_log) {
//     file_put_contents(
//         $_ckia_log,
//         date('c') . " UNCAUGHT EXCEPTION (" . get_class($e) . "): " . $e->getMessage() .
//         " in " . $e->getFile() . ":" . $e->getLine() . "\n",
//         FILE_APPEND
//     );
//     restore_exception_handler();
//     throw $e;
// });
// ─────────────────────────────────────────────────────────────────────────────

// Stub so $_ckia_step calls below are no-ops when diagnostics are off
$_ckia_step = function (string $msg) {};

@ini_set('memory_limit', '256M');

$_ckia_step('start');

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
    $_ckia_step('ERROR: vendor/autoload.php missing');
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

$_ckia_step('autoloader found');
require $composer;
$_ckia_step('autoloader loaded');

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

// Use the theme's own storage/ dir — guaranteed writable since PHP creates boot-test.log here.
// wp-content/uploads/ may be restricted by open_basedir or suexec on this host.
if (! defined('ACORN_STORAGE_PATH')) {
    define('ACORN_STORAGE_PATH', __DIR__ . '/storage');
}

foreach ([
    ACORN_STORAGE_PATH . '/framework/cache/data',
    ACORN_STORAGE_PATH . '/framework/views',
    ACORN_STORAGE_PATH . '/framework/sessions',
    ACORN_STORAGE_PATH . '/logs',
] as $_acorn_dir) {
    if (! is_dir($_acorn_dir)) {
        $result = mkdir($_acorn_dir, 0755, true);
        $_ckia_step("mkdir {$_acorn_dir}: " . ($result ? 'OK' : 'FAILED'));
    } else {
        $_ckia_step("dir exists: $_acorn_dir");
    }
}
unset($_acorn_dir, $result);

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

$_ckia_step('filters registered');

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

try {
    Application::configure()
        ->withProviders([
            ThemeServiceProvider::class,
        ])
        ->boot();
    $_ckia_step('acorn boot() called OK');
} catch (\Throwable $e) {
    $_ckia_step('acorn boot() EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    throw $e;
}

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

$_ckia_step('loading theme files');
collect(['setup', 'filters', 'ckia'])
    ->each(function ($file) use ($_ckia_step) {
        $_ckia_step("  including app/{$file}.php");
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'ckia'), $file)
            );
        }
        $_ckia_step("  app/{$file}.php OK");
    });
$_ckia_step('functions.php complete');

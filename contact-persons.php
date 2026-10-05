<?php

/**
 * Plugin Name:       Contact Persons
 * Description:       Kontaktpersoner med block och mallar för WordPress-projekt.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Madeleine Enberg
 * Text Domain:       contact-persons
 * Domain Path:       /languages
 */

declare(strict_types=1);

namespace MEnberg\ContactPersons;

defined('ABSPATH') || exit;

const VERSION     = '0.1.0';
const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

add_action('plugins_loaded', static function (): void {
    Plugin::instance()->boot();
});

<?php

declare(strict_types=1);

namespace menberg\ContactPersons;

final class Plugin
{
    private static ?Plugin $instance = null;
    private bool $booted = false;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function boot(): void
    {
        if($this->booted) {
            return;
        }

        $this->booted = true;

        add_action('init', [$this, 'loadTextdomain']);
        do_action('contact_persons/booted', $this);
    }

    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'contact-persons',
            false,
            dirname(plugin_basename(PLUGIN_FILE)) . '/languages'
        );
    }
}
<?php

declare(strict_types=1);

namespace MEnberg\ContactPersons;

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
        (new PersonTypes())->register();

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

register_activation_hook(__FILE__, static function (): void {
    (new PersonTypes())->registerAll();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
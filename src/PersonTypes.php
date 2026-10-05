<?php

declare(strict_types=1);

namespace MEnberg\ContactPersons;

final class PersonTypes
{
    public function register(): void
    {
        add_action('init', [$this, 'registerAll']);
    }

    /** @return array<string, array<string, mixed>> */
    public static function types(): array
    {
        $defaults = [
            'contact_person' => [
                'singular'   => __('Contact person', 'contact-persons'),
                'plural'     => __('Contact persons', 'contact-persons'),
                'slug'       => 'contact',
                'public'     => false,
                'menu_icon'  => 'dashicons-id',
                'supports'   => ['title', 'thumbnail', 'excerpt', 'page-attributes'],
                'taxonomies' => ['contact_group', 'contact_location'],
            ],
        ];

        $custom = get_option('contact_persons_types', []);

        $types = array_merge($defaults, is_array($custom) ? $custom : []);
        $types = apply_filters('contact_persons/post_types', $types);

        return array_map(
            static fn(array $config): array => wp_parse_args($config, [
                'singular'   => '',
                'plural'     => '',
                'slug'       => '',
                'public'     => false,
                'menu_icon'  => 'dashicons-groups',
                'supports'   => ['title', 'thumbnail', 'page-attributes'],
                'taxonomies' => [],
            ]),
            $types
        );
    }

    /** @return array<string, array<string, mixed>> */
    public static function taxonomies(): array
    {
        $defaults = [
            'contact_group' => [
                'singular' => __('Group', 'contact-persons'),
                'plural'   => __('Groups', 'contact-persons'),
                'slug'     => 'contact-group',
            ],
            'contact_location' => [
                'singular' => __('Location', 'contact-persons'),
                'plural'   => __('Locations', 'contact-persons'),
                'slug'     => 'contact-location',
            ],
        ];

        $taxonomies = apply_filters('contact_persons/taxonomies', $defaults);

        return array_map(
            static fn(array $config): array => wp_parse_args($config, [
                'singular'     => '',
                'plural'       => '',
                'slug'         => '',
                'public'       => false,
                'hierarchical' => true,
            ]),
            $taxonomies
        );
    }

    public function registerAll(): void
    {
        $types = self::types();

        foreach (self::taxonomies() as $taxonomy => $config) {
            $objectTypes = array_keys(array_filter(
                $types,
                static fn(array $type): bool => in_array($taxonomy, $type['taxonomies'], true)
            ));

            if ($objectTypes === []) {
                continue;
            }

            register_taxonomy($taxonomy, $objectTypes, $this->taxonomyArgs($taxonomy, $config));
        }

        foreach ($types as $postType => $config) {
            register_post_type($postType, $this->postTypeArgs($postType, $config));
        }
    }

    private function postTypeArgs(string $postType, array $c): array
    {
        $args = [
            'labels' => [
                'name'          => $c['plural'],
                'singular_name' => $c['singular'],
                'menu_name'     => $c['plural'],
                'all_items'     => $c['plural'],
                /* translators: %s: singular name, e.g. "Speaker" */
                'add_new_item'  => sprintf(__('Add %s', 'contact-persons'), $c['singular']),
                /* translators: %s: singular name */
                'edit_item'     => sprintf(__('Edit %s', 'contact-persons'), $c['singular']),
            ],
            'public'              => $c['public'],
            'publicly_queryable'  => $c['public'],
            'exclude_from_search' => ! $c['public'],
            'has_archive'         => $c['public'],
            'show_ui'             => true,
            'show_in_rest'        => true,
            'menu_icon'           => $c['menu_icon'],
            'supports'            => $c['supports'],
            'rewrite'             => $c['public']
                ? ['slug' => $c['slug'] ?: $postType, 'with_front' => false]
                : false,
        ];

        return apply_filters('contact_persons/post_type_args', $args, $postType, $c);
    }

    private function taxonomyArgs(string $taxonomy, array $c): array
    {
        $args = [
            'labels' => [
                'name'          => $c['plural'],
                'singular_name' => $c['singular'],
                'menu_name'     => $c['plural'],
            ],
            'public'            => $c['public'],
            'hierarchical'      => $c['hierarchical'],
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => $c['public']
                ? ['slug' => $c['slug'] ?: $taxonomy, 'with_front' => false]
                : false,
        ];

        return apply_filters('contact_persons/taxonomy_args', $args, $taxonomy, $c);
    }
}

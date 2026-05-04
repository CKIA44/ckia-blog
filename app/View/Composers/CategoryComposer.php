<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class CategoryComposer extends Composer
{
    /**
     * Bind to category archive templates.
     */
    protected static $views = ['category'];

    public function with(): array
    {
        $category = get_queried_object();

        $activeTag = sanitize_text_field($_GET['tag'] ?? '');
        $sort      = sanitize_text_field($_GET['sort'] ?? 'newest');

        $posts = $this->getPosts($category, $activeTag, $sort);
        $allTags = $this->getTagsForCategory($category);

        return [
            'category' => $category,
            'posts'    => $posts,
            'allTags'  => $allTags,
        ];
    }

    protected function getPosts(?\WP_Term $category, string $activeTag, string $sort): array
    {
        $args = [
            'posts_per_page' => 24,
            'post_status'    => 'publish',
        ];

        if ($category) {
            $args['cat'] = $category->term_id;
        }

        if ($activeTag) {
            $args['tag'] = $activeTag;
        }

        switch ($sort) {
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            case 'longest':
                $args['meta_key'] = '_read_minutes';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'shortest':
                $args['meta_key'] = '_read_minutes';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            default: // newest
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
        }

        return get_posts($args);
    }

    protected function getTagsForCategory(?\WP_Term $category): array
    {
        if (!$category) {
            return [];
        }

        $posts = get_posts([
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'cat'            => $category->term_id,
            'post_status'    => 'publish',
        ]);

        if (!$posts) return [];

        $tags = wp_get_object_terms($posts, 'post_tag', ['fields' => 'names']);

        if (is_wp_error($tags)) return [];

        $unique = array_unique($tags);
        return array_slice($unique, 0, 8);
    }
}

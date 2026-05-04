<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class HomepageComposer extends Composer
{
    /**
     * Bind to the front-page template.
     */
    protected static $views = ['front-page'];

    public function with(): array
    {
        $featured   = $this->getFeatured();
        $allRecent  = $this->getAllRecent($featured);

        return [
            'featured'   => $featured,
            'allRecent'  => $allRecent,
            'reviews'    => $this->getByCategory('reviews'),
            'tips'       => $this->getByCategory('tips'),
            'destinations' => $this->getByCategory('destinations'),
            'popular'    => $this->getPopular(),
        ];
    }

    protected function getFeatured(): ?\WP_Post
    {
        // First try a post with _featured meta
        $posts = get_posts([
            'posts_per_page' => 1,
            'meta_key'       => '_featured',
            'meta_value'     => '1',
            'post_status'    => 'publish',
        ]);

        if ($posts) {
            return $posts[0];
        }

        // Fallback: most recent sticky post
        $stickies = get_option('sticky_posts');
        if ($stickies) {
            $post = get_post(reset($stickies));
            if ($post) return $post;
        }

        // Final fallback: most recent post
        $posts = get_posts(['posts_per_page' => 1, 'post_status' => 'publish']);
        return $posts[0] ?? null;
    }

    protected function getByCategory(string $slug, int $limit = 6): array
    {
        return get_posts([
            'posts_per_page' => $limit,
            'category_name'  => $slug,
            'post_status'    => 'publish',
        ]);
    }

    protected function getPopular(): array
    {
        // Uses _view_count meta if set, otherwise falls back to recent
        $byViews = get_posts([
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'meta_key'       => '_view_count',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        ]);

        if (count($byViews) >= 5) {
            return $byViews;
        }

        return get_posts(['posts_per_page' => 5, 'post_status' => 'publish']);
    }

    protected function getAllRecent(?\WP_Post $featured): array
    {
        $posts = get_posts([
            'posts_per_page' => 7,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if ($featured) {
            $posts = array_filter($posts, fn($p) => $p->ID !== $featured->ID);
        }

        return array_values(array_slice($posts, 0, 6));
    }
}

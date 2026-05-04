<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class SingleComposer extends Composer
{
    /**
     * Bind to single post template.
     */
    protected static $views = ['single'];

    public function with(): array
    {
        $post = get_post();
        if (!$post) {
            return ['related' => []];
        }

        return [
            'related' => $this->getRelated($post),
        ];
    }

    protected function getRelated(\WP_Post $post): array
    {
        $categories = get_the_category($post->ID);
        if (!$categories) return [];

        $catId = $categories[0]->term_id;

        return get_posts([
            'posts_per_page'      => 3,
            'category__in'        => [$catId],
            'exclude'             => [$post->ID],
            'post_status'         => 'publish',
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        ]);
    }
}

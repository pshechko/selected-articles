<?php

$title = isset($args['instance']['title']) ? $args['instance']['title'] : __('Selected Articles', 'woothemes');
$articles = isset($args['instance']['articles']) ? $args['instance']['articles'] : [];
$redirect = isset($args['instance']['redirect']) ? $args['instance']['redirect'] : '';
$articles_id = $this->get_field_id('articles');
$selected_articles_id = $this->get_field_id('selected_articles');
$article_name = $this->get_field_name('articles[]');
$cacheid = $this->get_field_name('cache');
$loadmore = $this->get_field_id('loadfromcahe');

$args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1
];

if ($articles) {
    $args['post__not_in'] = $articles;

    $sel_args = [
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post__in' => $articles,
        'orderby' => 'post__in',
        'post_status' => 'publish',
    ];

    $selected_articles_query = new WP_Query($sel_args);
}

$this->get_admin_part('title', [
    'id' => $this->get_field_id('title'),
    'name' => $this->get_field_name('title'),
    'title' => esc_attr($title)
]);

$this->get_admin_part('redirect', [
    'name' => $this->get_field_name('redirect'),
    'url' => $redirect,
    'id' => $this->get_field_id('redirect'),
]);

$this->get_admin_part('head');

$articles_query = new WP_Query($args);

if (!$articles_query->have_posts() && (!is_a($selected_articles_query, 'WP_Query') || !$selected_articles_query->have_posts())) {
    $this->get_admin_parts(['no-articles', 'foot']);
    wp_die();
}

$this->enqueue_admin_scripts();
$this->enqueue_admin_styles();

$this->get_admin_part('el-start', [
    'element' => 'ul',
    'id' => $articles_id,
    'class' => 'connectedSortable',
        ]
);

if ($articles_query->have_posts()) {

    $i = 0;
    while ($articles_query->have_posts()) {
        $articles_query->the_post();
        $has_thumbnail = has_post_thumbnail(get_the_ID());
        if ($has_thumbnail) {
            $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium')[0];
        }
        $this->get_admin_part('article', [
            'thumbnail' => $thumbnail_url,
            'input-id' => $this->get_field_id(get_the_ID()),
            'title' => get_the_title(),
            'editlinlk' => get_edit_post_link(get_the_ID()),
            'article-id' => get_the_ID()
        ]);
        if (++$i === $this->per_page && $articles_query->post_count > $this->per_page) {
            $this->get_admin_part('loadmore', ['id' => $loadmore]);
            $this->get_admin_part('el-end', ['element' => 'ul']);
            $this->get_admin_part('el-start', [
                'element' => 'ul',
                'id' => $cacheid,
                'class' => 'hidden cache',
                'attributes' => [
                    'seleced-articles-role' => 'cache'
                ]
            ]);
        }
    }
    wp_reset_postdata();
    wp_reset_query();
}

$this->get_admin_part('el-end', ['element' => 'ul']);
$this->get_admin_part('el-start', [
    'element' => 'ul',
    'id' => $selected_articles_id,
    'class' => 'connectedSortable'
]);

if (is_a($selected_articles_query, 'WP_Query') && $selected_articles_query->have_posts()) {
    while ($selected_articles_query->have_posts()) {
        $selected_articles_query->the_post();
        $has_thumbnail = has_post_thumbnail(get_the_ID());
        $thumbnail_url = $has_thumbnail ? wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium')[0] : false;
        $this->get_admin_part('article', [
            'thumbnail' => $thumbnail_url,
            'input-id' => $this->get_field_id(get_the_ID()),
            'title' => get_the_title(),
            'editlinlk' => get_edit_post_link(get_the_ID()),
            'article-id' => get_the_ID(),
            'value' => get_the_ID(),
            'name' => $article_name
        ]);
    }
    wp_reset_postdata();
    wp_reset_query();
}

$this->get_admin_part('el-end', ['element' => 'ul']);
$this->get_admin_part('after-lists');

$this->add_js_ids([
    "list" => $articles_id,
    "selected" => $selected_articles_id,
    "name" => $article_name,
    "loadmore" => $loadmore
]);


$this->get_admin_part('foot');


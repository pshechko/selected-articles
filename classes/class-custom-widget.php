<?php

class SelectedArticlesWidget extends WP_Widget {

    private $template_folder = 'selected-articles-parts';
    private $pluginpath = CUSTOMARTICLESDIR;
    private $pluginuri = CUSTOMARTICLESURI;

    function __construct() {
        parent::__construct(
                'selarticles_widget', __('Selected Articles', 'woothemes'), ['description' => __('Selected Articles widget', 'woothemes'),]
        );
        $this->register_scripts();
        $this->register_styles();
    }

    public function register_scripts() {
        wp_register_script('selected-articles', $this->pluginuri . '/assets/scripts/selected-articles.js', array('jquery'), '1.0.0');
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('selected-articles');
    }

    public function register_styles() {
        wp_register_style('selected-articles-stylesheet', $this->pluginuri . '/assets/styles/selected-articles.css');
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('selected-articles-stylesheet');
    }

    public function before_widget($args) {
        if (array_key_exists('before_widget', $args)) {
            echo $args['before_widget'];
        }
    }

    public function after_widget($args) {
        if (array_key_exists('after_widget', $args)) {
            echo $args['after_widget'];
        }
    }

    public function get_template_part($template_name, $args = []) {
        $related = $this->template_folder . '/' . $template_name . ".php";
        $template_path = locate_template($related, true);

        if (empty($template_path)) {
            include($this->pluginpath . "/" . $related);
        }
    }

    public function get_admin_part($template_name, $args = []) {
        include($this->pluginpath . "admin-parts/" . $template_name . ".php");
    }

    public function get_admin_parts($templates) {
        foreach ($templates as $template_name => $args) {
            if (is_int($template_name) && is_string($args)) {
                $this->get_admin_part($args);
                continue;
            }
            $this->get_admin_part($template_name, $args);
        }
    }

    public function unique_script($list, $selecred_list, $name, $number) {
        echo "<script>
                jQuery(function ($) {

                    var number = '{$number}';
                    var args = {
                        list: '{$list}',
                        selected: '{$selecred_list}',
                        name: '{$name}'
                    };

                    make_sortable(number, args);
                });
            </script>";
    }

    public function form_body($articles_query, $selected_articles_query, $articles_id, $selected_articles_id, $article_name, $cacheid) {

        $this->enqueue_admin_scripts();
        $this->enqueue_admin_styles();

        $this->get_admin_part('ul-start', ['id' => $articles_id]);

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
                if (++$i === 15 && $articles_query->post_count > 15) {
                    $this->get_admin_part('loadmore', ['id' => $this->get_field_id('loadfromcahe')]);
                    $this->get_admin_part('ul-end');
                    $this->get_admin_part('cache-ul-start', ['id' => $cacheid]);
                }
            }
            wp_reset_postdata();
            wp_reset_query();
        }

        $this->get_admin_part('ul-end');
        $this->get_admin_part('ul-start', ['id' => $selected_articles_id]);

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

        $this->get_admin_part('ul-end');
        $this->get_admin_part('after-lists');
    }

    public function widget($args, $instance) {

        $this->before_widget($args);

        if (is_array($instance['articles']) && count($instance['articles'])) {
            $this->get_template_part('head');
            foreach ($instance['articles'] as $i => $article) {
                $current_post = get_post($article);
                $post_date = get_the_date("F j, Y", $article);
                $author = get_the_author_meta('user_nicename', $current_post->post_author);
                $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($article), 'thumbnail')[0];
                $title = get_the_title($article);
                $permalink = get_the_permalink($article);

                $this->get_template_part('article', [
                    "id" => $article,
                    "title" => $title,
                    "author" => $author,
                    "date" => $post_date,
                    "image" => $thumbnail_url,
                    "permalink" => $permalink
                ]);
            }
        }

        $this->get_template_part('foot', ['url' => $instance['redirect']]);
        $this->after_widget($args);
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Selected Articles', 'woothemes');
        $articles = isset($instance['articles']) ? $instance['articles'] : [];
        $redirect = isset($instance['redirect']) ? $instance['redirect'] : '';
        $articles_id = $this->get_field_id('articles');
        $selected_articles_id = $this->get_field_id('selected_articles');
        $article_name = $this->get_field_name('articles[]');
        $cacheid = $this->get_field_name('cache');

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
            return;
        }

        $this->form_body($articles_query, $selected_articles_query, $articles_id, $selected_articles_id, $article_name, $cacheid);
        $this->unique_script($articles_id, $selected_articles_id, $article_name, $this->number);
        $this->get_admin_part('foot');
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['articles'] = (!empty($new_instance['title']) ) ? $new_instance['articles'] : [];
        $instance['redirect'] = (!empty($new_instance['redirect']) ) ? $new_instance['redirect'] : [];

        return $instance;
    }

}

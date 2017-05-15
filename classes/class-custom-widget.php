<?php

class SelectedArticlesWidget extends WP_Widget {

    private $template__folder = 'selected-articles-parts';
    private $pluginpath = CUSTOMARTICLESDIR;
    private $pluginuri = CUSTOMARTICLESURI;

    function __construct() {
        parent::__construct(
                'selarticles_widget', __('Selected Articles', 'woothemes'), ['description' => __('Selected Articles widget', 'woothemes'),]
        );
        $this->register_scripts();
        $this->register_styles();
    }

    private function register_scripts() {
        wp_register_script('selected-articles', $this->pluginuri . '/assets/scripts/selected-articles.js', array('jquery'), '1.0.0');
    }

    private function enqueue_admin_scripts() {
        wp_enqueue_script('selected-articles');
    }

    private function register_styles() {
        wp_register_style('selected-articles-stylesheet', $this->pluginuri . '/assets/styles/selected-articles.css');
    }

    private function enqueue_admin_styles() {
        wp_enqueue_style('selected-articles-stylesheet');
    }

    private function before_widget($args) {
        if (array_key_exists('before_widget', $args)) {
            echo $args['before_widget'];
        }
    }

    private function after_widget($args) {
        if (array_key_exists('after_widget', $args)) {
            echo $args['after_widget'];
        }
    }

    private function get_template_part($template_name, $args = []) {
        $related = $this->template__folder . '/' . $template_name . ".php";
        $template_path = locate_template($related, true);

        if (empty($template_path)) {
            include($this->pluginpath . "/" . $related);
        }
    }

    private function get_admin_part($template_name, $args = []) {
        include($this->pluginpath . "admin-parts/" . $template_name . ".php");
    }

    private function unique_script($list, $selecred_list, $name, $number) {
        ?>
        <script>
            jQuery(function ($) {

                var number = "<?= $number; ?>";
                var args = {
                    list: "<?= $list; ?>",
                    selected: "<?= $selecred_list; ?>",
                    name: "<?= $name; ?>"
                };

                updateindex(number, args);

                $("#" + args.list + ", #" + args.selected).sortable({
                    connectWith: "#" + args.list + ", #" + args.selected,
                    update: function (e, ui) {
                        $('#' + args.list + ' [seleced-articles-role="input"]').removeAttr('name');
                        $('#' + args.selected + ' [seleced-articles-role="input"]').each(function () {
                            $(this).attr('name', args.name).val($(this).attr('article-id'));
                        });
                    }
                }).disableSelection();
            });

        </script>
        <?php
    }

    public function widget($args, $instance) {

        $this->before_widget($args);
        if (is_array($instance['articles']) && count($instance['articles'])) {
            $this->get_template_part('head');
            foreach ($instance['articles'] as $i => $article) {
                $cpost = get_post($article);
                $post_date = get_the_date("F j, Y", $article);
                $author = get_the_author_meta('user_nicename', $cpost->post_author);
                $thur = wp_get_attachment_image_src(get_post_thumbnail_id($article), 'thumbnail')[0];
                $title = get_the_title($article);

                $this->get_template_part('article', [
                    "id" => $article,
                    "title" => $title,
                    "author" => $author,
                    "date" => $post_date,
                    "image" => $thur
                ]);
            }
        }

        $this->get_template_part('foot', ['url' => $instance['redirect']]);
        $this->after_widget($args);
    }

    public function form($instance) {
        global $post;
        $title = isset($instance['title']) ? $instance['title'] : __('Selected Articles', 'woothemes');
        $articles = isset($instance['articles']) ? $instance['articles'] : [];
        $redirect = isset($instance['redirect']) ? $instance['redirect'] : '';
        $artid = $this->get_field_id('articles');
        $selartid = $this->get_field_id('selected_articles');
        $artname = $this->get_field_name('articles[]');
        $wigdetprefix = $this->get_field_id('');
        $cacheid = $this->get_field_name('cache');
        //echo $artid . " "
        $p = $post;

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

            $sel_query = new WP_Query($sel_args);
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

        $query = new WP_Query($args);

        if ($query->have_posts() || (is_a($sel_query, 'WP_Query') && $sel_query->have_posts())) {
            $this->enqueue_admin_scripts();
            $this->enqueue_admin_styles();

            $this->get_admin_part('ul-start', ['id' => $artid]);

            if ($query->have_posts()) {

                $i = 0;
                while ($query->have_posts()) {
                    $query->the_post();
                    $has_th = has_post_thumbnail(get_the_ID());
                    if ($has_th) {
                        $thur = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium')[0];
                    }
                    $this->get_admin_part('article', [
                        'thumbnail' => $thur,
                        'input-id' => $this->get_field_id(get_the_ID()),
                        'title' => get_the_title(),
                        'editlinlk' => get_edit_post_link(get_the_ID()),
                        'article-id' => get_the_ID()
                    ]);
                    if (++$i === 15 && $query->post_count > 15) {
                        $this->get_admin_part('loadmore', ['id' => $this->get_field_id('loadfromcahe')]);
                        $this->get_admin_part('ul-end');
                        $this->get_admin_part('cache-ul-start', ['id' => $cacheid]);
                    }
                }
                wp_reset_postdata();
                wp_reset_query();
            }

            $this->get_admin_part('ul-end');
            $this->get_admin_part('ul-start', ['id' => $selartid]);

            if (is_a($sel_query, 'WP_Query') && $sel_query->have_posts()) {
                $it = 0;
                while ($sel_query->have_posts()) {
                    $sel_query->the_post();
                    $has_th = has_post_thumbnail(get_the_ID());
                    $thur = $has_th ? wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium')[0] : false;
                    $this->get_admin_part('article', [
                        'thumbnail' => $thur,
                        'input-id' => $this->get_field_id(get_the_ID()),
                        'title' => get_the_title(),
                        'editlinlk' => get_edit_post_link(get_the_ID()),
                        'article-id' => get_the_ID(),
                        'value' => get_the_ID(),
                        'name' => $artname
                    ]);
                }
                wp_reset_postdata();
                wp_reset_query();
            }

            $this->get_admin_part('ul-end');
            $this->get_admin_part('after-lists');
        } else {
            $this->get_admin_part('no-articles');
        }
        $this->get_admin_part('foot');
        $this->unique_script($artid, $selartid, $artname, $this->number);
        $post = $p;
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['articles'] = (!empty($new_instance['title']) ) ? $new_instance['articles'] : [];
        $instance['redirect'] = (!empty($new_instance['redirect']) ) ? $new_instance['redirect'] : [];

        return $instance;
    }

}

<?php

class SelectedArticlesWidget extends WP_Widget {

    private $template_folder = 'selected-articles-parts';
    private $pluginpath = CUSTOMARTICLESDIR;
    private $pluginuri = CUSTOMARTICLESURI;
    private $per_page = 10;
    private $load_next = 5;
    private $tabs = [
        'select',
        'config',
        'templates'
    ];
    private $js_params = [
        'ids' => [],
        'vars' => []
    ];

    function __construct() {
        parent::__construct(
                'selarticles_widget', __('Selected Articles', 'woothemes'), ['description' => __('Selected Articles widget', 'woothemes'),]
        );
        $this->register_scripts();
        $this->register_styles();
    }

    public function register_scripts() {
        wp_register_script('selected-articles', $this->pluginuri . '/assets/scripts/selected-articles.js', [
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-tabs'
                ], '1.0.0');
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('selected-articles');
    }

    public function register_styles() {
        wp_register_style('selected-articles-stylesheet', $this->pluginuri . '/assets/styles/selected-articles.css');
        wp_enqueue_style('selected-articles-jquery-ui-admin-css', $this->pluginuri . '/assets/styles/jquery-ui.min.css');
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

    public function add_js_ids($ids) {
        $this->js_params['ids'] = array_merge($this->js_params['ids'], $ids);
    }

    public function add_js_vars($vars) {
        $this->js_params['vars'] = array_merge($this->js_params['vars'], $vars);
    }

    public function get_template_part($template_name, $args = []) {
        $related = $this->template_folder . '/' . $template_name . ".php";
        $template_path = locate_template($related);

        include(empty($template_path) ? $this->pluginpath . "/" . $related : $template_path);
    }

    public function get_admin_part($template_name, $args = []) {
        include($this->pluginpath . "admin-parts/" . $template_name . ".php");
    }

    public function get_admin_tab($tab_name, $args = []) {
        $this->get_admin_part('el-start', [
            'element' => 'div',
            'id' => $args['id']
        ]);

        include($this->pluginpath . "admin-tabs/" . $tab_name . ".php");

        $this->get_admin_part('el-end', [
            'element' => 'div',
        ]);
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

    public function get_admin_tabs() {
        foreach ($this->tabs as $slug => $args) {
            $args['slug'] = $slug;
            $args['id'] = $this->get_field_id($slug);
            $this->get_admin_tab($slug, $args);
        }
    }

    public function get_admin_tabs_heading() {
        $this->get_admin_part('el-start', ['element' => 'ul']);
        foreach ($this->tabs as $slug => $args) {
            $args['slug'] = $slug;
            $args['id'] = $this->get_field_id($slug);
            $this->get_admin_part('tab-heading', $args);
        }
        $this->get_admin_part('el-end', ['element' => 'ul']);
    }

    public function unique_script() {
        echo "<script>
                jQuery(function ($) {

                    var number = '{$this->number}';
                    var jsparams = " . json_encode($this->js_params) . "
                    
                    update_index(number, jsparams.ids);
                    add_listeners(jsparams);
                    init_tabs(jsparams);
                    make_sortable(jsparams);
                });
            </script>";
    }

    public function widget($args, $instance) {

        $this->before_widget($args);

        if (is_array($instance['articles']) && count($instance['articles'])) {
            $this->get_template_part('head');
            foreach ($instance['articles'] as $i => $article) {
                $current_post = get_post($article);
                $post_date = get_the_date("F j, Y", $article);
                $author = get_the_author_meta('display_name', $current_post->post_author);
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

        $tab_wrapper_id = $this->get_field_id('tabs');

        $this->tabs = [
            'select' => [
                'instance' => $instance,
            ],
            'config' => [
                'instance' => $instance,
            ]
        ];

        $this->get_admin_parts(
                [
                    'el-start' => [
                        'element' => 'div',
                        'id' => $tab_wrapper_id
                    ],
                    'tabs-start'
                ]
        );

        $this->get_admin_tabs_heading();
        $this->get_admin_tabs();

        $this->get_admin_parts(
                [
                    'tabs-end',
                    'el-end' => [
                        'element' => 'div',
                    ]
                ]
        );

        $this->add_js_ids(['wrapper' => $tab_wrapper_id]);
        $this->add_js_vars(['load_next' => $this->load_next]);
        $this->unique_script();
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['articles'] = (!empty($new_instance['title']) ) ? $new_instance['articles'] : [];
        $instance['redirect'] = (!empty($new_instance['redirect']) ) ? $new_instance['redirect'] : [];

        return $instance;
    }

}

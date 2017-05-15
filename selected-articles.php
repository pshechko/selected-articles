<?php

/*
  Plugin Name: Selected Articles Widget
  Plugin URI: https://houstonapps.co/
  Description: Adds a selected articles widget
  Version: 1
  Author: pshechko
  Author URI: https://houstonapps.co/
 */

define("CUSTOMARTICLESURI", plugins_url('', __FILE__));
define("CUSTOMARTICLESDIR", plugin_dir_path( __FILE__ ));

require "classes/class-custom-widget.php";

function selarticles_load_widget() {
    register_widget('SelectedArticlesWidget');
}

add_action('widgets_init', 'selarticles_load_widget');

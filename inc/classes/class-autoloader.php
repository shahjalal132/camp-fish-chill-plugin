<?php
/**
 * Bootstraps the plugin. load class.
 */

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Singleton;

class Autoloader {
    use Singleton;

    protected function __construct() {

        // load class.
        I18n::get_instance();
        Enqueue_Assets::get_instance();
        Post_Loop::get_instance();
        Admin_Menu::get_instance();
        Blog_Post_Loop::get_instance();
    }
}
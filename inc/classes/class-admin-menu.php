<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Admin_Menu {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_filter( 'plugin_action_links_' . PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );
    }

    public function admin_menu() {
        add_menu_page( 'Camp Fish Chill', 'Camp Fish Chill', 'manage_options', 'camp-fish-chill', [ $this, 'admin_page' ], 'dashicons-welcome-widgets-menus' );
    }

    public function admin_page() {
        require_once PLUGIN_BASE_PATH . '/templates/admin-page.php';
    }

    public function plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=camp-fish-chill">' . __( 'Settings', 'camp-fish-chill' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

}
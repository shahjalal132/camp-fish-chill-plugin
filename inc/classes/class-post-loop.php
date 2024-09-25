<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Post_Loop {

    use Singleton;
    use Program_Logs;

    private $posts_json;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_shortcode( 'camp_post_loop', [ $this, 'custom_post_loop' ] );
        add_action( 'wp_ajax_load_posts', [ $this, 'ajax_load_posts_shortcode' ] );
        add_action( 'wp_ajax_nopriv_load_posts', [ $this, 'ajax_load_posts_shortcode' ] );
    }

    public function custom_post_loop() {
        ob_start(); ?>

        <div id="posts-wrapper">
            <div id="posts-loop">
                <?php $this->load_posts( 1 ); ?>
            </div>
            <div id="pagination">
                <?php $this->paginate_posts( 1 ); ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function ajax_load_posts_shortcode() {

        // Check nonce
        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'camp_post_loop' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Extract the page number from the pagination link's href
        $paged = isset( $_POST['page'] ) ? $_POST['page'] : 1;

        // Load posts
        ob_start();
        $this->load_posts( $paged );
        $posts_content = ob_get_clean();

        // Load pagination
        ob_start();
        $this->paginate_posts( $paged );
        $pagination_content = ob_get_clean();

        // Send JSON response
        wp_send_json( [
            'posts'      => $posts_content,
            'pagination' => $pagination_content,
        ] );

        wp_reset_postdata();
        die();
    }

    private function load_posts( $paged ) {

        // Make query
        $args = [
            'post_type'      => 'post',
            'posts_per_page' => 5,
            'paged'          => $paged,
        ];

        // Execute query to get posts
        $custom_query = new \WP_Query( $args );

        // convert posts to json
        $this->posts_json = json_encode( $custom_query->posts );
        // put posts to log
        // $this->put_program_logs( 'Posts JSON: ' . $this->posts_json );

        if ( $custom_query->have_posts() ) :
            while ( $custom_query->have_posts() ) :
                $custom_query->the_post(); ?>

                <div class="post-item">
                    <h2><?php the_title(); ?></h2>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                </div>

            <?php endwhile;
        else :
            echo '<p>No posts found.</p>';
        endif;

        wp_reset_postdata();
    }

    private function paginate_posts( $paged ) {

        // Make query
        $args = [
            'post_type'      => 'post',
            'posts_per_page' => 5,
            'paged'          => $paged,
        ];

        // Execute query to get posts
        $custom_query = new \WP_Query( $args );

        // Get max number of pages
        echo paginate_links( [
            'total'     => $custom_query->max_num_pages,
            'current'   => $paged,
            'prev_text' => __( '« Previous' ),
            'next_text' => __( 'Next »' ),
            'type'      => 'list',
        ] );
    }
}

<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Post_Loop {

    use Singleton;
    use Program_Logs;

    private $posts_json;
    private $posts_per_page;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_shortcode( 'camp_post_loop', [ $this, 'custom_post_loop' ] );
        add_action( 'wp_ajax_load_posts', [ $this, 'ajax_load_posts_shortcode' ] );
        add_action( 'wp_ajax_nopriv_load_posts', [ $this, 'ajax_load_posts_shortcode' ] );

        add_filter( 'excerpt_length', [ $this, 'custom_excerpt_length' ], 999 );
        add_filter( 'excerpt_more', [ $this, 'custom_excerpt_more' ] );

        // get _display_post_items from option initially 6
        $this->posts_per_page = get_option( '_display_post_items', 6 );
    }

    public function custom_post_loop() {
        ob_start(); ?>

        <div id="posts-wrapper">
            <div id="posts-loop" class="row">
                <?php $this->load_posts( 1 ); ?>
            </div>
            <div id="posts-loop-overlay"></div>
            <div id="camp-pagination" class="mt-3">
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
            'posts_per_page' => $this->posts_per_page,
            'paged'          => $paged,
        ];

        // Execute query to get posts
        $custom_query = new \WP_Query( $args );

        // convert posts to json
        // $this->posts_json = json_encode( $custom_query->posts );
        // put posts to log
        // $this->put_program_logs( 'Posts JSON: ' . $this->posts_json );

        if ( $custom_query->have_posts() ) :
            while ( $custom_query->have_posts() ) :
                $custom_query->the_post(); ?>

                <!-- Display single post item -->
                <div class="post-item col-lg-4 col-md-6 col-sm-12 mb-4">

                    <!-- Display post thumbnail and link to post -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="camp-post-thumbnail text-center">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail(); ?>
                                <div class="thumbnail-overlay">
                                    <i class="fas fa-search-plus zoom-icon"></i>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Post title with link -->
                    <h2 class="camp-post-title text-center">
                        <a href="<?php the_permalink(); ?>" class="camp-post-title">
                            <?php the_title(); ?>
                        </a>
                    </h2>

                    <!-- Post excerpt -->
                    <div class="camp-post-excerpt text-center">
                        <?php the_excerpt(); ?>
                    </div>

                    <!-- Read More Button -->
                    <div class="read-more-btn-wrapper text-center">
                        <a href="<?php the_permalink(); ?>" class="camp-read-more"><?php _e( 'view post', 'camp' ); ?></a>
                    </div>
                </div>
                <!-- /Display single post item -->

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
            'posts_per_page' => $this->posts_per_page,
            'paged'          => $paged,
        ];

        // Execute query to get posts
        $custom_query = new \WP_Query( $args );

        // Get total number of pages
        $total_pages = $custom_query->max_num_pages;

        // Display pagination if there are more than one page
        if ( $total_pages > 1 ) {

            // Start the pagination UL
            echo '<ul class="page-numbers">';

            // Add the "Page X of Y" count as the first item in the pagination list
            echo '<li class="page-count">Page ' . intval( $paged ) . ' of ' . intval( $total_pages ) . '</li>';

            // Output pagination links
            $links = paginate_links( [
                'total'     => $total_pages,
                'current'   => max( 1, $paged ), // Ensure we don't get a page less than 1
                'prev_text' => __( '« Previous' ),
                'next_text' => __( 'Next »' ),
                'type'      => 'array', // Return as array to allow custom placement
            ] );

            // Loop through the pagination links and display them
            if ( $links ) {
                foreach ( $links as $link ) {
                    echo '<li>' . $link . '</li>';
                }
            }

            // Close the pagination UL
            echo '</ul>';
        }
    }

    // Set custom excerpt length to 25 words
    public function custom_excerpt_length( $length ) {
        return 25;
    }

    // Modify the excerpt "more" string to display [...]
    public function custom_excerpt_more( $more ) {
        return ' [...]';
    }

}

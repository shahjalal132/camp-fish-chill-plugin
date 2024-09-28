<!-- Title -->
<h1><?php _e( 'Camp Fish Chill Settings', 'camp-fish-chill' ); ?></h1>

<style>
    .camp-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<!-- Content -->
<div class="camp-wrapper">
    <form method="POST" action="">
        <label for="display_post_items"><?php _e( 'Display Posts: ', 'camp-fish-chill' ); ?></label>
        <input type="number" name="display_post_items" id="display_post_items"
            value="<?php echo esc_attr( get_option( '_display_post_items' ) ); ?>" />
        <input type="submit" name="save_display_post_items" value="<?php _e( 'Save', 'camp-fish-chill' ); ?>"
            class="button button-primary" />
        <?php wp_nonce_field( 'save_display_post_items_action', 'save_display_post_items_nonce' ); ?>
    </form>
</div>

<?php
if ( isset( $_POST['save_display_post_items'] ) ) {
    // Verify the nonce for security
    if ( isset( $_POST['save_display_post_items_nonce'] ) && wp_verify_nonce( $_POST['save_display_post_items_nonce'], 'save_display_post_items_action' ) ) {

        // Sanitize and update the option
        $display_post_items = intval( $_POST['display_post_items'] );
        update_option( '_display_post_items', $display_post_items );

        // Optional: Add a success message
        echo '<p style="color:green;">' . __( 'Settings saved successfully!', 'camp-fish-chill' ) . '</p>';
    } else {
        echo '<p style="color:red;">' . __( 'Security check failed. Please try again.', 'camp-fish-chill' ) . '</p>';
    }
}
?>
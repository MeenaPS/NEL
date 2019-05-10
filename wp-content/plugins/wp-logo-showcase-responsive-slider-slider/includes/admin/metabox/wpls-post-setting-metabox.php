<?php
/**
 * Function Custom meta box for slider link
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.2.8
 */
function wplss_add_meta_box() {
		add_meta_box('custom-metabox',__( 'Add Link URL for Logo', 'link_textdomain' ),'wplss_box_callback','logoshowcase');
}
add_action( 'add_meta_boxes', 'wplss_add_meta_box' );

function wplss_box_callback( $post ) {
	wp_nonce_field( 'wplss_save_meta_box_data', 'wplss_meta_box_nonce' );
	$value = get_post_meta( $post->ID, 'wplss_slide_link', true );
	echo '<input type="url" id="wplss_slide_link" name="wplss_slide_link" value="' . esc_url( $value ) . '" size="25" /><br />';
	echo 'ie http://www.google.com';

}

function wplss_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['wplss_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['wplss_meta_box_nonce'], 'wplss_save_meta_box_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'logoshowcase' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	if ( ! isset( $_POST['wplss_slide_link'] ) ) {
		return;
	}
	$link_data = stripslashes_deep( $_POST['wplss_slide_link'] );
	update_post_meta( $post_id, 'wplss_slide_link', $link_data );
}
add_action( 'save_post', 'wplss_save_meta_box_data' );
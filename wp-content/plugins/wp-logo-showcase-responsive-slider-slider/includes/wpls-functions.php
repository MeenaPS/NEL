<?php
/**
 * Function to get plugin image sizes array
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.2.8
 */
function wplss_get_unique() {
	static $unique = 0;
	$unique++;
	
	return $unique;
}

/**
 * Function to get post featured image
 * 
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.1.7
 */
function wpls_get_logo_image( $post_id = '', $size = 'full' ) {	
	// If external image is blank then take featured image	
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );	
	if( !empty($image) ) {
		$image = isset($image[0]) ? $image[0] : '';
		}	
	return $image;
}


/**
 * Function to get plugin image sizes array
 * 
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.0.0
 */
function wpls_logo_designs() {
	$design_arr = array(
		'design-1'	=> __('Design 1', 'wp-logo-showcase-responsive-slider-slider')		
		);	
	return apply_filters('wpls_logo_designs', $design_arr );
}


/**
 * Function to add array after specific key
 * 
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.2.5
 */
function wpls_logo_add_array(&$array, $value, $index, $from_last = false) {
    
    if( is_array($array) && is_array($value) ) {

        if( $from_last ) {
            $total_count    = count($array);
            $index          = (!empty($total_count) && ($total_count > $index)) ? ($total_count-$index): $index;
        }
        
        $split_arr  = array_splice($array, max(0, $index));
        $array      = array_merge( $array, $value, $split_arr);
    }
    
    return $array;
}

// Manage Category Shortcode Columns

add_filter("manage_wplss_logo_showcase_cat_custom_column", 'wplss_logoshowcase_cat_columns', 10, 3);
add_filter("manage_edit-wplss_logo_showcase_cat_columns", 'wplss_logoshowcase_cat_manage_columns'); 
function wplss_logoshowcase_cat_manage_columns($columns) {
   $new_columns['wpls_logo_shortcode'] = __( 'Category Shortcode', 'wp-logo-showcase-responsive-slider-slider' );
		$columns = wpls_logo_add_array( $columns, $new_columns, 2 );
		return $columns;
}

function wplss_logoshowcase_cat_columns($ouput, $column_name, $tax_id) {
	if( $column_name == 'wpls_logo_shortcode' ) {
			$ouput .= '[logoshowcase cat_id="' . $tax_id. '"]';
			
	    }		
	    return $ouput;

}

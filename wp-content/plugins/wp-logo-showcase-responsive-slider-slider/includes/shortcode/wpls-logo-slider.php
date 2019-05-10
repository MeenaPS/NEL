<?php
/**
 * 'logoshowcase' Shortcode
 * 
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Function to handle the `logoshowcase` shortcode
 * 
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.0.0
 */
function wpls_logo_slider( $atts, $content ) {

	global $post;

	// Shortcode Parameter
	extract(shortcode_atts(array(
		'limit' 			=> '15',
		'design'			=> 'design-1',
		'cat_id'			=> '',
		'cat_name' 			=> '',
		'slides_column'		=> '4',
		'slides_scroll'		=> '1',
		'dots'				=> 'true',
		'arrows'			=> 'true',
		'autoplay'			=> 'true',
		'autoplay_interval'	=> '2000',
		'speed'				=> '1000',
		'center_mode'		=> 'false',
		'rtl'				=> '',
		'loop'				=> 'true',
		'link_target'		=> 'self',
		'show_title'		=> 'false',
		'image_size'		=> 'original',
		'orderby'			=> 'date',
		'order'				=> 'ASC',
		'hide_border'		=> 'true',
		'max_height'        => '250',
		), $atts));

		$shortcode_designs	= wpls_logo_designs();
		$design 			= array_key_exists( trim($design), $shortcode_designs ) ? $design 	: 'design-1';
		$limit				= !empty($limit) ? $limit : '15';
		$cat 				= (!empty($cat_id))	? explode(',',$cat_id) 	: '';
		$cat_name			= !empty($cat_name) ? $cat_name : '';
		$slides_scroll 		= !empty($slides_scroll) ? $slides_scroll : 1;
		$dots 				= ($dots == 'false') 				? 'false' 	: 'true';
		$arrows 			= ($arrows == 'false') 				? 'false' 	: 'true';
		$autoplay 			= ($autoplay == 'false') 			? 'false' 	: 'true';
		$autoplay_interval 	= ($autoplay_interval !== '') 		? $autoplay_interval : '2000';
		$speed 				= (!empty($speed)) 					? $speed 	: '300';
		$loop 				= ($loop == 'false') 				? 'false'	: 'true';
		$link_target 		= ($link_target == 'blank') 		? '_blank' 	: '_self';
		$show_title 		= ($show_title == 'false') 			? 'false'	: 'true';
		$image_size 		= (!empty($image_size)) 			? $image_size	: 'original';
		$order 				= ( strtolower($order) == 'asc' ) 	? 'ASC' : 'DESC';
		$orderby 			= !empty($orderby)	 				? $orderby 	: 'date';
		$hide_border 		= ($hide_border == 'true') 			? 'sliderimage_hide_border' 	: '';
		$max_height 		= (!empty($max_height)) 			? $max_height 	: '250';
		
		// For RTL
		if( empty($rtl) && is_rtl() ) {
			$rtl = 'true';
		} elseif ( $rtl == 'true' ) {
			$rtl = 'true';
		} else {
			$rtl = 'false';
		}

		// Taking some globals
		$unique	= wplss_get_unique();
		
		// Shortcode file
		$design_file_path 	= WPLS_DIR . '/templates/' . $design . '.php';
		$design_file_path 	= (file_exists($design_file_path)) ? $design_file_path : '';

		// WP Query Parameters
		$query_args = array(
						'post_type' 			=> WPLS_POST_TYPE,
						'post_status' 			=> array( 'publish' ),
						'posts_per_page'		=> $limit,
						'order'          		=> $order,
						'orderby'        		=> $orderby,
					);

		if($cat != "") {
            	$query_args['tax_query'] = array(
            	 		array( 
            	 			'taxonomy' => WPLS_CAT_TYPE, 
            	 			'field' => 'term_id', 
            	 			'terms' => $cat,
            	 			)
            	);
        } 
		
		// Enqueue required script
		wp_enqueue_script( 'wpos-slick-jquery' );
		wp_enqueue_script( 'wpls-public-js' );
		
		global $post;
		
		// WP Query Parameters
		$logo_query = new WP_Query($query_args);
		$post_count = $logo_query->post_count;

		// Slider configuration and taken care of centermode
		$slides_column 		= (!empty($slides_column) && $slides_column <= $post_count) ? $slides_column : $post_count;
		$center_mode		= ($center_mode == 'true' && $slides_column % 2 != 0 && $slides_column != $post_count) ? 'true' : 'false';
		$center_mode_cls	= ($center_mode == 'true') ? 'center' : '';
		
		// Slider configuration
		$slider_conf = compact('slides_column', 'slides_scroll', 'dots', 'arrows', 'autoplay', 'autoplay_interval', 'loop' , 'rtl', 'speed', 'center_mode');
		
		ob_start();

		// If post is there
		if( $logo_query->have_posts() ) { ?>
		<?php
			if($cat_name != '') { ?>
				<h2><?php echo $cat_name; ?> </h2>	
			<?php	} ?>
		<style>
			#wpls-logo-showcase-slider-<?php echo $unique; ?> .wp-post-image,
			#wpls-logo-showcase-slider-<?php echo $unique; ?> .wp-post-image img.wp-post-image{max-height:<?php echo $max_height; ?>px; }
		</style>
		<div class="wpls-logo-showcase-slider-wrp wpls-logo-clearfix">
			<div class="wpls-logo-showcase logo_showcase wpls-logo-slider  wpls-<?php echo $design; ?> <?php echo $center_mode_cls; ?> <?php echo $hide_border; ?>" id="wpls-logo-showcase-slider-<?php echo $unique; ?>" >
				<?php while ($logo_query->have_posts()) : $logo_query->the_post();
					
					$feat_image = wpls_get_logo_image( $post->ID, $image_size);
					$logourl 	= get_post_meta( $post->ID, 'wplss_slide_link', true );
					$feat_image_alt = get_post_meta(get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true);
					
					// Include shortcode html file
					if( $design_file_path ) {
						include( $design_file_path );
					}
					
					endwhile; ?>
			</div>
			<div class="wpls-logo-showacse-slider-conf" data-conf="<?php echo htmlspecialchars(json_encode($slider_conf)); ?>"></div>
		</div>
			
		<?php
			wp_reset_query(); // Reset WP Query
			$content .= ob_get_clean();
			return $content;
	}
}

// `logoshowcase` slider shortcode
add_shortcode( 'logoshowcase', 'wpls_logo_slider' );
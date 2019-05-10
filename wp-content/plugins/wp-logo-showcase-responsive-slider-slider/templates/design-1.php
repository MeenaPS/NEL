<?php
/**
 * 'logoshowcase' Design 1 Shortcodes HTML
 *
 * @package WP Logo Showcase Responsive Slider Pro
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpls-logo-cnt" title="<?php the_title(); ?>">	
	<div class="wpls-fix-box">		
		<?php if ($logourl != '') { ?>			
			<a href="<?php echo esc_url($logourl); ?>" target="<?php echo $link_target; ?>">
				<img class="wp-post-image" src="<?php echo $feat_image; ?>" alt="<?php echo $feat_image_alt; ?>" />
			</a>		
		<?php } else { ?>			
			<img class="wp-post-image" src="<?php echo $feat_image; ?>" alt="<?php echo $feat_image_alt; ?>" />		
		<?php } ?>	
	</div>
	<?php if($show_title == "true") { ?>		
		<div class="logo-title"><?php the_title(); ?></div>	
	<?php } ?>
</div>
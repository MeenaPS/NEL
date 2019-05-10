<?php  
/* 
Template Name: Spare parts
*/  
get_header();   
?> 

	<div class="col-lg-12 col-12 col-md-12">
	    <div class="row">
	      <div class="col-lg-6 col-12 col-md-6">
	          <div class="single-breadcrumbs"><?php woodmart_current_breadcrumbs( 'shop' ); ?></div>
	      </div>
	    </div>
	</div>

<?php
	global $wp_query, $wpdb;
 
	/*$getProductCat = get_the_terms( 2653, 'category' ); //as it's returning an array
	foreach ( $getProductCat as $productInfo ) {
	    $productInfo->name;
	}*/
    	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args = array( 'posts_per_page' => 8, 'post_type' => 'spare', 'post_status' => 'publish', 'paged' => $paged);
		// Instantiate custom query
		$custom_query = new WP_Query( $args );
		// Pagination fix
		$temp_query = $wp_query;
		$wp_query   = NULL;
		$wp_query   = $custom_query;
 	?>

	 	<div class="col-lg-12 col-12 col-md-12">
			<div class="row spare_page">
		       <?php if (have_posts()) while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
		                
		        	<div class="col-lg-3 col-3 col-md-3 spare_detail">
						<a href="<?php echo get_post_meta( get_the_ID(), 'spare_url', true ); ?>" target="_blank"><?php the_post_thumbnail(); ?></a>
						<?php echo the_content(); ?>
						
					</div>                    
		    			
	            <?php endwhile; ?>
	    	</div>    
	   </div>     
	<?php
		// Reset postdata
        wp_reset_postdata();
        echo "<div class='pagination'>";
        // Custom query loop pagination
        previous_posts_link( '' );
        next_posts_link( '', $custom_query->max_num_pages );
        $big = 999999999; // need an unlikely integer

        echo paginate_links( array(
          'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
          'format' => '?paged=%#%',
          'current' => max( 1, get_query_var('paged') ),
          'total' => $custom_query->max_num_pages
        ) );
        // Reset main query object
        echo "</div>";
        $wp_query = NULL;
        $wp_query = $temp_query;
                                               
    ?> 

<?php get_footer(); ?>

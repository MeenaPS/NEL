<?php  
/* 
Template Name: My Quote
*/  
get_header();   
?> 
<?php 
	global $wp_query, $wpdb;
	global $woocommerce;
	$current_user = wp_get_current_user();
	$login_email  = $current_user->user_email;
	/* Get logged in users quote details*/
	    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		$limit = 8; // number of rows in page
		$offset = ( $pagenum - 1 ) * $limit;
		$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM nel_request_quote WHERE user_email = '$login_email' " );
		$num_of_pages = ceil( $total / $limit );
	$quote_details = $wpdb->get_results( "SELECT id, user_name, user_email, product_id, product_name, customer_status, request_date FROM nel_request_quote WHERE user_email = '$login_email' ORDER BY id DESC LIMIT $offset, $limit");
	$quo_count = $wpdb->num_rows; //get email id row count 
	?>

	<?php
	$qustatus = $_GET['res'];
	if($qustatus == 'accept'){
		echo '<div id ="message_hide" class="message_hide accept_msg">Your order was created successfully</div>';
	}
	if($qustatus == 'decline'){
		echo '<div id ="message_hide" class="decline_msg_error">Your quote was declined successfully</div>';
	}
	
	?>

	
	<div class="col-lg-12 col-12 col-md-12">
		<div class="row">
        	<div class="col-lg-6 col-12 col-md-6">
          		<div class="single-breadcrumbs"><?php woodmart_current_breadcrumbs( 'shop' ); ?></div>
      		</div>
        </div>
    </div>
<?php
	echo '<h2 class="quote_title">Myquote <span class="blue">'.$total .'</span></h2>';
	
	if($total > 0)
	{
		foreach ($quote_details as $key => $value) {
			 $quote_id 	= $value->id;
			 $name 		= $value->user_name;
			 $email 	= $value->user_email;
			 $pro_id 	= $value->product_id;
			 $pro_name  = $value->product_name;
			 $status 	= $value->status;
			 $cus_status = $value->customer_status;
			 $date 		= $value->request_date;
			 $newDate = date("d/M/Y", strtotime($date)); //convert date format
			 if( $pro_id ){
			 	$item_name = get_post( $pro_id ); 
				$pro_slug = $item_name->post_name;
				$pro_url = get_home_url().'/'.$pro_slug;
		        $pro_img = wp_get_attachment_image_src( get_post_thumbnail_id( $pro_id ), 'thumbnail' ); //get image 
		        $img = $pro_img['0'];
		        $thumb = "<a href='".$pro_url."'><img src ='".$img."' alt = 'Image'></a>";
    	}
    	if($cus_status == "New"){$class = 'status-new';}
    	elseif($cus_status == "Quote Received"){$class = 'status-recived';}
    	elseif($cus_status == "Quote Accepted"){$class = 'status-accept';}
    	elseif($cus_status == "Quote Declined"){$class = 'status-reject';}
		$list = '<div class="quote_list col-lg-12 col-12 col-md-12">';
    	$list .= '<div class="imag_detail row"><div class="col-lg-1 col-l col-md-1"><div class="q-pro-img">'.$thumb.'</div></div>';
		$list .= '<div class="col-lg-8 col-sm-12 col-md-8"><div class="q-pro-detail"><h3><span>'.$pro_id.'-</span>'.$pro_name.'</h3>';
    	$list .= '<ul><li><i class="fa fa-calendar" aria-hidden="true"></i>'.$newDate.'</li><li class="'.$class.'">'.$cus_status.'</li></ul></div></div>';
    	if($cus_status != "New"){
    	$list .= '<div class="col-lg-3 col-sm-12 col-md-3"><a href="'.get_home_url().'/view-quote?qid='.$quote_id.'&item='.$pro_id.'" class="quote_view"><i class="fa fa-eye" aria-hidden="true"></i> View Quote</a></div>';
    	}
    	$list .='</div>';
    	echo $list .= '</div>';
		
	}// end foreach
		$page_links = paginate_links( array(
			    'base' => add_query_arg( 'pagenum', '%#%' ),
			    'format' => '',
			    'prev_text' => __( '&laquo;', 'aag' ),
			    'next_text' => __( '&raquo;', 'aag' ),
			    'total' => $num_of_pages,
			    'current' => $pagenum
			) );

			if ( $page_links ) {
			    echo '<div class="page-n"><div class="tablenav-pages" style="margin: 1em 0">' . 
			$page_links . '</div></div>';
			}
		
	}
	else{
		echo $quote_errors = '<h3 class="quote_error"> No Records found for this user </h3>';
	}
?>
<?php get_footer(); ?> 
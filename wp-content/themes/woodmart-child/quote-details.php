<?php  
/* 
Template Name: Quote details page
*/  
get_header();   

?> 
<?php
	global $wp_query, $wpdb, $woocommerce;
	$current_user 	= 	wp_get_current_user();
	 $cruser_id 	= 	$current_user->ID;
	 $bill_name 	= 	$current_user->user_login;
	 $bill_email 	=	$current_user->user_email;
   $bill_phone_num  =   get_user_meta( $cruser_id, 'phone_num', 'true' );
	 $billcmp 		= 	get_user_meta( $cruser_id, 'company', 'true' );
	 $bill_country 	= 	get_user_meta( $cruser_id, 'user_country', 'true' );
	 $bill_addr 	= 	get_user_meta( $cruser_id, 'user_addr', 'true' );
   $myquote_url = get_home_url();
	
	$req_quo_id = $_GET['qid'];
	$view_btn = $wpdb->get_var( "SELECT customer_status FROM nel_request_quote WHERE id = '$req_quo_id' " );
	$view_details = $wpdb->get_results( "SELECT id, product_id, product_name, price, description, customer_status, request_date FROM nel_request_quote WHERE id = '$req_quo_id' ");
	$view_count = $wpdb->num_rows; //get email id row count 
	?>
	<div class="col-lg-12 col-12 col-md-12">
		<div class="row">
        	<div class="col-lg-6 col-12 col-md-6">
          		<div class="single-breadcrumbs"><?php woodmart_current_breadcrumbs( 'shop' ); ?></div>
      		</div>
        </div>
    </div>
<?php
	/* Accept quote - order creation start */
   if(isset($_POST['submit'])) 
    {
      $machineid    = $_POST['vitem_id']; //product id
      $mac_quote    = $_POST['vquote_id']; //quote id
      $amt          = $_POST['vitem_amt'];
      $billingname  = $_POST['bill_name'];
      $billingemail = $_POST['bill_email'];
      $billingphone = $_POST['bill_phone_num'];
      $billingcmpy  = $_POST['bill_cmpy_name'];
      $billingctry  = $_POST['bill_country'];
      $billingaddr  = $_POST['bill_addr'];

      $shippingname   = $_POST['shipp_name'];
      $shippingemail  = $_POST['shipp_email'];
      $shippingphone  = $_POST['shipp_phone_num'];
      $shippingcmpy   = $_POST['shipp_cmpy_name'];
      $shippingctry   = $_POST['shipp_country'];
      $shippingaddr   = $_POST['shipp_addr'];
      $bill_address = array(
            'first_name' => $billingname,
            'last_name'  => '',
            'company'    => $billingcmpy,
            'email'      => $billingemail,
            'phone'      => $billingphone,
            'address_1'  => $billingaddr,
            //'country'    => $billingctry,
            'state'      => $billingctry
        );
        $shipp_address = array(
            'first_name' => $shippingname,
            'last_name'  => '',
            'company'    => $shippingcmpy,
            'email'      => $shippingemail,
            'phone'      => $shippingphone,
            'address_1'  => $shippingaddr,
            //'country'    => $shippingctry,
            'state'      => $shippingctry
        );
        $order = wc_create_order(array('customer_id'=>$cruser_id));
        // The order ID
        $order_id = wc_get_order($order->id);
        $orderid = $order->id;
        $order->add_product( get_product( $machineid ), 1 ); //(get_product with id and next is for quantity)
        $order->set_address( $bill_address, 'billing' );
        $order->set_address( $shipp_address, 'shipping' );
        /*$order->calculate_totals();*/
        $order->set_total($amt);
        update_post_meta($order->id, '_customer_user', $cruser_id);
        $itemcreate = $order->update_status( 'Pending payment' );
        $order_item_id = $wpdb->get_var( "SELECT order_item_id FROM nel_woocommerce_order_items WHERE order_id = '$orderid' " );
        wc_update_order_item_meta( $order_item_id, '_line_subtotal', $amt );
        wc_update_order_item_meta( $order_item_id, '_line_total', $amt );
        if($itemcreate){
        $view_status = 'Quote Accepted';
        update_usermeta($user_id, 'company', $_POST['company']);
        $wpdb->query("UPDATE nel_request_quote SET status='$view_status', customer_status='$view_status', order_id='$orderid' WHERE id='$mac_quote'");
        //echo '<div id ="message_hide" class="message_hide accept_msg">Your order was created successfully</div>';
        $des_url = $myquote_url.'/my-quote/?res=accept'; 
        wp_redirect( $des_url );
        exit;
      }

 }/* Accept quote - end */
  /* Quote Decline code*/
	if(isset($_POST['reject'])){
	    $decline = $_POST['reject_id'];
	    $rej_status = "Quote Declined";
	    $wpdb->query("UPDATE nel_request_quote SET status='$rej_status', customer_status='$rej_status' WHERE id='$decline'");
	    //echo '<div id ="message_hide" class="decline_msg_error">Your quote was rejected successfully</div>';
      //product status code
      $rej_quote_id = $wpdb->get_var( "SELECT product_id FROM nel_request_quote WHERE id = '$decline' " );
      $rej_quote_status = get_post_meta( $rej_quote_id, '_stock_status', true );
      if($rej_quote_status == 'onrequest'){
          $stock_sta= 'instock';
          update_post_meta( $rej_quote_id, '_stock_status', wc_clean( $stock_sta ) );
         } //end
      $des_url = $myquote_url.'/my-quote/?res=decline'; 
      wp_redirect( $des_url );
      exit;
    }
	
	if($view_count > 0)
	{
		foreach ($view_details as $key => $value) {
			 $quote_id 		= $value->id;
			 $view_id 		= $value->product_id;
			 $view_name  	= $value->product_name;
			 $view_price  	= $value->price;
			 $view_des  	= $value->description;
			 $status 		= $value->status;
			 $view_cus_status = $value->customer_status;
			 $date 			= $value->request_date;
			 $reqDate 		= date("d/M/Y", strtotime($date)); //convert date format
			 $price = "<span class='blue'>Price</span>SEK ".$view_price;
			 if( $view_id ){
			 	$item_name = get_post( $view_id ); 
				$pro_slug = $item_name->post_name;
				$pro_url = get_home_url().'/'.$pro_slug;
		        $pro_img = wp_get_attachment_image_src( get_post_thumbnail_id( $view_id ), 'thumbnail' ); //get image 
		        $img = $pro_img['0'];
		        $thumb = "<a href='".$pro_url."'><img src ='".$img."' alt = 'Image'></a>";
    	}
      $view_pro_status = get_post_meta( $view_id, '_stock_status', true ); //get product stock status
		  $view = '<div class="quote_list col-lg-12 col-12 col-md-12">';
    	$view .= '<div class="q-img-view_detail row"><div class="q-v-img col-3 col-md-3">'.$thumb.'</div><div class="q-v-cont col-7 col-md-7"><h3><span>'.$view_id.'-</span>'.$view_name.'</h3>';
    	$view .= '<ul><li><i class="fa fa-calendar" aria-hidden="true"></i>'.$reqDate.'</li><li>'.$view_cus_status.'</li></ul>';
    	$view .= '<div class="q-v-des">'.$view_des.'</div>';
    	$view .= '<div class="q-v-price">'.$price.'</div>';
    	if($view_btn == "Quote Received" && $view_pro_status != 'outofstock'){
	    	$view .= '<div class="q-v-action"><a href="#"class="accept popup"><i class="fa fa-check" aria-hidden="true"></i> Accept</a>';
	    	$view .= '<form action="" method="post">';
	    	
	      	$view .='<button type="submit" name="reject" id="reject" class="reject quote_cancel"><i class="fa fa-times" aria-hidden="true"></i>Decline</button>';
	      	$view .='<input type="hidden" name="reject_id" value="'.$req_quo_id.'">';
	      	$view .= '</form></div></div>';
	    	
	    	$view .='</div>';
    	}
    	echo $view .= '</div>';
    	
	}
		
	}
?>
<?php
if($view_btn == "Quote Accepted"){
 $cr_order_id = $wpdb->get_var( "SELECT order_id FROM nel_request_quote WHERE id = '$req_quo_id' " );
?>
<div class="col-lg-12 col-12 col-md-12">
<div id="addr_form">
  <form id="billing_form" class="billing_form row" action="" method="post" enctype="multipart/form-data" >
    <div class="col-lg-3 col-3 col-md-3">&nbsp;</div>
    <div class="col-lg-4 col-4 col-md-4">
      <div class="billing_section">
        
        <h3> BILLING ADDRESS </h3>
        <div class="inner-addon left-addon">
        <i class="fa fa-user-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_name" id="bill_name" placeholder="Enter your name" value="<?php echo get_post_meta( $cr_order_id, '_billing_first_name', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-envelope-o" aria-hidden="true"></i>
        <input class="form-control" type="email" name="bill_email" id="bill_email" placeholder="Enter your email" value="<?php echo get_post_meta( $cr_order_id, '_billing_email', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-phone" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_phone_num" id="bill_phone_num" placeholder="Enter the phone no" value="<?php echo get_post_meta( $cr_order_id, '_billing_phone', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-building-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_cmpy_name" id="bill_cmpy_name" placeholder="Enter your company name"  value="<?php echo get_post_meta( $cr_order_id, '_billing_company', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-building-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_country" id="bill_country" placeholder="Enter your country" value="<?php echo get_post_meta( $cr_order_id, '_billing_state', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-map-marker" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_addr" id="bill_addr" placeholder="Enter your address" value="<?php echo get_post_meta( $cr_order_id, '_billing_address_1', 'true' ); ?>" readonly />
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-4 col-md-4">
      <div class="shipping_section">
        <h3> SHIPPING ADDRESS </h3>
        <div class="inner-addon left-addon">
        <i class="fa fa-user-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_name" id="shipp_name" placeholder="Enter your name" value="<?php echo get_post_meta( $cr_order_id, '_shipping_first_name', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-envelope-o" aria-hidden="true"></i>
        <input class="form-control" type="email" name="shipp_email" id="shipp_email" placeholder="Enter your email" value="<?php echo get_post_meta( $cr_order_id, '_shipping_email', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-phone" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_phone_num" id="shipp_phone_num" placeholder="Enter the phone no" value="<?php echo get_post_meta( $cr_order_id, '_shipping_phone', 'true' ); ?>" readonly/>
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-building-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_cmpy_name" id="shipp_cmpy_name" placeholder="Enter your company name" value="<?php echo get_post_meta( $cr_order_id, '_shipping_company', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-building-o" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_country" id="shipp_country" placeholder="Enter your country" value="<?php echo get_post_meta( $cr_order_id, '_shipping_state', 'true' ); ?>" readonly />
        </div>
        <div class="inner-addon left-addon">
        <i class="fa fa-map-marker" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_addr" id="shipp_addr" placeholder="Enter your address" value="<?php echo get_post_meta( $cr_order_id, '_shipping_address_1', 'true' ); ?>" readonly />
          </div>
        </div>
      </div>
    </form> 
  </div>
</div>

<?php } ?>

<div class="col-lg-12 col-12 col-md-12">
<div id="addr_form" style="display:none">
	<form id="billing_form" class="billing_form row" action="" method="post" enctype="multipart/form-data" >
		<div class="col-lg-3 col-3 col-md-3">&nbsp;</div>
		<div class="col-lg-4 col-4 col-md-4">
			<div class="billing_section">
				<h3> BILLING ADDRESS </h3>
				<div class="inner-addon left-addon">
				<i class="fa fa-user-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="bill_name" id="bill_name" placeholder="Enter your name" value="<?php echo $bill_name; ?>" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-envelope-o" aria-hidden="true"></i>
				<input class="form-control" type="email" name="bill_email" id="bill_email" placeholder="Enter your email" value="<?php echo $bill_email; ?>" />
				</div>
        <div class="inner-addon left-addon">
        <i class="fa fa-phone" aria-hidden="true"></i>
        <input class="form-control" type="text" name="bill_phone_num" id="bill_phone_num" placeholder="Enter the phone no" value="<?php echo $bill_phone_num; ?>" />
        </div>
				<div class="inner-addon left-addon">
				<i class="fa fa-building-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="bill_cmpy_name" id="bill_cmpy_name" placeholder="Enter your company name"  value="<?php echo $billcmp; ?>" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-building-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="bill_country" id="bill_country" placeholder="Enter your country" value="<?php echo $bill_country; ?>" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-map-marker" aria-hidden="true"></i>
				<input class="form-control" type="text" name="bill_addr" id="bill_addr" placeholder="Enter your address" value="<?php echo $bill_addr; ?>" />
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-4 col-md-4">
			<div class="shipping_section">
				<h3> SHIPPING ADDRESS </h3>
				<div class="inner-addon left-addon">
				<i class="fa fa-user-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="shipp_name" id="shipp_name" placeholder="Enter your name" value="" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-envelope-o" aria-hidden="true"></i>
				<input class="form-control" type="email" name="shipp_email" id="shipp_email" placeholder="Enter your email" value="" />
				</div>
        <div class="inner-addon left-addon">
        <i class="fa fa-phone" aria-hidden="true"></i>
        <input class="form-control" type="text" name="shipp_phone_num" id="shipp_phone_num" placeholder="Enter the phone no" value="" />
        </div>
				<div class="inner-addon left-addon">
				<i class="fa fa-building-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="shipp_cmpy_name" id="shipp_cmpy_name" placeholder="Enter your company name" value="" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-building-o" aria-hidden="true"></i>
				<input class="form-control" type="text" name="shipp_country" id="shipp_country" placeholder="Enter your country" value="" />
				</div>
				<div class="inner-addon left-addon">
				<i class="fa fa-map-marker" aria-hidden="true"></i>
				<input class="form-control" type="text" name="shipp_addr" id="shipp_addr" placeholder="Enter your address" value="" />
				</div>
			</div>
			<input type="hidden" name="vquote_id" value="<?php echo $req_quo_id; ?>">
			<input type="hidden" name="vitem_id" value="<?php echo $_GET['item']; ?>">
			<input type="hidden" name="vitem_amt" value="<?php echo $view_price; ?>">
			<div class="sub-in">
				<label class="container">Same as billing address
				  <input id="sameadd" name="sameadd" type="checkbox" value="Sameadd" onchange="CopyAdd();"/>
				  <span class="checkmark"></span>
				</label>
				<button type="submit" id="submit" class="searchsubmit" name="submit" value="Submit">Submit</button>
				</div>
			</div>
	</form> 
</div>
</div>
<script> 
function CopyAdd() {
  
  var cb1 = document.getElementById('sameadd');
  var a1 = document.getElementById('bill_name');
  var al1 = document.getElementById('shipp_name');
  var a2 = document.getElementById('bill_email');
  var al2 = document.getElementById('shipp_email');
  var a3 = document.getElementById('bill_cmpy_name');
  var al3 = document.getElementById('shipp_cmpy_name');
  var v1 = document.getElementById('bill_country');
  var vl1 = document.getElementById('shipp_country');
  var t1 = document.getElementById('bill_addr');
  var tl1 = document.getElementById('shipp_addr');
  var t2 = document.getElementById('bill_phone_num');
  var tl2 = document.getElementById('shipp_phone_num');
  
	if (cb1.checked) {
    al1.value = a1.value;
    al2.value = a2.value;
    al3.value = a3.value;
    vl1.value = v1.value;
    tl1.value = t1.value;
    tl2.value = t2.value;
    } else {
    al1.value = '';
    al2.value = '';
    al3.value = '';
    vl1.value = '';
    tl1.value = '';
    tl2.value = '';
   }
}
</script>
<script type="text/javascript">

jQuery('.popup').click(function() {
  jQuery('#addr_form').show();
  return false;
});
</script>     
<?php get_footer(); ?> >
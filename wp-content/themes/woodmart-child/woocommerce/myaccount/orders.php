<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST['submit'])){ //order received code
	$ship_id = $_POST['ship'];
	$shiporder = new WC_Order($ship_id);
	$shiporder->update_status('wc-order');
}
/* update payment reference number -start*/
if(isset($_POST['update'])){
	global $wp_query, $wpdb;
	$my_payment_no = $_POST['ref_num'];
	$my_order_id = $_POST['update_order_id'];
	update_post_meta($my_order_id, 'payment_ref_number', $my_payment_no);
	$order = new WC_Order($my_order_id);
	$order->update_status('wc-paid');

	//product stock status code
	$paid_pro_id = $wpdb->get_var( "SELECT product_id FROM nel_request_quote WHERE order_id = '$my_order_id' " );
	$curr_status = get_post_meta( $paid_pro_id, '_stock_status', true );
	if($curr_status == 'onrequest'){
          $currstock= 'outofstock';
          update_post_meta( $paid_pro_id, '_stock_status', wc_clean( $currstock ) );
     }//end
	
	$login_user = wp_get_current_user();
	$login_email = $login_user->user_email;
		$admin_email = get_option('admin_email'); //admin mail id
	    $header1    = 'From: '.$admin_email . "\r\n";
	    $header1   .= "MIME-Version: 1.0\r\n";
	    $header1   .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	    $ordersub    = "Northern Lights- Order payment details";
	    $msg        = "<p style='margin-left:20px;'>Dear customer</p>";
	    $msg       .= "<p style='margin-left:20px;'>Your payment details was successfully updated</p>";
	    $msg       .= "<p style='margin-left:20px;'>Thank you</p><br>";
	    
	    /* admin message content*/
	    $header2     = 'From: '.$login_email . "\r\n";
	    $header2    .= "MIME-Version: 1.0\r\n";
	    $header2    .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	    $adminsub    = "Northern Lights- Order payment details";
	    $ordermsg        = "<p style='margin-left:20px;'>Dear admin,</p>";
	    $ordermsg       .= "<p style='margin-left:20px;'>Kindly find the payment details below.</p>";
	    $ordermsg       .= "<p style='margin-left:20px;'>Payment reference no : $my_payment_no</p>";
	    $ordermsg       .= "<p style='margin-left:20px;'>Order ID : $my_order_id</p>";
	    
	    wp_mail( $login_email, $ordersub, $msg, $header1 ); /* client mail content */
	    wp_mail( $admin_email, $adminsub, $ordermsg, $header2 ); /* admin mail content */
	    echo '<div id ="message_hide" class="message_hide accept_msg">Your reference number successfully updated</div>';
}
/* end */

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders   account-orders-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $customer_orders->orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count();
				$items = $order->get_items(); // get product details

				foreach ( $items as $item ): //display product details - loop start
         			$item_id = $item['order_id']; 
         			$product_name = $item['name'];
         			$product_id = $item['product_id'];
        			$pro_img = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'thumbnail' ); //get image 
		        	$img = $pro_img['0'];
		         	$thumb = "<img src ='".$img."' alt = 'Image'>";
    
				?>
				<tr class="woocommerce-orders-table__row my-order woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
					<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

							<?php elseif ( 'order-number' === $column_id ) : 
								 	echo $thumb; ?>
								<p><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?>
								</a>
								<span><?php echo $product_name; ?></span>
								</p>
							<p><?php elseif ( 'order-date' === $column_id ) : ?>
								<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
								<span><?php $format = "M/d/Y"; 
								echo esc_html( wc_format_datetime( $order->get_date_created(), $format ) ); ?></span></time></p>

							<?php elseif ( 'order-total' === $column_id ) : ?>
							<?php
							/* translators: 1: formatted order total 2: total order items */
							printf( _n( '%1$s', '%1$s', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count );
							if ( 'pending' != $order->status ) {
							 	echo '<span class="ref">Ref no:<p>'.get_post_meta( $item_id, 'payment_ref_number', 'true' ).'</p></span>';
							}
							?>

							<?php elseif ( 'order-status' === $column_id ) : ?>
								<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>

							<?php elseif ( 'order-actions' === $column_id ) : ?>
								<?php
								$order_pro_status = get_post_meta( $product_id, '_stock_status', true ); //get product stock status
								if ( 'pending' == $order->status && $order_pro_status != 'outofstock') {
								echo '<form action="" method="post" enctype="multipart/form-data">';
								echo '<input type="text" name="ref_num" id="ref_num" placeholder="Enter your payment ref no" value="" />';
								echo '<input type="hidden" name="update_order_id" value="'.$order->get_order_number().'">';
								echo '<input type="submit" id="update" class="pay_btn update" name="update" value="Submit" />';
								
								echo'</form>';
								}
								else{
									echo '<div class="shipment_status">';
									if('shipped' == $order->status){
									echo '<form method="post" name="shipstatus" class ="shipstatus" action="" id="shipstatus">';
									echo '<input type="hidden" id="ship" name="ship"  value="'.$order->get_order_number().'">';
									echo '<button type="submit" id="submit" class="order_cnf" name="submit"  value="submit">Received</button>';
									echo '</form></div>';
									}		
								}
								$actions = wc_get_account_orders_actions( $order );

								if ( ! empty( $actions ) ) {
									/*foreach ( $actions as $key => $action ) {
										echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
									}*/
								}
								?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			<?php endforeach; ?><!-- product loop end here -->
		</tbody>
	</table>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Go shop', 'woocommerce' ) ?>
		</a>
		<?php _e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>

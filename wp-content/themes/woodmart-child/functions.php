<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 1000 );

/*woocommerce - add custom column on admin product list page*/
add_filter( 'manage_edit-product_columns', 'show_product_order',15 );
function show_product_order($columns){
	
   //remove column from admin product list page
   unset($columns['price']);
   unset($columns['sku']);
   unset($columns['product_tag']);
   unset($columns['featured']);
   
   //add custom 
   $columns['series'] = __( 'Series'); 
   $columns['manufactured_year'] = __( 'Manufactured Year'); 
   return $columns;
}

/*woocommerce - display each product custom field value on admin product list page*/
add_action( 'manage_product_posts_custom_column', 'wpso23858236_product_column_series', 10, 2 );
function wpso23858236_product_column_series( $column, $postid ) {
	if ( $column == 'series' ) {
        echo get_post_meta( $postid, 'series', true );
    }
    if ( $column == 'manufactured_year' ) {
        echo get_post_meta( $postid, 'manufactured_year', true );
    }
}

/* Change Read More text to View details on shop page*/
function custom_woocommerce_product_add_to_cart_text( $text ) {
 
    if( 'Read more' == $text ) {
        $text = __( 'View Details', 'woocommerce' );
    }
       return $text;
 }
add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );

/* add custom fields for user form on admin - start*/
function custom_user_profile_fields($user){
  ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
      <tr>
            <th><label for="phone_num">Phone No</label></th>
            <td>
                <input type="text" class="regular-text" name="phone_num" value="<?php echo esc_attr( get_the_author_meta( 'phone_num', $user->ID ) ); ?>" id="company" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="company">Company Name</label></th>
            <td>
                <input type="text" class="regular-text" name="company" value="<?php echo esc_attr( get_the_author_meta( 'company', $user->ID ) ); ?>" id="company" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="user_country">Country</label></th>
            <td>
                <input type="text" class="regular-text" name="user_country" value="<?php echo esc_attr( get_the_author_meta( 'user_country', $user->ID ) ); ?>" id="user_country" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="user_addr">Company Address</label></th>
            <td>
                <input type="text" class="regular-text" name="user_addr" value="<?php echo esc_attr( get_the_author_meta( 'user_addr', $user->ID ) ); ?>" id="user_addr" /><br />
                <!-- <span class="description">Where are you?</span> -->
            </td>
        </tr>
    </table>
  <?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );

function save_custom_user_profile_fields($user_id){
    # again do this only if you can
    if(!current_user_can('manage_options'))
        return false;

    # save my custom field
    update_usermeta($user_id, 'phone_num', $_POST['phone_num']);
    update_usermeta($user_id, 'company', $_POST['company']);
    update_usermeta($user_id, 'user_country', $_POST['user_country']);
    update_usermeta($user_id, 'user_addr', $_POST['user_addr']);
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');
/* End */

add_shortcode('login', 'login_function');
function login_function() {
  global $wpdb;
  $custom_menu[] = '';
  if ( is_user_logged_in() ) {
    $quote_url = get_home_url().'/my-quote/';
    $order_url = get_home_url().'/my-account/orders';
    $logout_url = wp_logout_url( get_home_url() );
    $custom_menu = "<a class='custom_menu logout' href='".$logout_url."' title='Logout'>Logout</a>";
    $custom_menu .= "<a class='custom_menu quote_menu' href='".$quote_url."'> My Quote </a>";
    $custom_menu .= "<a class='custom_menu order_menu' href='".$order_url."'> My Order </a>";
} else {
   $login_url = get_home_url().'/login/';
   $custom_menu = "<a class='custom_menu login' href='".$login_url."'> Login </a>";
}
return $custom_menu;
}

// **********************************************************************// 
// Search form -start
// **********************************************************************// 
if( ! function_exists( 'woodmart_search_form' ) ) {
  function woodmart_search_form( $args = array() ) {

    $args = wp_parse_args( $args, array(
      'ajax' => false,
      'post_type' => false,
      'show_categories' => false,
      'type' => 'form',
      'thumbnail' => true,
      'price' => true,
      'count' => 20,
      'icon_type' => '',
      'search_style' => '',
      'custom_icon' => '',
    ) ); 

    extract( $args ); 

    $class = '';
    $data  = '';

    if ( $show_categories && $post_type == 'product' ) {
      $class .= ' has-categories-dropdown';
    } 

    if ( $icon_type == 'custom' ) {
      $class .= ' woodmart-searchform-custom-icon';
    }

    if ( $search_style ) {
      $class .= ' search-style-' . $search_style;
    }

    $ajax_args = array(
      'thumbnail' => $thumbnail,
      'price' => $price,
      'post_type' => $post_type,
      'count' => $count
    );

    if( $ajax ) {
      $class .= ' woodmart-ajax-search';
      woodmart_enqueue_script( 'woodmart-autocomplete' );
      foreach ($ajax_args as $key => $value) {
        $data .= ' data-' . $key . '="' . $value . '"';
      }
    }

    switch ( $post_type ) {
      case 'product':
        $placeholder = esc_attr_x( 'Search for products', 'submit button', 'woodmart' );
        $description = esc_html__( 'Start typing to see products you are looking for.', 'woodmart' );
      break;

      case 'portfolio':
        $placeholder = esc_attr_x( 'Search for projects', 'submit button', 'woodmart' );
        $description = esc_html__( 'Start typing to see projects you are looking for.', 'woodmart' );
      break;
    
      default:
        $placeholder = esc_attr_x( 'Search for posts', 'submit button', 'woodmart' );
        $description = esc_html__( 'Start typing to see posts you are looking for.', 'woodmart' );
      break;
    }

    ?>
      <div class="child-theme woodmart-search-<?php echo esc_attr( $type ); ?>">
        <?php if ( $type == 'full-screen' ): ?>
          <span class="woodmart-close-search"><?php esc_html_e('close', 'woodmart'); ?></span>
        <?php endif ?>
        <form role="search" method="get" class="searchform <?php echo esc_attr( $class ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo ( $data ); ?>>
          <input type="text" class="s" placeholder="<?php echo ($placeholder); ?>" value="<?php echo get_search_query(); ?>" name="s" />
          <input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
          <?php if( $show_categories && $post_type == 'product' ) woodmart_show_categories_dropdown(); ?>
          <button type="submit" class="searchsubmit">
            <?php echo esc_attr_x( 'Search', 'submit button', 'woodmart' ); ?>
            <?php 
              if ( $icon_type == 'custom' ) {
                echo whb_get_custom_icon( $custom_icon );
              }
            ?>
          </button>
        </form>
        <?php if ( $type == 'full-screen' ): ?>
          <div class="search-info-text"><span><?php echo ($description); ?></span></div>
        <?php endif ?>
        <?php if ( $ajax ): ?>
          <div class="search-results-wrapper"><div class="woodmart-scroll"><div class="woodmart-search-results woodmart-scroll-content"></div></div><div class="woodmart-search-loader"></div></div>
        <?php endif ?>
      </div>
    <?php
  }
}
if( ! function_exists( 'woodmart_show_categories_dropdown' ) ) {
  function woodmart_show_categories_dropdown() {
    $args = array( 
      'hide_empty' => 1,
      'parent' => 0
    );
    $terms = get_terms('product_cat', $args);
    if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
      ?>
      <div class="search-by-category input-dropdown">
        <div class="input-dropdown-inner woodmart-scroll-content">
          <!-- select cat name change on result page -->
            <?php if (isset($_REQUEST["product_cat"])) { 
            $select_cat = '';
               $get_cat = $_REQUEST['product_cat'];
              foreach ($terms as $term) {
                if($get_cat == $term->slug) { $select_cat = $get_cat; }
              }
  
              if (empty($select_cat)) { $select_cat = "shop by category"; } ?>
              <input type="hidden" name="product_cat" value="<?php echo $_REQUEST["product_cat"]; ?>">
              <a href="#" data-val="0"><?php echo $select_cat; ?></a>
             
          <?php } else {  ?>  
              <input type="hidden" name="product_cat" value="0">
              <a href="#" data-val="0"><?php esc_html_e('shop by category', 'woodmart'); ?></a>
               
          <?php } ?>

          <!-- end here -->
        <div class="list-wrapper woodmart-scroll">
            <ul class="woodmart-scroll-content">
              <?php if (isset($_REQUEST["product_cat"])) {?>
                <li><a href="#" data-val="0"><?php esc_html_e('Shop by category', 'woodmart'); ?></a></li>
              <?php } ?>
              <li style="display:none;"><a href="#" data-val="0"><?php esc_html_e('Shop by category', 'woodmart'); ?></a></li>
              <?php
                if( ! apply_filters( 'woodmart_show_only_parent_categories_dropdown', false ) ) {
                      $args = array(
                          'title_li' => false,
                          'taxonomy' => 'product_cat',
                          'walker' => new WOODMART_Custom_Walker_Category(),
                      );
                      wp_list_categories($args);
                } else {
                    foreach ( $terms as $term ) {
                      ?>
                      <li><a href="#" data-val="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_attr( $term->name ); ?></a></li>
                      <?php
                    }
                }
              ?>
            </ul>
          </div>
        </div>
      </div>
      <?php
    }
  }
}
// **********************************************************************// 
// Search form - End
// **********************************************************************// 
/** Add custom status to order list **/
add_action( 'init', 'register_custom_statuses_as_order_status' );
function register_custom_statuses_as_order_status() {

    register_post_status( 'wc-paid', array(
        'label'                     => __('Payment made'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Payment made <span class="count">(%s)</span>', 'Payment made <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-received', array(
        'label'                     => __('Payment received'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Payment received <span class="count">(%s)</span>', 'Payment received <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-shipped', array(
        'label'                     => __('Shipped'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-order', array(
        'label'                     => __('Order received'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Order received <span class="count">(%s)</span>', 'Order received <span class="count">(%s)</span>' )
    ) );
}

// Add custom status to order page drop down
add_filter( 'wc_order_statuses', 'add_additional_custom_statuses_to_order_statuses' );
function add_additional_custom_statuses_to_order_statuses( $order_statuses ) {
    $new_order_statuses = array();
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-pending' === $key ) {
            $new_order_statuses['wc-paid'] = __('Payment made');
            $new_order_statuses['wc-received'] = __('Payment received');
            $new_order_statuses['wc-shipped'] = __('Shipped');
            $new_order_statuses['wc-order'] = __('Order received');
        }
    }
    return $new_order_statuses;
} 

//admin order status change email notification
add_action("woocommerce_order_status_changed", "status_custom_notification");

function status_custom_notification($order_id, $checkout=null) {
   global $woocommerce;
   $order = new WC_Order( $order_id );
   $ordernum = $order->get_order_number();
      $admin_email = get_option('admin_email'); //admin mail id
      $header1    = 'From: '.$admin_email . "\r\n";
      $header1   .= "MIME-Version: 1.0\r\n";
      $header1   .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
   if($order->status === 'received' ) {
      // Create a mailer
      $paysub    = "Northern Lights- Payment Received Confirmation";
      $cus_msg        = "<p style='margin-left:20px;'>Dear customer</p>";
      $cus_msg       .= "<p style='margin-left:20px;'>We have received your payment</p>";
      $cus_msg       .= "<p style='margin-left:20px;'>Your order id is :$ordernum </p>";
      $cus_msg       .= "<p style='margin-left:20px;'>Thank you</p><br>";
      wp_mail( $order->billing_email, $paysub, $cus_msg, $header1 ); /* client mail content */
      
     }
     if($order->status === 'shipped' ){
      $deliversub    = "Northern Lights- Order Delivered";
      $deliver_msg        = "<p style='margin-left:20px;'>Dear customer</p>";
      $deliver_msg       .= "<p style='margin-left:20px;'>We have successfully shipped your order</p>";
      $deliver_msg       .= "<p style='margin-left:20px;'>Your order id is :$ordernum </p>";
      $deliver_msg       .= "<p style='margin-left:20px;'>Thank you</p><br>";
      wp_mail( $order->billing_email, $deliversub, $deliver_msg, $header1 ); /* client mail content */

     }

   }

// contact form 7 sales form field validation
add_filter( 'wpcf7_validate_text', 'alphanumeric_validation', 10, 2 );
add_filter( 'wpcf7_validate_text*', 'alphanumeric_validation', 10, 2 );

function alphanumeric_validation( $result, $tag ) {
$tag = new WPCF7_Shortcode( $tag );

  if ( 'username' == $tag->name ) {
    $name = isset( $_POST['username'] ) ? trim( $_POST['username'] ) : ''; 
    if ( !preg_match('/^[a-zA-Z0-9 ]+$/',$name) ) {
    $result->invalidate( $tag, "Please enter alphanumeric characters only" );
    }
  }

  if ( 'company' == $tag->name ) {
    $company = isset( $_POST['company'] ) ? trim( $_POST['company'] ) : ''; 
    if ( !preg_match('/^[a-zA-Z0-9 ]+$/',$company) ) {
    $result->invalidate( $tag, "This field accept alphanumeric characters only" );
    }
  }

  if ( 'equipment' == $tag->name ) {
    $equipment = isset( $_POST['equipment'] ) ? trim( $_POST['equipment'] ) : ''; 
    if ( !preg_match('/^[a-zA-Z0-9 ]+$/',$equipment) ) {
    $result->invalidate( $tag, "Please enter alphanumeric characters only" );
    }
  }
  if ( 'manufacture' == $tag->name ) {
    $manufacture = isset( $_POST['manufacture'] ) ? trim( $_POST['manufacture'] ) : ''; 
    if ( !preg_match('/^[a-zA-Z0-9 ]+$/',$manufacture) ) {
    $result->invalidate( $tag, "Please enter alphanumeric characters only" );
    }
  }
return $result;
}

// contact form 7 phone number field validation
 function custom_phone_validation($result,$tag){
        $type = $tag['type'];
        $name = $tag['name'];
        if($name == 'phone'){
            $phone = isset( $_POST['phone'] ) ? trim( $_POST['phone'] ) : ''; 
            if ( !preg_match('/^[0-9]+$/',$phone) ) {
            $result->invalidate( $tag, "Please enter only numbers" );
        }
      }
        return $result;
    }
add_filter('wpcf7_validate_tel','custom_phone_validation', 10, 2);
add_filter('wpcf7_validate_tel*', 'custom_phone_validation', 10, 2);

/** Spares Custom post type  **/
function spare_init() {
    $args = array(
            'label'                => 'Spares',
            'public'               => true,
            'publicly_queryable'   => true,
            'show_ui'              => true,
            'hierarchical'         => false,
            'query_var'            => true,
            'rewrite'              => array('slug' => 'spare'),
            'capability_type'      => 'post',
            'has_archive'          => false,     
            'menu_icon'            => 'dashicons-video-alt',
            'taxonomies' => array('category'),
            'supports' => array(
                    'title',
                    'editor',
                    'excerpt',
                    'trackbacks',
                    'custom-fields',
                    'comments',
                    'revisions',
                    'thumbnail',
                    'author',
                    'page-attributes',)
        );
    register_post_type( 'spare', $args );
}
add_action( 'init', 'spare_init' );
/** include breadcrumbs on static pages **/
add_shortcode('breadcrumbs', 'breadcrumb_function');
function breadcrumb_function() {
  global $woocommerce;
  $breadcrumb_path = woodmart_current_breadcrumbs( 'shop' );
  $breadcrumb = "<div class='col-lg-12 col-12 col-md-12'>";
  $breadcrumb .= "<div class='row'>";
  $breadcrumb .= "<div class='col-lg-6 col-12 col-md-6'>";
  $breadcrumb .= "<div class='single-breadcrumbs'>$breadcrumb_path</div>";
  $breadcrumb .= "</div>";
  $breadcrumb .= "</div>";
  $breadcrumb .= "</div>";
  return $breadcrumb;
}
/* search - To restrict category search goes to details page instead of listing */
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
/* My order page column name re-order */
function woodmart_custom_account_orders_columns() {
  $columns = array(
    'order-number'  => __( 'Order', 'woocommerce' ),
    'order-date'    => __( 'Date', 'woocommerce' ),
    'order-total'   => __( 'Total', 'woocommerce' ),
    'order-status'  => __( 'Status', 'woocommerce' ),
    'order-actions' => __( 'Actions', 'woocommerce' ),
  );

  return $columns;
}
add_filter( 'woocommerce_account_orders_columns', 'woodmart_custom_account_orders_columns' );
/* sales enquiry form - name validation */
add_filter("gform_field_validation_5_24", "namenumeric_validation", 10, 4);

function namenumeric_validation($result, $value, $form, $field){
    if(empty($value)){
                $result["is_valid"] = false;
        $result["message"] = "This field is required.";

    }
    elseif(!preg_match('/^[a-zA-Z0-9 ]+$/', $value)){
        $result["is_valid"] = false;
        $result["message"] = "Please enter alphanumeric characters only.";
    }
    return $result;
}

//sales form phone number field validation 5- form id, 10- field id
add_filter("gform_field_validation_5_25", "custom_validation", 10, 4);
function custom_validation($result, $value, $form, $field){
    
    if(empty($value)){
                $result["is_valid"] = false;
        $result["message"] = "This field is required.";
      }
    elseif(!preg_match('~^\d+$~', $value)){
        $result["is_valid"] = false;
        $result["message"] = "Please enter only digits.";
    }
    return $result;
}


/**
 * ------------------------------------------------------------------------------------------------
 * Woodmart product label - shop page
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'woodmart_product_label' ) ) {
  function woodmart_product_label() {
    global $product;

    $output = array();
    $stock_status = $product->get_stock_status();

    $product_attributes = woodmart_get_product_attributes_label();
    $percentage_label = woodmart_get_opt( 'percentage_label' );

    if ( $product->is_on_sale() ) {

      $percentage = '';

      if ( $product->get_type() == 'variable' && $percentage_label ) {

        $available_variations = $product->get_variation_prices();
        $max_percentage = 0;

        foreach( $available_variations['regular_price'] as $key => $regular_price ) {
          $sale_price = $available_variations['sale_price'][$key];

          if ( $sale_price < $regular_price ) {
            $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

            if ( $percentage > $max_percentage ) {
              $max_percentage = $percentage;
            }
          }
        }

        $percentage = $max_percentage;
      } elseif ( ( $product->get_type() == 'simple' || $product->get_type() == 'external' ) && $percentage_label ) {
        $percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
      }

      if ( $percentage ) {
        $output[] = '<span class="onsale product-label">-' . $percentage . '%' . '</span>';
      }else{
        $output[] = '<span class="onsale product-label">' . esc_html__( 'Sale', 'woodmart' ) . '</span>';
      }
    }
    //customized stock status label and function
    if($stock_status == 'instock'){
      $output[] = '<span class="out-of-stock stavailable product-label">' . esc_html__( 'Available', 'woodmart' ) . '</span>';
     }
     elseif($stock_status == 'onrequest'){
      $output[] = '<span class="out-of-stock strequest product-label">' . esc_html__( 'Reserved', 'woodmart' ) . '</span>';
     }
     elseif($stock_status == 'outofstock'){
      $output[] = '<span class="out-of-stock stsale product-label">' . esc_html__( 'Sold', 'woodmart' ) . '</span>';
     }
     elseif($stock_status == 'onbackorder'){
      $output[] = '<span class="out-of-stock product-label">' . esc_html__( 'backorder', 'woodmart' ) . '</span>';
     }

    if ( $product_attributes ) {
      foreach ( $product_attributes as $attribute ) {
        $output[] = $attribute;
      }
    }
    
    if ( $output ) {
      echo '<div class="product-labels labels-' . woodmart_get_opt( 'label_shape' ) . '">' . implode( '', $output ) . '</div>';
    }
  }
}
add_filter( 'woocommerce_sale_flash', 'woodmart_product_label', 10 );

/* shop page based on status order Available, Reserved, Sold */
add_action( 'woocommerce_product_query', 'bbloomer_sort_by_stock_status_then_alpha', 999 );
 
function bbloomer_sort_by_stock_status_then_alpha( $query ) {
    if ( is_admin() ) return;
    $query->set( 'meta_key', '_stock_status' );
    $query->set( 'orderby', array( 'meta_value' => 'ASC' ) );
}

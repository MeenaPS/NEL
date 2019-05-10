<?php  
/* 
Template Name: request quote form
*/  
 get_header(); 

?> 
 <?php 
global $wpdb, $product;
$ID = $_GET['pid'];
$pro_details = get_post( $ID ); 
$slug = $pro_details->post_name;
$back_url = get_home_url().'/'.$slug;
$current_user = wp_get_current_user();
$cruser_id = $current_user->ID;
$regname = $current_user->user_login;
$regemail = $current_user->user_email;
$reg_phone = get_user_meta( $cruser_id, 'phone_num', 'true' );
$reg_company = get_user_meta( $cruser_id, 'company', 'true' );
$reg_country = get_user_meta( $cruser_id, 'user_country', 'true' );
$req_succ = $back_url.'?quote=succ';
if(isset($_POST['submit'])) 
  	{ 
  		$quote_error = '';
  		$quote_msg = 0;
  		$login_email   = $current_user->user_email;
  		$name          = $_POST['req_user_name'];
  		$cname         = $_POST['cmpy_name'];
  		$phone         = $_POST['req_user_phone'];
  		$uemail        = $_POST['req_user_email'];
  		$product_id    = $_POST['product_id'];
  		$productname   = get_the_title($product_id);
      $status        = 'New';
      $date          = date("Y-m-d");
  		if($login_email == $uemail){
  					$quote_msg = 1;
            $stock_status= 'onrequest';
            /* insert quote details on db*/
             $sql = "INSERT INTO `nel_request_quote` (`user_name`, `user_email`, `phone_num`, `company_name`, `product_id`,`product_name`, `status`, `customer_status`, `request_date`) VALUES ('$name', '$uemail', $phone, '$cname', $product_id, '$productname', '$status', '$status', '$date')";
            $wpdb->query($sql);
            //update product stock status
            update_post_meta( $product_id, '_stock_status', wc_clean( $stock_status ) );

  					        $admin_email = get_option('admin_email'); //admin mail id
                    $headers    = 'From: '.$admin_email . "\r\n";
                    $headers   .= "MIME-Version: 1.0\r\n";
                    $headers   .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject    = "Northern Lights- Request For Quote";
                    $msg        = "<p style='margin-left:20px;'><img style='display: block;margin: 30px auto;' src='https://northearn.wpengine.com/wp-content/uploads/2018/12/northernlight-logo.jpg'></p>";
                    $msg       .= "<p style='margin-left:20px;'>Hi $name</p>";
                    $msg       .= "<p style='margin-left:20px;'>Thank you for contacting with us,</p>";
                    $msg       .= "<p style='margin-left:20px;'>Our team will get back to you shortly.</p><br>";
                    $msg       .= "<p style='margin-left:20px;'>Thanks</p><br>";
                    
                    /* admin message content*/
                    $header1     = 'From: '.$uemail . "\r\n";
                    $header1    .= "MIME-Version: 1.0\r\n";
                    $header1    .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $msg1        = "<p style='margin-left:20px;'><img style='display: block;margin: 30px auto;' src='https://northearn.wpengine.com/wp-content/uploads/2018/12/northernlight-logo.jpg'></p>";
                    $msg1        = "<p style='margin-left:20px;'>Dear admin,</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Kindly find the quote details below.</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Name : $name</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Company Name : $cname</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Phone No : $phone</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Email : $uemail</p>";
                    $msg1       .= "<p style='margin-left:20px;'>Product Name : $productname</p>";

                    wp_mail( $uemail, $subject, $msg, $headers ); /* client mail content */
                    wp_mail( $admin_email, $subject, $msg1, $header1 ); /* admin mail content */
                    $_POST = array(); // lets pretend nothing was posted
                    wp_redirect( $req_succ );
                    exit;
  		}
  		else{
  			$quote_error = "The given email does not match with login email";
  		}

  }
?>
<div class="col-lg-12 col-12 col-md-12">
<div class="row">
        <div class="col-lg-6 col-12 col-md-6">
          <div class="single-breadcrumbs"><?php woodmart_current_breadcrumbs( 'shop' ); ?></div>
      </div>
      <div class="col-lg-6 col-12 col-md-6">
          <div class="product-details pull-right"><span class="back-pro"><a href="<?php echo $back_url ?>" >Back to Details</a></span></div>
      </div>
  </div>
</div>
<div class="quote_page">

  <div class="col-lg-12 col-12 col-md-12">
     
    <div class="row">
        <div class="quote_left_sec col-lg-6 col-12 col-md-6">
          <?php if( $ID ){
                  $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'full' );
                  $url = $thumb['0'];
                  echo "<img src ='".$url."' alt = 'Image'>";
              }?>
        </div>
    <div class="quote_right_sec col-lg-6 col-12 col-md-6">
        <div class="form__section text-left">
            <h2 class="quote_title">Request for Price</h2>

            <?php if($quote_error) { ?>
                <span class="wpcf7-not-valid-tip quote_error"><?php echo trim($quote_error, '"'); ?></span> <!--error message -->
            <?php } ?>
            <?php //if($quote_msg == 1){ ?>
                <!-- <div id ="message_hide" class="message_hide wpcf7-not-valid-tip">Thank you! Your quote was sent successfully.</div> --> <!--success message -->
            <?php //} ?>
          <form id="request_form" class="user-reg-form" action="" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for=""><i class="fa fa fa-user-o" aria-hidden="true"></i> Name *</label>
                    <input class="form-control" type="text" name="req_user_name" id="req_user_name" placeholder="Enter your name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your name'" value="<?php echo $regname; ?>" />
                </div>
                <div class="form-group">
                    <label for=""><i class="fa fa-building-o" aria-hidden="true"></i> Company Name *</label>
                    <input class="form-control" type="text" name="cmpy_name" id="cmpy_name" placeholder="Enter your company name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your company name'" value="<?php echo $reg_company; ?>" />
                </div>

                <div class="form-group">
                    <label for=""><i class="fa fa-phone" aria-hidden="true"></i> Phone Number *</label>
                    <div class="arrow">
                      <select name="country" class="form-control" id="country">
                        <option <?php if ($reg_country == "Sweden" ) echo 'selected' ; ?> value="Sweden">+46-Sweden</option>
                        <option <?php if ($reg_country == "Norway" ) echo 'selected' ; ?> value="Norway">+47-Norway</option>
                        <option <?php if ($reg_country == "Denmark" ) echo 'selected' ; ?> value="Denmark">+45-Denmark</option>
                        <option <?php if ($reg_country == "India" ) echo 'selected' ; ?> value="India">+91-India</option>
                      </select>
                    </div>
                    <input class="form-control" type="text" name="req_user_phone" id="req_user_phone" placeholder="Phone number" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Phone number'" value="<?php echo $reg_phone; ?>" />
                </div>

                <div class="form-group">
                    <label for=""><i class="fa fa-envelope-o" aria-hidden="true"></i> Email *</label>
                    <input class="form-control" type="email" name="req_user_email" id="req_user_email" placeholder="Enter your email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your email'" value="<?php echo $regemail; ?>" />
                </div>

                
                <div><input type="hidden" name="product_id" id="product_id" value="<?php echo $ID; ?>" /></div>

                <div class="form-group">
                  <div class="signup__button text-right">
                      <img src="/wp-content/uploads/2018/11/view-arrow.png" alt="">
                      <span class="text-uppercase"><input type="submit" id="submit" class="reg-btn" name="submit" value="Submit" /></span>
                  </div>
             </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>

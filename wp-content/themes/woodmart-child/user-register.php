<?php  
/* 
Template Name: user Registration
*/  
   
get_header();   
  
?>  
  <?php 

    global $wpdb;
    if(!empty($_POST['submit'])) 
      {  

        $errorString = '';
        $valid = 0;

        $username       = $wpdb->escape($_REQUEST['user_name']);
        $email          = $wpdb->escape($_REQUEST['user_email']);
        $password       = $wpdb->escape($_REQUEST['user_pass']);
        $phone_num      = $wpdb->escape($_REQUEST['user_phone']);
        $company_name   = $wpdb->escape($_REQUEST['company_name']);
        $cmp_country    = $wpdb->escape($_REQUEST['cmp_country']);
        $company_id     = $wpdb->escape($_REQUEST['company_id']);
        $country        = $wpdb->escape($_REQUEST['country']);

        $address        = $wpdb->escape($_REQUEST['user_addr']); 
        
        // Check username is present and not already in use
        if( username_exists( $username ) ) 
        {  
             $valid = 1;
             $errorString = "Username already exists, please try another"; 
        } 
        // Check email address is present and valid  
          
        if( email_exists( $email ) && $valid == 0 ) 
        {  
            $valid = 1;
            $errorString = "This email address is already exists";  
        }   
        if(empty($errorString) && $valid == 0) 
         {  
   
            $user_id = wp_create_user( $username, $password, $email );
            add_user_meta($user_id,'phone_num', $phone_num); 
            add_user_meta($user_id,'company', $company_name);
            add_user_meta($user_id,'user_addr', $address);
            add_user_meta($user_id,'user_country', $country);  
            
                    $from = get_option('admin_email');
                    $headers  = 'From: '.$from . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject  = "Northern Lights- User Registration";
                    $msg      = "<p style='margin-left:20px;'><img style='display: block;margin: 30px auto;' src='https://northearn.wpengine.com/wp-content/uploads/2018/12/northernlight-logo.jpg'></p>";
                    $msg  .= "Thank you for Registering with us";
                    $msg .= "<p style='margin-left:20px;'>Kindly find your details below.</p>";
                    $msg .= "<p style='margin-left:20px;'>User name : $username</p>";
                    $msg .= "<p style='margin-left:20px;'>password : $password</p>";
                   
                    wp_mail( $email, $subject, $msg, $headers );

                    $_POST = array(); // lets pretend nothing was posted
                    
                    $login_url = get_home_url().'/login/';
                    wp_redirect( $login_url );
                    exit;

        }  

    }

  ?>
    
<!-- signup page start -->
        <div class="wrapper">
            <div class="sidebar">
                
            </div> 
					
            <div class="right__content__section">
			<div class="side-tab">
				<p class="vericaltext"><a href="/login">Sign in</a></p>
				<p class="vericaltext"><a href="/user-registration" class="active">Sign up</a></p>
			</div>
			
                <div class="logo__center text-center">  
                    <a href="<?php echo get_home_url(); ?>"><img src="/wp-content/uploads/2018/12/logo.png" alt=""></a>
                </div>

                <div class="form__section text-left sign-up-sec">
                    <?php if($errorString) { ?>
                    <span class="wpcf7-not-valid-tip"><?php echo trim($errorString, '"'); ?></span> <!--error message -->
                    <?php } ?>
                    <?php if($success == 1) { ?>
                    <span class="success-msg"><?php echo trim($succ_msg, '"'); ?></span> <!--success message -->
                    <?php } ?>
                    <form id="user_signup_form" class="user-reg-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <div class="form-group">
                            <label for=""><i class="fa fa-user-o" aria-hidden="true"></i> Name *</label>
                            <input class="form-control" type="text" name="user_name" id="user_name" placeholder="Enter your name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your name'" value="<?php echo $_POST['user_name'];?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-envelope-o" aria-hidden="true"></i> Email *</label>
                            <input class="form-control" type="email" name="user_email" id="user_email" placeholder="Enter your email id" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your email id'" value="<?php echo $_POST['user_email']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-unlock-alt" aria-hidden="true"></i> Password *</label>
                            <input class="form-control" type="password" name="user_pass" id="user_pass" placeholder=". . . . . . . . . . " onfocus="this.placeholder = ''" onblur="this.placeholder = '. . . . . . . . . . " >
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-phone" aria-hidden="true"></i> Phone Number *</label>
                            <div class="arrow">
                                <select name="country" class="form-control" id="country">
                                <option <?php if ($_POST['country'] == "Sweden" ) echo 'selected' ; ?> value="Sweden">+46-Sweden</option>
                                <option <?php if ($_POST['country'] == "Norway" ) echo 'selected' ; ?> value="Norway">+47-Norway</option>
                                <option <?php if ($_POST['country'] == "Denmark" ) echo 'selected' ; ?> value="Denmark">+45-Denmark</option>
                                <option <?php if ($_POST['country'] == "India" ) echo 'selected' ; ?> value="India">+91-India</option>
                                </select>
                            </div>
                            <input class="form-control" type="text" name="user_phone" id="user_phone" placeholder="Enter your phone number " onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your phone number" value="<?php echo $_POST['user_phone']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-building-o" aria-hidden="true"></i> Company Name *</label>
                            <input class="form-control" type="text" name="company_name" id="company_name" placeholder="Enter your company name " onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your company name" value="<?php echo $_POST['company_name']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-building-o" aria-hidden="true"></i> Company Address *</label>
                            <input class="form-control" type="text" name="user_addr" id="user_addr" placeholder="Enter your company address " onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your company address" value="<?php echo $_POST['user_addr']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-building-o" aria-hidden="true"></i> Company Country *</label>
                            <input class="form-control" type="text" name="cmp_country" id="cmp_country" placeholder="Enter your company country " onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your company country" value="<?php echo $_POST['cmp_country']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-building-o" aria-hidden="true"></i> Company ID *</label>
                            <input class="form-control" type="text" name="company_id" id="company_id" placeholder="Enter your company ID " onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your company ID"  value="<?php echo $_POST['company_id']; ?>" />
                        </div>
                        <div class="form-group">
                            
                        </div> 
                        <div class="form-group">
                            <div class="signup__button text-right">
                                <img src="/wp-content/uploads/2018/11/view-arrow.png" alt="">
                                <span class="text-uppercase"><input type="submit" id="submit" class="reg-btn" name="submit" value="Sign Up" /></span>
                            </div>
                        </div>

                        <div class="text-center m-t-20">
                            <img src="/wp-content/uploads/2018/12/separator.png" alt="" class="img-responsive">
                        </div>
                        <?php $login_url = get_home_url().'/login/';?>
                        <div class="">
                            <div class="text-center link-change">Already have an account? <a href="<?php echo $login_url ?>">signin</a></div>
                        </div>
                    </form>

                </div>

            </div>  
        </div>
    <!-- signup page ends here -->
    
<?php get_footer(); ?>
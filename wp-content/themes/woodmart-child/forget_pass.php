<?php  
/* 
Template Name: Forget password
*/  
   get_header();   
?>  
<?php 
if(isset($_POST['submit'])) 
  	{
  		$act_msg = 0;
  		$email_error = '';
		$useremail = $wpdb->escape($_REQUEST['email']); 
  		if ( !email_exists( $useremail ) ) 
		{ $email_error = "There is no user available for this email."; }
		else{
				 $user = get_user_by( 'email', $useremail ); //get user 
				 $user_login = $user->user_login; //user login name
				 $user_id = $user->ID; //user id
				 $salt = wp_generate_password(20); // 20 character "random" string
				 $key = sha1($salt . $useremail . uniqid(time(), true));
				 $reset_url = get_home_url().'/reset-password/?key='.$key.'&login='.$user_login;

 				      $from = get_option('admin_email');
       		    $headers = 'From: '.$from . "\r\n";
              $headers .= "MIME-Version: 1.0\r\n";
              $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
              $subject = "Northern Lights- Password reset request";
              $msg     = "<p style='margin-left:20px;'><img style='display: block;margin: 30px auto;' src='https://northearn.wpengine.com/wp-content/uploads/2018/12/northernlight-logo.jpg'></p>";
              $msg .= "<p style='margin-left:20px;'>Someone has requested a new password for the following account.</p>";
              $msg .= "<p style='margin-left:20px;'>User name : $user_login</p>";
              $msg .= "<p>If you didn't make this request, just ignore this email. If you'd like to proceed:";
              $msg .= "<a target='_blank' href='".$reset_url."'> Click here to reset your password</a>";
              $msg .= "</p>";
              $msg .= "<p>Thank You.</p><br> </body></html>";
				      wp_mail( $useremail, $subject, $msg, $headers );
				  $act_msg = 1;
			}
	}?>
  
<!-- forget pass page start -->
        <div class="wrapper">
            <div class="sidebar">
                
            </div>      
            <div class="content__section">
			
                <div class="logo__center text-center">  
                    <a href="<?php echo get_home_url(); ?>"><img src="/wp-content/uploads/2018/12/logo.png" alt=""></a>
                </div>

                <div class="form__section text-left">
                    <?php if($email_error) { ?>
                        <span class="wpcf7-not-valid-tip"><?php echo trim($email_error, '"'); ?></span> <!--error message -->
                    <?php } ?>
                    <?php if($act_msg == 1) { ?>
                        <div id ="message_hide" class="message_hide">Reset password link send to your mail. Please check.</div> 
                    <?php } ?>
                    
                    <form id="forget_form" class="user-reg-form" action="" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for=""><i class="fa fa-envelope-o" aria-hidden="true"></i> Email *</label>
                            <input class="form-control" type="email" placeholder="Enter your register email id" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your register email id'" name="email" id="email" value="" />
                        </div>
                        <div class="form-group">
                            <div class="signup__button text-right">
                                <img src="/wp-content/uploads/2018/11/view-arrow.png" alt="">
                                <span class="text-uppercase"><input type="submit" id="submit" class="reg-btn" name="submit" value="Submit" /></span>
                            </div>
                        </div>
						 <div class="text-center m-t-20">
                            <img src="/wp-content/uploads/2018/12/separator.png" alt="" class="img-responsive">
                        </div>
                        <?php $login_url = get_home_url().'/login/';?>
						<div class="text-center link-change">Back to <a href="<?php echo $login_url ?>">sign in</a></div>
                    </form>
                </div>
            </div>  
        </div>
    
<?php get_footer(); ?>
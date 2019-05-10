<?php  
/* 
Template Name: user login
*/  
   get_header();   
?>  
<?php 

    global $user_ID, $username, $password, $remember, $wpdb;

    if(isset($_POST['submit'])) 
  	{ 
		//We shall SQL escape all inputs 
		$login_error = '';
		$login_valid = 1;
		$useremail = $wpdb->escape($_REQUEST['useremail']); 
		$password = $wpdb->escape($_REQUEST['userpass']); 
		$remember = $wpdb->escape($_REQUEST['rememberme']); 
		if($remember) {$remember = "true";} 
		else {$remember = "false";} 
		$login_data = array(); 
		$login_data['user_login'] = $useremail; 
		$login_data['user_password'] = $password; 
		$login_data['remember'] = $remember; 
		
		if ( !email_exists( $useremail ) ) 
		{
			$login_error ="There is no user available for this email.";
			$login_valid = 0;
		}
		elseif( $login_valid == 1)
		{
			$user_verify = wp_signon( $login_data, false );
		if ( is_wp_error($user_verify) ) 
		{ 
			$login_error = "Invalid username or password. Please try again"; 
		} 
		else{ 
			$shopurl = get_home_url().'/shop';
			wp_redirect( $shopurl );
			exit; 
		} 
	}
}

?>
<!-- signin page start -->
        <div class="wrapper">
            <div class="sidebar">
                
            </div>
            <div class="content__section">
			<div class="side-tab">
				<p class="vericaltext"><a href="/login" class="active">Sign in</a></p>
				<p class="vericaltext"><a href="/user-registration">Sign up</a></p>
			</div>
                <div class="logo__center text-center">  
                    <a href="<?php echo get_home_url(); ?>"><img src="/wp-content/uploads/2018/12/logo.png" alt=""></a>
                </div>

                <div class="form__section text-left">
                    <?php if($login_error) { ?>
                    <span class="wpcf7-not-valid-tip"><?php echo trim($login_error, '"'); ?></span> <!--error message -->
                    <?php } ?>
                    
                    <form id="user_login_form" class="user-reg-form" action="" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for=""><i class="fa fa-envelope-o" aria-hidden="true"></i> Email *</label>
                            <input class="form-control" type="email" name="useremail" id="useremail" placeholder="sample@gmail.com" onfocus="this.placeholder = ''" onblur="this.placeholder = 'sample@gmail.com'" value="<?php echo $_POST['useremail']; ?>" />
                        </div>
                        <div class="form-group">
                            <label for=""><i class="fa fa-unlock-alt" aria-hidden="true"></i> Password *</label>
                            <input class="form-control" type="password" placeholder=". . . . . . . . . . " name="userpass" id="userpass" 
							onfocus="this.placeholder = ''" onblur="this.placeholder = '. . . . . . . . . . '">
                        </div>
                        <div class="control-group">
                            <label class="control control--checkbox">Remember me
                                <input class="myremember" name="rememberme" type="checkbox" value="forever">
                                <div class="control__indicator"></div>
                            </label>
                        </div>
                        <!-- <label> 
							<input class="myremember" name="rememberme" type="checkbox" value="forever"><span class="">Remember me</span>
						</label> -->
                        <div class="form-group">
                            <div class="signup__button text-right">
                                <img src="/wp-content/uploads/2018/11/view-arrow.png" alt="">
                                <span class="text-uppercase"><input type="submit" id="submit" class="reg-btn" name="submit" value="LOGIN" /></span>
                            </div>
                             <?php $signup_url = get_home_url().'/user-registration/'; 
                        $frgt_url = get_home_url().'/forget-password/';
                        ?>
                        <div class="form-group">
                            <div class="text-right text-capitalize frgt_pass"><a href="<?php echo $frgt_url ?>">Forgot Password?</a></div>
                        </div>
                        </div>

                        <div class="form-group text-center m-t-20">
                            <img src="/wp-content/uploads/2018/12/separator.png" alt="" class="img-responsive">
                        </div>
                       
                        <div class="form-group">
                            <div class="text-center link-change">Don't have an account? <a href="<?php echo $signup_url ?>">SIGNUP</a></div>
                        </div>
                    </form>

                </div>

            </div>  
        </div>
    <!-- signin page ends here -->



<!-- <form id="user_login_form" action="" method="post" enctype="multipart/form-data"> 

<div class="space"><input type="text" name="useremail" id="useremail" placeholder="Email *"  value="<?php echo $_POST['useremail']; ?>" /></div> 
<div class="space"><input type="password" name="userpass" id="userpass" placeholder="Password *"  value=""> </div> 

<label> 
<input class="myremember" name="rememberme" type="checkbox" value="forever"><span class="">Remember me</span></label>
 
<input type="submit" id="submit" name="submit" value="LOGIN"> 
<?php $signup_url = get_home_url().'/user-registration/';
$frgt_url = get_home_url().'/forget-password/';
?>
<div class="frgt_pass"><a href="<?php echo $frgt_url ?>">Forget Password</a></div>
<div class="sign_up">Don't have an account?<a href="<?php echo $signup_url ?>">SIGNUP</a></div>
</form>
 -->

<?php get_footer(); ?>
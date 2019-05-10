<?php  
/* 
Template Name: Reset password
*/  
   get_header();   
?> 

<?php 
global $wpdb;
$login_url = get_home_url().'/login/';
if(isset($_POST['submit'])) 
  	{ 
  		$update_val = 0;
  		$new_password = $_POST['newpass'];
  		$user_name = $_POST['user'];
  		$user_data = get_user_by( 'login', $user_name );
  		//$cus_name = $user_data->user_login; //user login name
  		$cus_id = $user_data->ID; //user id
  		if(!empty($cus_id))
  		{
  			wp_set_password( $new_password, $cus_id );
  			$update_val = 1;
  			//$login_url = get_home_url().'/login/';
            wp_redirect( $login_url );
            exit; 
		}
  	}
?>

        <div class="wrapper">
            <div class="sidebar">
                
            </div>      
            <div class="content__section">
			
                <div class="logo__center text-center">  
                    <a href="<?php echo get_home_url(); ?>"><img src="/wp-content/uploads/2018/12/logo.png" alt=""></a>
                </div>
		<div class="form__section text-left sign-up-sec">
		<form id="reset_form" class="user-reg-form" action="" method="post" enctype="multipart/form-data"> 
		<div class="form-group">
			<label for=""><i class="fa fa-unlock-alt" aria-hidden="true"></i> New password *</label>
			<input class="form-control" type="password" name="newpass" id="newpass" placeholder="Enter new password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter new password" >
		</div>
		<div class="form-group">
			<label for=""><i class="fa fa-unlock-alt" aria-hidden="true"></i> Confirm password *</label>
			<input class="form-control" type="password" name="cnfpass" id="cnfpass" placeholder="Enter confirm password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter confirm password" >
		</div>
						
	
	<div class="space"><input type="hidden" name="user" id="user" placeholder=""  value="<?php echo $_GET['login']; ?>" /> </div>
	 <div class="form-group">
		<div class="signup__button text-right">
			<img src="/wp-content/uploads/2018/11/view-arrow.png" alt="">
			<span class="text-uppercase"><input type="submit" id="submit" name="submit" class="reg-btn" value="Submit" /></span>
		</div>
	</div>

 <div class="text-center m-t-20">
	<img src="/wp-content/uploads/2018/12/separator.png" alt="" class="img-responsive">
</div>
<div class="text-center link-change">Back to <a href="<?php echo $login_url ?>">sign in</a></div>
</form>
</div>
</div>
</div>
<?php get_footer(); ?>
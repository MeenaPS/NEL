<?php
/**
 * The template for displaying the footer
 *
 */
?>
<?php if ( woodmart_needs_footer() ): ?>
	<?php if ( ! woodmart_is_woo_ajax() ): ?>
		</div><!-- .main-page-wrapper --> 
	<?php endif ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<?php
		$page_id = woodmart_page_ID();
		$disable_prefooter = get_post_meta( $page_id, '_woodmart_prefooter_off', true );
		$disable_footer_page = get_post_meta( $page_id, '_woodmart_footer_off', true );
		$disable_copyrights_page = get_post_meta( $page_id, '_woodmart_copyrights_off', true );
	?>
	<?php if ( ! $disable_prefooter && woodmart_get_opt( 'prefooter_area' ) ): ?>
		<div class="woodmart-prefooter">
			<div class="container">
				<?php echo do_shortcode( woodmart_get_opt( 'prefooter_area' ) ); ?>
			</div>
		</div>
	<?php endif ?>
	
	<!-- FOOTER -->
	<footer class="footer-container color-scheme-<?php echo esc_attr( woodmart_get_opt( 'footer-style' ) ); ?>">

		<?php
			if ( ! $disable_footer_page && woodmart_get_opt( 'disable_footer' ) ) {
				get_sidebar( 'footer' );
			}
		 ?>
		<?php if ( !$disable_copyrights_page && woodmart_get_opt( 'disable_copyrights' ) ): ?>
			<div class="copyrights-wrapper copyrights-<?php echo esc_attr( woodmart_get_opt( 'copyrights-layout' ) ); ?>">
				<div class="container">
					<div class="min-footer">
						<div class="col-left reset-mb-10">
							<?php if ( woodmart_get_opt( 'copyrights' ) != '' ): ?>
								<?php echo do_shortcode( woodmart_get_opt( 'copyrights' ) ); ?>
							<?php else: ?>
								<p>&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All rights reserved', 'woodmart' ) ?></p>
							<?php endif ?>
						</div>
						<?php if ( woodmart_get_opt( 'copyrights2' ) != '' ): ?>
							<div class="col-right reset-mb-10">
								<?php echo do_shortcode( woodmart_get_opt( 'copyrights2' ) ); ?>
							</div>
						<?php endif ?>
					</div>
				</div>
			</div>
		<?php endif ?>

	</footer>
<?php endif ?>
</div> <!-- end wrapper -->
<div class="woodmart-close-side"></div>

<script language="javascript" type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.validate.min.js"></script>
<script>

jQuery.validator.addMethod("customemail", 
    function(value, element) {
        return /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value);
    }, "Please enter a valid email address." );

jQuery.validator.addMethod("lettersonly", function(value, element) {
return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Field can contain only alphabet values.");

jQuery.validator.addMethod("numeric", function(value, element) {
return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Field can contain only numeric values.");

jQuery.validator.addMethod("alphanumeric", function(value, element) {
return this.optional(element) || /^[a-zA-Z0-9 ]+$/.test(value);
}, "Field can contain only alphanumeric values.");

var $j = jQuery.noConflict();

$j().ready(function() {

  $j( "#user_signup_form" ).validate({
           rules: {
            
           user_name: {
                       required: true,
                       alphanumeric: true
                   },
            user_email: {
                      required: true,
                      email: true
                  },
           user_pass: {
                      required: true,
                      minlength: 6,
                      maxlength: 15
                 },
           user_phone: {
                      required: true,
                      number: true
                  },
            company_name: {
                      required: true,
                      alphanumeric: true
                },
            user_addr: {
                      required: true
                      //alphanumeric: true
                },
            cmp_country: {
                      required: true,
                      lettersonly: true
                },
            company_id: {
                      required: true,
                      alphanumeric: true
                }
          
        },
           messages: {

                user_name: {
                     required: "User name is required."
                   },
                  user_email: {
                    required: "Email address is required.",
                    email: "Enter a valid email address."
                   },
                   user_pass: {
                     required: "Password cannot be blank.",
                     minlength: "Try a password with at least 6 characters.",
                     maxlength:"Password should not exceed 15 characters."
                   },
                   user_phone: {
                     required: "Phone number is required."
                   },
                   company_name: {
                     required: "Company name is required."
                   },
                   user_addr: {
                     required: " Company address is required."
                   },
                   cmp_country: {
                     required: " Company country is required."
                   },
                   company_id: {
                     required: "Company id is required."
                   },
                   
       }
   });
  
  $j( "#user_login_form" ).validate({
           rules: {
            useremail: {
                    required: true,
                    email: true
                  },
               userpass: {
                    required: true,
                  }
              },
           messages: {
              useremail: {
                  required: "Email address is required.",
                  email: "Enter a valid email address."
                 },
                 userpass: {
                   required: "Please enter the password."
                 },
            }
   });

  $j( "#reset_form" ).validate({
           rules: {
            newpass: {
                      required: true,
                      minlength: 6,
                      maxlength: 15
                    },
               cnfpass : {
                    required: true,
                    equalTo : "#newpass"
                }
              },
           messages: {
              newpass: {
                   required: "Please enter the password.",
                   minlength: "Try a password with at least 6 characters.",
                   maxlength:"Password should not exceed 15 characters."
                 },
            cnfpass: {
                    required: "Re-enter your password.",
                    equalTo: "Password does not match."
                  },
              }
   });
  $j( "#forget_form" ).validate({
           rules: {
            email: {
                    required: true,
                    email: true
                  }
              },
           messages: {
            email: {
                  required: "Email address is required.",
                  email: "Enter a valid email address."
                 },
              }
        });

  $j( "#request_form" ).validate({
           rules: {
              req_user_name: {
                         required: true,
                         alphanumeric: true
                     },
             cmpy_name: {
                         required: true,
                         alphanumeric: true
                     },
             req_user_phone: {
                       required: true,
                       number: true
                     },
              req_user_email: {
                      required: true,
                      email: true
                    }
              },
           messages: {
                req_user_name: {
                       required: "Name is required."
                       },
                 cmpy_name: {
                      required: "Company name is required."
                 },
                 req_user_phone: {
                      required: "Phone number is required."
                 },
                 req_user_email: {
                      required: "Email address is required.",
                      email: "Enter a valid email address."
                     },
              }
        });

});
setTimeout(function() {
            $j('#message_hide').hide('fast');
        }, 7000);

jQuery(function() {
      var pgurl = window.location.href;
       <?php 
      $quotelink = get_home_url().'/my-quote'; 
      $orderlink = get_home_url().'/my-account/orders';
      ?>
      var target = "<?php echo $quotelink; ?>" ;
      var endpage = "<?php echo $orderlink; ?>" ;
      if ( target == pgurl){
        jQuery('.quote_menu').addClass('active');
      }
      if ( endpage == pgurl){
        jQuery('.order_menu').addClass('active');
      }
  });
/* home page script */
jQuery(document).ready(function() {
  jQuery('.content1').addClass('active');
  jQuery('.cr-btn1').addClass('current');
   jQuery('.faq-sec').css('display','none');
   jQuery('.contact-sec').css('display','none');
});
function about() {
    jQuery('.content1').addClass('active');
    jQuery('.content2').removeClass('active');
    jQuery('.content3').removeClass('active');
    jQuery('.faq-sec').css('display','none');
    jQuery('.contact-sec').css('display','none');
    jQuery('.about-sec').css('display','block');
    jQuery('.cr-btn1').addClass('current');
    jQuery('.cr-btn2').removeClass('current');
    jQuery('.cr-btn3').removeClass('current');
  }
function faq() {
    jQuery('.content2').addClass('active');
    jQuery('.content1').removeClass('active');
    jQuery('.content3').removeClass('active');
    jQuery('.about-sec').css('display','none');
    jQuery('.contact-sec').css('display','none');
    jQuery('.faq-sec').css('display','block');
    jQuery('.cr-btn2').addClass('current');
    jQuery('.cr-btn1').removeClass('current');
    jQuery('.cr-btn3').removeClass('current');
  }
function contact() {
    jQuery('.content3').addClass('active');
    jQuery('.content1').removeClass('active');
    jQuery('.content2').removeClass('active');
    jQuery('.contact-sec').css('display','block');
    jQuery('.about-sec').css('display','none');
    jQuery('.faq-sec').css('display','none');
    jQuery('.cr-btn3').addClass('current');
    jQuery('.cr-btn1').removeClass('current');
    jQuery('.cr-btn2').removeClass('current');
  }
</script>

<?php wp_footer(); ?>
</body>
</html>
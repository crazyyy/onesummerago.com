<?php
/**
 * Template Name: Contact
 * The main template file for display contact page.
 *
 * @package WordPress
*/


/**
*	if not submit form
**/
if(!isset($_GET['your_name']))
{

get_header(); 

?>
		<?php
			if(has_post_thumbnail($current_page_id, 'full'))
			{
			    $image_id = get_post_thumbnail_id($current_page_id); 
			    $image_thumb = wp_get_attachment_image_src($image_id, 'full', true);
			    $pp_page_bg = $image_thumb[0];
			}
			else
			{
				$pp_page_bg = get_stylesheet_directory_uri().'/example/bg.jpg';
			}
		?>
		<script type="text/javascript"> 
			jQuery.backstretch( "<?php echo $pp_page_bg; ?>", {speed: 'slow'} );
		</script>

		<!-- Begin content -->
		<div id="page_content_wrapper">
		
			<div class="inner">
			
			<div class="sidebar_content full_width" style="padding-bottom:0">
				<h1 class="cufon"><?php the_title(); ?></h1><br/><hr/>
			</div>
			
			<div class="sidebar_content" style="width:43%;margin-top:-10px">
				
				<?php the_content(); ?>
				
				<!-- Begin main content -->
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>		

							<?php do_shortcode(the_content()); ?>

						<?php endwhile; ?>
						
						<?php
							$pp_contact_form = unserialize(get_option('pp_contact_form_sort_data'));
						?>
						<form id="contact_form" method="post" action="<?php echo curPageURL(); ?>">
							<?php 
								if(is_array($pp_contact_form) && !empty($pp_contact_form))
								{
									foreach($pp_contact_form as $form_input)
									{
										switch($form_input)
										{
											case 1:
							?>
											 <p style="margin-top:20px">
						    					<input id="your_name" name="your_name" type="text" style="width:94%" title="Name*"/>
						    				</p>				
							<?php
											break;
											
											case 2:
							?>
											 <p style="margin-top:20px">
						    					<input id="email" name="email" type="text" style="width:94%" title="Email*"/>
						    				</p>				
							<?php
											break;
											
											case 3:
							?>
											 <p style="margin-top:20px">
						    					<textarea id="message" name="message" rows="7" cols="10" style="width:94%" title="Message*"></textarea>
						    				</p>				
							<?php
											break;
											
											case 4:
							?>
											 <p style="margin-top:20px">
						    					<input id="address" name="address" type="text" style="width:94%" title="Address"/>
						    				</p>				
							<?php
											break;
											
											case 5:
							?>
											 <p style="margin-top:20px">
						    					<input id="phone" name="phone" type="text" style="width:94%" title="Phone"/>
						    				</p>				
							<?php
											break;
											
											case 6:
							?>
											 <p style="margin-top:20px">
						    					<input id="mobile" name="mobile" type="text" style="width:94%" title="Mobile"/>
						    				</p>				
							<?php
											break;
											
											case 7:
							?>
											 <p style="margin-top:20px">
						    					<input id="company" name="company" type="text" style="width:94%" title="Address"/>
						    				</p>				
							<?php
											break;
											
											case 8:
							?>
											 <p style="margin-top:20px">
						    					<input id="country" name="country" type="text" style="width:94%" title="Country"/>
						    				</p>				
							<?php
											break;
										}
									}
								}
							?>
						    <p style="margin-top:20px"><br/>
								<input type="submit" value="Send Message"/>
							</p>
						</form>
						<div id="reponse_msg"></div>
				<!-- End main content -->
				</div>
				
				<div class="sidebar_wrapper" style="width:40%;margin-top:-35px">
						<div class="sidebar" style="width:100%">
							
							<div class="content">
							
								<ul class="sidebar_widget">
									<?php dynamic_sidebar('Contact Sidebar'); ?>
								</ul>
								
							</div>
						
						</div>
						<br class="clear"/>
			
			</div>
			</div>
			
			<br class="clear"/>
		</div>
		<!-- End content -->
				
		<br class="clear"/><br/>
<?php get_footer(); ?>

<script>
$j(document).ready(function(){ 
	$j.validator.setDefaults({
		submitHandler: function() { 
		    var actionUrl = $j('#contact_form').attr('action');
		    
		    $j.ajax({
  		    	type: 'GET',
  		    	url: actionUrl,
  		    	data: $j('#contact_form').serialize(),
  		    	success: function(msg){
  		    		$j('#contact_form').hide();
  		    		$j('#reponse_msg').html(msg);
  		    	}
		    });
		    
		    return false;
		}
	});
		    
		
	$j('#contact_form').validate({
		rules: {
		    your_name: "required",
		    email: {
		    	required: true,
		    	email: true
		    },
		    message: "required"
		},
		messages: {
		    your_name: "Please enter your name",
		    email: "Please enter a valid email address",
		    agree: "Please enter some message"
		}
	});
});
</script>
				
				
<?php
}

//if submit form
else
{

	/*
	|--------------------------------------------------------------------------
	| Mailer module
	|--------------------------------------------------------------------------
	|
	| These module are used when sending email from contact form
	|
	*/
	
	//Get your email address
	$contact_email = get_option('pp_contact_email');
	
	//Enter your email address, email from contact form will send to this addresss. Please enter inside quotes ('myemail@email.com')
	define('DEST_EMAIL', $contact_email);
	
	//Change email subject to something more meaningful
	define('SUBJECT_EMAIL', 'Email from contact form');
	
	//Thankyou message when message sent
	define('THANKYOU_MESSAGE', 'Thank you! We will get back to you as soon as possible');
	
	//Error message when message can't send
	define('ERROR_MESSAGE', 'Oops! something went wrong, please try to submit later.');
	
	
	/*
	|
	| Begin sending mail
	|
	*/
	
	$from_name = $_GET['your_name'];
	$from_email = $_GET['email'];
	
	$mime_boundary_1 = md5(time());
    $mime_boundary_2 = "1_".$mime_boundary_1;
    $mail_sent = false;
 
    # Common Headers
    $headers = "";
    $headers .= 'From: '.$from_name.'<'.$from_email.'>'.PHP_EOL;
    $headers .= 'Reply-To: '.$from_name.'<'.$from_email.'>'.PHP_EOL;
    $headers .= 'Return-Path: '.$from_name.'<'.$from_email.'>'.PHP_EOL;        // these two to set reply address
    $headers .= "Message-ID: <".$now."webmaster@".$_SERVER['SERVER_NAME'].">";
    $headers .= "X-Mailer: PHP v".phpversion().PHP_EOL;                  // These two to help avoid spam-filters
	
	$message = 'Name: '.$from_name.PHP_EOL;
	$message.= 'Email: '.$from_email.PHP_EOL.PHP_EOL;
	$message.= 'Message: '.PHP_EOL.$_GET['message'];
	
	if(isset($_GET['address']))
	{
		$message.= 'Address: '.$_GET['address'].PHP_EOL;
	}
	
	if(isset($_GET['phone']))
	{
		$message.= 'Phone: '.$_GET['phone'].PHP_EOL;
	}
	
	if(isset($_GET['mobile']))
	{
		$message.= 'Mobile: '.$_GET['mobile'].PHP_EOL;
	}
	
	if(isset($_GET['company']))
	{
		$message.= 'Company: '.$_GET['company'].PHP_EOL;
	}
	
	if(isset($_GET['country']))
	{
		$message.= 'Country: '.$_GET['country'].PHP_EOL;
	}
	    
	
	if(!empty($from_name) && !empty($from_email) && !empty($message))
	{
		mail(DEST_EMAIL, SUBJECT_EMAIL, $message, $headers);
	
		echo THANKYOU_MESSAGE;
		
		exit;
	}
	else
	{
		echo ERROR_MESSAGE;
		
		exit;
	}
	
	/*
	|
	| End sending mail
	|
	*/
}

?>
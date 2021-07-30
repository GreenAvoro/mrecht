<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!-- #main --> </div> </div><!-- #page -->

<div class="footer-mailchimp">
	<div class="container">
		<div class="footer-signup">
			<h5><span>Sign up and save!</span> Stay up to date with our special discounts to your inbox</h5>
			<div class="footer-signup-input">
				<input class="footer-input-text" type="text" placeholder="Email Address" name="mailchimp_email" />
				<input class="footer-submit" type="submit" value="SUBSCRIBE" />
			</div>
		</div>
	</div>
</div>

<footer id="footer" class="footer-area">
	<div class="footer-nav desktop">
		<div class="menu-item">
			<h4>SERVICES</h4>
			<li><a href="/contact-us/custom-printed-labels/">Custom Printed Labels</a></li>
			<li><a href="/contact-us/custom-woven-labels/">Custom Woven Labels</a></li>
			<?php wp_list_pages( ['child_of' => 130, 'title_li'=>NULL] )?>
		</div>
		<div class="menu-item">
			<h4>RESOURCES</h4>
			<?php wp_list_pages( ['child_of' => 154, 'title_li'=>NULL, 'depth'=>1] )?>
		</div>
		<div class="menu-item">
			<h4>TERMS & CONDITIONS</h4>
			<?php wp_list_pages( ['child_of' => 259, 'title_li'=>NULL, 'depth'=>1] )?>
		</div>
		<div class="menu-item">
			<h4>CONTACT US</h4>
			<?php wp_list_pages( ['child_of' => 77, 'title_li'=>NULL, 'depth'=>1] )?>
		</div>
	</div>
	
	<div class="footer-nav mobile">
		<div class="menu-item">
			<h4>SERVICES</h4>
			<div class="dropdown">
				<li><a href="/contact-us/custom-printed-labels/">Custom Printed Labels</a></li>
				<li><a href="/contact-us/custom-woven-labels/">Custom Woven Labels</a></li>
				<?php wp_list_pages( ['child_of' => 130, 'title_li'=>NULL] )?>
			</div>
		</div>
		<div class="menu-item">
			<h4>RESOURCES</h4>
			<div class="dropdown">
				<?php wp_list_pages( ['child_of' => 154, 'title_li'=>NULL, 'depth'=>1] )?>
			</div>
		</div>
		<div class="menu-item">
			<h4>TERMS & CONDITIONS</h4>
			<?php wp_list_pages( ['child_of' => 259, 'title_li'=>NULL, 'depth'=>1] )?>
		</div>
		<div class="menu-item">
			<h4>CONTACT US</h4>
			<?php wp_list_pages( ['child_of' => 77, 'title_li'=>NULL, 'depth'=>1] )?>
		</div>
	</div>
	<div class="footer-extras">
		<div class="extra">
			<img src="<?=get_template_directory_uri()?>/img/icons/phone-icon.png" alt="phone icon">
			<a href="tel:1300941941">1300 941 941</a>
		</div>
		<div class="extra">
			<img src="<?=get_template_directory_uri()?>/img/icons/Pin-icon.svg" alt="phone icon">
			<h6>Head Office</h6>
			<a href="https://www.google.com/maps/place/53-57+Cambridge+St,+Collingwood+VIC+3066,+Australia/@-37.8067524,144.98266,17z/data=!3m1!4b1!4m5!3m4!1s0x6ad642e738d03cc3:0xb033258500ad92f7!8m2!3d-37.8067567!4d144.9848487">
				<p>53-57 Cambridge Street,</p>
				<p>Collingwood,</p>
				<p>VIC, AU 3066</p>
			</a>
		</div>
		<div class="extra">
			<img src="<?=get_template_directory_uri()?>/img/icons/Pin-icon-hollow.svg" alt="phone icon">
			<a href="/contact-us/sales-offices/"><h6>Other Locations</h6></a>
		</div>
		<div class="extra">
			<img src="<?=get_template_directory_uri()?>/img/icons/mail-icon.png" alt="phone icon">
			<a href="mailto:info@mrecht.com.au">Email us</a>
		</div>

		<div class='logo-list'>
			<a href="https://instagram.com/m.recht_accessories">
				<img src="<?=get_template_directory_uri()?>/img/icons/IG-icon.png" alt="Instagram icon">
			</a>
			<a href="https://facebook.com/m.rechtaccessories">
				<img src="<?=get_template_directory_uri()?>/img/icons/FB-icon.png" alt="Facebook icon">
			</a>
			<a href="https://twitter.com/MrechtAcc">
				<img src="<?=get_template_directory_uri()?>/img/icons/Twitter-Logo.svg" alt="Twitter icon">
			</a>
		</div>
	</div>
</footer> 
<div class="footer-footer">
	<div class="container-fluid">
		<div class="row">
			<img src="<?=get_template_directory_uri()?>/img/rapidssl.png" alt="Rapid SLL Protection">
			<div class="we-accept">
				<p class="footer-footer-bold">WE ACCEPT</p>
				<div class="footer-lower-payment">
					<img src="<?=get_template_directory_uri().'/img/icons/paypal.svg'?>" alt="paypal">
					<img src="<?=get_template_directory_uri().'/img/icons/visa.svg'?>" alt="visa">
					<img src="<?=get_template_directory_uri().'/img/icons/mastercard.svg'?>" alt="mastercard">
				</div>
			</div>
			<p>&copy<?=date('Y').' '?> M.Recht Accessories.</p>
		</div>
	</div>
</div>



<button id="scrollBtn" title="Go to top"><span class="fa fa-arrow-up"></span></button> 
 
<?php wp_footer(); ?>
</body>
</html>
<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

include(get_template_directory().'/includes/getCategories.php');
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title>
		<?php wp_title( '|', true, 'right' ); ?>
	</title>
	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
	
	<?php wp_head(); ?>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

	<!-- reCAPTCHA -->
	<?php if ( is_page( array( 'contact-us', 'homepage' ) ) ) : ?>
	<!-- <script src='https://www.google.com/recaptcha/api.js?render=6LdY87YUAAAAAAVYGFI7ILrEfs5bdcQpIfjbhV3e'></script> -->
	<?php endif; ?>

</head>

<?php
//Get the child pages of the contact page for displaying in the nav
$contact_post = get_post(77);

?>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<div class="topbar-wrapper">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="topbar">
								<p>We ship worldwide - <a href="/terms-conditions/shipping-delivery/">Learn more</a></p>
								<p><img src="<?= get_template_directory_uri() .'/img/icons/phone-icon.png'?>"/>CALL US TODAY ON:  <a style="color: black" href="tel:1300941941">1300 941 941</a></p>
								<div class="top-menu">
									<a href="/about-us/">About Us</a>
									<div class="contact-item">
										<a href="/contact-us/general-sales-enquiry">Contact</a>
										<div class="contact-dropdown">
											<?=wp_list_pages(['child_of' => $contact_post->ID, 'title_li'=>NULL]);?>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<nav class="navbar navbar-inverse desktop desktopnav top">
				<div class="container">
					<div class="row">
						<div class="header-container col-md-12">
							<div class="header-top">
								<div class="header-top--logo">
									<a href="<?php echo home_url() ?>">
										<img src="<?php echo get_template_directory_uri() . '/img/main-logo-white.svg' ?>" class="" />
									</a>
								</div>

								<div class="desktop-menu">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'primary_menu',
										'menu_class' => 'weka-main-menu',
										'link_after'	=> '<img src="'.get_template_directory_uri().'/img/arrow-down-sm.svg" style="margin-left: 4px"/>',
									) );			  
								?>
								</div>

								<div class="header-top">

									<form role="search" class="search-form" method="get" action="/">
										<button class="search-form--submit" type="submit"><img src="<?php echo get_template_directory_uri() . '/img/icons/search-icon.png' ?>"/></button>
										<input class="search-form--input" name="s" placeholder="I'm looking for..." type="text">
									</form>

									<div class="weka-icon-container">
										<a class="header-icon" href="/my-account">
											<img class="sm-icon" src="<?php echo get_template_directory_uri() . '/img/icons/user-icon-lrg.png' ?>"/>
											<p><?=is_user_logged_in()?"ACCOUNT":"SIGN IN"?></p>
										</a>
									</div>
								
									<div class="weka-icon-container">
										<a class="header-icon" href="/cart">
											<img class="sm-icon" src="<?php echo get_template_directory_uri() . '/img/icons/cart-icon-lrg.png' ?>"/>
											<p>CART</p>
										</a>
									</div>
	  
								</div>	
							</div>

							
						</div>	
					</div>
				</div>
			</nav>
			<nav class="navbar navbar-inverse mobile mobilenav">
				<div class="container">
					<div class="header-container">
						<div class="navbar-container">
							<div class="mobile-nav">
								<img id="menu-icon" src="<?=get_template_directory_uri()?>/img/icons/menu.svg" alt="Menu">
							</div>
							<a class="navbar-brand" href="<?php echo home_url() ?>"><img src="<?php echo get_template_directory_uri() . '/img/main-logo-white.svg' ?>" class="scroll-image" /></a>
							<div class="mobile-icons">
							
							
								<div class="icon-container">
									<a href="/cart">
										<img src="<?php echo get_template_directory_uri() . '/img/icons/cart-icon-lrg.png' ?>"/>
									</a>
								</div>
							</div>
						</div>
					</div>

					<div class="mobile--search-bar">
						<form role="search" class="search-form" method="get" action="/">
							<input class="search-form--input" name="s" placeholder="Search our site" type="text">
							<button class="search-form--submit" type="submit"><img src="<?php echo get_template_directory_uri() . '/img/icons/search-icon.png' ?>"/></button>
						</form>
					</div>
				</div>
			</nav> 	
		</header>

		<div class="slide-out-menu">
			<div class="slide-out-menu--container">
				<div class="slide-out-menu--top-bar">
					<div class="menu-login">
						<?php if ( !is_user_logged_in() ): ?>
							<a href="#">Login</a><span> / </span><a href="#">Sign Up</a>
						<?php else: ?>
							<a href="#">Logout</a>
						<?php endif; ?>
					</div>
					<div id="menu-close">
						<i class="fas fa-times" aria-hidden="true"></i>
					</div>
				</div>
				<div class="slide-out-menu--accounts">
					<div class="row">
						<div class="col-xs-6">
							<a href="/my-account">
								<div class="accounts-item accounts-item-account">Account</div>
							</a>
						</div>
						<div class="col-xs-6">
							<a href="/orders">
								<div class="accounts-item accounts-item-orders">Orders</div>
							</a>
						</div>
					</div>
				</div>
				<div class="slide-out-menu--screens">
					<?php 
						wp_nav_menu( array(
							'theme_location' => 'primary_menu',
							'menu_class' => 'nav navbar-nav desktop_nav'
						) );			  
					?>
					
				</div>
			</div>
		</div>

		<div class="slide-out-menu-2">
			<div class="slide-out-header">
				<div class="top-row">
					<div id="menu-close">
						<i class="fas fa-times" aria-hidden="true" style="color: white; font-size: 1.5em"></i>
					</div>
					<img src="<?php echo get_template_directory_uri() . '/img/main-logo-white.svg' ?>" class="scroll-image" />
					<a href="/cart">
						<img src="<?php echo get_template_directory_uri() . '/img/icons/cart-icon-lrg.png' ?>"/>
					</a>
				</div>
				<div class="search-bar">
					<form role="search" class="search-bar-form" method="get" action="/">
						<input class="search-bar-input" name="s" placeholder="I'm looking for..." type="text">
						<img src="<?php echo get_template_directory_uri() . '/img/icons/search.svg' ?>" alt="">
					</form>
				</div>
			</div>
			<div class="slide-out-account">
				<a href="/my-account/">
					<img src="<?php echo get_template_directory_uri() . '/img/icons/account.svg' ?>" alt="">
					<p>Sign In</p>
				</a>
				<a href="/registration/">
					<img src="<?php echo get_template_directory_uri() . '/img/icons/account-plus.svg' ?>" alt="">
					<p>Join</p>
				</a>
			</div>
			<div class="slide-out-list">
				<?php 
					wp_nav_menu( array(
						'menu'			=> 'mobile_menu',
						'menu_class' 	=> 'slide-out-list-menu',
						'container'		=> ''
					) );			  
				?>
				<div class="slide-out-list-items">
					<div class="back-button hide" data-parent="0">
						<p><i class="fas fa-chevron-left"></i>Back</p>
					</div>
					<div class="slide-out-list-items-header hide">
						Categories
					</div>
					<?php
					$cats = getCategories(false);
					foreach($cats as $cat):
						if($cat->name == "Uncategorized") continue;
						$root = $cat->parent == 0 ? '' : 'hide';
						$hasChild = false;
						foreach($cats as $cat2){
							if($cat2->parent == $cat->term_id){
								$hasChild = true;
								break;
							}
						}
					?>
						<div class="item <?= $root?>" data-parent="<?=$cat->parent?>" data-id="<?=$cat->term_id?>" data-hasChild="<?=$hasChild?>" data-name="<?=$cat->name?>">
							<a href="<?=get_term_link($cat->slug, 'product_cat')?>" data-id="<?=$cat->term_id?>"><?=$cat->name?></a>
							<?= $hasChild ? '<i class="fas fa-chevron-right"></i>' : ''?>
						</div>
					<?php 
					endforeach;?>
				</div>
			</div>

		</div>
		<!-- #masthead -->

		<?php if ( is_front_page() ) : ?>
			<div id="main">
				<?php include(get_template_directory().'/templates/dropdown.html.php');?>
				<?php include(get_template_directory().'/templates/dropdown-2.html.php');?>
				<?php include(get_template_directory().'/templates/dropdown-3.html.php');?>
		<?php else : ?>
			<div id="main">
				<?php include(get_template_directory().'/templates/dropdown.html.php');?>
				<?php include(get_template_directory().'/templates/dropdown-2.html.php');?>
				<?php include(get_template_directory().'/templates/dropdown-3.html.php');?>
		<?php endif; ?>
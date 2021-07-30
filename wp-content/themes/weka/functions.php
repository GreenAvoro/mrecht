<?php
session_start();

class Captcha{
    public function getCaptcha($SecretKey){
        $Resposta=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdY87YUAAAAAFgAZ5FLKtmNqL7rniD0efJyE150&response={$SecretKey}");
        $Retorno=json_decode($Resposta);
        return $Retorno;
    }
}

if(isset($_REQUEST['message'])) {
	if($_REQUEST['message'] == 'yes') {

		$ObjCaptcha = new Captcha();
		$Retorno = $ObjCaptcha->getCaptcha($_POST['g-recaptcha-response']); 

		if($Retorno->success) {

			function wpdocs_set_html_mail_content_type() {
				return 'text/html';
			}

			add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

			$message = 'You have received an enquiry from your website<br/><br/> Please DO NOT reply to this email. Create a new one. <br/><br/>';
			foreach($_POST as $name => $value) {
				if($name == 'Enquiry') {
					$message .= ucwords(str_replace('-',' ',$name)) .': <br/><br/><p style="white-space: pre-wrap;">' . $value .'</p><br/>';
				} else if ($name == 'g-recaptcha-response') {
					continue;
				} else if($name != 'submit') {
					$message .= ucwords(str_replace('-',' ',$name)) .': ' . $value .'<br/>';
				}
			}

			$headers[]   = 'Reply-To: '.$_POST['fName'].' '.$_POST['lName'].' <'.$_POST['email'].'>';

			wp_mail( 'leon@wekaonline.co.nz', 'Enquiry from the Durofast website', $message,$headers );

			remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

			$_SESSION['response'] = json_encode(array('status'=>'success','message'=>'We have received your enquiry, and will get back to you within 3 business days.'));
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit();
		}
	}
}


function scripts_init() {
    if (!is_admin()) {
		wp_enqueue_style( 'js_composer_front' );
		wp_enqueue_style( 'wekacss', get_template_directory_uri() . '/css/main.css',false,date('his'),'all');
		wp_enqueue_style( 'actualwekacss', get_template_directory_uri() . '/css/weka.css',false,date('his'),'all');
		wp_enqueue_style( 'owl-carousel-default-css', get_template_directory_uri() . '/owlcarousel/owl.theme.default.min.css' );
		wp_enqueue_style( 'owl-carousel-css', get_template_directory_uri() . '/owlcarousel/owl.carousel.min.css' );
		wp_enqueue_style( 'font-din', 'https://use.typekit.net/jsv1nzg.css' );
		wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'load-bs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
		wp_enqueue_script( 'load-bsj', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' );
		wp_enqueue_script( 'wekajavascript', get_template_directory_uri() .'/weka.js', array(), date('his'), true );
		wp_enqueue_script( 'owl-carousel-js', get_template_directory_uri() .'/owlcarousel/owl.carousel.min.js', array(), false, true );	
		wp_enqueue_script( 'product-js', get_template_directory_uri() .'/product.js', array(), date('his'), true );	
		wp_enqueue_script( 'select-2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
		wp_enqueue_style( 'select-2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
		wp_enqueue_script( 'password-strength', get_template_directory_uri().'/password_strength.js', array(), false, true );
    }
}

add_action('wp_enqueue_scripts', 'scripts_init');


function register() {
	register_nav_menu('primary_menu',__( 'Primary Menu' ));
	register_nav_menu('Aboutus_menu',__( 'About Us' ));
	register_nav_menu('delivery_menu',__( 'Delivery' ));
	register_nav_menu('top_menu',__( 'Top-bar Menu' ));

	add_role('trade', 'Trade/Wholesale');
	add_role('schl', 'School');
}


add_action( 'init', 'register' );


function weka_widgets_init() {

	register_sidebar( array(
		'id'	=> 'global_sidebar_1',
		'name' => __( 'Global Sidebar', 'weka' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3><div class="line"></div><div class="clear"></div>'
	));


	register_sidebar( array(
		'id'	=>'footer-1',
		'name' => __( 'Footer 1', 'weka' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4><div class="line"></div><div class="clear"></div>'
	));

	register_sidebar( array(
		'id'	=>'footer-2',
		'name' => __( 'Footer 2', 'weka' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4><div class="line"></div><div class="clear"></div>'
	));

	register_sidebar( array(
		'id'	=>'footer-3',
		'name' => __( 'Footer 3', 'weka' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4><div class="line"></div><div class="clear"></div>'
	));

	register_sidebar( array(
		'id'	=>'footer-4',
		'name' => __( 'Footer 4', 'weka' ),
		'before_widget' => '<div id="%1$s" class="vc_col-sm-3 widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4><div class="line"></div><div class="clear"></div>'
	));

}

add_action( 'widgets_init', 'weka_widgets_init' );

/**
 * Register support for Gutenberg wide images in your theme
 */
function mytheme_setup() {
	add_theme_support( 'align-wide' );
	add_theme_support( 'post-thumbnails', array( 'post' ) );
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_setup' );


add_action( 'admin_menu', 'homepage_categories_register' );

function homepage_categories_register()
{
    add_menu_page(
        'Homepage Categories',     // page title
        'Homepage Categories',     // menu title
        'manage_options',   // capability
        'homepage-categories',     // menu slug
        'homepage_categories_render',
		'',
		58
	);
}
if(is_admin()){
	if(isset($_POST['homepage-categories'])){
		update_option('homepage_carousel_1', $_POST['carousel_1']);
		update_option('homepage_carousel_2', $_POST['carousel_2']);

		update_option('major_category_1', $_POST['major-category-1']);
		update_option('major_category_2', $_POST['major-category-2']);
		update_option('major_category_3', $_POST['major-category-3']);
		update_option('major_category_4', $_POST['major-category-4']);
		update_option('major_category_5', $_POST['major-category-5']);
		update_option('major_category_6', $_POST['major-category-6']);
		update_option('major_category_7', $_POST['major-category-7']);
		update_option('major_category_8', $_POST['major-category-8']);

	}
}

function homepage_categories_render()
{
    global $title;

	if(is_admin()){
		$carousel_1 = get_option('homepage_carousel_1');
		$carousel_2 = get_option('homepage_carousel_2');
		$major_cat_1= get_option('major_category_1');
		$major_cat_2= get_option('major_category_2');
		$major_cat_3= get_option('major_category_3');
		$major_cat_4= get_option('major_category_4');
		$major_cat_5= get_option('major_category_5');
		$major_cat_6= get_option('major_category_6');
		$major_cat_7= get_option('major_category_7');
		$major_cat_8= get_option('major_category_8');
	}

	ob_start();?>
    <div class="wrap">
		<h1><?=$title?></h1>
		<br>
		<form action="" method="POST">
			<label>Carousel 1</label><br>
			<select name="carousel_1" id="carousel_1">
				<?php
				$args = [
					'taxonomy'   	=> "product_cat",
					'hide_empty'	=> false
				];
				$product_categories = get_terms($args);
				foreach($product_categories as $category):?>
					<?php if($carousel_1 == $category->slug):?>
						<option value="<?=$category->slug?>" selected><?=$category->name?></option>
					<?php else: ?>
						<option value="<?=$category->slug?>"><?=$category->name?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<br>
			<br>
			<label for="categories-with-image">Categories With Image 1</label>
			<div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr" >
				<select name="major-category-1" id="major-category-1">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_1 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-2" id="major-category-2">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_2 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-3" id="major-category-3">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_3 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-4" id="major-category-4">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_4 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<br>
			<br>
			<label for="categories-with-image">Categories With Image 2</label>
			<div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr" >
				<select name="major-category-5" id="major-category-5">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_5 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-6" id="major-category-6">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_6 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-7" id="major-category-7">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_7 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<select name="major-category-8" id="major-category-8">
					<?php
					$args = [
						'taxonomy'   	=> "product_cat",
						'hide_empty'	=> false
					];
					$product_categories = get_terms($args);
					foreach($product_categories as $category):?>
						<?php if($major_cat_8 == $category->slug):?>
							<option value="<?=$category->slug?>" selected><?=$category->name?></option>
						<?php else: ?>
							<option value="<?=$category->slug?>"><?=$category->name?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>

			<br>
			<label>Carousel 2</label><br>
			<select name="carousel_2" id="carousel_2">
				<?php
				$args = [
					'taxonomy'   	=> "product_cat",
					'hide_empty'	=> false
				];
				$product_categories = get_terms($args);
				foreach($product_categories as $category):?>
					<?php if($carousel_2 == $category->slug):?>
						<option value="<?=$category->slug?>" selected><?=$category->name?></option>
					<?php else: ?>
						<option value="<?=$category->slug?>"><?=$category->name?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<br>
			<br>
			<input type="submit" name="homepage-categories" value="Save Changes" class="button button-primary">
		</form>
    </div>
	<?php echo ob_get_clean();
}

add_action('woocommerce_after_shop_loop_item_title','add_button');
function add_button(){
	global $product;
	ob_start()?>
	<a class="common-button" href="<?=get_permalink($product->get_id())?>">VIEW ACCESSORY</a>
	<?php echo ob_get_clean();
}


add_filter('woocommerce_product_tabs', 'weka_remove_product_tabs', 10);
function weka_remove_product_tabs($tabs){
	unset($tabs['description']);
	// return $tabs;
}


/**
 * Remove related products output
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );




add_filter( 'woocommerce_quantity_input_args', 'wc_qty_input_args', 10, 2 );
function wc_qty_input_args( $args, $product ) {
	
	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	
	
	$attributes = $product->get_attributes();

	if(array_key_exists('pa_purchased-in-multiples', $attributes)){
		$value = get_term($attributes['pa_purchased-in-multiples']->get_options()[0])->name;
		// var_dump(get_term($value));
		if(isset($value) && $value != ''){
			$args['step'] = $value;
			$args['min_value'] = $value;
		}
	}

	return $args;
}

add_action('woocommerce_before_add_to_cart_quantity', 'weka_before_add_to_cart_qty');
function weka_before_add_to_cart_qty(){
	global $product, $wpdb;
	$attributes = $product->get_attributes();


	/**________________________________________________
	 * 
	 * Get the product price breaks
	 * 
	 */
	$product_sku = $product->get_sku();
	$sql = "SELECT * FROM `{$wpdb->prefix}woocommerce_customer_pricing` WHERE `product_sku`=%s";
	// Get records where SKUs match the current product SKU
	$results = $wpdb->get_results(
		$wpdb->prepare($sql,$product_sku)
	);
	//Only output the section if there are quantity price breaks for this product in the database
	if($results != null){
		ob_start()?>
		<p style="margin: 0; margin-top: 2em" class="quantity-label">Bulk Quantity Buys:</p>
		<div class="product-break-list">
			<?php
			foreach($results as $price_break):?>
				<div class="price-break">
					<p>Minimum <?=$price_break->quantity?> pieces:</p>
					<p>$<?=$price_break->price?> per Piece</p>
				</div>
	
			<?php 
			endforeach;
			?>
		</div>
		<?php
		echo ob_get_clean();
	}
	/**________________________________________________
	 * 
	 * Check if the product needs to be ordered in multiples of X
	 * 
	 */
	if(array_key_exists('pa_purchased-in-multiples', $attributes)){
		$value = get_term($attributes['pa_purchased-in-multiples']->get_options()[0])->name;
	}

	/**_______________________________________________
	 * 
	 * Ouput the quantity input 
	 * 
	 */
	ob_start();?>
	<p style="margin: 0; margin-top: 2em" class="quantity-label">Quantity:</p>
	<div id="multiples" data-step="<?=$value?>" style="display: none"></div>
	<?php
	if(isset($value) && ($value != '' || $value != '1')):?>
		<p style="margin: 0;" class='multiples-text'>This product must be purchased in multiples of <?=$value?></p>	
	
	<?php
	endif;
	echo ob_get_clean();
}
add_shortcode( 'go_back_button', 'go_back_button' );
function go_back_button($atts){
	ob_start();?>
	<a class="go-back-button" href="<?=$atts['link']?>"> <i class="fas fa-caret-left"></i> GO BACK</a>
	<?php echo ob_get_clean();
}
add_shortcode('list_colour_cards', 'list_colour_cards');
function list_colour_cards(){
	ob_start();?>
	<div class="colour-card-list">
		<?php wp_list_pages( ['child_of' => 166, 'title_li'=>NULL] );?>
	</div>
	<?php echo ob_get_clean();
}




/**
 * ======================================================================================
 * 		CUSTOM REGISTRATION PAGE														=
 * ======================================================================================
 */

 add_shortcode('weka_custom_registration', 'weka_custom_registration');
 function weka_custom_registration(){
	 if(is_admin()) return;
	//  if(is_user_logged_in()) return;
	 ob_start();

	 do_action('woocommerce_before_customer_login_form');
	 ?>
	 	<h1>Registration</h1>
		<hr>
		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			
 
			<?php do_action( 'woocommerce_register_form_start' ); ?>
			<!--
				ACCOUNT TYPE
			-->
			<div class="custom-form-container grey">
				<h3>ACCOUNT TYPE</h3>
				<?=custom_input_field("account-type", "Account type", 'select',true, '', [
					'customer' => 'New customer',
					'schl' => 'Student',
					'trade' => 'Trade account'
				]);?>
				<div class="school-inputs hide">
					<?php
					$schools = [''=>'Select a School','ADLINST2014' => 'Adelaide Institute of Tafe', 'ALVALELAUN2014' => 'Alanvale College Launceston', 'ALVALENEW2014' => 'Alanvale College Newnham', 'AFDTHUNTNSW2014' => 'Applied Fashion Design Technology at Hunter TAFE NSW', 'AUSACADMY2014' => 'Australian Academy of Design', 'AUSCATHNSW2014' => 'Australian Catholic University NSW', 'AUSCATHVIC2014' => 'Australian Catholic University VIC', 'AUSCOLART2014' => 'Australian College of the Arts', 'AUSTINSTCD2014' => 'Australian Institute of Creative Designs ', 'BILLYBL2014' => 'Billy Blue College of Design - NSW', 'BILLYBLQLD2014' => 'Billy Blue College of Design - QLD', 'BILLYBLMEL2014' => 'Billy Blue MELBOURNE', 'BOXHILL2014' => 'Box Hill Institute', 'CENINSTACT2014' => 'Central Institute of Technology Reid ACT', 'CENINSTWA2014' => 'Central Institute of Technology WA', 'CHALINSTFREO2014' => 'Challenger Institute of Tech Fremantle', 'COLLFASH2014' => 'College of Fashion Design', 'CURTINWA2014' => 'Curtin University WA', 'ECTAFEMOOL2014' => 'East Coast Tafe Mooloolaba', 'EDITHCOWN2014' => 'Edith Cowan University WA', 'LIZBENC2014' => 'Elizabeth Bence School of Fashion', 'FASHTAFENSW2014' => 'Fashion Design Studio TAFE NSW ', 'FBIFASH2014' => 'FBI Fashion College', 'GORDGEL2014' => 'Gordon Tafe Geelong', 'HOLMESNSW2014' => 'Holmes College NSW', 'HOLMGLEN2014' => 'Holmesglen Institute', 'HUNTAFNEW2014' => 'Hunter TAFE Newcastle', 'CIT2014' => 'Institute Technology of Canberra (CIT)', 'KANG2014' => 'Kangan Institute', 'KARLVBNSW2014' => 'Karl von Busse Design College NSW', 'KEMPTAFENSW2014' => 'Kempsey TAFE NSW', 'LCIMEL2014' => 'LCI MELBOURNE', 'MELFASH2014' => 'Melbourne Fashion Institute', 'MELBSCHFASH2014' => 'Melbourne School of Fashion', 'MSITMTG2014' => 'Metropolitan South Institute of TAFE, Mt Gravatt Brisbane', 'NARRBUND2014' => 'Narrabundah College of Canberra', 'NIDA2014' => 'National institute of Dramatic Art', 'NTHCSTTAFEPM2014' => 'North Coast Institute of TAFE, Port Macquarie', 'NCTCOFF2014' => 'North Coast TAFE Coffs Harbour', 'NMTPERTH2014' => 'North Metropolitan Tafe (NMT) Perth', 'NTHMELINST2014' => 'Northern Melbourne Institute', 'ACFASHAUCK2014' => 'NZ Academy of Fashion, Auckland', 'FASHTECHWEL2014' => 'NZ Fashion Tech, Wellington', 'OPCOLLSYD2014' => 'Open Colleges Sydney', 'OPCOLLWA2014' => 'Open Colleges WA', 'OTPOLYDUN2014' => 'Otago Polytechnic Dunedin NZ', 'POLYWBENT2014' => 'Polytechnic West, Bentley  WA', 'PORTMACQ2014' => 'Port Macquarie Tafe', 'QUT2014' => 'Queensland University of Technology', 'RAFFLESNSW2014' => 'Raffles College of Design & Commerce NSW', 'RMITBW2014' => 'RMIT Brunswick Campus', 'RMITCTY2014' => 'RMIT City Campus', 'STHMETROTAFEWA2014' => 'South Metro TAFE WA', 'TAFESTHBRISB2014' => 'South TAFE Brisbane ', 'SWITBUN2014' => 'South West Institute of Technology Bunbury', 'SWSLID2014' => 'South Western Sydney Institute Lidcombe College', 'STHCROSSNSW2014' => 'Southern Cross University NSW', 'SCTMOOL2014' => 'Sunshine Coast TAFE Mooloolaba', 'SWSIMACQ2014' => 'SWSI Macquarie Fields Tafe', 'TAFENSWWOL2014' => 'TAFE NSW (wollongbar)', 'TAFEQLDMTGR2014' => 'Tafe Queensland Mt Gravatt ', 'TAFESA2014' => 'TAFE SA', 'TASTBURN2014' => 'TasTAFE Burnie', 'TASTHOB2014' => 'TasTAFE Hobart', 'TASTLAUN2014' => 'TasTAFE Launceston', 'FASHINST2014' => 'The Fashion Institute', 'FRIENDHOB2014' => "The Friend's High School, Hobart", 'ULTIMONSW2014' => 'Ultimo College Tafe NSW', 'UNITECHSYD2014' => 'University of Technology Sydney', 'VICCOLLART2014' => 'Victoria College of the Arts', 'WHITECLIFFNZ2014' => 'Whitecliff College (Parnell) NZ', 'WHITEHSE2014' => 'Whitehouse Institute of Design NSW', 'WHITEHSEV2014' => 'Whitehouse Institute of Design VIC', 'WODVIC2014' => 'Wodonga Tafe VIC', 'WOLLONGBNSW2014' => 'Wollongbar TAFE NSW'];
					?>
					<?=custom_input_field("school", "School Name", 'select', true, '', $schools);?>
					<?=custom_input_field("school-id", "School ID", 'text', true);?>
					<div class="custom-form-input-container">
						<div></div>
						<p>(Not your student ID)</p>
					</div>
				</div>
				<div class="trade-inputs hide">
					<?=custom_input_field("company", "Business Name", 'text', true);?>
					<?=custom_input_field("abn-acn", "ABN/ACN", 'text', true);?>
				</div>
				<div class="other-inputs">
					<?=custom_input_field("company", "Business Name");?>
					<?=custom_input_field("abn-acn", "ABN/ACN");?>
				</div>
			</div>
			<div class="custom-form-container">
				<h3>LOGIN DETAILS</h3>
				<?php if (get_option( 'woocommerce_registration_generate_username' ) === 'no' ) : ?>
					<?=custom_input_field("username", 'Username', 'text', true);?>
				<?php endif; ?>

				<?= custom_input_field('email',"Email", 'email', true);?>

				<?php if ( get_option( 'woocommerce_registration_generate_password' ) === 'no' ) : ?>
					<?= custom_input_field('password',"Password", 'password', true);?>
					<div id="score-bar"><div class="inner"></div></div>
					<?= custom_input_field('confirm-password',"Confirm Password", 'password', true);?>

				<?php else : ?>

					<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>
			
			</div>
			<div class="custom-form-container">
					<h3>PERSONAL DETAILS</h3>
					<?= custom_input_field('first-name',"First Name", 'text', true);?>
					<?= custom_input_field('last-name',"Surname", 'text', true);?>
					<?= custom_input_field('address-1',"Address", 'text', true);?>
					<?= custom_input_field('address-2',"", 'text');?>
					<?= custom_input_field('address-3',"", 'text');?>
					<?= custom_input_field('suburb',"Suburb", 'text', true);?>
					<?php
					global $woocommerce;
					$countries_obj = new WC_Countries();
					$countries = $countries_obj->__get('countries');
					$states = [];
					foreach($countries as $k=>$v){
						$list = $countries_obj->get_states($k);
						if($list != false) $states[$v] = $list;
					}
					?>
					<?= custom_input_field('country', "Country", 'select', true, '', $countries);?>
					<?= custom_input_field('state', "State", 'select', false, '', $states, true);?>
					<?= custom_input_field('postcode',"Postcode", 'text', true);?>
					<?= custom_input_field('phone',"Contact Phone", 'text', true);?>
			</div>
			<div class="custom-form-container">
				<hr>
				<label for="mailing-list" class="checkbox">
					<input type="checkbox" name="mailing-list" id="input-mailing-list">
					Would you like to subscribe to our mailing list, and receive specials and news periodically?
				</label>

				<?php do_action( 'woocommerce_register_form' ); ?>
				<p class="woocommerce-FormRow form-row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="common-button red woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</p>

			</div>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	 <?php
	 return ob_get_clean();
 }
 
add_action( 'woocommerce_register_post', 'weka_validate_extra_register_fields', 10, 3 );
function weka_validate_extra_register_fields( $username, $email, $validation_errors ) {
	if ( isset( $_POST['first-name'] ) && empty( $_POST['first-name'] ) ) {
		$validation_errors->add( 'first-name_error', __( 'First name is required!', 'woocommerce' ) );
	}
	if ( isset( $_POST['last-name'] ) && empty( $_POST['last-name'] ) ) {
		$validation_errors->add( 'last-name_error', __( 'Last name is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['confirm-password'] ) && empty( $_POST['confirm-password'] ) ) {
		$validation_errors->add( 'last-name_error', __( '<strong>Error</strong>: Confirm Password is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['confirm-password'] ) && $_POST['confirm-password'] != $_POST['password'] ) {
		$validation_errors->add( 'last-name_error', __( 'Passwords do not match.', 'woocommerce' ) );
	}
	if ( isset( $_POST['address-1'] ) && empty( $_POST['address-1'] ) ) {
		$validation_errors->add( 'address-1_error', __( 'Address Line 1 is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['suburb'] ) && empty( $_POST['suburb'] ) ) {
		$validation_errors->add( 'suburb_error', __( 'Suburb is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['postcode'] ) && empty( $_POST['postcode'] ) ) {
		$validation_errors->add( 'postcode_error', __( 'Postcode is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['phone'] ) && empty( $_POST['phone'] ) ) {
		$validation_errors->add( 'phone_error', __( 'Contact Phone is required!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['account-type'] ) && empty( $_POST['account-type'] ) ) {
		if($_POST['account-type'] === 'schl'){
			if ( isset( $_POST['school'] ) && empty( $_POST['school'] ) ) {
				$validation_errors->add( 'school_error', __( 'School Name is required!.', 'woocommerce' ) );
			}
			if ( isset( $_POST['school-id'] ) && empty( $_POST['school-id'] ) ) {
				$validation_errors->add( 'school-id_error', __( 'School ID is required!.', 'woocommerce' ) );
			}

		}
	}
	
	
	return $validation_errors;
}

add_action( 'woocommerce_created_customer', 'weka_save_extra_register_fields' );
function weka_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['phone'] ) ) {
		// Phone input filed which is used in WooCommerce
		update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['phone'] ) );
	}
	if ( isset( $_POST['first-name'] ) ) {
		//First name field which is by default
		update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['first-name'] ) );
		// First name field which is used in WooCommerce
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['first-name'] ) );
	}
	if ( isset( $_POST['last-name'] ) ) {
		// Last name field which is by default
		update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['last-name'] ) );
		// Last name field which is used in WooCommerce
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['last-name'] ) );
	}
	if ( isset( $_POST['address-1'] ) ) {
		update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( $_POST['address-1'] ) );
	}
	if ( isset( $_POST['address-2'] ) ) {
		update_user_meta( $customer_id, 'billing_address_2', sanitize_text_field( $_POST['address-2'] ) );
	}
	if ( isset( $_POST['address-3'] ) ) {
		update_user_meta( $customer_id, 'billing_address_3', sanitize_text_field( $_POST['address-3'] ) );
	}
	if ( isset( $_POST['suburb'] ) ) {
		update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['suburb'] ) );
	}
	if ( isset( $_POST['postcode'] ) ) {
		update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['postcode'] ) );
	}
	if ( isset( $_POST['country'] ) ) {
		update_user_meta( $customer_id, 'billing_country', sanitize_text_field( $_POST['country'] ) );
	}
	if ( isset( $_POST['state'] ) ) {
		update_user_meta( $customer_id, 'billing_state', sanitize_text_field( $_POST['state'] ) );
	}
	if ( isset( $_POST['company'] ) ) {
		update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['company'] ) );
	}
	if ( isset( $_POST['abn-acn'] ) ) {
		update_user_meta( $customer_id, 'abn-acn', sanitize_text_field( $_POST['abn-acn'] ) );
	}
	if ( isset( $_POST['account-type'] ) ) {
		global $wpdb;
		update_user_meta( $customer_id, "{$wpdb->prefix}capabilities", [sanitize_text_field( $_POST['account-type'] )=> true] );

		if($_POST['account-type'] == 'schl'){
			if ( isset( $_POST['school'] ) ) {
				update_user_meta( $customer_id, 'school_name', sanitize_text_field( $_POST['school'] ) );
			}
			if ( isset( $_POST['school-id'] ) ) {
				update_user_meta( $customer_id, 'school_id', sanitize_text_field( $_POST['school-id'] ) );
			}
		}
	}

	header("Location: /my-account/");
	exit();

}


function custom_input_field($name, $label, $type='text', $required=false, $placeholder='', $options = ['1' => 'Option 1', '2' => 'Option 2'], $states=false){
	if($states){
		ob_start();?>	
		<div class="custom-form-input-container">
			<label for="input-<?=$name?>"><?=$label?><?=$required?'<span class="required">*</span>':''?></label>
			<select name="<?=$name?>" id="input-<?=$name?>">
			<?php
			foreach($options as $k=>$v):?>
				<optgroup label="<?=$k?>">
					<?php foreach($v as $code=>$name): ?>
						<option value="<?=$code?>"><?=$name?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach;?>
			</select>
		</div>
		<?php echo ob_get_clean();
		return;
	}
	if($type === 'select'){
		ob_start();?>	
		<div class="custom-form-input-container">
			<label for="input-<?=$name?>"><?=$label?><?=$required?'<span class="required">*</span>':''?></label>
			<select name="<?=$name?>" id="input-<?=$name?>">
			<?php
			foreach($options as $k=>$v):?>
				
				<option value="<?=$k?>"<?=$v==='Australia'?'selected':''?>><?=$v?></option>
			<?php endforeach;?>
			</select>
		</div>
		<?php echo ob_get_clean();

	}else{
		ob_start();?>
		<div class="custom-form-input-container">
			<label for="input-<?=$name?>"><?=$label?><?=$required?'<span class="required">*</span>':''?></label>
			<input type="<?=$type?>" name="<?=$name?>" id="input-<?=$name?>" placeholder="<?=$placeholder?>"<?= $required?'required="true"':''?>/>
		</div>
		<?php echo ob_get_clean();
	}
}

add_action('woocommerce_after_customer_login_form', 'weka_add_registration');
function weka_add_registration(){
	ob_start();?>
	<h1>No Account? Register an account with us</h1>
	<a href="/registration/" class="common-button red large"> Register</a>
	<?php echo ob_get_clean();
}

add_filter('woocommerce_account_menu_items', 'remove_my_account_tabs', 999);
function remove_my_account_tabs($items) {
    
    unset($items['downloads']);
	$my_items = array_slice($items,5,6);
	
	$front = array_slice($items, 0, 2);
	$back = array_slice($items,2,5);
	$items = $front + $my_items + $back;

    return $items;
}


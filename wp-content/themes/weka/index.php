<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme
 *
 * @since weka 1.0
 */
get_header();
?>
<script>
	let bannerStart = 2
	let bannerEnd = 4
	function changeBanner($){
		$(".custom-home-banner").css("background-image", "url(<?=get_template_directory_uri()?>/img/banner-"+bannerStart+".jpg)")
		bannerStart = bannerStart >= bannerEnd ? 1 : bannerStart += 1

	}
</script>

<div id="main" class="row">
	<?php
	if(is_front_page()){
		include(get_template_directory().'/templates/homepage.html.php');
	}else{
		?>
		<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
		<?php endwhile; 
	}?>
</div>
<?php get_footer(); ?>

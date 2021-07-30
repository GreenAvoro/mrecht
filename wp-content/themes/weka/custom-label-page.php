<?php
/**
* Template Name: Custom Printed Labels Page
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }


get_header(); ?>
	<div class="container">
		<?php
            if ( function_exists('yoast_breadcrumb') && !is_page( array('homepage') ) ) {
                yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
            }
        ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); $img = get_the_post_thumbnail_url();?>      	
			<div class="row">
				<?php if(is_front_page()) { ?>
					<div class="col-md-12">
						<?php the_content(); ?>
					</div>

				<?php	
				} else {
				?>
					<div class="col-md-12 mainsection">
						<?php the_content(); ?>
					</div>
				<?php
				}
			endwhile; ?></div>
		<?php else : ?>
			<?php get_template_part( 'no-results', 'page' ); ?>
		<?php endif; ?>
	</div>
    <script>
        let imgContainer = document.getElementById("custom-label-form-img")
        imgContainer.innerHTML = '<img style="width: 100%" src="<?=$img?>"/>'
    </script>
<?php  get_footer(); ?>
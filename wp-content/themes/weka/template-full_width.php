<?php
/*
Template Name: Full Width
*/


// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>      	
		<div class="">

                <?php the_content(); ?>
                
			<?php	
		endwhile; ?></div>
	<?php else : ?>
		<?php get_template_part( 'no-results', 'page' ); ?>
	<?php endif; ?>
<?php  get_footer(); ?>
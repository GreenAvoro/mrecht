<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
	<div class="container">
		<?php
            if ( function_exists('yoast_breadcrumb') && !is_page( array('homepage') ) ) {
                yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
            }
        ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>      	
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
<?php  get_footer(); ?>
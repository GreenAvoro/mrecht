<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
<div id="main" class="container">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>      	
		<div class="row">
			<?php if(is_front_page()) { ?>
				<div class="col-md-12">
					<?php the_content(); ?>
				</div>

			<?php	
			} else {
			?>
				<div class="col-md-9 mainsection">
					<?php the_content(); ?>
				</div>
				<div class="col-md-3 sidebar">
					<?php  get_sidebar(); ?>
				</div>
			<?php
			}
		endwhile; ?></div>
	<?php else : ?>
		<?php get_template_part( 'no-results', 'page' ); ?>
	<?php endif; ?>
</div><!-- #content -->	
<?php  get_footer(); ?>
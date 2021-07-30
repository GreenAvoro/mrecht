<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
	<div class="row">
		<div class="col-md-9 mainsection">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>      	
					<?php the_content(); ?>
			<?php
		endwhile; ?>
		</div>
		<div class="col-md-3 sidebar">
			<?php  get_sidebar(); ?>
		</div>		
	</div>
	<?php else : ?>
		<?php get_template_part( 'no-results', 'page' ); ?>
	<?php endif; ?>
<?php  get_footer(); ?>
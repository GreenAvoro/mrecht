<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage weka
 * @since weka 1.0
 */

get_header();
?>
<div id="main" class="row">
	<div class="col-md-12 nf_main">
		<header class="page-header">
			<h1 class="page-title nf_h1">
				<?php _e( 'PAGE NOT FOUND', 'weka' ); ?>
			</h1>
		</header>
		<div class="page-wrapper container">
			<div class="page-content">
				<h2 class="nf_h2">
					<?php _e( 'This is somewhat embarrassing, isn’t it?', 'weka' ); ?>
				</h2>
				<p class="nf_paragraph">
					<?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'weka' ); ?>
				</p>

				<?php get_search_form(); ?>

			</div>
			<!-- .page-content -->
		</div>
		<!-- .page-wrapper -->
	</div>
	<!-- #content -->
</div> <!-- #primary -->

<?php get_footer(); ?>
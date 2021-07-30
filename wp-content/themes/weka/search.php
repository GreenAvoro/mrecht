<?php get_header();?>
	<div class="container">
		<div class="search-results">
		<?php
		$s = get_search_query();
		$args = array(
			's' => $s
		);
		// The Query
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {

			_e( '<div class="col-md-12"><h1 class="main-heading-1__left">Search Results for: ' . get_query_var( 's' ) . '</h1></div>' );
		
			while ( $the_query->have_posts() ) {
		
				$the_query->the_post();
		
				?>
		
				<div class="col-md-12">
		
					<div>
						<br>
						<h3 class="entry-title"><a class="link-text-7__inline" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p> <?php echo wp_trim_excerpt() ?> </p>
						<br>
						<hr>
					</div>
		
		
				</div> <?php
		
			}
		
		} else {
		?>
			<h1>NOTHING FOUND</h1>
			<div class="alert alert-info">
				<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
			</div> 
		<?php
		}
		?>
		</div>
	</div>
<?php get_footer(); ?>
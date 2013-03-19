<?php
/**
 * Template Name: Show Blog Archive
 * Description: A Page Template that displays an archive of show blog posts
 */

get_header(); ?>
		

		<section id="primary">
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php echo get_the_title($_GET['show_id']); ?> Blog Archive</h1>
				</header>
				<!-- this is the important part... be careful when you're changing this -->
				<?php
					//since this is a custom query, we have to do a little trickery to get pagination to work
				$args = array( 
							'post_type' => 'post', 
							'posts_per_page' => 10, 
							'orderby' => 'post_date', 
							'order' => 'desc',
							'meta_query' => array(
												array( 
													'key' => 'post_showblog_id', 
													'value' => $_GET['show_id']
												)
											), 
							'paged' => $paged
						);
				$temp = $wp_query;
				$wp_query = null;
				$wp_query = new WP_Query( $args );
				?>
				<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
					
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
							<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

							<div class="show-date-and author">
								<?php echo the_date(); ?> - 
								Posted by <?php the_author_posts_link(); ?>
							</div>
					</header><!-- .entry-header -->
					
					<div class="entry-summary">
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php endwhile; ?>
				
				<div class="navigation">
				  <div class="alignleft"><?php next_posts_link('&laquo; Older') ?></div>
				  <div class="alignright"><?php previous_posts_link('Newer &raquo;') ?></div>
				</div>
				
				<?php 
					$wp_query = null;
					$wp_query = $temp;
				?>
				<!-- /end important part -->

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>

			</div><!-- #content -->
		</section><!-- #primary -->

<?php get_footer(); ?>
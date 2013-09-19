<?php
/**
 * The Template for displaying all single playlist posts.  Based on TwentyEleven.
 */

get_header(); ?>
		<div id="primary">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<nav id="nav-single">
						<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
						<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', 'twentyeleven' ) ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></span>
					</nav><!-- #nav-single -->

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( 'post' == get_post_type() ) : ?>
						<div class="entry-meta">
							<?php twentyeleven_posted_on(); ?>
						</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->
					
					<div class="entry-content">
					
						<!-- custom show output : This portion can be edited or inserted into your own theme files -->
						<div class="alignleft">
						<h3>Hosted by:</h3>
						<?php 
							$djs = get_post_meta(get_the_ID(), 'show_user_list', true);
							$count = 0;
							
							if($djs) {
								foreach($djs as $dj) {
									$count ++;
									$user_info = get_userdata($dj);
									
									echo '<a href="'.get_author_posts_url($dj).'">'.$user_info->display_name.'</a>';
									
									if( ($count == 1 && count($djs) == 2) || (count($djs) > 2 && $count == count($djs)) ) {
										echo ' and ';
									}
									elseif($count < count($djs) && count($djs) > 2) {
										echo ', ';
									}
									else {
										//do nothing
									}
								}
							}
						?>
						</div>
						
						<div class="station-genres alignright">
							<h3>Genre:</h3>
							<?php
								//use this function instead if you would like the genres to link to an archive page 
								//wp_list_categories( array('taxonomy' => 'genres', 'title_li' => '') ); 
							?>
							<ul>
							<?php 
								$terms = wp_get_post_terms( get_the_ID(), 'genres' );
								foreach($terms as $genre) {
									echo '<li>'.$genre->name.'</li>';
								}
							?>
							</ul>
						</div>
						
						<div style="clear:both;"><hr /></div>
						
						<div class="station-featured-image alignright">
							<?php 
								if(has_post_thumbnail()) { 
									the_post_thumbnail('medium'); 
								}
							?>
							<?php if($show_email = get_post_meta(get_the_ID(), 'show_email', true)): ?>
							<p class="station-dj-email"><a href="mailto:<?php echo $show_email; ?>">Email the DJ</a></p>
							<?php endif; ?>
							
							<?php if($show_link = get_post_meta(get_the_ID(), 'show_link', true)): ?>
							<p class="station-show-link"><a href="<?php echo $show_link; ?>">Show Website</a></p>
							<?php endif; ?>
						</div>
						
						<?php the_content(); ?>
						
						<div class="station-broadcast-file">
							<a href="<?php echo get_post_meta(get_the_ID(), 'show_file', true); ?>">Most recent broadcast</a>
						</div>
						
						<div class="station-show-schedules">
							<h3>Schedule</h3>
								<ul>
								<?php 
									//12-hour time
									$shifts = get_post_meta(get_the_ID(), 'show_sched', true);
									if($shifts) {
										foreach($shifts as $shift) {
											echo '<li>';
											echo $shift['day'].' - '.$shift['start_hour'].':'.$shift['start_min'].' '.$shift['start_meridian'].' to '.$shift['end_hour'].':'.$shift['end_min'].' '.$shift['end_meridian'];
											echo '</li>';
										}
									}
									
									//24-hour time
									/*
									$shifts = get_post_meta(get_the_ID(), 'show_sched', true);
									if($shifts) {
										foreach($shifts as $shift) {
											if($shift['start_hour'] != 12 && $shift['start_meridian'] == 'pm') {
												$shift['start_hour'] = $shift['start_hour'] + 12;
											}
											
											if($shift['end_hour'] != 12 && $shift['end_meridian'] == 'pm') {
												$shift['end_hour'] = $shift['end_hour'] + 12;
											}
											
											echo '<li>';
											echo $shift['day'].' - '.$shift['start_hour'].':'.$shift['start_min'].' to '.$shift['end_hour'].':'.$shift['end_min'];
											echo '</li>';
										}
									}
									*/
								?>
								</ul>
						</div>
						
						<div class="station-show-playlists">
							<h3>Playlists</h3>
							<?php echo do_shortcode('[get-playlists show="'.get_the_ID().'" limit="5"]'); ?>
						</div>
						
						<?php echo myplaylist_get_posts_for_show(get_the_ID(), 'Blog Posts', '10'); ?>
						 
						<!-- /custom show output -->
						
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
					</article>

					<?php //comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
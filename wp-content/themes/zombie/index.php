<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file 
 *
 * Please see /includes/zombie-helpers.php for info on Zombie_Helpers::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Zombie
 * @since 		Zombie 1.0
 */
?>
<?php Zombie_Helpers::get_template_parts( array( 'partials/html-header', 'partials/header' ) ); ?>

<?php if ( have_posts() ): ?>
<h2>Latest Posts</h2>	
<ol>
<?php while ( have_posts() ) : the_post(); ?>
	<li>
		<article>
			<h2><a href="<?php esc_url( the_permalink() ); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<time datetime="<?php the_time( 'Y-m-d' ); ?>" pubdate><?php the_date(); ?> <?php the_time(); ?></time> <?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?>
			<?php the_content(); ?>
		</article>
	</li>
<?php endwhile; ?>
</ol>
<?php else: ?>
<h2>No posts to display</h2>
<?php endif; ?>

<?php Zombie_Helpers::get_template_parts( array( 'partials/footer','partials/html-footer') ); ?>
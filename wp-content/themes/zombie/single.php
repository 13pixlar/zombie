<?php
/**
 * The Template for displaying all single posts
 *
 * Please see /includes/zombie-helpers.php for info on Zombie_Helpers::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Zombie
 * @since 		Zombie 1.0
 */
?>
<?php Zombie_Helpers::get_template_parts( array( 'partials/html-header', 'partials/header' ) ); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<article>

	<h2><?php the_title(); ?></h2>
	<time datetime="<?php the_time( 'Y-m-d' ); ?>" pubdate><?php the_date(); ?> <?php the_time(); ?></time> <?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?>
	<?php the_content(); ?>			

	<?php if ( get_the_author_meta( 'description' ) ) : ?>
	<?php echo get_avatar( get_the_author_meta( 'user_email' ) ); ?>
	<h3>About <?php echo get_the_author() ; ?></h3>
	<?php the_author_meta( 'description' ); ?>
	<?php endif; ?>

	<?php comments_template( '', true ); ?>

</article>
<?php endwhile; ?>

<?php Zombie_Helpers::get_template_parts( array( 'partials/footer','partials/html-footer' ) ); ?>
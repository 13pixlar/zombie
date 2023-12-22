<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
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
<h2><?php the_title(); ?></h2>
<?php the_content(); ?>
<?php comments_template( '', true ); ?>
<?php endwhile; ?>

<?php Zombie_Helpers::get_template_parts( array( 'partials/footer','partials/html-footer' ) ); ?>
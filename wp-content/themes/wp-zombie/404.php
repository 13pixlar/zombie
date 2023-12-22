<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Please see /includes/zombie-helpers.php for info on Zombie_Helpers::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Zombie
 * @since 		Zombie 1.0
 */
?>
<?php Zombie_Helpers::get_template_parts( array( 'partials/html-header', 'partials/header' ) ); ?>

<h2>Page not found</h2>

<?php Zombie_Helpers::get_template_parts( array( 'partials/footer','partials/html-footer' ) ); ?>
<?php

// Theme specific settings
add_theme_support('post-thumbnails');
register_nav_menus(array('primary' => 'Primary Navigation'));

// Actions and Filters
add_action( 'wp_enqueue_scripts', 'Zombie_script_enqueuer' );
add_filter( 'body_class', array( 'Zombie_Helpers', 'add_slug_to_body_class' ) );

// Add scripts via wp_head()
function Zombie_script_enqueuer() {

	// Enqueue concatenated styles
	wp_register_style( 'screen', get_stylesheet_directory_uri().'/style.css', '', '', 'screen' );
    wp_enqueue_style( 'screen' );

    // Enqueue concatenated scripts
   	wp_register_script( 'site', get_template_directory_uri().'/dist/js/site.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'site' );
}

 ?>
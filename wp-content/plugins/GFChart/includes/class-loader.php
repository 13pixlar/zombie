<?php
/*
 * @package   GFChart\GFChart_Loader
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.53
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Loader
 *
 * Adapted from WP Metadata API UI
 *
 * @since  0.53
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Loader {

	private static $_autoload_classes = array(
		'GFChart'                       => 'class-gfchart.php',
		'GFChart_CPT'                   => 'class-cpt.php',
		'GFChart_Data_Retriever'        => 'class-data-retriever.php',
		'GFChart_Addon'                 => 'class-addon.php',
		'GFChart_Shortcode_Adder'       => 'class-shortcode-adder.php',
		'GFChart_Shortcode_Processor'   => 'class-shortcode-processor.php',
		'GFChart_Stats'                 => 'class-stats.php',
		'GFChart_API'                   => 'class-api.php',
		'GFChart_Import_Export'         => 'class-import-export.php',
		'GFChart_Blocks'                => 'class-blocks.php',
		'GFChart_EDD_SL_Plugin_Updater' => 'class-plugin-updater.php'
	);

	static function load() {

		spl_autoload_register( array( __CLASS__, '_autoloader' ) );

	}

	/**
	 * @param string $class_name
	 * @param string $class_filepath
	 *
	 * @return bool Return true if it was registered, false if not.
	 */
	static function register_autoload_class( $class_name, $class_filepath ) {

		if ( ! isset( self::$_autoload_classes[ $class_name ] ) ) {

			self::$_autoload_classes[ $class_name ] = $class_filepath;

			return true;

		}

		return false;

	}

	/**
	 * @param string $class_name
	 */
	static function _autoloader( $class_name ) {

		if ( isset( self::$_autoload_classes[ $class_name ] ) ) {

			$filepath = self::$_autoload_classes[ $class_name ];

			/**
			 * @todo This needs to be made to work for Windows...
			 */
			if ( '/' == $filepath[0] ) {

				require_once( $filepath );

			} else {

				require_once( dirname( __FILE__ ) . "/{$filepath}" );

			}

		}

	}
}
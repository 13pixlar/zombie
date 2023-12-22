<?php
/*
 * @package   GFChart\GFChart
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.53
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart
 *
 * Main plugin class
 *
 * @since  0.53
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart {

	/**
	 * Gravity Forms Add-On object
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @var GFChart_Addon
	 */
	private $addon = null;

	/**
	 * GFChart Data object
	 *
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @var GFChart_Data_Retriever
	 */
	private $data = null;

	/**
	 * Constructor
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function construct() {
	}

	/**
	 * Load WordPress functions
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function run() {

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

	}


	/**
	 * Create GF Add-On and enable stats
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function plugins_loaded() {

		$this->load_textdomain();

		if ( class_exists( 'GFForms' ) ) {

			if ( ! class_exists( 'GFFeedAddOn' ) ) {

				GFForms::include_feed_addon_framework();

			}

			$this->data = new GFChart_Data_Retriever();

			new GFChart_CPT();

			$this->addon = new GFChart_Addon( array(
				                                  'version'                    => GFCHART_CURRENT_VERSION,
				                                  'min_gf_version'             => GFCHART_MIN_GF_VERSION,
				                                  'plugin_slug'                => GFCHART_SLUG,
				                                  'path'                       => plugin_basename( GFCHART_FILE ),
				                                  'full_path'                  => GFCHART_FILE,
				                                  'title'                      => 'GFChart',
				                                  'short_title'                => 'GFChart',
				                                  'url'                        => 'http://gfchart.com',
				                                  'capabilities'               => array(
					                                  'gfchart_plugin_settings',
					                                  'gfchart_list_page',
					                                  'gfchart_uninstall'
				                                  ),
				                                  'capabilities_settings_page' => array( 'gfchart_plugin_settings' ),
				                                  'capabilities_plugin_page'   => array( 'gfchart_list_page' ),
				                                  'capabilities_uninstall'     => array( 'gfchart_uninstall' )
			                                  ) );

			new GFChart_Shortcode_Adder();

			new GFChart_Shortcode_Processor();
			
			new GFChart_Import_Export();

			new GFChart_Blocks();

		} else {

			add_action( 'admin_notices', array( $this, 'wt_gf_error' ) );

		}
	}

	/**
	 * Load language files
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function load_textdomain() {

		$gfchart_lang_dir = dirname( plugin_basename( GFCHART_FILE ) ) . '/languages/';
		$gfchart_lang_dir = apply_filters( 'gfchart_language_dir', $gfchart_lang_dir );

		$locale = apply_filters( 'plugin_locale', get_locale(), 'gfchart' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'gfchart', $locale );

		$mofile_local  = $gfchart_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/gfchart/' . $mofile;

		if ( file_exists( $mofile_global ) ) {

			load_textdomain( 'gfchart', $mofile_global );

		} elseif ( file_exists( $mofile_local ) ) {

			load_textdomain( 'gfchart', $mofile_local );

		} else {

			load_plugin_textdomain( 'gfchart', false, $gfchart_lang_dir );

		}
	}


	/**
	 * Return GF Add-On object
	 *
	 * @since  0.53
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @return GFChart_Addon
	 */
	public function get_addon_object() {

		return $this->addon;

	}

	/**
	 * Return GFChart_Data object
	 *
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @return GFChart_Data_Retriever
	 */
	public function get_data_object() {

		return $this->data;

	}

	public function wt_gf_error() {

		$html = '<div class="error" style="padding: 10px">';
		$html .= 'The "GFChart" plugin is an extension to the "Gravity Forms" plugin. Please install and activate the "Gravity Forms" plugin first.';
		$html .= '</div>';

		echo $html;
	}


}
<?php
/*
* @package   GFChart\GFChart_Blocks
* @copyright 2018-2020 gravity+
* @license   GPL-2.0+
* @since     1.14.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
die;
}

/**
* Class GFChart_Blocks
*
* Main plugin class
*
* @since  1.14.0
*
* @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
*/
class GFChart_Blocks {

	/**
	 * GFChart_Blocks constructor.
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function __construct() {

		require_once( GFCHART_PATH . 'includes/blocks/chart/class-block-chart.php' );

		new GFChart_Block_Chart();
	}

}
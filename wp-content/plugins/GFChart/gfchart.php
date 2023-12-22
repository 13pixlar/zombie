<?php
/**
 * @wordpress-plugin
 * Plugin Name: GFChart
 * Plugin URI: http://gfchart.com/
 * Description: Easily chart and count information captured via Gravity Forms
 * Version: 2.2.0.RC1
 * Author: Mensard, with gravity+
 * Author URI: mensard.com
 * Text Domain: gfchart
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package   GFChart
 * @version   2.2.0
 * @author    gravity+ <support@gravityplus.pro>
 * @license   GPL-2.0+
 * @link      https://gravityplus.pro
 * @copyright 2014-2021 gravity+
 *
 * last updated: April 20, 2021
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GFCHART_CURRENT_VERSION', '2.2.0.RC1' );

/**
 * Minimum Gravity Forms version allowed
 *
 * @since  1.0.0
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
define( 'GFCHART_MIN_GF_VERSION', '2.4' );

define( 'GFCHART_FILE', __FILE__ );

define( 'GFCHART_PATH', plugin_dir_path( __FILE__ ) );

define( 'GFCHART_URL', plugin_dir_url( __FILE__ ) );

define( 'GFCHART_SLUG', plugin_basename( dirname( __FILE__ ) ) );

define( 'GFCHART_EDD_STORE_URL', 'http://gfchart.com/' );

define( 'GFCHART_EDD_ITEM_NAME', 'GFChart' );

define( 'GFCHART_EDD_ITEM_ID', 3184 );

//Load all of the necessary class files for the plugin
require_once( 'includes/class-loader.php' );
GFChart_Loader::load();

$gfp_gfchart = new GFChart();
$gfp_gfchart->run();
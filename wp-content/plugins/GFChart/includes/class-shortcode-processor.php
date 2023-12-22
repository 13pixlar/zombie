<?php
/*
 * @package   GFChart\GFChart_Shortcode_Processor
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_Shortcode_Processor
 *
 * Processes GFChart shortcode
 *
 * @since  0.6
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Shortcode_Processor {

	/**
	 * GFChart_Shortcode_Processor constructor.
	 *
	 * @since
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * @since
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function init() {

		add_filter( 'widget_text', 'do_shortcode', 10 );

		add_shortcode( 'gfchart', array( $this, 'gfchart' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );


	}

	/**
	 * @since
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function wp_enqueue_scripts() {

		wp_register_style( 'gfchart-style', GFCHART_URL .  'css/style.css' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'jquery' );

		wp_register_script( 'gfchart_google_jsapi', 'https://www.google.com/jsapi', array( 'jquery' ), GFCHART_CURRENT_VERSION, true );

		wp_register_script( 'gfchart-draw', GFCHART_URL . "js/gfchart-draw{$suffix}.js", array(
			'jquery',
			'gfchart_google_jsapi'
		), GFCHART_CURRENT_VERSION, true );

	}


	/**
	 * Display chart
	 *
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $args
	 *
	 * @return int
	 */
	public function gfchart( $args ) {

		global $gfp_gfchart;

		if ( IS_ADMIN ){

			$this->wp_enqueue_scripts();
			
		}

		wp_enqueue_script( 'gfchart_google_jsapi' );

		wp_enqueue_script( 'gfchart-draw' );
		
		wp_enqueue_style( 'gfchart-style' );

		do_action( 'gfchart_shortcode_scripts' );

		$debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

		$style = '';

		$params = shortcode_atts( array( 'id' => 0, 'goal' => 0, 'filter_field' => 0, 'filter_condition' => 'is', 'filter_value' => '', 'calc_filter_field' => 0, 'calc_filter_condition' => 'is', 'calc_filter_value' => '' ), $args );

		if ( empty( $params[ 'id' ] ) ) {

			$chart_object_info = array(
				'chart_type' => 'none',
				'id'         => 0,
				'data'       => __( 'No chart ID given', 'gfchart' ),
				'options'    => '',
				'location'    => '',
				'debug'      => $debug
			);

		} else {

			$gfp_gfchart->shortcode_args = $params;

			$post_id = $params[ 'id' ];

			$source_form_id = get_post_meta( $post_id, 'source_form', true );

			$gfchart_config = apply_filters( 'gfchart_config', get_post_meta( $post_id, '_gfchart_config', true ), $post_id, $source_form_id );

			$override_values = ( empty( $params['filter_field'] ) || empty( $params['filter_condition'] ) ) ? array() :
				array( 'filter_field' => $params['filter_field'],
				       'filter_condition' => $params['filter_condition'],
				       'filter_value' => $params['filter_value']
				);

			if ( ! empty( $override_values['filter_condition'] ) ) {

				switch($override_values['filter_condition'] ) {

					case 'greaterthan':

						$override_values['filter_condition'] = '>';

						break;

					case 'lessthan':

						$override_values['filter_condition'] = '<';

						break;
				}
			}

			$filters = GFChart_API::get_gfchart_filter_vars( $post_id, true, $override_values );

			$data = array();

			$chart_type_settings = GFChart_API::get_chart_type_settings( $gfchart_config['chart_type'] );


			if ( is_callable( $chart_type_settings['data_retriever'] ) ) {

				$data = call_user_func( $chart_type_settings['data_retriever'], $gfchart_config, $source_form_id, $filters );

			}

			if ( empty( $data ) || ! is_array( $data ) ) {

				$chart_object_info = array(
					'chart_type' => ucfirst( $gfchart_config[ 'chart_type' ] ),
					'id'         => $post_id,
					'data'       => __( 'No data found', 'gfchart' ),
					'options'    => '',
					'location'    => '',
					'debug'      => $debug
				);

			} else {

				if ( is_callable( $chart_type_settings['data_formatter'] ) ) {

					$data = call_user_func( $chart_type_settings['data_formatter'], $data, $gfchart_config );

				}

				$chart_options = GFChart_API::format_chart_options( $gfchart_config );

				$chart_object_info = array(
					'chart_type' => ucfirst( $gfchart_config[ 'chart_type' ] ),
					'id'         => $post_id,
					'data'       => $data,
					'options'    => $chart_options,
					'location'    => '',
					'debug'      => $debug
				);

				if ( ! empty( $gfchart_config['responsive'] ) ) {

					$style = "style='display:inline-block;width:100%;height:100%;'";

				}

			}

		}

		$formatted_chart_object_info = GFChart_API::format_chart_object_info_for_js( $chart_object_info );

		$script = 'gfchart_js.charts.push(' . wp_json_encode( $formatted_chart_object_info ) . ');';

		$gfchart = "<span id='gfchart-{$chart_object_info['chart_type']}_chart_{$chart_object_info['id']}' class='gfchart-{$chart_object_info['chart_type']}_chart' {$style}></span><script type='text/javascript'>jQuery(document).on('gfchart_object_declared', function(){{$script}});</script>";


		return $gfchart;

	}

}
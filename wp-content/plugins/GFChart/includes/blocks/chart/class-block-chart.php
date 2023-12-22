<?php
/*
* @package   GFChart\GFChart_Block_Chart
* @copyright 2018-2020 gravity+
* @license   GPL-2.0+
* @since     1.14.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
die;
}

/**
* Class GFChart_Block_Chart
*
* Main plugin class
*
* @since  1.14.0
*
* @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
*/
class GFChart_Block_Chart {

	/**
	 * GFChart_Block_Chart constructor.
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * Register chart block
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function init(){

		if ( function_exists( 'register_block_type' ) ) {

			register_block_type(
				'gfchart/chart',
				array(
					'editor_script' => 'gfchart-block-chart-editor-js',
					'render_callback' => array( $this, 'render_block' ),
					'attributes'      => array(
						'chart_id'      => array( 'type' => 'string' )
					)

				)
			);

		}

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

	}

	/**
	 * Enqueue block editor JS
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function enqueue_block_editor_assets(){

		wp_enqueue_script(
			'gfchart-block-chart-editor-js',
			GFCHART_URL . 'includes/blocks/chart/editor-chart.js',
			array(
				'wp-blocks',
				'wp-i18n',
				'wp-element',
			)
		);

		wp_localize_script( 'gfchart-block-chart-editor-js', 'gfchart_block', array(
			'charts'     => $this->get_chart_options(),
			'icon'       => GFCHART_URL . '/images/icon.svg'
		) );

	}

	/**
	 * Get a list of all charts
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @return array
	 */
	private function get_chart_options() {

		$charts = get_posts( array( 'numberposts' => -1, 'post_type' => 'gfchart' ) );

		$charts_options = array(
			array(
				'label' => esc_html__( 'Select a Chart/Calculation', 'gfchart' ),
				'value' => '',
			)
		);

		foreach ( $charts as $chart ) {

			$charts_options[] = array( 'label' => esc_html( $chart->post_title ),
			                           'value' => absint( $chart->ID ) );

		}


		return $charts_options;
	}

	/**
	 * Render chart
	 *
	 * @since  1.14.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param array $attributes
	 *
	 * @return string|void
	 */
	public function render_block( $attributes = array() ){

		global $gfp_gfchart;


		$chart_id     = rgar( $attributes, 'chart_id' ) ? $attributes['chart_id'] : false;

		//TODO handle other shortcode attributes, like calc_filter

		//TODO do an in-editor preview?

		if ( is_array( $chart_id ) ) {

			return;

		}

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'gfchart_google_jsapi' );

		wp_enqueue_script( 'gfchart-draw' );
		
		wp_enqueue_style( 'gfchart-style' );

		do_action( 'gfchart_shortcode_scripts' );

		$debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

		$style = '';

		//$params = shortcode_atts( array( 'id' => 0, 'goal' => 0, 'calc_filter_field' => 0, 'calc_filter_condition' => 'is', 'calc_filter_value' => '' ), $args );

		if ( empty( $chart_id ) ) {

			$chart_object_info = array(
				'chart_type' => 'none',
				'id'         => 0,
				'data'       => __( 'No chart ID given', 'gfchart' ),
				'options'    => '',
				'location'    => '',
				'debug'      => $debug
			);

		} else {

			$gfp_gfchart->shortcode_args = $attributes;

			$post_id = $chart_id;

			$source_form_id = get_post_meta( $post_id, 'source_form', true );

			$gfchart_config = apply_filters( 'gfchart_config', get_post_meta( $post_id, '_gfchart_config', true ), $post_id, $source_form_id );

			$filters = GFChart_API::get_gfchart_filter_vars( $post_id, true );

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

	public function preview_block( $attributes = array() ) {}
}
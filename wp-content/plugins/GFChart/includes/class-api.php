<?php
/*
 * @package   GFChart\GFChart_API
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_API
 *
 * Useful functions
 *
 * @since  0.6
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_API {

	/**
	 * @param $chart_type
	 *
	 * @return array
	 * @since  1.9.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function get_chart_type_settings( $chart_type ) {

		$chart_type_settings = array();


		$chart_types = GFChart_CPT::get_chart_types();

		foreach ( $chart_types as $type ) {

			if ( $type['id'] == $chart_type ) {

				$chart_type_settings = $type;

				break;

			}

		}


		return $chart_type_settings;

	}


	/**
	 * Get user condition for database query
	 *
	 * @param array $gfchart_config
	 * @param array $search_criteria
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  2.0.0 add GF_Query $search_criteria
	 * @since  0.9
	 */
	public static function get_user_condition( $gfchart_config, $search_criteria ) {

		if ( '1' == rgar( $gfchart_config, 'user_only' ) ) {

			$search_criteria['created_by'] = get_current_user_id();
		}


		return $search_criteria;
	}

	/**
	 * Get payment status condition for database query
	 *
	 * @param array      $gfchart_config
	 * @param array $search_criteria
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  2.0.0  add GF_Query $search_criteria
	 * @since  1.1.0
	 */
	public static function get_payment_status_condition( $gfchart_config, $search_criteria ) {

		if ( ! empty( $gfchart_config['payment_status'] ) ) {

			$search_criteria['payment_status'] = $gfchart_config['payment_status'];

		}

		return $search_criteria;
	}

	/**
	 * Get date condition for results query
	 *
	 * @param $gfchart_config
	 *
	 * @return array
	 *
	 * @author   Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since    2.0.0 add $search_criteria and use GF_Query
	 * @since    0.9
	 *
	 */
	public static function get_date_condition( $gfchart_config, $search_criteria ) {

		if ( ! empty( $gfchart_config['date_filter_start'] ) ) {

			$search_criteria['start_date'] = $gfchart_config['date_filter_start'];

		}

		if ( ! empty( $gfchart_config['date_filter_end'] ) ) {

			$search_criteria['end_date'] = $gfchart_config['date_filter_end'];
		}


		return $search_criteria;
	}


	/**
	 * Get field filters formatted for GF_Query
	 *
	 * @since 2.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $filters
	 * @param $search_criteria
	 *
	 * @return mixed
	 */
	public static function get_query_field_filters( $filters, $search_criteria ) {

		if ( ! empty( $filters['filters'] ) ) {

			foreach ( $filters['filters'] as $filter ) {

				if ( in_array( $filter['field'], array( 'created_by_user_role' ) ) ) {

					$search_criteria['created_by_user_role'] = array( 'operator' => $filter['operator'], 'value' => $filter['value']);


					continue;
				}

				if ( 'created_by' == $filter['field'] && ! empty( $search_criteria['created_by'] ) ) {

					continue;
				}

				$search_criteria['field_filters'][] = array( 'key'      => $filter['field'],
				                                             'operator' => $filter['operator'],
				                                             'value'    => $filter['value']
				);

			}

			if ( ! empty( $search_criteria['field_filters'] ) ) {

				$search_criteria['field_filters']['mode'] = $filters['mode'];

			}

		}

		return $search_criteria;
	}

	/**
	 * @param string    $date
	 *
	 * @param bool      $start
	 *
	 * @return string
	 * @author   Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @see      RGFormsModel::get_date_range_where
	 *
	 * @since    1.5.0
	 */
	public static function format_date_for_search( $date, $start = true ) {

		try {
			$datetime_obj = new DateTime( $date );

			$datetime_str = $datetime_obj->format( 'Y-m-d H:i:s' );

			$date_str = $datetime_obj->format( 'Y-m-d' );

			if ( $datetime_str == $date_str . ' 00:00:00' ) {

				$date_str = $date_str . ( $start ? ' 00:00:00' : ' 23:59:59' );

			} else {

				$date_str = $datetime_obj->format( 'Y-m-d H:i:s' );

			}


			$formatted_date = get_gmt_from_date( $date_str );

		}
		catch( Exception $e ) {

			$formatted_date = '';
		}


		return $formatted_date;
	}

	/**
	 * Format pie chart data for Google Charts rendering
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function format_pie_chart_data( $data, $chart_config = array() ) {

		$pie_chart_data['cols'] = array( array( 'type' => 'string' ), array( 'type' => 'number' ) );

		foreach ( $data as $label => $number ) {

			$pie_chart_data['rows'][]['c'] = array( array( 'v' => $label ), array( 'v' => $number ) );

		}

		return $pie_chart_data;
	}

	/**
	 * Format chart options for Google Charts rendering
	 *
	 * @param $chart_config
	 *
	 * @return array|mixed
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function format_chart_options( $chart_config ) {

		$chart_options = array(
			'title'  => empty( $chart_config['title'] ) ? '' : $chart_config['title'],
			'width'  => empty( $chart_config['width'] ) ? 400 : $chart_config['width'],
			'height' => empty( $chart_config['height'] ) ? 300 : $chart_config['height'],
		);

		$chart_type_settings = GFChart_API::get_chart_type_settings( $chart_config['chart_type'] );


		if ( is_callable( $chart_type_settings['format_chart_options'] ) ) {

			$chart_options = call_user_func( $chart_type_settings['format_chart_options'], $chart_config, $chart_options );

		}

		if ( ! empty( $chart_config['responsive'] ) ) {

			$chart_options['width'] = "100%";

			$chart_options['responsive'] = true;

		} else {

			$chart_options['responsive'] = false;

		}


		return apply_filters( 'gfchart_api_format_chart_options', $chart_options, $chart_config );
	}

	/**
	 * Format pie chart options for Google Charts rendering
	 *
	 * @param $chart_config
	 * @param $default_options
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.7
	 */
	public static function format_pie_chart_options( $chart_config, $default_options ) {

		if ( '3D' == $chart_config['style'] ) {

			$default_options['is3D'] = true;

		} else if ( 'donut' == $chart_config['style'] ) {

			$default_options['pieHole'] = 0.4;

		}

		$default_options['legend']['position'] = $chart_config['legend'];

		$additional_options = json_decode( str_replace( array(
			"\r",
			"\n"
		), '', $chart_config['additional_code'] ), true );

		$chart_options = wp_parse_args( $additional_options, $default_options );

		return $chart_options;
	}

	/**
	 * Format bar chart options for Google Charts rendering
	 *
	 * @param $chart_config
	 * @param $default_options
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.7
	 *
	 */
	public static function format_bar_chart_options( $chart_config, $default_options ) {

		$default_options['bars'] = $chart_config['orientation'];

		$default_options['legend']['position'] = $chart_config['legend'];

		$default_options['hAxis'] = array( 'title' => $chart_config['xaxis-label'] );

		if ( ! empty( $chart_config['show_zero_values'] ) ) {

			$default_options['hAxis']['showTextEvery'] = '1';

		}

		$default_options['vAxis'] = array( 'title' => $chart_config['yaxis-label'] );

		if ( ! empty( $chart_config['xaxis-segment-display'] ) ) {

			switch ( $chart_config['xaxis-segment-display'] ) {

				case 'stackabsolute':

					$default_options['isStacked'] = 'true';

					break;

				case 'stackpercent':

					$default_options['isStacked'] = 'percent';

					break;

			}

		}

		$additional_options = json_decode( str_replace( array(
			"\r",
			"\n"
		), '', $chart_config['additional_code'] ), true );

		$chart_options = wp_parse_args( $additional_options, $default_options );

		return $chart_options;
	}

	/**
	 * Format bar chart data for Google Charts rendering
	 *
	 * @param $data
	 * @param $addl_segment
	 *
	 * @return mixed
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.7
	 *
	 */
	public static function format_bar_chart_data( $data, $chart_config = array() ) {

		if ( empty( $data['segments'] ) ) {

			$bar_chart_data['cols'] = array( array( 'type' => 'string' ), array( 'type' => 'number' ) );

			foreach ( $data['results'] as $label => $number ) {

				$bar_chart_data['rows'][]['c'] = array( array( 'v' => $label ), array( 'v' => $number ) );

			}

		} else {

			$bar_chart_data['cols'] = array(

				array( 'type' => 'string' )

			);

			for ( $i = 0; $i < count( $data['segments'] ); $i ++ ) {

				$bar_chart_data['cols'][] = array( 'type' => 'number', 'label' => $data['segments'][ $i ] );

			}

			$row = array();

			foreach ( $data['results'] as $label => $row_data ) {

				$row[] = array( 'v' => $label );

				foreach ( $data['segments'] as $segment ) {

					$row[] = array( 'v' => array_key_exists( $segment, $row_data ) ? $row_data[ $segment ] : '0' );

				}

				$bar_chart_data['rows'][]['c'] = $row;

				unset( $row );

			}

		}


		return apply_filters( 'gfchart_api_format_bar_chart_data', $bar_chart_data, $chart_config );

	}

	/**
	 * Format calc chart options for chart rendering
	 *
	 * @param $chart_config
	 * @param $default_options
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.8
	 *
	 */
	public static function format_calc_chart_options( $chart_config, $default_options ) {

		return array();

	}

	/**
	 * Format calc chart data for Google Charts rendering
	 *
	 * @param $data
	 *
	 * @param $chart_config
	 *
	 * @return mixed
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.7.0
	 *
	 */
	public static function format_calc_chart_data( $data, $chart_config ) {

		$number_format = empty( $chart_config['number_format'] ) ? 'decimal_dot' : $chart_config['number_format'];

		$rounding = ! isset( $chart_config['number_rounding'] ) ? 'norounding' : $chart_config['number_rounding'];

		$number = is_array( $data ) ? $data[0] : $data;

		$calc_chart_data = GFCommon::format_number( GFCommon::round_number( $number, $rounding ), $number_format, '', 'true' );


		return array( $calc_chart_data );
	}

	/**
	 * Format progressbar chart options for chart rendering
	 *
	 * @param $chart_config
	 * @param $default_options
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.0.0
	 *
	 */
	public static function format_progressbar_chart_options( $chart_config, $default_options ) {

		global $gfp_gfchart;

		$goal_display_format = empty( $chart_config['goal_display_format'] ) ? 'decimal_dot' : $chart_config['goal_display_format'];

		$goal = empty( $gfp_gfchart->shortcode_args['goal'] ) ? GFCommon::clean_number( $chart_config['goal'] ) : GFCommon::clean_number( $gfp_gfchart->shortcode_args['goal'] );

		if ( 'currency' == $goal_display_format ) {

			if ( empty( $currency ) ) {
				$currency = GFCommon::get_currency();
			}

			$currency       = new RGCurrency( $currency );
			$formatted_goal = $currency->to_money( $goal, true );

		} else {

			$formatted_goal = GFCommon::format_number( $goal, $goal_display_format, '', 'true' );

		}


		return array( 'format' => $goal_display_format, 'formatted_goal' => $formatted_goal );

	}

	/**
	 * Get field choices
	 *
	 * @param $form_id
	 * @param $field_id
	 *
	 * @return array
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.1.0
	 *
	 */
	public static function get_field_choices( $form_id, $field_id ) {

		$field_choices = array();

		$field = GFChart_API::get_field( GFAPI::get_form( $form_id ), $field_id );

		if ( ! empty( $field ) && ! empty( $field['choices'] ) ) {

			$field_choices = $field['choices'];

		}

		return $field_choices;
	}

	/**
	 * Get field
	 *
	 * @param $form
	 * @param $field_id
	 *
	 * @return array | bool
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.53
	 *
	 */
	public static function get_field( $form, $field_id ) {

		$field = false;

		if ( is_array( $form['fields'] ) ) {

			foreach ( $form['fields'] as $form_field ) {

				if ( $form_field['id'] == $field_id ) {

					$field = $form_field;

					break;

				}

			}
		}

		return $field;
	}

	/**
	 * @param $form_id
	 * @param $field_id
	 *
	 * @return bool
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since
	 *
	 */
	public static function is_multi_field( $form_id, $field_id ) {

		global $wpdb;

		$multi = false;
//TODO table name below needs to be updates for GF2.3
		$sql       = $wpdb->prepare( "SELECT display_meta FROM {$wpdb->prefix}rg_form_meta WHERE form_id = %d", $form_id );
		$form_meta = json_decode( $wpdb->get_var( $sql ) );

		if ( ! empty( $form_meta ) && is_object( $form_meta ) ) {

			if ( isset( $form_meta->fields ) && ! empty( $form_meta->fields ) ) {

				if ( is_array( $form_meta->fields ) ) {

					foreach ( $form_meta->fields as $field ) {

						if ( is_object( $field ) ) {

							if ( $field->id == $field_id ) {

								if ( ( strpos( $field->type, "checkbox" ) !== false ) || strpos( $field->type, "multiselect" ) !== false ) {

									$multi = true;

									break;
								}

							}

						}

					}

				}

			}

		}

		return $multi;
	}

	/**
	 * Get filters for chart
	 *
	 * @param       $post_id
	 * @param bool  $admin_formatting
	 * @param array $override_values
	 *
	 * @return array|bool|mixed
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function get_gfchart_filter_vars( $post_id, $admin_formatting = false, $override_values = array() ) {

		if ( 0 == count( $override_values ) ) {

			$init_filter_vars = get_post_meta( $post_id, '_gfchart_filters', true );

			if ( empty( $init_filter_vars ) ) {

				return false;

			}

			if ( $admin_formatting && array_key_exists( 'mode', $init_filter_vars ) ) {

				$mode = $init_filter_vars['mode'];

				unset( $init_filter_vars['mode'] );

				$init_filter_vars = array(
					'filters' => $init_filter_vars,
					'mode'    => $mode
				);

				foreach ( $init_filter_vars['filters'] as &$filter ) {

					$filter['field'] = $filter['key'];

				}

			}

		} else {

			$init_filter_vars = array(

				'filters' => array(

					array(
						'key'      => $override_values['filter_field'],
						'field'    => $override_values['filter_field'],
						'operator' => $override_values['filter_condition'],
						'value'    => $override_values['filter_value']
					)

				),

				'mode' => 'all'

			);

		}


		return apply_filters( 'gfchart_get_filter_vars', $init_filter_vars, $post_id, $admin_formatting );

	}

	/**
	 * Get list of charts
	 *
	 * @param int    $form_id form ID
	 * @param string $type    chart type
	 *
	 * @return array list of charts with post ID, config, type, filters
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  1.3.0
	 *
	 */
	public static function get_charts( $form_id = 0, $type = '' ) {

		$charts = $meta_query = array();

		if ( ! empty( $form_id ) ) {

			$meta_query[] = array(
				'key'   => 'source_form',
				'value' => $form_id
			);

		}

		if ( ! empty( $type ) ) {

			$meta_query[] = array(
				'key'   => '_gfchart_type',
				'value' => $type
			);

		}

		$charts = get_posts( array( 'post_type' => 'gfchart', 'meta_query' => $meta_query, 'posts_per_page' => - 1 ) );

		foreach ( $charts as &$chart ) {

			$meta = get_post_custom( $chart->ID );

			foreach ( $meta as $meta_key => $meta_value ) {

				$meta[ $meta_key ] = maybe_unserialize( $meta_value[0] );

			}

			$chart->meta = $meta;

		}


		return $charts;
	}

	/**
	 *Taken from WP_Scripts->localize
	 *
	 * @param array $chart_object_info
	 *
	 * @return mixed
	 * @since  0.6
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function format_chart_object_info_for_js( $chart_object_info ) {

		if ( is_array( $chart_object_info ) ) {

			foreach ( $chart_object_info as $key => $value ) {

				if ( ! is_scalar( $value ) ) {

					continue;

				}

				$chart_object_info[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );

			}
		}

		return $chart_object_info;
	}

	/**
	 * Get REST API field value
	 *
	 * @param $object
	 * @param $field_name
	 * @param $request
	 *
	 * @return mixed
	 * @since  1.7.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public static function get_rest_field( $object, $field_name, $request ) {

		return get_post_meta( $object['id'], $field_name, true );

	}

	/**
	 * @param int|array $form_ids
	 * @param array     $search_criteria
	 * @param null      $sorting
	 * @param null      $paging
	 * @param null      $total_count
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 * @see   GFAPI::get_entries()
	 *
	 */
	public static function get_entries( $form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null ) {

		if ( empty( $sorting ) ) {

			$sorting = array( 'key' => 'id', 'direction' => 'DESC', 'is_numeric' => true );

		}

		$q = new GF_Query( $form_ids, $search_criteria, $sorting, $paging );

		$q = apply_filters( 'gfchart_api_get_entries_query', $q, $search_criteria );


		$entries = $q->get();

		$total_count = $q->total_found;

		$page = 0;

		while ( count( $entries ) < $total_count ) {

			$page ++;

			$q->offset( $page * ( isset( $paging['page_size'] ) ? $paging['page_size'] : 20 ) );

			$more_entries = $q->get();

			$entries = array_merge( $entries, $more_entries );

		}


		return $entries;
	}

}
<?php
/* @package   GFChart\GFChart_Data_Retriever
 * @copyright 2015-2020 gravity+
 * @license   GPL-2.0+
 * @since     0.7
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_Data_Retriever
 *
 * Retrieves data for chart render
 *
 * @since  0.7
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Data_Retriever {

	/**
	 *
	 * @param $gfchart_config
	 * @param $source_form_id
	 * @param $filters
	 *
	 * @return array|string
	 *
	 * @since  2.0.0 use GF_Query
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function get_pie_chart_data( $gfchart_config, $source_form_id, $filters ) {

		$source_field_id = $gfchart_config['source-field'];

		$data = $filtered_results = array();


		$results = $this->build_and_execute_gf_query( $gfchart_config, $source_form_id, $filters );


		if ( empty( $results ) ) {

			return __( 'No data.' );

		}

		$form = GFAPI::get_form( $source_form_id );

		$source_field = RGFormsModel::get_field( $form, $source_field_id );

		/**
		 * @internal checkboxes are only stored with their input, e.g. 1.1, 1.2
		 */
		if ( ( ( 'checkbox' == $source_field->type ) || ( 'checkbox' == $source_field->inputType ) ) && ( false === strpos( $source_field_id, '.' ) ) ) {

			$field_input_count = count( $source_field->inputs );
		}

		foreach ( $results as $result ) {

			if ( empty( $field_input_count ) ) {

				if ( empty( $result[ $source_field_id ] ) ) { continue; }

					$actual_value = ( false !== strpos( $result[ $source_field_id ], '|' ) ) ? strstr( $result[ $source_field_id ], '|', true ) : $result[ $source_field_id ];

					$filtered_results[] = wp_specialchars_decode( $actual_value, ENT_QUOTES );

			}
			else {

				for ( $i = 1; $i <= $field_input_count; $i ++ ) {

					if ( empty( $result["{$source_field_id}.{$i}"] ) ) {

						continue;
					}

					$actual_value = ( false !== strpos( $result[ "{$source_field_id}.{$i}" ], '|' ) ) ? strstr( $result[ "{$source_field_id}.{$i}" ], '|', true ) : $result[ "{$source_field_id}.{$i}" ];

					$filtered_results[] = wp_specialchars_decode( $actual_value, ENT_QUOTES );

				}

			}

		}

		foreach ( $filtered_results as $filtered_result ) {

			$data[ $filtered_result ] = ( array_key_exists( $filtered_result, $data ) ) ? $data[ $filtered_result ] + 1 : 1;

		}


		$data = apply_filters( 'gfchart_data_retriever_pre_convert_survey_field_data', $data, $gfchart_config, $source_form_id ); //TODO test attached filters

		$data = $this->convert_survey_field_data( $source_field_id, $source_form_id, $data, 'pie' );


		return apply_filters( 'gfchart_data_retriever_get_pie_chart_data', $data, $gfchart_config, $source_form_id );

	}

	/**
	 * @param $gfchart_config
	 * @param $source_form_id
	 * @param $filters
	 *
	 * @return array|string
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function get_bar_chart_data( $gfchart_config, $source_form_id, $filters ) {

		$source_field_id = $gfchart_config['xaxis-main-field'];

		if ( empty( $source_field_id ) ) {

			return __( 'Error obtaining data.', 'gfchart' );

		}


		$data = $filtered_results = array();


		$results = $this->build_and_execute_gf_query( $gfchart_config, $source_form_id, $filters );


		if ( empty( $results ) ) {

			return __( 'No data.', 'gfchart' );

		}

		$form = GFAPI::get_form( $source_form_id );

		$source_field = RGFormsModel::get_field( $form, $source_field_id );

		if ( ( ( 'checkbox' == $source_field->type ) || ( 'checkbox' == $source_field->inputType ) ) && ( false === strpos( $source_field_id, '.' ) ) ) {

			$field_input_count = count( $source_field->inputs );
		}


		$segmented = ! empty( $gfchart_config['xaxis-segment-field'] );

		$segment_field_id = rgar( $gfchart_config, 'xaxis-segment-field', 0 );

		//TODO some of these result values are serialized. Need to run maybe_unserialize?

		if ( $segmented ) {

			//TODO how to handle checkbox fields for segment

			foreach ( $results as $result ) {

				if ( empty( $field_input_count ) ) {

					if ( empty( $result[ $source_field_id ] ) ) {

						continue;
					}

					$data[] = array(
						'entry_id' => $result['id'],
						'main'     => $result[ $source_field_id ],
						'segment'  => rgar( $result, $segment_field_id )
					);


				} else {

						$answer_data = [];

						foreach ($result as $key => $value) {
						
							if(false !== strpos($key,"{$source_field_id}.")){
						
								$answer_data[$key]= $value;
						
							}

						}

						foreach ($answer_data as $field => $value) {
								
							if ( empty( $result["{$field}"] ) ) {
							
								continue;
							
							}

							$data[] = array(
								'entry_id' => $result['id'],
								'main'     => $result["{$field}"],
								'segment'  => rgar( $result, $segment_field_id )
							);

						}

				}

			}

		} else {

				foreach ( $results as $result ) {

					if ( empty( $field_input_count ) ) {

						if ( empty( $result[ $source_field_id ] ) ) {

							continue;
						}

						$data[] = array( 'entry_id' => $result['id'], 'meta_value' => $result[ $source_field_id ] );


					} else {

						$answer_data = [];

						foreach ($result as $key => $value) {
						
							if(false !== strpos($key,"{$source_field_id}.")){
						
								$answer_data[$key]= $value;
						
							}

						}

						foreach ($answer_data as $field => $value) {
								
							if ( empty( $result["{$field}"] ) ) {
							
								continue;
							
							}

							$data[] = array(
								'entry_id'   => $result['id'],
								'meta_value' => $result["{$field}"]
							);

						}

					}

				}
		}


		if ( ! $segmented ) {

			$data = $this->convert_survey_field_data( $source_field_id, $source_form_id, $data, 'bar' );

		}


		if ( ( is_object( $source_field ) && 'survey' !== $source_field->type ) || ( 'rank' !== $source_field->inputType ) ) {

			if ( ! empty( $gfchart_config['xaxis-sum-field'] ) ) {

				$data = $this->add_sum_field_value_to_results( $results, $data, $gfchart_config['xaxis-sum-field'] );

			}

			if ( 'count' == $gfchart_config['yaxis'] ) {

				$data = $this->count_results( $data, $source_field, $segmented );

			} else {

				$data = $this->sum_results( $data, $segmented );

			}

			if ( ! empty( $gfchart_config['show_zero_values'] ) ) {

				$data = $this->add_zero_values( $data, $source_form_id, $source_field_id );

			}

		}

		$sortby    = empty( $gfchart_config['sortby'] ) ? 'label' : $gfchart_config['sortby'];
		$sort_type = empty( $gfchart_config['sort_type'] ) ? 'asc' : $gfchart_config['sort_type'];

		$data = $this->sort_results( $data, $sortby, $sort_type );

		$data = $this->collapse_data( $data, $gfchart_config );


		return apply_filters( 'gfchart_data_retriever_get_bar_chart_data', $data, $gfchart_config, $source_form_id );

	}

	/**
	 * Get calculation chart data from the DB
	 *
	 * @param $gfchart_config
	 * @param $source_form_id
	 * @param $filters
	 *
	 * @return array|null|object|string
	 * @since  0.8
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function get_calc_chart_data( $gfchart_config, $source_form_id, $filters ) {

		$source_field_id = $gfchart_config['source-field'];

		$data = array();


		$results = $this->build_and_execute_gf_query( $gfchart_config, $source_form_id, $filters );


		if ( empty( $results ) || ! is_array( $results ) ) {

			return array( 0 );

		}

		$number_of_items = count( $results );

		if ( 'count' == $gfchart_config['calculation'] ) {

			$data = $number_of_items;

		} else if ( 'count_unique' == $gfchart_config['calculation'] ) {

			$values = array();

			foreach ( $results as $result ) {

				$value = rgar( $result, $source_field_id );

				if ( empty( $value ) ) {

					continue;
				}

				$values[] = $value;

			}


			$data = count( array_unique( $values ) );

		} else {

			$total = 0;


			foreach ( $results as $result ) {

				$value = rgar( $result, $source_field_id );

				if ( false !== strpos( $value, '|' ) ) {

					$exploded_value = explode( '|', $value );

					$total += floatval( $exploded_value[1] );

				} else {

					$total += floatval( $value );

				}

			}

			$data = ( 'avg' == $gfchart_config['calculation'] ) ? $total / $number_of_items : $total;

		}


		return array( $data );

	}

	/**
	 * @param $gfchart_config
	 * @param $source_form_id
	 * @param $filters
	 *
	 * @return array
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function get_progressbar_chart_data( $gfchart_config, $source_form_id, $filters ) {

		global $gfp_gfchart;

		$progress = array( 'total' => 0, 'goal' => 0, 'percent' => 0 );

		$calc_chart_id = $gfchart_config['calc-chart'];

		if ( ! empty( $calc_chart_id ) ) {

			$calc_chart_config = get_post_meta( $calc_chart_id, '_gfchart_config', true );

			$calc_chart_source_form_id = get_post_meta( $calc_chart_id, 'source_form', true );

			if ( ! empty( $gfp_gfchart->shortcode_args['calc_filter_field'] ) && ! empty( $gfp_gfchart->shortcode_args['calc_filter_condition'] ) && ! empty( $gfp_gfchart->shortcode_args['calc_filter_value'] ) ) {

				$calc_chart_filters = array(
					'filters' => array(
						array(
							'key'      => $gfp_gfchart->shortcode_args['calc_filter_field'],
							'field'    => $gfp_gfchart->shortcode_args['calc_filter_field'],
							'operator' => $gfp_gfchart->shortcode_args['calc_filter_condition'],
							'value'    => $gfp_gfchart->shortcode_args['calc_filter_value']
						)
					),
					'mode'    => 'all'
				);
			} else {

				$calc_chart_filters = GFChart_API::get_gfchart_filter_vars( $calc_chart_id, true );

			}

			$calc_chart_data = array();

			$calc_chart_data = call_user_func( array(
				$gfp_gfchart->get_data_object(),
				"get_{$calc_chart_config['chart_type']}_chart_data"
			), $calc_chart_config, $calc_chart_source_form_id, $calc_chart_filters );

			if ( ! empty( $calc_chart_data ) && is_array( $calc_chart_data ) && ! empty( $gfchart_config['goal'] ) ) {

				//$calc_chart_value = GFCommon::clean_number( $calc_chart_data[0] );
				$calc_chart_value = GFCommon::clean_number( round( $calc_chart_data[0], 2 ) );

				$goal = empty( $gfp_gfchart->shortcode_args['goal'] ) ? GFCommon::clean_number( $gfchart_config['goal'] ) : GFCommon::clean_number( $gfp_gfchart->shortcode_args['goal'] );

				$progress = array(
					'total'   => $calc_chart_value,
					'goal'    => $goal,
					'percent' => ( $calc_chart_value <= $goal ) ? floor( ( $calc_chart_value / $goal ) * 100 ) : 100
				);

			}

		}


		return $progress;

	}

	public function build_and_execute_gf_query( $gfchart_config, $source_form_id, $filters ) {

		$search_criteria = array();


		$search_criteria['status'] = 'active';


		$search_criteria = GFChart_API::get_date_condition( $gfchart_config, $search_criteria );

		$search_criteria = GFChart_API::get_user_condition( $gfchart_config, $search_criteria );

		$search_criteria = GFChart_API::get_payment_status_condition( $gfchart_config, $search_criteria );

		$search_criteria = GFChart_API::get_query_field_filters( $filters, $search_criteria );


		add_filter( 'gfchart_api_get_entries_query', $user_condition_callback = function ( $q, $sc ) {

			return $this->add_user_condition_to_query( $q, $sc );
		}, 10, 2 );

		add_filter( 'gfchart_api_get_entries_query', $payment_status_condition_callback = function ( $q, $sc ) {

			return $this->add_payment_status_condition_to_query( $q, $sc );
		}, 10, 2 );


		$results = GFChart_API::get_entries( $source_form_id, $search_criteria, null, array(
			'offset'    => 0,
			'page_size' => 200
		) );

		if ( isset( $user_condition_callback ) ) {

			remove_filter( 'gfchart_api_get_entries_query', $user_condition_callback );

		}

		if ( isset( $payment_status_condition_callback ) ) {

			remove_filter( 'gfchart_api_get_entries_query', $payment_status_condition_callback );

		}


		return $results;

	}

	public function add_user_condition_to_query( $query, $search_criteria ) {

		if ( ! empty( $search_criteria['created_by'] ) ) {

			$user_condition = new GF_Query_Condition(
				new GF_Query_Column( 'created_by' ),
				GF_Query_Condition::EQ,
				new GF_Query_Literal( $search_criteria['created_by'] ) );

			$query_parameters = $query->_introspect();

			$query->where( \GF_Query_Condition::_and( $query_parameters['where'], $user_condition ) );

			return $query;
		}

		if ( ! empty( $search_criteria['created_by_user_role'] ) ) {

			$value = $search_criteria['created_by_user_role']['value'];

			$comparison = ( 'is' == $search_criteria['created_by_user_role']['operator'] ) ? "LIKE '%%\"{$value}\"%%'" : "NOT LIKE '%%\"{$value}\"%%'";

			global $wpdb;

			$subquery = "SELECT user_id from {$wpdb->prefix}usermeta WHERE meta_key='{$wpdb->prefix}capabilities' AND meta_value {$comparison}";

			$results = $wpdb->get_results( $subquery, ARRAY_A );

			if ( ! empty( $results ) ) {

				foreach ( $results as $result ) {

					$user_ids[] = new GF_Query_Literal( $result['user_id'] );
				}


			}

			$user_role_condition = new GF_Query_Condition(
				new GF_Query_Column( 'created_by' ),
				GF_Query_Condition::IN,
				new GF_Query_Series( $user_ids ) );

			$query_parameters = $query->_introspect();

			$query->where( \GF_Query_Condition::_and( $query_parameters['where'], $user_role_condition ) );

			/*$user_role_condition = new GF_Query_Condition(
				new GF_Query_Column( 'created_by' ),
				GF_Query_Condition::IN,
				new GF_Query_Call( 'IN', array($subquery) ) );

			$query_parameters = $query->_introspect();

			$query->where( \GF_Query_Condition::_and( $query_parameters['where'], $user_role_condition ) );*/

			//array('key' => '1', 'operator' => 'not in', value' => array( 'Alex', 'David', 'Dana' );

		}


		return $query;
	}

	public function add_payment_status_condition_to_query( $query, $search_criteria ) {

		if ( ! empty( $search_criteria['payment_status'] ) ) {

			$payment_status_condition = new GF_Query_Condition(
				new GF_Query_Column( 'payment_status' ),
				GF_Query_Condition::EQ,
				new GF_Query_Literal( $search_criteria['payment_status'] ) );

			$query_parameters = $query->_introspect();

			$query->where( \GF_Query_Condition::_and( $query_parameters['where'], $payment_status_condition ) );

		}


		return $query;
	}

	/**
	 * @param $results
	 * @param $sum_field_id
	 *
	 * @return mixed
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since
	 *
	 */
	public function add_sum_field_value_to_results( $raw_db_results, $current_data, $sum_field_id ) {

		foreach ( $current_data as &$data ) {

			foreach ( $raw_db_results as $result ) {

				if ( $result['id'] == $data['entry_id'] ) {

					$data['sum_value'] = rgar( $result, $sum_field_id, 0 );

					break;

				}

			}

		}


		return $current_data;

	}

	/**
	 * @param          $results
	 *
	 * @param GF_Field $field
	 * @param bool     $segmented
	 *
	 * @return array
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function count_results( $results, $field, $segmented = false ) {

		$data = array( 'segments' => array(), 'results' => array() );


		if ( $segmented ) {

			foreach ( $results as $result ) {

				if ( false !== strpos( $result['main'], '|' ) ) {

					$result['main'] = strstr( $result['main'], '|', true );

				}

				if ( false !== strpos( $result['segment'], '|' ) ) {

					$result['segment'] = strstr( $result['segment'], '|', true );

				}

				if ( array_key_exists( $result['main'], $data['results'] ) ) {

					if ( array_key_exists( $result['segment'], $data['results'][ $result['main'] ] ) ) {

						$data['results'][ $result['main'] ][ $result['segment'] ] = $data['results'][ $result['main'] ][ $result['segment'] ] + 1;

					} else {

						$data['results'][ $result['main'] ][ $result['segment'] ] = 1;

					}

				} else {

					$data['results'][ $result['main'] ][ $result['segment'] ] = 1;

				}

				if ( ! in_array( $result['segment'], $data['segments'] ) ) {

					$data['segments'][] = $result['segment'];
				}

			}

		} else {

			foreach ( $results as $result ) {

				if ( false !== strpos( $result[ 'meta_value' ], '|' ) ) {

					$result = strstr( $result[ 'meta_value' ], '|', true );

				} else if ( 'multiselect' == $field->type /*|| '[' == $result[ $field_value_column ][0]*/ ) {

					$multiselect_values = $field->to_array( $result[ 'meta_value' ] );

					if ( empty( $multiselect_values ) ) {

						continue;
					}

					foreach ( $multiselect_values as $multiselect_value ) {

						$multiselect_value = wp_specialchars_decode( $multiselect_value, ENT_QUOTES );

						if ( array_key_exists( $multiselect_value, $data['results'] ) ) {

							$data['results'][ $multiselect_value ] = $data['results'][ $multiselect_value ] + 1;

						} else {

							$data['results'][ $multiselect_value ] = 1;

						}

					}

					unset ( $multiselect_values, $multiselect_value );

					continue;

				} else {

					$result = wp_specialchars_decode( $result[ 'meta_value' ], ENT_QUOTES );

				}

				if ( array_key_exists( $result, $data['results'] ) ) {

					$data['results'][ $result ] = $data['results'][ $result ] + 1;

				} else {

					$data['results'][ $result ] = 1;

				}

			}

		}


		return $data;
	}

	/**
	 * @param $results
	 *
	 * @return array
	 * @since  0.7
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function sum_results( $results, $segmented = false ) {

		$data = array( 'segments' => array(), 'results' => array() );

		if ( $segmented ) {

			foreach ( $results as $result ) {

				if ( false !== strpos( $result['main'], '|' ) ) {

					$result['main'] = explode( '|', $result['main'] );

				} else {

					$result['main'] = array( $result['main'], $result['main'] );
				}

				if ( false !== strpos( $result['segment'], '|' ) ) {

					$result['segment'] = explode( '|', $result['segment'] );

				} else {

					$result['segment'] = array( $result['segment'], $result['segment'] );

				}

				$main_label = $result['main'][0];

				$segment_label = $result['segment'][0];

				$value = array_key_exists( 'sum_value', $result ) ? $result['sum_value'] : $result['main'][1];

				if ( array_key_exists( $main_label, $data['results'] ) ) {

					if ( array_key_exists( $segment_label, $data['results'][ $main_label ] ) ) {

						$data['results'][ $main_label ][ $segment_label ] += $value;

					} else {

						$data['results'][ $main_label ][ $segment_label ] = $value;

					}

				} else {

					$data['results'][ $main_label ][ $segment_label ] = $value;

				}

				if ( ! in_array( $segment_label, $data['segments'] ) ) {

					$data['segments'][] = $segment_label;
				}

				unset( $main_label, $segment_label, $value );

			}


		} else {

			foreach ( $results as $result ) {

				if ( false !== strpos( $result[ 'meta_value' ], '|' ) ) {

					$result_value = explode( '|', $result[ 'meta_value' ] );

					$label = wp_specialchars_decode( $result_value[0], ENT_QUOTES );

					$value = array_key_exists( 'sum_value', $result ) ? $result['sum_value'] : $result_value[1];

					if ( array_key_exists( $label, $data['results'] ) ) {

						$data['results'][ $label ] += $value;

					} else {

						$data['results'][ $label ] = $value;

					}

				} else {

					$label = wp_specialchars_decode( $result[ 'meta_value' ], ENT_QUOTES );

					$value = array_key_exists( 'sum_value', $result ) ? $result['sum_value'] : $result[ 'meta_value' ];


					if ( array_key_exists( $label, $data['results'] ) ) {

						$data['results'][ $label ] += $value;

					} else {

						$data['results'][ $label ] = $value;

					}

				}

				unset( $result_value, $label, $value );

			}

		}


		return $data;
	}

	/**
	 * Add zero values to results
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @since  1.1.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function add_zero_values( $data, $form_id, $source_field_id ) {

		$field_choices = GFChart_API::get_field_choices( $form_id, $source_field_id );

		if ( ! empty( $field_choices ) ) {

			if ( empty( $data['segments'] ) ) {


				foreach ( $field_choices as $choice ) {

					if ( ! array_key_exists( $choice['text'], $data['results'] ) ) {

						$data['results'][ $choice['text'] ] = '0';

					}

				}

			} else {

				foreach ( $field_choices as $choice ) {

					if ( ! array_key_exists( $choice['text'], $data['results'] ) ) {

						$data['results'][ $choice['text'] ] = array();

						foreach ( $data['segments'] as $segment ) {

							$data['results'][ $choice['text'] ][ $segment ] = '0';

						}

					}

				}


			}
		}

		return $data;
	}

	/**
	 * Sort bar chart results
	 *
	 * @param array  $data
	 * @param string $sortby    Whether to sort by label or value
	 * @param string $sort_type ascending or descending order
	 *
	 * @return array
	 * @since  1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	public function sort_results( $data, $sortby, $sort_type ) {

		if ( 'label' == $sortby ) {

			switch ( $sort_type ) {

				case 'asc':

					ksort( $data['results'] );


					break;

				case 'desc':

					krsort( $data['results'] );


					break;

			}

		} else if ( 'value' == $sortby ) {

			$segmented = ! empty( $data['segments'] );

			if ( $segmented ) {

				$main_field_totals = array();

				foreach ( $data['results'] as $main_field => $segment ) {

					$main_field_totals[ $main_field ] = array_sum( $segment );

				}

			}

			switch ( $sort_type ) {

				case 'asc':

					if ( $segmented ) {

						version_compare( phpversion(), '5.4.0', '>=' ) ? asort( $main_field_totals, SORT_NATURAL | SORT_FLAG_CASE ) : asort( $main_field_totals );

					} else {

						version_compare( phpversion(), '5.4.0', '>=' ) ? asort( $data['results'], SORT_NATURAL | SORT_FLAG_CASE ) : asort( $data['results'] );

					}

					break;

				case 'desc':

					if ( $segmented ) {

						version_compare( phpversion(), '5.4.0', '>=' ) ? arsort( $main_field_totals, SORT_NATURAL | SORT_FLAG_CASE ) : arsort( $main_field_totals );

					} else {

						version_compare( phpversion(), '5.4.0', '>=' ) ? arsort( $data['results'], SORT_NATURAL | SORT_FLAG_CASE ) : arsort( $data['results'] );

					}

					break;

			}

			if ( $segmented ) {

				$former_data_results = $data['results'];

				$data['results'] = array();

				foreach ( $main_field_totals as $main_field => $total ) {

					$data['results'][ $main_field ] = $former_data_results[ $main_field ];

				}

			}

		}


		return $data;
	}

	/**
	 * @param $data
	 * @param $chart_config
	 *
	 * @return mixed
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @since  0.7
	 *
	 */
	public function collapse_data( $data, $chart_config ) {

		if ( ( ! empty( $data['segments'] ) ) && ( ! empty( $chart_config['xaxis-segment-max-entries'] ) ) && ( count( $data['segments'] ) > $chart_config['xaxis-segment-max-entries'] ) ) {

			$segment_count = count( $data['segments'] );

			$extra_entries = $chart_config['xaxis-segment-max-entries'] - $segment_count;

			array_splice( $data['segments'], $extra_entries );


			foreach ( $data['results'] as $label => $segment_data ) {

				$data['results'][ $label ]['Other'] = 0;

				foreach ( $segment_data as $segment_label => $segment_value ) {

					if ( ! in_array( $segment_label, $data['segments'] ) ) {

						$data['results'][ $label ]['Other'] += $segment_value;

						unset( $data['results'][ $label ][ $segment_label ] );

					}

				}

			}

			$data['segments'][] = 'Other';

		}

		if ( ( ! empty( $chart_config['xaxis-main-max-entries'] ) ) && ( count( $data['results'] ) > $chart_config['xaxis-main-max-entries'] ) ) {

			$data_count = count( $data['results'] );

			$extra_entries = $chart_config['xaxis-main-max-entries'] - $data_count;

			$addl_data = array_slice( $data['results'], $extra_entries, null, true );

			array_splice( $data['results'], $extra_entries );

			if ( empty( $data['segments'] ) ) {

				$data['results']['Other'] = array_sum( $addl_data );

			} else {

				$data['results']['Other'] = array();

				foreach ( $addl_data as $segment_label => $segment_value ) {

					if ( array_key_exists( $segment_label, $data['results']['Other'] ) ) {

						$data['results']['Other'][ $segment_label ] += $segment_value;

					} else {

						$data['results']['Other'][ $segment_label ] = $segment_value;

					}

				}

			}

		}


		return $data;
	}

	/**
	 * Convert results data if this was a survey field
	 *
	 * @param $field_id
	 * @param $form_id
	 * @param $data
	 *
	 * @return mixed
	 * @since  1.2.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 */
	private function convert_survey_field_data( $field_id, $form_id, $data, $chart_type ) {

		$form = GFAPI::get_form( $form_id );

		$field = RGFormsModel::get_field( $form, $field_id );

		if ( ! is_object( $field ) ) {

			return $data;
		}

		if ( in_array( $field->type, array( 'survey', 'poll', 'quiz' ) ) ) {

			switch ( $field->inputType ) {

				case 'radio' :
				case 'checkbox' :
				case 'select' :
				case 'rating' :
					/*case 'multiselect' :
						break;*/
				case 'likert' :

					if ( 'pie' == $chart_type ) {

						foreach ( $data as $label => $number ) {

							$true_label = wp_specialchars_decode( RGFormsModel::get_choice_text( $field, $label ), ENT_QUOTES );

							$new_data[ $true_label ] = $number;

						}

						$data = empty( $new_data ) ? $data : $new_data;

					} else if ( 'bar' == $chart_type ) {

						foreach ( $data as $key => $result ) {

							$true_value = RGFormsModel::get_choice_text( $field, $result[ 'meta_value' ] );

							$data[ $key ][ 'meta_value' ] = $true_value;

						}

					}

					break;

				case 'rank' :

					if ( 'pie' == $chart_type ) {

						$new_data = array();

						foreach ( $data as $encoded_ranked_values_list => $number ) {

							$score = count( rgar( $field, 'choices' ) );

							$encoded_ranked_values = explode( ',', $encoded_ranked_values_list );

							foreach ( $encoded_ranked_values as $value ) {

								$decoded_label = wp_specialchars_decode( RGFormsModel::get_choice_text( $field, $value ), ENT_QUOTES );

								if ( array_key_exists( $decoded_label, $new_data ) ) {

									$new_data[ $decoded_label ] += $score;

								} else {

									$new_data[ $decoded_label ] = $score;

								}

								$score --;

							}

						}

						$data = empty( $new_data ) ? $data : $new_data;

					} else if ( 'bar' == $chart_type ) {

						$new_data = array();

						foreach ( $data as $key => $result ) {

							$score = count( rgar( $field, 'choices' ) );

							$values = explode( ',', $result[ 'meta_value' ] );

							foreach ( $values as $ranked_value ) {

								$label = wp_specialchars_decode( RGFormsModel::get_choice_text( $field, $ranked_value ), ENT_QUOTES );

								if ( array_key_exists( $label, $new_data ) ) {

									$new_data[ $label ] += $score;

								} else {

									$new_data[ $label ] = $score;

								}

								$score --;

							}

						}

						$data['results'] = empty( $new_data ) ? $data['results'] : $new_data;

					}

			}

		}


		return $data;
	}

}
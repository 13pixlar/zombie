<?php
/*
 * @package   GFChart\GFChart_Import_Export
 * @copyright 2016-2020 gravity+
 * @license   GPL-2.0+
 * @since     1.3.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GFChart_Import_Export
 *
 * Handles importing & exporting charts
 *
 * @since  1.3.0
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */
class GFChart_Import_Export {

	/**
	 * @since  1.3.0
	 *
	 * @autho Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 */
	public function __construct() {

		add_filter( 'gform_export_form', array( $this, 'gform_export_form' ) );
		
		add_action( 'gform_forms_post_import', array( $this, 'gform_forms_post_import' ) );

	}

	/**
	 * Add charts to form object to be exported
	 *
	 * @since 1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $form
	 *
	 * @return array
	 */
	public function gform_export_form( $form ) {
		
		$form['charts'] = GFChart_API::get_charts( $form['id'] );
		
		return $form;
		
	}

	/**
	 * Import charts with forms that are being imported
	 *
	 * @since 1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param array $forms
	 */
	public function gform_forms_post_import( $forms ) {

		$charts_imported = false;

		foreach ( $forms as $form ) {

			if ( ! empty( $form['charts'] ) ) {

				$this->import_form_charts( $form['id'], $form['charts'] );

				$charts_imported = true;

			}

		}

		if ( $charts_imported ) {

			GFCommon::add_message( __( 'GFCharts imported', 'gfchart' ) );

		}

	}

	/**
	 * Create charts and add post meta
	 *
	 * @since 1.3.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $form_id
	 * @param $charts
	 */
	private function import_form_charts( $form_id, $charts ) {

		global $gfp_gfchart;

		$gfp_gfchart->get_addon_object()->log_debug( "Importing charts for form {$form_id}" );


		foreach( $charts as $chart ) {

			$post = array(
				'post_title'  => $chart['post_title'],
				'post_status' => $chart['post_status'],
				'post_type'   => $chart['post_type'],
			);

			$post_id = wp_insert_post( $post );

			$gfp_gfchart->get_addon_object()->log_debug( 'Post: ' . print_r( $post_id, true ) );

			if ( is_int( $post_id ) && $post_id > 0 ) {

				add_post_meta( $post_id, 'source_form', $form_id );

				add_post_meta( $post_id, '_gfchart_config', $chart['meta']['_gfchart_config'] );

				add_post_meta( $post_id, '_gfchart_type', $chart['meta'][ '_gfchart_type' ] );

				add_post_meta( $post_id, '_gfchart_filters', $chart['meta'][ '_gfchart_filters' ] );

			}

		}

	}

}
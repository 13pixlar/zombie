<?php
/**
 * GFChart Configuration Metabox — Select data Tab
 */
?>
<div id="gfchart-config-section-select-data">

	<?php if ( ! empty( $only_show_chart_type_settings) ) { ?>

			<div id="gfchart-<?php esc_attr_e( $chart_type_settings[ 'id' ] ) ?>-select-data" class="gfchart-select-data-settings" style="display:none;">

				<?php $view = $this->get_config_section_view( "select-data/chart-type-{$chart_type_settings['id']}", false );

				if ( $view ) {

					include( $view );

				}

				    unset( $view );
				?>

			</div>

		<?php } else { ?>

			<?php foreach ( $chart_types as $chart_type ) { ?>

			<div id="gfchart-<?php esc_attr_e( $chart_type[ 'id' ] ) ?>-select-data" class="gfchart-select-data-settings" style="display:none;">

							<?php $view = $this->get_config_section_view( "select-data/chart-type-{$chart_type['id']}", false );

							if ( $view ) {

								include( $view );

							}

								unset( $view );
							?>

						</div>

			<?php } ?>

			<?php unset( $chart_type ); ?>

		<?php } ?>

	<?php do_action( 'gfchart_config_section_select-data_after'); ?>


</div>

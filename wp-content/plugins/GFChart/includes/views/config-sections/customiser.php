<?php
/**
 * GFChart Configuration Metabox — Customiser Tab
 */
?>

<div id="gfchart-config-section-customiser">
	
	<?php if ( ! empty( $only_show_chart_type_settings) ) { ?>
		
				<div id="gfchart-<?php esc_attr_e( $chart_type_settings[ 'id' ], 'gfchart' ) ?>-customiser" class="gfchart-customiser-settings" style="display:none;">
		
					<?php $view = $this->get_config_section_view( "customiser/chart-type-{$chart_type_settings['id']}", false );

					if ( $view ) {

						include( $view );

					}

					unset( $view );
					?>
		
				</div>
		
			<?php } else { ?>
		
				<?php foreach ( $chart_types as $chart_type ) { ?>
		
			<div id="gfchart-<?php esc_attr_e( $chart_type[ 'id' ], 'gfchart' ) ?>-customiser" class="gfchart-customiser-settings" style="display:none;">
					
								<?php $view = $this->get_config_section_view( "customiser/chart-type-{$chart_type['id']}", false );

								if ( $view ) {

									include( $view );

								}

								unset( $view );?>
					
							</div>
		
				<?php } ?>
		
				<?php unset( $chart_type ); ?>
		
			<?php } ?>

	<?php
	/**
	 * For backwards compatibility
	 */
    do_action( 'gfchart_config_section_labels-and-fonts_after'); ?>

	<?php do_action( 'gfchart_config_section_customiser_after'); ?>


</div>
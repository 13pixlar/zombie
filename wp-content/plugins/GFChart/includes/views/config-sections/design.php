<?php
/**
 * GFChart Configuration Metabox — Design Tab
 */
?>


<div id="gfchart-config-section-design">

	<?php if ( ! empty( $only_show_chart_type_settings ) ) { ?>

        <span class="gforms_edit_form gfchart-chart-type-choice">

	        <label for="gfchart-chart-type-<?php esc_attr_e( $chart_type_settings['id'], 'gfchart' ) ?>">
		        <<?php esc_attr_e( $chart_type_settings['icon_element'] ) ?> class="<?php esc_attr_e( $chart_type_settings['icon_class'] ) ?>">
	        </<?php esc_attr_e( $chart_type_settings['icon_element'] ) ?>>
            </label>
            <br/>
			<input type="radio"
                                   id="gfchart-chart-type-<?php esc_attr_e( $chart_type_settings['id'], 'gfchart' ) ?>"
                                   name="gfchart_config[chart_type]"
                                   value="<?php esc_attr_e( $chart_type_settings['id'], 'gfchart' ) ?>" <?php checked( rgar( $gfchart_config, 'chart_type' ), $chart_type_settings['id'], true ); ?> />
			<label for="gfchart-chart-type-<?php esc_attr_e( $chart_type_settings['id'], 'gfchart' ) ?>"><?php esc_html_e( $chart_type_settings['label'], 'gfchart' ) ?></label>

						</span>

	<?php } else { ?>

		<?php foreach ( $chart_types as $chart_type ) { ?>

            <span class="gforms_edit_form gfchart-chart-type-choice">

					<label
                            for="gfchart-chart-type-<?php esc_attr_e( $chart_type['id'] ) ?>"><<?php esc_attr_e( $chart_type['icon_element'] ) ?>
					                                                               class="<?php esc_attr_e( $chart_type['icon_class'] ) ?>
					                                                               "></<?php esc_attr_e( $chart_type['icon_element'] ) ?>
				></label>                    <br/>
					<input type="radio" id="gfchart-chart-type-<?php esc_attr_e( $chart_type['id'] )?>"
                           name="gfchart_config[chart_type]"
                           value="<?php esc_attr_e( $chart_type['id'] ) ?>" <?php checked( rgar( $gfchart_config, 'chart_type' ), $chart_type['id'], true ); ?> />
				<?php esc_html_e( $chart_type['label'] ) ?>

				</span>

		<?php } ?>

		<?php unset( $chart_type ); ?>

	<?php } ?>

    <hr style="border-bottom: 1px dotted;margin-top: 65px;"/>

	<?php if ( ! empty( $only_show_chart_type_settings ) ) { ?>

        <div id="gfchart-chart-type-<?php esc_attr_e( $chart_type_settings['id'] ) ?>-basic-settings"
             class="gfchart-chart-type-basic-settings" style="display:none;">

			<?php $view = $this->get_config_section_view( "design/chart-type-{$chart_type_settings['id']}", false );

			if ( $view ) {

				include( $view );

			}

			unset( $view );
			?>

        </div>

	<?php } else { ?>

		<?php foreach ( $chart_types as $chart_type ) { ?>

            <div id="gfchart-chart-type-<?php esc_attr_e( $chart_type['id'] ) ?>-basic-settings"
                 class="gfchart-chart-type-basic-settings" style="display:none;">

				<?php $view = $this->get_config_section_view( "design/chart-type-{$chart_type['id']}", false );

				if ( $view ) {

					include( $view );

				}

				unset( $view );
				?>

            </div>

		<?php } ?>

		<?php unset( $chart_type ); ?>

	<?php } ?>

	<?php do_action( 'gfchart_config_section_design_after' ); ?>

</div>
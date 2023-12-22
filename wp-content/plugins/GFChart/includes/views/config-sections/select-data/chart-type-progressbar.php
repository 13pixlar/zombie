<?php
/**
 * GFChart Configuration Metabox — Select Data Tab — Progress Bar Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Progress Bar', 'gfchart' )?></h1>

<table class="form-table striped">

	<tbody>

	<tr>
		<th><?php _e( 'Source Form', 'gfchart' ) ?></th>
		<td colspan="2">
			<?php esc_html_e( $source_form[ 'title' ] ) ?>
		</td>
	</tr>
	<tr>
				<th><?php _e( 'Total (choose Calculation Chart)', 'gfchart' ) ?></th>
				<td colspan="2">
					<select id="gfchart-progressbar-calc-chart" name="gfchart_config[calc-chart]">
						<option value=""></option>
						<?php
						$calc_charts = get_posts( array( 'post_type' => 'gfchart', 'meta_key' => '_gfchart_type', 'meta_value' => 'calc', 'posts_per_page' => -1 ) );

						foreach ( $calc_charts as $calc_chart ) {

							$option_label = esc_html( GFCommon::truncate_middle( $calc_chart->post_title, 40 ) );
							?>
							<option
								value="<?php esc_attr_e( $calc_chart->ID ) ?>" <?php selected( rgar( $gfchart_config, 'calc-chart' ), $calc_chart->ID, true ) ?>><?php esc_html_e( $option_label ) ?></option>

						<?php } ?>
					</select>
				</td>
			</tr>
	<tr>
			<th><?php _e( 'Goal', 'gfchart' ) ?></th>
			<td colspan="2">
				<input type="number" id="gfchart-progressbar-goal" name="gfchart_config[goal]"
							       value="<?php esc_attr_e( rgar( $gfchart_config, 'goal' ) ) ?>"/>

			</td>
		</tr>

	</tbody>

</table>
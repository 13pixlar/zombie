<?php
/**
 * GFChart Configuration Metabox — Design Tab — Pie Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Pie', 'gfchart' ) ?></h1>
<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Style', 'gfchart' ) ?></th>
		<td>
			<label for="gfchart-chart-type-pie-style-default"><?php _e( 'Default', 'gfchart' ) ?></label>
						<input
							type="radio" id="gfchart-chart-type-pie-style-default" name="gfchart_config[style]"
							value="default" <?php checked( rgar( $gfchart_config, 'style' ), 'default', true ); ?> />
			<label for="gfchart-chart-type-pie-style-donut"><?php _e( 'Donut', 'gfchart' ) ?></label>
			<input
				type="radio" id="gfchart-chart-type-pie-style-donut" name="gfchart_config[style]"
				value="donut" <?php checked( rgar( $gfchart_config, 'style' ), 'donut', true ); ?> /> 
			<label for="gfchart-chart-type-pie-style-3d"><?php _e( '3D', 'gfchart' ) ?></label>
			<input type="radio"
			       id="gfchart-chart-type-pie-style-3d"
			       name="gfchart_config[style]"
			       value="3D" <?php checked( rgar( $gfchart_config, 'style' ), '3D', true ); ?> />

		</td>
	</tr>
	<tr>
		<th><?php _e( 'Legend', 'gfchart' ) ?></th>
		<td>
			<select name="gfchart_config[legend]">
				<option value="none" <?php selected( rgar( $gfchart_config, 'legend' ), 'none', true ); ?>><?php _e( 'None', 'gfchart' ) ?>
								</option>
								<option value="bottom" <?php selected( rgar( $gfchart_config, 'legend' ), 'bottom', true ); ?>><?php _e( 'Bottom', 'gfchart' ) ?>
								</option>
								<option value="top" <?php selected( rgar( $gfchart_config, 'legend' ), 'top', true ); ?>><?php _e( 'Top', 'gfchart' ) ?></option>
								<option value="right"<?php selected( rgar( $gfchart_config, 'legend' ), 'right', true ); ?>><?php _e( 'Right', 'gfchart' ) ?>
								</option>
								<option value="left" <?php selected( rgar( $gfchart_config, 'legend' ), 'left', true ); ?>><?php _e( 'Left', 'gfchart' ) ?></option>
								<option value="labeled" <?php selected( rgar( $gfchart_config, 'legend' ), 'labeled', true ); ?>>
				<?php _e( 'Labeled', 'gfchart' ) ?>
								</option>
			</select>
		</td>
	</tr>
	</tbody>
</table>
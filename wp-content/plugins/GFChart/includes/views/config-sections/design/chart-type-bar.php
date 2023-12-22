<?php
/**
 * GFChart Configuration Metabox — Design Tab — Bar Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Bar', 'gfchart' )?></h1>
<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Orientation', 'gfchart' ) ?></th>
		<td>
			<label for="gfchart-chart-type-bar-orientation-vertical"><?php _e( 'Vertical', 'gfchart' ) ?></label>
						<input
							type="radio" id="gfchart-chart-type-bar-orientation-vertical" name="gfchart_config[orientation]"
							value="vertical" <?php checked( rgar( $gfchart_config, 'orientation' ), 'vertical', true ); ?> />
			<label for="gfchart-chart-type-bar-orientation-horizontal"><?php _e( 'Horizontal', 'gfchart' ) ?></label>
									<input
										type="radio" id="gfchart-chart-type-bar-orientation-horizontal" name="gfchart_config[orientation]"
										value="horizontal" <?php checked( rgar( $gfchart_config, 'orientation' ), 'horizontal', true ); ?> />

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
				<option value="in" <?php selected( rgar( $gfchart_config, 'legend' ), 'in', true ); ?>><?php _e( 'In', 'gfchart' ) ?>
				</option>
			</select>
		</td>
	</tr>
	</tbody>
</table>
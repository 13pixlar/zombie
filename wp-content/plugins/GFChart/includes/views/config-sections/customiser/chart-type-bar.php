<?php
/**
 * GFChart Configuration Metabox — Customiser Tab — Bar Chart Type Basic Settings
 */
?>
<h1><?php _e( 'Bar', 'gfchart' ) ?></h1>

<table class="form-table striped">
	<tbody>
	<tr>
		<th><?php _e( 'Chart title', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-bar-chart-title" name="gfchart_config[title]"
			       value="<?php esc_attr_e( rgar( $gfchart_config, 'title' ) ) ?>"/>
		</td>
	</tr>
	<!--<tr>
			<th><?php //_e( 'Chart sub-title', 'gfchart' ) ?></th>
			<td>
				<input type="text" id="gfchart-bar-chart-subtitle" name="gfchart_config[subtitle]" value="<?php //echo rgar( $gfchart_config, 'subtitle' ) ?>" />
			</td>
		</tr>-->
	<tr>
		<th><?php _e( 'Chart height', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-bar-chart-height" name="gfchart_config[height]"
			       value="<?php esc_attr_e(  ( '' == rgar( $gfchart_config, 'height' ) ) ? 300 : rgar( $gfchart_config, 'height' ) ) ?>"/>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Chart width', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-bar-chart-width" name="gfchart_config[width]"
			       value="<?php esc_attr_e(  ( '' == rgar( $gfchart_config, 'width' ) ) ? 400 : rgar( $gfchart_config, 'width' ) ) ?>"/>
		</td>
	</tr>
    <tr>
        <th><?php _e( 'Responsive', 'gfchart' ) ?></th>
        <td>
            <input type="checkbox" id="gfchart-bar-responsive" name="gfchart_config[responsive]"
                   value="1" <?php checked( rgar( $gfchart_config, 'responsive' ), '1' ) ?>" />

        </td>
    </tr>
	<tr>
		<th><?php _e( 'Horizontal axis label', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-bar-chart-xaxis-label" name="gfchart_config[xaxis-label]"
			       value="<?php esc_attr_e(  rgar( $gfchart_config, 'xaxis-label' ) ) ?>"/>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Vertical axis label', 'gfchart' ) ?></th>
		<td>
			<input type="text" id="gfchart-bar-chart-yaxis-label" name="gfchart_config[yaxis-label]"
			       value="<?php esc_attr_e(  rgar( $gfchart_config, 'yaxis-label' ) ) ?>"/>
		</td>
	</tr>
	<tr>
		<th><?php //_e( 'y-axis auto-scale', 'gfchart' ) ?></th>
		<td>
			<input type="checkbox" id="gfchart-bar-yaxis-auto" name="gfchart_config[yaxis-auto]" value="1"
			       disabled <?php /*checked( rgar( $gfchart_config, 'yaxis-auto' ), '1' )*/
			checked( '1', '1' ) ?>" />
		</td>
	</tr>
	<tr id="gfchart-bar-chart-yaxis-max-container">
		<!--<th><?php //_e( 'y-axis maximum', 'gfchart' ) ?></th>
				<td>
					<input type="text" id="gfchart-bar-chart-yaxis-max" name="gfchart_config[yaxis-max]" value="<?php //echo rgar( $gfchart_config, 'yaxis-max' ) ?>" />
				</td>-->
	</tr>
	<tr id="gfchart-bar-chart-yaxis-lines-container">
		<!--<th><?php //_e( 'y-axis number of lines', 'gfchart' ) ?></th>
					<td>
						<input type="text" id="gfchart-bar-chart-yaxis-lines" name="gfchart_config[yaxis-lines]" value="<?php //echo rgar( $gfchart_config, 'yaxis-lines' ) ?>" />
					</td>-->
	</tr>
	<tr>
		<th><?php _e( 'Additional code', 'gfchart' ) ?></th>
		<td>
			<textarea id="gfchart-bar-additional-code"
			          name="gfchart_config[additional_code]"><?php echo rgar( $gfchart_config, 'additional_code' ) ?></textarea>
			<span class="instruction" style="display: block;font-weight:normal;">
				Example Formatting: <br/>
				<code>{"height": 300, <br/>"width": 400,<br/>"backgroundColor": "white"}</code>
			</span>
		</td>
	</tr>

	<?php do_action( 'gfchart_config_section_customiser_bar_settings_after', $gfchart_config, $source_form); ?>

    </tbody>
</table>

<script type="text/html" id="tmpl-gfchart-shortcode-default-edit-form">

	<form class="gfchart-edit-shortcode-form">

		<h2 class="gfchart-edit-shortcode-form-title"><?php _e('Insert a Chart/Calculation', 'gfchart'); ?></h2>
		<br />
		<div class="gfchart-edit-shortcode-form-required-attrs">
		</div>
		<br />
		<div class="gfchart-edit-shortcode-form-standard-attrs">
		</div>
		<br />

		<input id="gfchart-update-shortcode" type="button" class="button-primary" value="<?php _e( 'Update Chart/Calculation', 'gfchart' ); ?>" />
		<input id="gfchart-insert-shortcode" type="button" class="button-primary" value="<?php _e( 'Insert Chart/Calculation', 'gfchart' ); ?>" />&nbsp;&nbsp;&nbsp;
		<a id="gfchart-cancel-shortcode" class="button" style="color:#bbb;" href="#"><?php _e( 'Cancel', 'gfchart' ); ?></a>

	</form>

</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-text">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<input type="text" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="{{ data.value }}"/>
		<div style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A">{{ data.description }}</div>
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-url">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<input type="url" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="{{ data.value }}" class="code"/>
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-textarea">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<textarea name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}">{{ data.value }}</textarea>
	</div>

</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-select">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<select name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}">
			<# _.each( data.options, function( label, value ) { #>
				<option value="{{ value }}" <# if ( value == data.value ){ print('selected'); }; #> <# if (data.attr == 'id' && value == '') { print('disabled="disabled"')}; #>>{{ label }}</option>
			<# }); #>
		</select>
	</div>
	<div style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A">{{ data.description }}</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-radio">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}-{{ value }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<# _.each( data.options, function( label, value ) { #>
			<input id="gfchart-shortcode-attr-{{ data.attr }}-{{ value }}" type="radio" name="{{ data.attr }}" value="{{ value }}" <# if ( value == data.value ){ print('checked'); } #>>{{ label }}<br />
		<# }); #>
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-checkbox">
		<input type="checkbox" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="true" <# var val = ! data.value && data.default != undefined ? data.default : data.value; if ('true' == data.value ){ print('checked'); } #>>
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-email">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<input type="email" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="{{ data.value}}" />
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-number">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<input type="number" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="{{ data.value}}" />
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-hidden">
	<div class="field-block">
		<input type="hidden" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="true" />
	</div>
</script>

<script type="text/html" id="tmpl-gfchart-shortcode-ui-field-date">
	<div class="field-block">
		<label for="gfchart-shortcode-attr-{{ data.attr }}">{{ data.label }} <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_{{data.action}}_{{data.attr}}" title="{{data.tooltip}}"><i class='fa fa-question-circle'></i></a></label>
		<input type="date" name="{{ data.attr }}" id="gfchart-shortcode-attr-{{ data.attr }}" value="{{ data.value }}" />
	</div>
</script>
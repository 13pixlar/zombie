/**
 * New chart JS
 */

jQuery( document ).ready( function ( $ ) {

	var $legacyButton = $( '.post-type-gfchart .page-title-action' );

	var $addNewButton = $( '<button>' )
		.addClass( 'page-title-action' )
		.text( $legacyButton.text() )
		.on( 'click', loadNewChartModal );

	$legacyButton.after( $addNewButton )

	$(document).on( 'submit', '#gf_new_chart_modal_form', handleNewChartSubmission );

} );

function loadNewChartModal() {

	resetNewChartModal();

	tb_show( 'Create a New Chart/Calculation', '#TB_inline?width=500&amp;inlineId=gf_new_chart_modal' );
	jQuery( '#new_chart_title' ).focus();

	return false;
}

function handleNewChartSubmission() {

	saveNewChart();

	return false;
}

function saveNewChart() {

	var createButton = jQuery( '#save_new_chart' );
	var spinner = new gfAjaxSpinner( createButton, gfchart_admin_js.spinner );

	jQuery( '#gf_new_chart_error_message' ).html( '' );

	var origVal = createButton.val();
	createButton.val( 'Creating Chart...' );

	var chart = {
		title: jQuery( '#new_chart_title' ).val(),
		source_form: jQuery( '#new_chart_source_form' ).val()
	}

	jQuery.post( ajaxurl, {
		chart: jQuery.toJSON( chart ),
		action: 'gf_save_new_chart',
		gf_save_new_chart: gfchart_admin_js.nonce
	}, function ( response ) {

		spinner.destroy();

		if ( true === response.success ) {

			location.href = response.data.redirect;
			createButton.val( 'Saved! Redirecting...' );

		}
		else {

			jQuery( '#gf_new_chart_error_message' ).html( response.data );
			addInputErrorIcon( '#new_chart_title' );
			createButton.val( origVal );

		}

	} );

}

function resetNewChartModal() {

	jQuery( '#new_chart_title' ).val( '' );
	jQuery( '#gf_new_chart_error_message' ).html( '' );

	removeInputErrorIcons( '.gf_new_chart_modal_container' );

}

function addInputErrorIcon( elem ) {

	var elem = jQuery( elem );

	elem.before( '<span class="gf_input_error_icon"></span>' );

}

function removeInputErrorIcons( elem ) {

	elem = jQuery( elem );

	elem.find( 'span.gf_input_error_icon' ).remove();
}

function gfAjaxSpinner( elem, imageSrc, inlineStyles ) {

	var imageSrc = typeof imageSrc == 'undefined' ? '/images/ajax-loader.gif' : imageSrc;
	var inlineStyles = typeof inlineStyles != 'undefined' ? inlineStyles : '';

	this.elem = elem;
	this.image = '<img class="gfspinner" src="' + imageSrc + '" style="' + inlineStyles + '" />';

	this.init = function () {
		this.spinner = jQuery( this.image );
		jQuery( this.elem ).after( this.spinner );
		return this;
	}

	this.destroy = function () {
		jQuery( this.spinner ).remove();
	}

	return this.init();
}
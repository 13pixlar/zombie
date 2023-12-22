/**
 * Admin Chart Configuration JS
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */

var GFChart = {};

var gfchart_admin_chart_config = (function ($) {

    var formUpdated = false;

    $(window).load(function () {

        gfchart_set_initial_chart_type_settings($('input[name="gfchart_config[chart_type]"]:checked').val());

        $('input[name="gfchart_config[chart_type]"]').on('click', gfchart_toggle_chart_type_settings);

        $('.gfchart-config-tab-nav-button').on('click', gfchart_nav_switch_tab);

        $('#post').on('change', gfchart_mark_form_as_updated);

        $(document).on('gfchart_config_switch_tab', gfchart_auto_save);

        $(document).on('gfchart_config_switch_tab', gfchart_do_preview);

        $('.gfchart-config-tab-nav-button-save').on('click', gfchart_save_chart_config);


        $('#gfchart-bar-xaxis-segment-toggle-on').on('click', {toggle: 'on'}, gfchart_bar_toggle_xaxis_segment);
        $('#gfchart-bar-xaxis-segment-toggle-off').on('click', {toggle: 'off'}, gfchart_bar_toggle_xaxis_segment);

        $('#gfchart-bar-yaxis').on('change', gfchart_bar_toggle_xaxis_sum_field);

        $('#gfchart-bar-yaxis-auto').on('click', gfchart_bar_toggle_yaxis_scale_options);

        $('input[name="gfchart_config[date_filter_start]"], input[name="gfchart_config[date_filter_end]"]').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            constrainInput: false
        });

        gfchart_add_publish_shortcode();

    });

    var admin_config = {};

    /*------------ PUBLISH BOX ------------*/
    function gfchart_add_publish_shortcode() {

        var postID = $('#post_ID').val();

        var shortcode = '[gfchart id="' + postID + '"]';

        var $shortcodeInput = $('<input type="text">')
            .addClass('gfchart_shortcode')
            .val(shortcode)
            .attr('readonly', 'readonly')
            .on( 'focus click', function() {
                $(this).select();
            });

        $('.post-type-gfchart #postbox-container-1 #misc-publishing-actions')
            .prepend($('<div class="misc-pub-section misc-pub-gfchart-shortcode">' + gfchart_admin_config.publish_text + '</div>').append($shortcodeInput))
            .show();
    }

    /*------------ SETTINGS DISPLAY ------------*/
    function gfchart_set_initial_chart_type_settings(chart_type) {

        if (chart_type) {

            $('#gfchart-chart-type-' + chart_type + '-basic-settings').show();
            $('#gfchart-' + chart_type + '-select-data').show();
            $('#gfchart-' + chart_type + '-customiser').show();

            $('#gfchart_' + chart_type + '_entry_filters').removeClass('hide-if-js').gfFilterUI(gfchart_admin_config.gformFieldFilters, gfchart_admin_config.gformInitFilter, true);

            gfchart_select_data_init( chart_type );

        }

    }

    function gfchart_toggle_chart_type_settings() {

        var chart_type = jQuery(this).val();


        gfchart_clear_chart_filters();

        gfchart_hide_all_chart_type_settings();

        gfchart_clear_chart_settings();


        $('#gfchart-config-section-design').append(gfchart_js.basic_settings[chart_type]);
        $('#gfchart-chart-type-' + chart_type + '-basic-settings').show();

        $('#gfchart-config-section-select-data').append(gfchart_js.select_data[chart_type]);
        $('#gfchart-' + chart_type + '-select-data').show();


        $('#gfchart-config-section-customiser').append(gfchart_js.customiser[chart_type]);
        $('#gfchart-' + chart_type + '-customiser').show();


        var filters_wrapper = $('#gfchart_' + chart_type + '_entry_filters');

        filters_wrapper.hide();

        filters_wrapper.removeClass('hide-if-js').gfFilterUI(gfchart_admin_config.gformFieldFilters, gfchart_admin_config.gformInitFilter, true);


        gfchart_select_data_init( chart_type );

    }

    function gfchart_hide_all_chart_type_settings() {

        if ('undefined' == typeof gfchart_js.basic_settings) {

            gfchart_js.basic_settings = {};

        }

        if ('undefined' == typeof gfchart_js.select_data) {

            gfchart_js.select_data = {};

        }

        if ('undefined' == typeof gfchart_js.customiser) {

            gfchart_js.customiser = {};

        }

        var chart_types = gfchart_admin_config.chart_types;

        chart_types.forEach( function( value ) {

            var basic_settings = $('#gfchart-chart-type-' + value + '-basic-settings');

            if (0 !== basic_settings.length) {

                gfchart_js.basic_settings[value] = basic_settings.hide();
                basic_settings.detach();

            }

            var select_data = $('#gfchart-' + value + '-select-data');

            if (0 !== select_data.length) {

                gfchart_js.select_data[value] = select_data.hide();
                select_data.detach();

            }

            var customiser = $('#gfchart-' + value + '-customiser');

            if (0 !== customiser.length) {

                gfchart_js.customiser[value] = customiser.hide();
                customiser.detach();

            }

        });

    }

    function gfchart_clear_chart_settings() {

        $('.gfchart-config-tab-nav-button-save').trigger('click');

    }

    function gfchart_clear_chart_filters() {

        $('input[name="gfchart_config[additional_filters]"]').prop('checked', false);

        $('#gform-field-filters').detach();

        gfchart_admin_config.gformInitFilter.filters.length = 0;
        gfchart_admin_config.gformInitFilter.mode = '';

    }


    /*------------ TAB NAVIGATION ------------*/
    function gfchart_nav_switch_tab(evt) {

        evt.preventDefault();

        var nav_button = $(this);

        var tab = nav_button.data('gfconfigTab');

        if (('customiser' == tab) && (nav_button.hasClass('gfchart-config-tab-nav-button-prev'))) {

            gfchart_config_switch_tab(tab);

        }
        else {

            var section_object = nav_button.parents('.gfchart-config-section');

            var save_button = section_object.find('.gfchart-config-tab-nav-button-save');

            save_button.hide();

            nav_button.hide();

            nav_button.after('<span id="gfchart-config-tab-loading-msg"><img src="' + gfchart_admin_config.loading_img + '" /> Saving...</span>');


            if (nav_button.hasClass('gfchart-config-tab-nav-button-next')) {

                var other_button = section_object.find('.gfchart-config-tab-nav-button-prev');

                if (0 !== other_button.length) {

                    other_button.hide();

                }

            }
            else if (nav_button.hasClass('gfchart-config-tab-nav-button-prev')) {

                var other_button = section_object.find('.gfchart-config-tab-nav-button-next');

                if (0 !== other_button.length) {

                    other_button.hide();

                }

            }

            $.post(ajaxurl, {

                data: $('#post').serialize(),
                action: 'gf_dynamically_save_chart_config'

            }).done(function (response) {

                if (true === response.success) {

                    gfchart_config_switch_tab(tab);

                }

                $('#gfchart-config-tab-loading-msg').remove();

                nav_button.show();

                save_button.show();

                other_button.show();

            }).fail(function (response) {

                if (window.console && window.console.log) {

                    console.log(response);

                }

            }).always();

        }

    }

    function gfchart_config_switch_tab(tab) {

        var tab_object = $('#gfchart-config-tab-' + tab);

        var navTabs = $('#gfchart-view-configuration-tabs').children('.nav-tab-wrapper'),
            tabIndex = null;

        navTabs.children().each(function () {

            if (!tab_object.hasClass('nav-tab-active')) {

                var old_tab_object = $('.nav-tab-active');
                old_tab_object.removeClass('nav-tab-active');

                tab_object.addClass('nav-tab-active');

                tabIndex = tab_object.index();

                $('#gfchart-view-configuration-tabs')
                    .children('div:not( .inside.hidden )')
                    .addClass('hidden');

                $('#gfchart-view-configuration-tabs')
                    .children('div:nth-child(' + ( tabIndex ) + ')')
                    .addClass('hidden');

                $('#gfchart-view-configuration-tabs')
                    .children('div:nth-child( ' + ( tabIndex + 2 ) + ')')
                    .removeClass('hidden');

                var old_tab = old_tab_object.prop('href').replace('#', '');

                $(document).trigger('gfchart_config_switch_tab', [tab, old_tab]);

            }

        });

    }

    function gfchart_mark_form_as_updated() {

        formUpdated = true;

    }

    function gfchart_auto_save(event, newTab, oldTab) {

        if ( newTab !== oldTab && formUpdated ) {

            $('#' + newTab)
                .find('.gfchart-config-tab-nav-button-save')
                .trigger('click');

            formUpdated = false;
        }

    }

    function gfchart_save_chart_config() {

        var save_button = $(this);
        var origVal = save_button.val();

        save_button.prop('disabled', true);

        var section_object = $(this).parents('.gfchart-config-section');

        var prev_button = section_object.find('.gfchart-config-tab-nav-button-prev');

        if (0 !== prev_button.length) {

            prev_button.hide();

        }

        var next_button = section_object.find('.gfchart-config-tab-nav-button-next');

        if (0 !== next_button.length) {

            next_button.hide();

        }

        save_button.val('Saving...');

        $.post(ajaxurl, {

            data: $('#post').serialize(),
            action: 'gf_dynamically_save_chart_config'

        }).done(function (response) {

            if (true === response.success) {

                save_button.val('Saved!');

                setTimeout(
                    function () {

                        save_button.val(origVal);
                        save_button.prop('disabled', false);
                        prev_button.show();
                        next_button.show();

                    }, 2000);

            }
            else {

                save_button.before(response.data);

                save_button.val(origVal);

                save_button.prop('disabled', false);

                prev_button.show();

                next_button.show();

            }


        }).fail(function (response) {

            if (window.console && window.console.log) {

                console.log(response);

            }

        }).always();

    }

    function gfchart_get_config_data() {

        $('input[name^="gfchart_config["]').each(function () {
        });

    }


    function gfchart_select_data_init( chart_type ) {

        switch( chart_type ){

            case 'pie':

                $('#gfchart-pie-additional-filters').click( { chart_type: 'pie' }, admin_config.gfchart_toggle_filters );

                break;

            case 'bar':

                if ('' !== $('#gfchart-bar-xaxis-segment-field').val()) {

                    $('#gfchart-bar-xaxis-segment-container').show('slow');
                    $('#gfchart-bar-xaxis-segment-toggle-on').hide();
                    $('#gfchart-bar-xaxis-segment-toggle-off').show();

                }

                $('#gfchart-bar-additional-filters').click({ chart_type: 'bar' }, admin_config.gfchart_toggle_filters);

                if ($('#gfchart-bar-yaxis-auto').is(':checked')) {

                    $('#gfchart-bar-chart-yaxis-max-container').hide('slow');
                    $('#gfchart-bar-chart-yaxis-lines-container').hide('slow');

                }

                if ('sum' === $('#gfchart-bar-yaxis').val()) {

                    $('#gfchart-bar-xaxis-sum-field-container').show();

                }

                break;
            case 'calc':

                $('#gfchart-calc-additional-filters').click({ chart_type: 'calc' }, admin_config.gfchart_toggle_filters);

                break;
            case 'progressbar':
                break;

        }

        gfchart.doAction( 'gfchart_select_data_init', chart_type );

    }

    admin_config.gfchart_toggle_filters = function( event ) {

        if ($(this).is(':checked')) {

            $('#gfchart_' + event.data.chart_type + '_entry_filters').show('slow');

        }
        else {

            var filters = $('#gfchart_' + event.data.chart_type + '_entry_filters');

            filters.hide('slow');

            filters.find('.gform-remove').each(function () {

                $(this).trigger('click');

            });

        }

    }

    /*------------ PIE ------------*/


    /*------------ BAR ------------*/

    /**
     * @since 1.1.0
     */
    function gfchart_bar_toggle_xaxis_sum_field() {

        if ('sum' == $(this).val()) {

            $('#gfchart-bar-xaxis-sum-field-container').show('slow');

        }
        else {

            $('#gfchart-bar-xaxis-sum-field-container').hide('slow');

            gfchart_bar_clear_xaxis_sum_field();

        }

    }

    /**
     * @since 1.1.0
     */
    function gfchart_bar_clear_xaxis_sum_field() {

        $('#gfchart-bar-xaxis-sum-field').val('');

    }

    function gfchart_bar_toggle_xaxis_segment(evt) {

        evt.preventDefault();

        if ('on' == evt.data.toggle) {

            $('#gfchart-bar-xaxis-segment-container').show('slow');
            $('#gfchart-bar-xaxis-segment-toggle-on').hide();
            $('#gfchart-bar-xaxis-segment-toggle-off').show();

        }
        else {

            $('#gfchart-bar-xaxis-segment-container').hide('slow');

            gfchart_bar_clear_xaxis_segment();

            $('#gfchart-bar-xaxis-segment-toggle-off').hide();
            $('#gfchart-bar-xaxis-segment-toggle-on').show();

        }

    }

    function gfchart_bar_clear_xaxis_segment() {

        $('#gfchart-bar-xaxis-segment-field').val('');
        $('#gfchart-bar-xaxis-segment-display').val([]);
        $('#gfchart-bar-xaxis-segment-max-entries').val('');

    }

    function gfchart_bar_toggle_yaxis_scale_options() {

        if ($(this).is(':checked')) {

            $('#gfchart-bar-chart-yaxis-max-container').hide('slow');
            $('#gfchart-bar-chart-yaxis-lines-container').hide('slow');

            $('input[name="gfchart_config[yaxis-max]"]').val('');
            $('input[name="gfchart_config[yaxis-lines]"]').val('');

        }
        else {

            $('#gfchart-bar-chart-yaxis-max-container').show('slow');
            $('#gfchart-bar-chart-yaxis-lines-container').show('slow');

        }

    }

    function gfchart_do_preview(event, new_tab, old_tab) {

        if ('preview' == new_tab) {

            //get chart type
            var chart_type = $('input[name="gfchart_config[chart_type]"]:checked').val();

            var preview_area = $('#gfchart-config-section-preview');

            preview_area.html('<img src="' + gfchart_admin_config.loading_img + '" /> Generating preview...');

            if ($('#' + new_tab).find('.gfchart-config-tab-nav-button-save').prop('disabled')) {

                setTimeout(function () {
                    gfchart_do_preview(event, new_tab, old_tab);
                }, 1000);

                return;
            }

            var post_id = $('#post_ID').val();

            $.post(ajaxurl, {
                post: post_id,
                action: 'gf_get_preview_chart_data',
                gfchart_config_nonce: $('#gfchart_config_nonce').val(),
            }).done(function (response) {

                if (true === response.success) {

                    if ( 'calc' === chart_type ) {

                        response.data.chart_data[0] = "<span style='font-weight:bold;font-size:x-large;'>" + response.data.chart_data[0] + "</span>";
                    }

                    var data = response.data.chart_data;


                    var uc_chart_type = chart_type.charAt(0).toUpperCase() + chart_type.slice(1);

                    gfchart_draw( uc_chart_type, post_id, data, response.data.chart_options, 'gfchart-config-section-preview', 0 );

                }
                else {

                    preview_area.html(response.data);

                    preview_area.show();

                }

            }).fail(function (response) {

                if (window.console && window.console.log) {

                    console.log(response);

                }

            }).always();

        }

    }

    return admin_config;

}(jQuery) );
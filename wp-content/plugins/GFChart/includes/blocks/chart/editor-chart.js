/**
 * Chart block editor JS
 *
 * @since  1.14.0
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */

( function( wp ) {

    var el = wp.element.createElement,
        registerBlockType = wp.blocks.registerBlockType,
        withAPIData = wp.components.withAPIData,
        RawHTML = wp.element.RawHTML,
        Placeholder = wp.components.Placeholder,
        InspectorControls = wp.editor.InspectorControls,
        SelectControl = wp.components.SelectControl,
        __ = wp.i18n.__;

    registerBlockType('gfchart/chart', {
        title: __('Chart', 'gfchart'),
        description: __( 'Select a chart below to add it to your page', 'gfchart'),
        category: 'embed',
        icon: 'chart-pie',
        keywords: [
            __('chart', 'gfchart'), __('gfchart', 'gfchart'), __('gravity', 'gfchart')
        ],
        attributes: {
            chart_id: {
                type: 'string'
            }
        },
        /*transforms: {
            from: [
                {
                    type: 'shortcode',
                    tag: 'gfchart',
                    attributes: {
                        chart_id: {
                            type: 'string',
                            shortcode: function( named ) {
                                var id = named.id;

                                return parseInt(id);
                            },
                        },
                    },
                },
            ]
        },*/
        supports: {
            html: false
        },
        edit: function (props) {

            var chart_id = props.attributes.chart_id;

            /*return el(
                'div',
                {},
                __('Hello from the editor!', 'gfchart')
            );*/

            function setChartID( new_id ) {

                props.setAttributes( { chart_id: new_id } );

            }

            var setChartIDFromPlaceholder = function setChartIDFromPlaceholder(e) {
                return setChartID(e.target.value);
            };

            return [
                el(
                    InspectorControls,
                    { key: 'inspector' },
                    el(SelectControl, {
                        label: __('Chart', 'gfchart'),
                        value: chart_id,
                        options: gfchart_block.charts,
                        onChange: setChartID
                    })
                ),
                el(
                Placeholder,
                { key: 'placeholder', className: 'wp-block-embed gform-block__placeholder' },
                el(
                    'div',
                    { className: 'gform-block__placeholder-brand' },
                    el('img', { src: gfchart_block.icon, width: '110' }),
                    el(
                        'p',
                        null,
                        el(
                            'strong',
                            null,
                            'GFChart'
                        )
                    )
                ),
                el(
                    'form',
                    null,
                    /*TODO: refactor to use SelectControl? */el(
                        'select',
                        { value: chart_id, onChange: setChartIDFromPlaceholder },
                        gfchart_block.charts.map(function (chart) {
                            return el(
                                'option',
                                { key: chart.value, value: chart.value },
                                chart.label
                            );
                        })
                    )
                )
            )];
        },
        save() {

            return (null);
            },
    });

} )( window.wp );
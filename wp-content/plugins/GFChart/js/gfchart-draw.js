/**
 * GFChart Draw JS
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 */

var gfchart_js = {charts: []};
jQuery(document).trigger('gfchart_object_declared');

/**
 * Taken from gravityforms.js and modified
 *
 * @since 1.9.0
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @type {{hooks: {action: {}, filter: {}}, addAction: gfchart.addAction, addFilter: gfchart.addFilter, doAction: gfchart.doAction, applyFilters: gfchart.applyFilters, removeAction: gfchart.removeAction, removeFilter: gfchart.removeFilter, addHook: gfchart.addHook, doHook: gfchart.doHook, removeHook: gfchart.removeHook}}
 */
var gfchart = {
    hooks: {action: {}, filter: {}},
    addAction: function (action, callable, priority, tag) {
        gfchart.addHook('action', action, callable, priority, tag);
    },
    addFilter: function (action, callable, priority, tag) {
        gfchart.addHook('filter', action, callable, priority, tag);
    },
    doAction: function (action) {
        gfchart.doHook('action', action, arguments);
    },
    applyFilters: function (action) {
        return gfchart.doHook('filter', action, arguments);
    },
    removeAction: function (action, tag) {
        gfchart.removeHook('action', action, tag);
    },
    removeFilter: function (action, priority, tag) {
        gfchart.removeHook('filter', action, priority, tag);
    },
    addHook: function (hookType, action, callable, priority, tag) {
        if (undefined == gfchart.hooks[hookType][action]) {
            gfchart.hooks[hookType][action] = [];
        }
        var hooks = gfchart.hooks[hookType][action];
        if (undefined == tag) {
            tag = action + '_' + hooks.length;
        }
        if (priority == undefined) {
            priority = 10;
        }

        gfchart.hooks[hookType][action].push({tag: tag, callable: callable, priority: priority});
    },
    doHook: function (hookType, action, args) {

        // splice args from object into array and remove first index which is the hook name
        args = Array.prototype.slice.call(args, 1);

        if (undefined != gfchart.hooks[hookType][action]) {
            var hooks = gfchart.hooks[hookType][action], hook;
            //sort by priority
            hooks.sort(function (a, b) {
                return a["priority"] - b["priority"]
            });
            for (var i = 0; i < hooks.length; i++) {
                hook = hooks[i].callable;
                if (typeof hook != 'function')
                    hook = window[hook];
                if ('action' == hookType) {
                    hook.apply(null, args);
                } else {
                    args[0] = hook.apply(null, args);
                }
            }
        }
        if ('filter' == hookType) {
            return args[0];
        }
    },
    removeHook: function (hookType, action, priority, tag) {
        if (undefined != gfchart.hooks[hookType][action]) {
            var hooks = gfchart.hooks[hookType][action];
            for (var i = hooks.length - 1; i >= 0; i--) {
                if ((undefined == tag || tag == hooks[i].tag) && (undefined == priority || priority == hooks[i].priority)) {
                    hooks.splice(i, 1);
                }
            }
        }
    }
};

/**
 * Pie Chart
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @param location
 * @param data
 * @constructor
 */
var GFChart_Pie = {

    /**
     * ID of the div where this chart will be rendered
     */
    location: '',

    data: '',

    options: '',

    init: function () {

        var obj = this;

        google.load('visualization', '1', {
            'packages': ['corechart'], 'callback': function () {
                obj.drawChart()
            }
        });

    },

    formatData: function () {
    },

    drawChart: function () {

        var data_table = new google.visualization.DataTable(this.data);

        var location = document.getElementById(this.location);

        var chart = new google.visualization.PieChart(location);


        chart.draw(data_table, this.options);

        jQuery('#' + this.location).show();

    }

};

/**
 * Bar Chart
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @param location
 * @param data
 * @constructor
 */
var GFChart_Bar = {

    /**
     * ID of the div where this chart will be rendered
     */
    location: '',

    data: '',

    options: '',

    init: function () {

        var obj = this;

        google.load('visualization', '1', {
            'packages': ['corechart'], 'callback': function () {
                obj.drawChart()
            }
        });

    },

    formatData: function () {
    },

    drawChart: function () {

        var data_table = new google.visualization.DataTable(this.data);

        var location = document.getElementById(this.location);

        if ('horizontal' == this.options.bars) {

            var chart = new google.visualization.BarChart(location);

        }
        else {

            var chart = new google.visualization.ColumnChart(location);

        }

        chart.draw(data_table, this.options);

        jQuery('#' + this.location).show();

    }

};

/**
 * Calc Chart
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @param location
 * @param data
 * @constructor
 */
var GFChart_Calc = {

    /**
     * ID of the div where this chart will be rendered
     */
    location: '',

    data: '',

    options: '',

    init: function () {

        var obj = this;

        obj.drawChart();

    },

    formatData: function () {
    },

    drawChart: function () {

        jQuery('#' + this.location).html(this.data).show();

    }

};

/**
 * Progress Bar Chart
 *
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @param location
 * @param data
 * @constructor
 */
var GFChart_Progressbar = {

    /**
     * ID of the div where this chart will be rendered
     */
    location: '',

    data: '',

    options: '',

    init: function () {

        var obj = this;

        obj.drawChart();

    },

    formatData: function () {
    },

    drawChart: function () {

        jQuery('#' + this.location).html('<div class="bar-main-container"><div class="bar-wrap"><div class="bar-percentage" data-percentage="' + this.data.percent + '"><span class="percentage"></span><span class="goal"></span></div> <div class="bar-container"> <div class="the-bar"></div> </div> </div> </div>').show();

        var goal = this.options.formatted_goal;


        jQuery('#' + this.location).find('.bar-percentage[data-percentage]').each(function () {

            var progress = jQuery(this);

            var percentage = Math.ceil(jQuery(this).attr('data-percentage'));


            progress.find('.percentage').text(percentage + '%') && progress.siblings().children().css('width', percentage + '%');

            progress.find('.goal').text(goal);

        });

    }

};

/**
 * @since
 *
 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
 *
 * @param chart_type
 * @param id
 * @param data
 * @param options
 * @param location
 * @param debug
 */
function gfchart_draw(chart_type, id, data, options, location, debug) {

    if ('object' == typeof( data )) {

        var chart_object = 'GFChart_' + chart_type;

        var chart_id = chart_type + '_chart_' + id;

        var chart_location = ( '' === location ) ? 'gfchart-' + chart_id : location;

        window[chart_id] = Object.create(this[chart_object], {
            'location': {value: chart_location},
            'data': {value: data},
            'options': {value: options}
        });

        window[chart_id].init();

    }
    else if (debug) {

        var chart_element = jQuery('#gfchart-' + chart_id);

        chart_element.html(data);

    }

}

jQuery(document).ready(function () {

    for (var i = 0; i < gfchart_js.charts.length; i++) {

        gfchart_draw(gfchart_js.charts[i].chart_type, gfchart_js.charts[i].id, gfchart_js.charts[i].data, gfchart_js.charts[i].options, gfchart_js.charts[i].location, gfchart_js.charts[i].debug);

    }

    jQuery(window).resize(function(){

        for (var i = 0; i < gfchart_js.charts.length; i++) {

            if ( 'undefined' !== typeof( gfchart_js.charts[i].options['responsive'] ) && true == gfchart_js.charts[i].options['responsive'] ) {

                window[gfchart_js.charts[i].chart_type + '_chart_' + gfchart_js.charts[i].id].drawChart();

            }

        }

    });

});
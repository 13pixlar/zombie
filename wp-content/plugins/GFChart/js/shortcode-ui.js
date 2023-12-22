//Props: Gravity Forms and https://github.com/fusioneng/Shortcake/
var GFChartShortcodeUI;

( function (gfcShortCodeUI, $) {

    var sui = window.GFChartShortcodeUI = {
        models: {},
        collections: {},
        views: {},
        utils: {},
        error_messages: {}
    };

    /**
     * Shortcode Attribute Model.
     */
    sui.models.ShortcodeAttribute = Backbone.Model.extend({
        defaults: {
            attr: '',
            label: '',
            type: '',
            section: '',
            description: '',
            default: '',
            value: ''
        }
    });

    /**
     * Shortcode Attributes collection.
     */
    sui.models.ShortcodeAttributes = Backbone.Collection.extend({

																	model: sui.models.ShortcodeAttribute,
        //  Deep Clone.
        clone: function () {

            return new this.constructor(_.map(this.models, function (m) {

				return m.clone();

			}));

		}

    });

    /**
     * Shortcode Model
     */
    sui.models.Shortcode = Backbone.Model.extend({

        defaults: {
            label: '',
            shortcode_tag: '',
            action_tag: '',
            attrs: sui.models.ShortcodeAttributes,
        },

        /**
         * Custom set method.
         * Handles setting the attribute collection.
         */
        set: function (attributes, options) {

            if (attributes.attrs !== undefined && !( attributes.attrs instanceof sui.models.ShortcodeAttributes )) {

                _.each(attributes.attrs, function (attr) {
                    if (attr.default != undefined) {
                        attr.value = attr.default
                    }
                });

                attributes.attrs = new sui.models.ShortcodeAttributes(attributes.attrs);
            }

            return Backbone.Model.prototype.set.call(this, attributes, options);
        },

        /**
         * Custom toJSON.
         * Handles converting the attribute collection to JSON.
         */
        toJSON: function (options) {

            options = Backbone.Model.prototype.toJSON.call(this, options);

			if (options.attrs !== undefined && ( options.attrs instanceof sui.models.ShortcodeAttributes )) {

				options.attrs = options.attrs.toJSON();

			}

			return options;
        },

        /**
         * Custom clone
         * Make sure we don't clone a reference to attributes.
         */
        clone: function () {

            var clone = Backbone.Model.prototype.clone.call(this);

			clone.set('attrs', clone.get('attrs').clone());

			return clone;

		},

        /**
         * Get the shortcode as... a shortcode!
         *
         * @return string eg [shortcode attr1=value]
         */
        formatShortcode: function () {

            var template, shortcodeAttributes, attrs = [], content, action = '', actions = [];

            this.get('attrs').each(function (attr) {

                var val = attr.get('value');
                var type = attr.get('type');
                var def = attr.get('default');

                // Skip empty attributes.
                // Skip unchecked checkboxes that have don't have default='true'.
                if (( ( !val || val.length < 1 ) && type != 'checkbox') || ( type == 'checkbox' && def != 'true' && !val )) {
                    return;
                }

                // Handle content attribute as a special case.
                if (attr.get('attr') === 'content') {
                    content = attr.get('value');
                } else {
                    attrs.push(attr.get('attr') + '="' + val + '"');
                }

            });


            template = "[{{ shortcode }} {{ attributes }}]"

            if (content && content.length > 0) {
                template += "{{ content }}[/{{ shortcode }}]"
            }

            template = template.replace(/{{ shortcode }}/g, this.get('shortcode_tag'));
            template = template.replace(/{{ attributes }}/g, attrs.join(' '));
            template = template.replace(/{{ content }}/g, content);

            return template;

        },

        validate: function (shortcode) {

			var errors = [];

			var id = shortcode.attrs.findWhere({attr: 'id'});

			if (!id.get('value')) {

                errors.push({'id': sui.error_messages.no_chart_selected});

			}

            return errors.length ? errors : null;
        }

    });

    // Shortcode Collection
    sui.collections.Shortcodes = Backbone.Collection.extend({

        model: sui.models.Shortcode

															});


    /**
     * Single edit shortcode content view.
     */
    sui.views.editShortcodeForm = wp.Backbone.View.extend({

        el: '#gfchart-shortcode-ui-container',

        template: wp.template('gfchart-shortcode-default-edit-form'),

        hasAdvancedValue: false,

        events: {
            'click #gfchart-update-shortcode': 'insertShortcode',
            'click #gfchart-insert-shortcode': 'insertShortcode',
            'click #gfchart-cancel-shortcode': 'cancelShortcode'
        },

        initialize: function () {

            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            var t = this;
            this.render = _.wrap(this.render, function (render) {
                t.beforeRender();
                render();
                t.afterRender();
                return t;
            });


            this.model.get('attrs').each(function (attr) {
                switch (attr.get('section')) {
                    case 'required':
                        t.views.add(
                            '.gfchart-edit-shortcode-form-required-attrs',
                            new sui.views.editAttributeField({model: attr, parent: t})
                        );
                        break;
                    case 'standard':
                        t.views.add(
                            '.gfchart-edit-shortcode-form-standard-attrs',
                            new sui.views.editAttributeField({model: attr, parent: t})
                        );
                        break;
                    default:
                        t.views.add(
                            '.gfchart-edit-shortcode-form-advanced-attrs',
                            new sui.views.editAttributeField({model: attr, parent: t})
                        );
                        if (!t.hasAdvancedVal) {
                            t.hasAdvancedVal = attr.get('value') !== '';
                        }
                }
            });

            this.listenTo(this.model, 'change', this.render);
        },

        beforeRender: function () {
            //
        },

        afterRender: function () {

			gform_initialize_tooltips();

            $('#gfchart-insert-shortcode').toggle(this.options.viewMode == 'insert');
            $('#gfchart-update-shortcode').toggle(this.options.viewMode != 'insert');

        },

        insertShortcode: function (e) {

            var isValid = this.model.isValid({validate: true});

            if (isValid) {
                send_to_editor(this.model.formatShortcode());
                tb_remove();

                this.dispose();

            } else {
                _.each(this.model.validationError, function (error) {
                    _.each(error, function (message, attr) {
                        alert(message);
                    });
                });
            }
        },
        cancelShortcode: function (e) {
            tb_remove();
            this.dispose();
        },
        dispose: function () {
            this.remove();
            $('#gfchart-shortcode-ui-wrap').append('<div id="gfchart-shortcode-ui-container"></div>');
        }
    });

    sui.views.editAttributeField = Backbone.View.extend({

        tagName: "div",

        initialize: function (options) {
            this.parent = options.parent;
        },

        events: {
            'keyup  input[type="text"]': 'updateValue',
            'keyup  textarea': 'updateValue',
            'change select': 'updateValue',
            'change #gfchart-shortcode-attr-action': 'updateAction',
            'change input[type=checkbox]': 'updateCheckbox',
            'change input[type=radio]': 'updateValue',
            'change input[type=email]': 'updateValue',
            'change input[type=number]': 'updateValue',
            'change input[type=date]': 'updateValue',
            'change input[type=url]': 'updateValue',

        },


        render: function () {
            this.template = wp.media.template('gfchart-shortcode-ui-field-' + this.model.get('type'));
            return this.$el.html(this.template(this.model.toJSON()));
        },

        /**
         * Input Changed Update Callback.
         *
         * If the input field that has changed is for content or a valid attribute,
         * then it should update the model.
         */
        updateValue: function (e) {
            var $el = $(e.target);
            this.model.set('value', $el.val());
        },

        updateCheckbox: function (e) {
            var $el = $(e.target);
            var val = $el.prop('checked');

            this.model.set('value', val);
        },

        updateAction: function (e) {
            var $el = $(e.target),
                val = $el.val();

            this.model.set('value', val);
            var m = this.parent.model;
            var newShortcodeModel = sui.shortcodes.findWhere({shortcode_tag: 'gravityform', action_tag: val});

            // copy over values to new shortcode model
            var currentAttrs = m.get('attrs');
            newShortcodeModel.get('attrs').each(function (attr) {
                var newAt = attr.get('attr');
                var currentAtModel = currentAttrs.findWhere({attr: newAt});
                if (typeof currentAtModel != 'undefined') {
                    var currentAt = currentAtModel.get('attr');
                    if (newAt == currentAt) {
                        var currentVal = currentAtModel.get('value');
                        attr.set('value', String(currentVal));
                    }
                }
            });
            $(this.parent.el).empty();
            var viewMode = this.parent.options.viewMode;
            this.parent.dispose();
            this.parent.model.set(newShortcodeModel);
            GFChartShortcodeUI = new sui.views.editShortcodeForm({model: newShortcodeModel, viewMode: viewMode});
            GFChartShortcodeUI.render();

        }

    });

    $(document).ready(function () {

        sui.error_messages = gfchart_shortcode_ui.error_messages;

        sui.shortcodes = new sui.collections.Shortcodes( gfchart_shortcode_ui.shortcodes );

        $(document).on('click', '.gfchart_media_link', function () {

            sui.shortcodes = new sui.collections.Shortcodes(gfchart_shortcode_ui.shortcodes);

			var shortcode = sui.shortcodes.findWhere({shortcode_tag: 'gfchart', action_tag: ''});

			GFChartShortcodeUI = new sui.views.editShortcodeForm({model: shortcode, viewMode: 'insert'});
            GFChartShortcodeUI.render();

			tb_show("Insert Chart/Calculation", "#TB_inline?inlineId=select_gravity_chart&width=500&height=400", "");

		});

    });

}(window.gfcShortcodeUI = window.gfcShortcodeUI || {}, jQuery));


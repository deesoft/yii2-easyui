(function($) {
    /**
     * Returns an array prototype with a shortcut method for adding a new deferred.
     * The context of the callback will be the deferred object so it can be resolved like ```this.resolve()```
     * @returns Array
     */
    var deferredArray = function() {
        var array = [];
        array.add = function(callback) {
            this.push(new $.Deferred(callback));
        };
        return array;
    };

    var oldValidation = $.fn.form.methods['validate'];
    $.extend($.fn.form.methods, {
        validate: function(jq) {
            var opts = $.data(jq[0], 'form').options;
            opts.validationState = 1;
            var result = oldValidation(jq);
            opts.validationState = undefined;
            return result;
        }
    });

    $.extend($.fn.validatebox.defaults.rules, {
        yiivalidation: {
            validator: function(value, param) {
                var $elem = $(this),
                    elemId = $elem.attr('id'),
                    $form = $elem.closest('form'),
                    valid = true,
                    msg = [],
                    defereds = deferredArray();

                if ($form.length) {
                    var formOpts = $form.form('options'),
                        attributes = formOpts.attributes || [];
                    $.each(attributes, function() {
                        if (elemId === this.id) {
                            if (this.validate) {
                                this.validate(this, value, msg, defereds);
                                return false;
                            }
                        }
                        if (this.enableAjaxValidation) {
                            $.ajax({
                            });
                        }
                    });
                    valid = msg.length == 0;
                    if (!valid && param && $.isArray(param)) {
                        param[0] = msg[0];
                    }
                }
                return valid;
            },
            message: 'Field invalid. {0}'
        }
    });
})(jQuery);
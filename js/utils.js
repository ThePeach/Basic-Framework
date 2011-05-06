/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * Utilities
 */
T.utils = (function () {
    /**
     * a form validator, the constructor
     * credit goes to Stoyan Stefanov
     */
    function Validator() {
        // error message in the current validation
        this.messages = [];
        this.config = {};
    }
    /**
     * validates the data to be passed in depending on the configuration
     */
    Validator.prototype = (function () {
        // Validator types of checks
        var types = {};
        /**
         * the data is not empty
         */
        types.isNotEmpty = {
            instructions: 'The value cannot be empty.',
            validate: function (value) {
                return value !== '';
            }
        };
        /**
         * the data is a number
         */
        types.isNumber = {
            instructions: 'The value can only be a number.',
            validate: function (value) {
                return !isNaN(value);
            }
        };
        /**
         * the data is alphanumeric
         */
        types.isAlphaNum = {
            instructions: 'The value can only contain characters and numbers.',
            validate: function (value) {
                return (/^[a-z0-9]+$/i).test(value);
            }
        };        
        /**
         * the data is in mail format
         */
        types.isEmail = {
            instructions: 'The value is not a valid email.',
            validate: function (value) {
                return (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i).test(value);
            }
        };
        /**
         * Validate the data passed in the form of
         * 'data' => 'value' based on the configuration previously set
         */
        function validate(data) {
            var i, msg, type, checker, result_ok;
            this.messages = [];
            for (i in data) {
                if (data.hasOwnProperty(i)) {
                    type = this.config[i];
                    checker = types[type];
                    
                    if (type) {
                        if (!checker) { // problems ahead
                            throw {
                                name: 'ValidationError',
                                message: 'No handler to validate ' + type
                            };
                        }

                        result_ok = checker.validate(data[i]);
                        if (!result_ok) {
                            msg = 'Invalid value for "' + i + '", ' + checker.instructions;
                            this.messages.push(msg);
                        }
                    }
                }
            }
            return !this.hasErrors();
        }
        /**
         * Returns if the validation had any problems
         */
        function hasErrors() {
            return this.messages.length !== 0;
        }
        // expose public api
        return {
            'validate': validate,
            'hasErrors': hasErrors
        };
    }());
    
    // expose API
    return {
        'Validator': Validator
    };
}());

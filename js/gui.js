/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * User Interface handling stuff
 */
T.gui = (function () {
    // box constructor
    function Box(node) {
        this.domNode = node;
        this.mask = $('#screen');
        this.validator = {};
        this.formReq = '';
    }
    // box prototype
    Box.prototype = (function () {
        var animSpeed = 'fast';
        // node position properties
        this.nodeProperties = {};
        // public functions 
        /**
         * Shows the box after greying out the background mask
         */
        function show() {
            var animSpeed = 'fast',
                node = this.domNode;
            this.setPosition();
            
            // TODO clear input fields and error div
            
            this.mask.fadeIn(animSpeed, function () {
                node.slideDown(animSpeed);
            });
        }
        /**
         * Hides the box and then fades out the background mask
         */
        function hide() {
            var mask = this.mask;
            this.domNode.slideUp(animSpeed, function () {
                mask.fadeOut(animSpeed);
            });
        }
        /**
         * displays the error message: if there are any in the validator uses
         * them otherwise uses the one passed as actual parameter
         */
        function displayMsgs(msgs) {
            if (msgs === '' || msgs === undefined) {
                msgs = this.validator.messages;
            } else if (msgs === false) {
                msgs = '';
            }
            var msgBox = this.domNode.children('.error');
            msgBox.hide();
            msgBox.html(msgs.join('<br\>'));
            msgBox.slideDown();
        }

        // private functions

        /**
         * Recalculate the position of the box and applies the style to it
         */
        function setPosition() {
            this.nodeProperties = {
                'top': (this.mask.height() - this.domNode.height()) / 2,
                'left': (this.mask.width() - this.domNode.width()) / 2
            };
            this.domNode.css(this.nodeProperties);
        }
        // expose public methods
        return {
            'show': show,
            'hide': hide,
            'displayMsgs': displayMsgs,
            'setPosition': setPosition
        };
    }());
    
    // expose widgets
    return {
        'Box': Box
    };
}());

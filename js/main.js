/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/* NOTE: probably to be kept in a separate file */
/**
 * User Interface handling stuff
 */
T.gui = (function () {
    // box constructor
    function Box(node) {
//        console.log('box constructor');
        this.domNode = node;
        this.mask = $('#screen');
        this.errorMsg = '';
//        this.setPosition();
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
         * displays the error message
         */
        function displayError(errorMsg) {
            if (errorMsg !== '' || errorMsg !== undefined) {
                this.errorMsg = errorMsg;
            } else if (errorMsg === false) {
                this.errorMsg = '';
            } else {
                return;
            }
            this.domNode.children('.error').html() = this.errorMsg;                
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
            show: show,
            hide: hide,
            displayError: displayError,
            setPosition: setPosition
        };
    }());
    
    // expose widgets
    return {
        'Box': Box
    };
}());

/**
 * Utilities
 */
T.utils = (function () {
    function Validator() {
        
    }
});

/**
 * User
 */
T.user = (function () {
    // private variable definition
    var loginBox = {},
        registerBox = {},
        mainBar = {},
        // import box module
        Box = T.gui.Box;

    /**
     * login function, will perform validation and authentication
     */
    function login() {
    }
//    
//    function logout() {        
//        // unregister user
//    }
////    
//    function register() {
//    }
    
//    function loggedIn() {
//        if (name === null) {
//            return false;
//        }
//        return true;
//    }
    
    function init() {
        // init all the different vars
        mainBar = $('#mainbar');
        loginBox = new Box($('#loginbox'));
        registerBox = new Box($('#registerbox'));
        mainBar.find('a[title="login"]').click(function () { loginBox.show(); });
        mainBar.find('a[title="register"]').click(function () { registerBox.show(); });
        loginBox.domNode.find('.close a').click(function () { loginBox.hide(); });
        registerBox.domNode.find('.close a').click(function () { registerBox.hide(); });
    }
    
    /** expose methods */
    return {
        'init': init
    };
}());

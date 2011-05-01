/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/* NOTE: probably to be kept in a separate file */
T.gui = (function () {
    // box constructor
    function Box(node) {
//        console.log('box constructor');
        this.domNode = node;
        this.mask = $('#screen');
        this.setPosition();
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
            setPosition: setPosition
        };
    }());
    
    // expose widgets
    return {
        'Box': Box
    };
}());
    

// The user
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

T.popupBox = (function() {
    var errorMessage = null,
        top = 0,
        left = 0;
    var show = function(box) {
    }
    var close = function(box) {
        $('#'+box+'box').hide(0, function() {
            $('#screen').hide();
        });
    }
    return {
        'show': 'show',
        'close': 'close'
    }
}());

/**
 * contains the current action: login or register
 */
T.currentAction = null;
/**
 * contains the default mainbar html
 */
T.mainbar = null;
/**
 * this function will popup the login/register box
 */
T.showBox = function(box) {
    var boxID = '#'+box+'box';
    T.currentAction = box;
    // we position the box properly in the middle of the screen
    var left = ( $('#screen').width() / 2 ) - 150;
    var top  = ( $('#screen').height() / 2 ) - ( $(boxID).height() / 2 )
    $(boxID).attr('style','top: '+top+'px; left: '+left+'px;');
    // then we show everything
    $('#screen').show(100, function() {
        $(boxID).show();
    });
    $(boxID).submit(function() {
        var allFine = true;
        var data = 'req='+box;
        // validate fields!
        if ($(boxID+' input[name="pwd"]').val() === '') {
            $(boxID+' input[name="pwd"]').attr('style','border: 1px solid red;');
            // we would like to write some nice error messages in the error box...
            allFine = false;
        } else {
            $(boxID+' input[name="pwd"]').attr('style','border: 0px;');
        }
        if ($(boxID+' input[name="login"]').val() === '') {
            $(boxID+' input[name="login"]').attr('style','border: 1px solid red;');
            allFine = false;
        } else {
            $(boxID+' input[name="login"]').attr('style','border: 0px;');
        }
        // if we are registering ensure a couple more things are ok before submitting
        if (T.currentAction === 'register') {
            if ($(boxID+' input[name="cc"]').val() === ''
                || $(boxID+' input[name="cc"]').val().length != 16) {
                $(boxID+' input[name="cc"]').attr('style','border: 1px solid red;');
                allFine = false;
            } else {
                $(boxID+' input[name="cc"]').attr('style','border: 0px;');
            }
            if ($(boxID+' input[name="name"]').val() === '') {
                $(boxID+' input[name="name"]').attr('style','border: 1px solid red;');
                allFine = false;
            } else {
                $(boxID+' input[name="name"]').attr('style','border: 0px;');
            }
            if ($(boxID+' input[name="pwd2"]').val() === '') {
                $(boxID+' input[name="pwd2"]').attr('style','border: 1px solid red;');
                allFine = false;
            } else {
                $(boxID+' input[name="pwd2"]').attr('style','border: 0px;');
            }
            // passwords mismatch
            if ($(boxID+' input[name="pwd"]').val() !== $(boxID+' input[name="pwd2"]').val()) {
                $(boxID+' input[name="pwd2"]').attr('style','border: 1px solid red;');
                allFine = false;
            }
        }
        // hash the pwd before sending it!!!
        var hashPwd = $.sha1($(boxID+' input[name="pwd"]').val());
        // now compose the post fields to be sent
        data = data+'&login='+$(boxID+' input[name="login"]').val();
        data = data+'&pwd='+hashPwd;
        if (T.currentAction === 'register') {
            data = data+'&user='+$(boxID+' input[name="name"]').val();
            data = data+'&cc='+$(boxID+' input[name="cc"]').val();
        }
        // if everything was fine send the data, otherwise display something
        if (!allFine) {
            $(boxID+' .error').html('Please correct the errors')
        } else {
            $(boxID+' .error').html('');
            // saving what can be saved
            T.user.email = $(boxID+' input[name="login"]').val();
            if (T.currentAction === 'register') {
                T.user.name = $(boxID+' input[name="name"]').val();
                T.user.cc = $(boxID+' input[name="cc"]').val();
            }
            $.post('index.php', data, function(success) {
                // verify the returned json contains no errors otherwise act properly
                if (success.error !== null) {
                    $('#'+T.currentAction+'box .error').html(success.error);
                } else {
                    // saving user and cc
                    if (T.currentAction === 'login') {
                        T.user.name = success.result.name;
                        T.user.cc = success.result.ccnumber;
                    }
                    // replace the mainbar with something more meaningful
                    T.welcomeUser();
                    // remove the popup box
                    T.closeBox(T.currentAction);
                }
            }, 'json');
        }
        return false;
    });
};
/**
 * This function will add a greeting to the user and substitute the
 * content of the mainbar
 */
T.welcomeUser = function() {
    // save content of mainbar
    if (null === T.mainbar) {
        T.mainbar = $('#mainbar').html();
    }
    $('#mainbar li:first').html('Welcome '+T.user.name);
    $('#mainbar li a').html('Logout');
    $('#mainbar li a').attr('title','logout');
    $('#mainbar li a').attr('onClick','javascript:T.logout(); return false;');
}

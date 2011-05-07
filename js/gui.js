/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * User Interface handling stuff
 */
T.gui = (function () { 
    // private vars
    var loginBox = {},
        registerBox = {},
        mainBar = {},
        mainBarContent = {},
        // import validator
        Validator = T.utils.Validator;

    /**
     * Creates a new Box based on a Jquery selected node
     * @constructor
     * 
     * @param {domNode} node the jquery selected node
     */
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
         * 
         * @param {Array} msgs a list of messages to be displayed
         */
        function displayMsgs(msgs) {
            if (msgs === '' || msgs === undefined) {
                msgs = this.validator.messages;
            } else if (msgs === false) {
                msgs = '';
            }
            var msgBox = this.domNode.children('.error');
            msgBox.hide();
            msgBox.html(msgs.join('<br\\>'));
            msgBox.slideDown();
        }

        /**
         * clears the fields and the error messages of the current box
         */
        function clear() {
            this.domNode.children('.error').html('');
            this.domNode.find('input').each(function () {
                var curField = $(this);
                if (curField.attr('type') !== 'submit') {
                    curField.attr('value', '');
                }
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
            'show': show,
            'hide': hide,
            'clear': clear,
            'displayMsgs': displayMsgs,
            'setPosition': setPosition
        };
    }());
    /**
     * Parses and Validates the form and submits the data
     * 
     * @param {Box}      box      a box item containing the form
     * @param {function} callback the function to be called if the parsing is valid
     */
    function parseAndValidateForm(box, callback) {
        var retData = {},
            clearPwd = '';
        box.domNode.find('input').each(function () {
            var curField = $(this);
            if (curField.attr('type') !== 'submit') {
                retData[curField.attr('name')] = curField.attr('value');
            }
        });

        if (box.validator.validate(retData)) {
            // save the clear pwd
            clearPwd = retData.pwd;
            // encode pwd before sending anything
            retData.pwd = $.sha1(retData.pwd);
            // set the request type for the server
            retData.req = box.reqType;
            
            $.post('index.php', retData, function (response) {
                if (!response.result) {
                    box.displayMsgs([response.error]);
                    // FIXME is something missing?
                } else {
                    // add relevant data for the later user init
                    response.result.pwd = clearPwd;
                    response.result.email = retData.login;
                    // call the callback function
                    callback(response.result);
                }
            }, 'json');
        } else {
            // update the error message box
            box.displayMsgs();
        }
    }
    /**
     * sets the mainbar with a welcome user content
     */
    function setUserBar(userName) {
        // save mainbar content
        // FIXME to be moved into the init function
        mainBarContent = mainBar.html();
        mainBar.find('li').hide();
        mainBar.find('ul').prepend('<li class="welcome">Welcome <strong>'+userName+'</strong>.</li>');
        logoutLink = $('<a href="#" title="logout">Logout</a>').click(function () {logout();});
        mainBar.find('ul').append($('<li/>').addClass('logout').append(logoutLink));
    }
    /**
     * login function, will perform validation and authentication
     */
    function login() {
        // parse input fields and validate them
        parseAndValidateForm(loginBox, function (userData) {
            // hide the login box and display the content or fetch it
            loginBox.clear();
            loginBox.hide();
            setUserBar(userData.name);
            T.user.login(userData);
        });
        return false;
    }
    /**
     * logout function, will unset the user and restore the mainbar as it was
     */
    function logout() {
        // restore previous mainbar content
        mainBar.find('li.welcome, li.logout').remove();
        mainBar.find('li').show();
        // ask the user to logout ;)
        T.user.logout();
    }
    /**
     * register function, will perform validation
     * 
     * TODO complete
     */
    function register() {
        // parse input fields and validate them
        parseAndValidateForm(registerBox, function (userData) {
            return false;
        });
        return false;
    }
    /**
     * Initialisation function, performs a basic initialisation of the boxes
     * and their content
     */
    function init() {
        mainBar = $('#mainbar');
        loginBox = new Box($('#loginbox'));
        registerBox = new Box($('#registerbox'));
        
        mainBar.find('a[title="login"]').click(function () {loginBox.show();});
        mainBar.find('a[title="register"]').click(function () {registerBox.show();});
        // loginBox related setup
        loginBox.reqType = 'login';
        loginBox.domNode.find('.close a').click(function () {loginBox.hide();});
        loginBox.validator = new Validator();
        loginBox.validator.config = {
            'login': 'isEmail',
            'pwd': 'isNotEmpty'
        };
        loginBox.domNode.children('form').submit(login);
        // registerBox related setup
        registerBox.reqType = 'register';
        registerBox.domNode.find('.close a').click(function () {registerBox.hide();});
        registerBox.validator = new Validator();
        registerBox.validator.config = {
            'name': 'isNotEmpty',
            'email': 'isEmail',
            'pwd': 'isNotEmpty',
            'pwd2': 'isNotEmpty'
        };
        registerBox.domNode.children('form').submit(register);
    }
    
    // expose widgets
    return {
        'init': init,
        'Box': Box
    };
}());

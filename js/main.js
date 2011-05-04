/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * User
 */
T.user = (function () {
    // private variable definition
    var loginBox = {},
        registerBox = {},
        mainBar = {},
        // import box module
        Box = T.gui.Box,
        // import validator
        Validator = T.utils.Validator;
    
    /**
     * Parses and Validates the form and submits the data
     * 
     * TODO: this has to moved off here
     */
    function parseAndValidateForm(domNode, box) {
        var retData = {};
        $(domNode).find('input').each(function () {
            var curField = $(this);
            if (curField.attr('type') !== 'submit') {
                retData[curField.attr('name')] = curField.attr('value');
            }
        });

        if (box.validator.validate(retData)) {
            console.log('we can now fetch the data and return');
        } else {
            // update the error message box
            box.displayMsgs();
        }
        return retData;
    }
    /**
     * login function, will perform validation and authentication
     */
    function login() {
        // parse input fields and validate them
        parseAndValidateForm(this, loginBox);
        return false;
    }
//    
//    function logout() {        
//        // unregister user
//    }
    /**
     * register function, will perform validation
     */
    function register() {
        return false;
    }
    
//    function loggedIn() {
//        if (name === null) {
//            return false;
//        }
//        return true;
//    }
    
    function init() {
        mainBar = $('#mainbar');
        loginBox = new Box($('#loginbox'));
        registerBox = new Box($('#registerbox'));
        
        mainBar.find('a[title="login"]').click(function () {loginBox.show();});
        mainBar.find('a[title="register"]').click(function () {registerBox.show();});
        // loginBox related setup
        loginBox.domNode.find('.close a').click(function () {loginBox.hide();});
        loginBox.validator = new Validator();
        loginBox.validator.config = {
            'login': 'isEmail',
            'pwd': 'isNotEmpty'
        };
        loginBox.domNode.children('form').submit(login);
        // registerBox related setup
        registerBox.domNode.find('.close a').click(function () {registerBox.hide();});
        registerBox.domNode.children('form').submit(register);
    }
    
    /** expose methods */
    return {
        'init': init
    };
}());

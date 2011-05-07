/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * User
 */
T.user = (function () {
    var name,
        pwd,
        email;
        
    /**
     * unregisters the user
     */
    function logout() {        
        name = '';
        pwd = '';
        email = '';
    }
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
    /**
     * login function, will perform validation and authentication
     */    
    function login(userData) {
        name = userData.name;
        pwd = userData.pwd;
        email = userData.email;
    }
    
    /** expose methods */
    return {
        'login': login,
        'logout': logout
    };
}());

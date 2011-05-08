/*global $ */
// setup our own namespace to store stuff in
var T = T || {};

/**
 * The User
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
     * login function, will perform validation and authentication
     * 
     * @param {Object} userData the user data to be saved internally
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

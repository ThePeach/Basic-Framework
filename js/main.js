// setup our own namespace to store stuff in
var T = {};
T.user = {};
T.user.name = null;
T.user.email = null;

T.showBox = function(box) {
    // we position the login box properly in the middle of the screen
    // then we show everything
    $('#screen').show(100, function() {
        $('#'+box+'box').show();
    });
};
T.closeBox = function(box) {
    $('#'+box+'box').hide(0, function() {
        $('#screen').hide();
    });
};
T.sendForm = function(form) {
    // sends the form as we want it via ajax to the backend
    console.log(form);
};
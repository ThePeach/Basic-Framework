// setup our own namespace to store stuff in
var T = {};
T.user = {};
T.user.name = null;
T.user.email = null;
T.user.cc   = null;
T.currentAction = null;
T.mainbar = null;

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
        if (box === 'register') {
            if ($(boxID+' input[name="cc"]').val() === '') {
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
        if (box === 'register') {
            data = data+'&user='+$(boxID+' input[name="name"]').val();
            data = data+'&cc='+$(boxID+' input[name="cc"]').val();
        }
        // if everything was fine send the data, otherwise display something
        if (!allFine) {
            $(boxID+' .error').html('Please correct the errors')
        } else {
            $(boxID+' .error').html('');
            T.user.email = $(boxID+' input[name="login"]').val();
            $.post('index.php', data, function(success) {
                // replace the mainbar with something more meaningful
                T.user.name = success.result.name;
                T.user.cc = success.result.ccnumber;
                T.welcomeUser();
                // remove the popup box
                T.closeBox(T.currentAction);
            }, 'json');
        }
        return false;
    });
};
T.closeBox = function(box) {
    $('#'+box+'box').hide(0, function() {
        $('#screen').hide();
    });
};
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
T.logout = function() {
    // unregister user
    T.user.name = null;
    T.user.cc = null;
    $('#mainbar').html(T.mainbar);
};
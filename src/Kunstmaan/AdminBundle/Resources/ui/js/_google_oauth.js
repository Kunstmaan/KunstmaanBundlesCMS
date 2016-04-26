var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.googleOAuth = (function($, window, undefined) {
    //functions
    var init,
        attachSignin,
        onSignIn,
        onFailure,
        onSignOut;

    var auth2,
        $input;

    init = function(){
        $input = $('#google_id_token');
        if ($input.length) {
            gapi.load('auth2', function() {
                auth2 = gapi.auth2.init({
                    client_id: $input.data('clientid'),
                    scope: 'profile email'
                });

                attachSignin(document.getElementById('app_oauth_signin'));
            });

            $('#app__logout').click(function(e){
                e.preventDefault();
                onSignOut();
                window.location = $(this).attr('href');
            });
        }
    };

    attachSignin = function(element) {
        auth2.attachClickHandler(element, {},
            function(googleUser) {
                onSignIn(googleUser);
            },
            function(error) {
                onFailure();
            });
    };

    onSignIn = function(googleUser){
        var id_token = googleUser.getAuthResponse().id_token;
        var $form = $('#app__login__form');
        var path = $input.data('url');
        $input.val(id_token);
        $form.attr('action', path);
        $form.submit();
    };

    onFailure = function(){
        //TODO: Make error nicer
        alert('It seems that you are not allowed to login with google on this website.');
    };

    onSignOut = function(){
        auth2.signOut();
    };

    return {
        init: init,
        onSignIn: onSignIn
    };

})(jQuery, window);

/**
 * mod_weatherwarnings – jQuery required
 */
$(document).ready(function () {

    var COOKIE = 'ww_closed';
    var TTL    = 3600; // hide for 1 hour after close

    function getCookie(name) {
        var m = document.cookie.match('(?:^|; )' + name + '=([^;]*)');
        return m ? decodeURIComponent(m[1]) : null;
    }

    function setCookie(name, val, seconds) {
        var exp = new Date(Date.now() + seconds * 1000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(val) +
            '; expires=' + exp + '; path=/; SameSite=Lax';
    }

    if (getCookie(COOKIE) === '1') {
        $('#weatherwarnings').addClass('hide');
        return;
    }

    $('#close-warnings').on('click', function () {
        $('#weatherwarnings').slideUp(400, function () {
            $(this).addClass('hide');
        });
        setCookie(COOKIE, '1', TTL);
    });
});

modules.require(['jquery'], function ($) {
    var loader = $('.ajax_lader');
    $(document).ajaxSend(function () {
        loader.show();
    }).ajaxStop(function () {
        loader.hide();
    });
});

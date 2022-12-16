$(document).ready(function () {
    $('.mobile-submenu__button').click(function () {
        console.log(this);
        $(this).toggleClass('mobile-submenu__button_open');
        $(this).siblings('.mobile-submenu').toggleClass('mobile-submenu_open');
    });
});

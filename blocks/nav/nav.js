// $(document).ready(function () {
$(function () {
    // $('.nav__item').has('.submenu').addClass('nav__item_arrow');
    $('.nav__item').mouseenter(function () {
        $(this).find('.submenu').addClass('submenu--open');
        if ($(this).find('.submenu').length) {
            $(this).addClass('nav__item_active');
        }
    });
    $('.nav__item').mouseleave(function () {
        $(this).find('.submenu').removeClass('submenu--open');
        if ($(this).find('.submenu')) {
            $(this).removeClass('nav__item_active');
        }
    });
    $('.nav__item:last-child .nav__link').click(function () {
        $('.top-menu__sub-nav').toggleClass('top-menu__sub-nav--open');
        $('.nav__item:last-child .nav__link').toggleClass('nav__link--open');
    });
});

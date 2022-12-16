$(document).ready(function () {
    $('.mobile-nav__button').click(function () {
        $('.mobile-nav__menu').toggleClass('mobile-nav__menu_open');
    });
    $('.mobile-nav__item').has('.mobile-submenu-category').addClass('mobile-nav__item_arrow');
    $('.mobile-nav__item').has('.mobile-submenu-magazine-newspaper')
        .addClass('mobile-nav__item_arrow');
});

$(document).ready(function () {
    $('.languages__nav').hover(function () {
        $('.languages__selected').toggleClass('languages__selected_hover icons_lang-black_hover');
    });

    $('.languages').hover(function () {
        $('.languages__nav').toggleClass('languages__nav_hover');
    });
});

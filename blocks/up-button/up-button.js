$(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() !== 0) {
            $('.up-button').fadeIn();
        } else {
            $('.up-button').fadeOut();
        }
    });
    $('.up-button').click(function () {
        $('body,html').animate({scrollTop: 0}, 800);
    });

    $(window).scroll(function () {
        if ($(window).scrollTop() === $(document).height() - $(window).height()) {
            $('.up-button').addClass('up-button_end-page');
        } else {
            $('.up-button').removeClass('up-button_end-page');
        }
    });
});

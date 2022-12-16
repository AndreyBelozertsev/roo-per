$(document).ready(function () {
    $('.quiz__box').each(function () {
        var maxPercent = 0;
        $(this).find('.quiz__graf-pic').each(function () {
            if ($(this).data('percent') > maxPercent) {
                maxPercent = $(this).data('percent');
            }
        });
        $(this).find('.quiz__graf-pic').each(function () {
            if ($(this).data('percent') === maxPercent) {
                $(this).removeClass('quiz__graf-pic_gray');
            }
        });
    });
});

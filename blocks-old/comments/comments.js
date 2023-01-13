$(document).ready(function () {
    $(function () {
        $('.comments__add-coment').click(function () {
            $('.comments__add-coment').toggleClass('comments__add-coment_red');
            $('.comments__form').toggleClass('comments__form_open');
        });
    });
});

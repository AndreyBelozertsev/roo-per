$(document).ready(function () {
    $('body').on('change', 'input.load-img-button', function () {
        var loadImg = $(this).closest('.preview-form').find('.load-img');
        var fileInfo = $(this)[0].files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            loadImg[0].src = e.target.result;
        };
        reader.readAsDataURL(fileInfo);
    });
});

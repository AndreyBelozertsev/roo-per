$(document).ready(function () {
    var body = $('body');
    body.on('rcrop-changed rcrop-ready', '#loadImg', function () {
        var wrp = $(this).closest('.preview-form');
        var inputs = {
            x: wrp.find('[id $= StartX]')[0],
            y: wrp.find('[id $= StartY]')[0],
            width: wrp.find('[id $= Width]')[0],
            height: wrp.find('[id $= Height]')[0]
        };
        var values = $(this).rcrop('getValues');
        for (var coord in inputs) {
            if (values[coord] < 0) {
                wrp.siblings('.errors').html(
                    '<div class="alert alert-danger">' + Translator.trans('file.to_small') +
                    '</div>'
                );
            }
            inputs[coord].value = values[coord];
        }
    });
    body.on('change', 'input.load-img-button', function () {
        var wrp = $(this).closest('.preview-form');
        wrp.siblings('.errors').html('');
        var reader = new FileReader();
        reader.onload = function (e) {
            var minWidt = parseInt(wrp.find('.rcrop-wrapper').data('minWidth'), 10);
            var minHeight = parseInt(wrp.find('.rcrop-wrapper').data('minHeight'), 10);
            if (!minWidt || !minHeight) {
                minWidt = 290;
                minHeight = 163;
            }
            wrp.find('.rcrop-wrapper').html(
                '<img id="loadImg" class="preview-image" src="' + e.target.result + '">'
            );
            $('#loadImg').rcrop({
                minSize: [minWidt, minHeight],
                preserveAspectRatio: true,
                grid: true,
                full: true
            });
        };
        reader.readAsDataURL($(this)[0].files[0]);
    });
});

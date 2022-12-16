$(document).ready(function () {
    var body = $('body');
    var $previewsHolder = $('.previews');

    body.on('click', '.btn_add_photo', function () {
        var prototype = $previewsHolder.data('prototype');
        var $questionForms = $('.preview-form');
        var idx = $questionForms.length;
        var $newFrmDiv = $('<div class="new preview-form" data-preview-index="' + idx + '"></div>')
            .append('<i class="file-icon"></i>')
            .append(prototype.replace(/__prototype_photo__/g, idx))
        ;
        $previewsHolder.append($newFrmDiv);
    });

    body.on('click', '.files-upload-cancel', function () {
        $(this).closest('.files-gallery').remove();
    });

    if ($('[name*="attachment"]').length === 0) {
        $('.btn_add_photo').click();
    }

    // change file icons
    $previewsHolder.on('change', 'input[type="file"]', function () {
        var icon = $(this)[0].files[0].name.split('.').pop().toLowerCase();
        $(this).closest('.preview-form').find('.file-icon').attr('class', 'file-icon ' + icon);
    });

    // remove empty, not allowed or to big files
    $('form[name="document_form"]').on('submit', function () {
        var allowSend = true;
        $('.new.preview-form input[type="file"]').each(function () {
            var wrp = $(this).closest('.preview-form');
            var errors = [];
            if ($(this)[0].files.length) {
                if ($(this).attr('accept').indexOf($(this)[0].files[0].type) === -1) {
                    errors.push(Translator.trans('file.wrong_type'));
                }
                if ($(this)[0].files[0].size > $(this).data('max-size')) {
                    errors.push(Translator.trans('file.to_big'));
                }
                if (errors.length) {
                    wrp.append('<div class="alert alert-danger">' + errors.join(', ') + '</div>');
                    $(this).val('');
                    allowSend = false;
                }
            } else {
                wrp.remove();
            }
        });

        return allowSend;
    });
});

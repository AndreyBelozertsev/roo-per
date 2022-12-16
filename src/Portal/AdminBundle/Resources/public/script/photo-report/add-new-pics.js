$(document).ready(function () {
    var $collectionQuestionHolder = $('.previews');
    $('body').on('click', '.btn_add_photo', function () {
        var prototype = $collectionQuestionHolder.data('prototype');
        var index;
        var $questionForms = $('.preview-form');
        if ($questionForms.length) {
            index = $questionForms.eq($questionForms.length - 1).data('previewIndex') + 1;
        } else {
            index = 1;
        }
        var newForm = prototype.replace(/__prototype_photo__/g, index);
        var $newFormDiv = $('<div class="preview-form" data-preview-index="' + index + '"></div>')
            .append('<img  class="load-img preview-image" id="loadImg-' + index + '" />')
            .append(newForm)
        ;
        $collectionQuestionHolder.append($newFormDiv);
    });
    $('body').on('click', '.files-upload-cancel', function () {
        $(this).closest('.files-gallery').remove();
    });
});

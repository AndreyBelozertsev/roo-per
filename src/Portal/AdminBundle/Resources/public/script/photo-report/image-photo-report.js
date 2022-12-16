$(document).ready(function () {
    var fileInput = $('#fileInput');
    var $prototypeHolder = $('.previews');
    var resultFiles = [];

    function dropErrMsg(file, errors) {
        $prototypeHolder.append(
            '<div class="alert alert-warning">' +
            '   <button type="button" class="close" data-dismiss="alert">×</button>' +
                (file ? file.name + '<br>' : '') + errors.join(', ') +
            '</div>'
        );
    }

    function addImgWrp(file) {
        var fileTypes = fileInput.attr('accept').split(',');
        var formWrp = $('.preview-form');
        var index = formWrp.length ? formWrp.eq(formWrp.length - 1).data('previewIndex') + 1 : 0;
        var errors = [];
        if (fileTypes.indexOf(file.type) < 0) {
            errors.push(Translator.trans('file.wrong_type'));
        }
        if (file.size > fileInput.data('maxFileSize')) {
            errors.push(Translator.trans('file.to_big'));
        }
        if (errors.length) {
            dropErrMsg(file, errors);
            return;
        }

        var newForm = $prototypeHolder.data('prototype').replace(/__prototype_photo__/g, index);
        var $newFormDiv = $('<div class="preview-form" data-preview-index="' + index + '"></div>')
            .append('<img class="load-img preview-image" id="loadImg-' + index + '" />')
            .append(newForm);
        $prototypeHolder.append($newFormDiv);
        $newFormDiv.find('input.load-img-button').parent().remove();

        var minWidt = 290;
        var minHeight = 163;
        var imgTeg = $newFormDiv.find('img.load-img');
        var reader = new FileReader();
        reader.onload = function (e) {
            imgTeg[0].src = e.target.result;
            imgTeg[0].onload = function () {
                if (this.width < minWidt || this.height < minHeight) {
                    $newFormDiv.remove();
                    dropErrMsg(file, [Translator.trans('file.to_small')]);
                    return false;
                }
                imgTeg.rcrop({
                    minSize: [minWidt, minHeight],
                    preserveAspectRatio: true,
                    grid: true,
                    full: true
                });
            };
        };
        reader.readAsDataURL(file);
        resultFiles.push(file);
    }

    $('body').on('rcrop-changed rcrop-ready', '.load-img', function () {
        var wrp = $(this).closest('.preview-form');
        var inputs = {
            x: wrp.find('[id $= StartX]')[0],
            y: wrp.find('[id $= StartY]')[0],
            width: wrp.find('[id $= Width]')[0],
            height: wrp.find('[id $= Height]')[0]
        };
        var values = $(this).rcrop('getValues');
        for (var coord in inputs) {
            inputs[coord].value = values[coord];
        }
    });

    var area = $('#drop_area');
    area[0].ondragenter = function (e) {
        e.stopPropagation();
        e.preventDefault();
    };
    area[0].ondragover = function (e) {
        if (!area.hasClass('drag_hover')) {
            area.addClass('drag_hover');
        }
        e.stopPropagation();
        e.preventDefault();
    };
    area[0].ondragleave = function (e) {
        area.removeClass('drag_hover');
        e.stopPropagation();
        e.preventDefault();
    };
    area[0].ondrop = function (e) {
        var files = e.dataTransfer.files;
        fileInput[0].files = files;
        area.removeClass('drag_hover');
        for (var i = 0; i < files.length; i++) {
            addImgWrp(files[i]);
        }
        e.stopPropagation();
        e.preventDefault();
    };

    fileInput.on('change', function () {
        var files = $(this)[0].files;
        for (var i = 0; i < files.length; i++) {
            addImgWrp(files[i]);
        }
    });

    if (fileInput[0].files.length > 0) {
        var files = fileInput[0].files;
        for (var i = 0; i < files.length; i++) {
            addImgWrp(files[i]);
        }
    }

    var formName = 'admin_photo_report';

    $('form[name="' + formName + '"]').on('submit', function (event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);

        // if no image selected
        if (!$('.preview-form').length) {
            dropErrMsg('', [Translator.trans('file.not_null')]);
            return false;
        }

        formData.delete('admin_photo_report[attachments][][file][file]');
        for (var i = 0; i < resultFiles.length; i++) {
            if (resultFiles[i]) {
                formData.append('admin_photo_report[attachments][][file][file]', resultFiles[i]);
            }
        }
        formData.append(
            'admin_photo_report[descriptionUk]',
            CKEDITOR.instances['admin_photo_report_descriptionUk'].getData()
        );
        formData.append(
            'admin_photo_report[descriptionRu]',
            CKEDITOR.instances['admin_photo_report_descriptionRu'].getData()
        );
        formData.append(
            'admin_photo_report[descriptionEn]',
            CKEDITOR.instances['admin_photo_report_descriptionEn'].getData()
        );

        var instanceCode = fileInput.data('instanceCode');
        var id = fileInput.data('id') || 0;
        $.ajax({
            url: Routing.generate('admin_instance_photo_report_edit', {
                id: id, instanceCode: instanceCode
            }),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (responce) {
                if (responce.status === false) {
                    var err = responce.errors;
                    for (var key in err) {
                        if (err.hasOwnProperty(key)) {
                            var field = err[key].field.split('.').slice(-1)[0];
                            $('input[name*="' + field + '"]')
                                .siblings('.errors')
                                .html('<div class="alert alert-danger">' + err[key].message +
                                    '</div>');
                            $(window).scrollTop(0);
                        }
                    }
                } else if (responce.status === true) {
                    window.location.href = responce.route;
                }
            }
        });

        return false;
    });
});

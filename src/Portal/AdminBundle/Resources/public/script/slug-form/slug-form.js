$(document).ready(function () {
    function CopyToClipboard(el) {
        var sel = window.getSelection();
        var range = document.createRange();
        range.selectNodeContents(el);
        sel.removeAllRanges();
        sel.addRange(range);
        document.execCommand('copy');
        sel.removeAllRanges();
    }

    $('#button_copy_ref').on('click', function (e) {
        e.preventDefault();
        var container = $(this).parents('.add-link-container');
        container.siblings('.alert.alert-success').remove();
        var slug = container.find('.add-link-con1').text().trim() + $('.add-link-con2 input').val();
        var alert = '<div class="alert alert-success">' +
            '<button type="button" class="close" data-dismiss="alert">×</button>' +
            'Ссылка скопирована в буфер обмена:<br><span>' + slug + '</span>' +
            '</div>';
        container.after($(alert)).hide().slideDown(200);
        CopyToClipboard(container.siblings('.alert.alert-success').find('span')[0]);
    });
    $('#button_copy_link_id').on('click', function (e) {
        e.preventDefault();
        var container = $(this).parents('.add-link-container');
        container.siblings('.alert.alert-success').remove();
        var link = container.find('.add-link-con1').text().trim();
        var alert = '<div class="alert alert-success">' +
            '<button type="button" class="close" data-dismiss="alert">×</button>' +
            'Ссылка скопирована в буфер обмена:<br><span>' + link + '</span>' +
            '</div>';
        container.after($(alert)).hide().slideDown(200);
        CopyToClipboard(container.siblings('.alert.alert-success').find('span')[0]);
    });

    var changeOnLinkId = function (checkbox, route) {
        var alert = '';
        var container = checkbox.parents('.add-link-container');
        container.siblings('.alert.alert-success').remove();
        $.post(route,
            function (response) {
                if (response.status === true) {
                    alert = '<div class="alert alert-success">' +
                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
                        response.message +
                        '</div>';
                    container.after($(alert)).hide().slideDown(200);
                    if(checkbox[0].checked) {
                        container.find('.add-link-con1').removeClass('hidden-link');
                        container.find('.add-link-con3').removeClass('hidden-link');
                        $('#view_slug').addClass('hidden-link');
                    } else {
                        container.find('.add-link-con1').addClass('hidden-link');
                        container.find('.add-link-con3').addClass('hidden-link');
                        $('#view_slug').removeClass('hidden-link');
                    }
                } else {
                    alert = '<div class="alert alert-success">' +
                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
                        response.message +
                        '</div>';
                    container.after($(alert)).hide().slideDown(200);
                    if (checkbox[0].checked) {
                        checkbox[0].removeAttr("checked")
                    } else {
                        checkbox[0].addAttr("checked")
                    }
                }
            }, 'json'
        );
    };

    $(document).on('change', '#menu_node_form_isLinkOnId', function() {
        var checkbox = $(this);

        var route = Routing.generate('admin_instance_menu_node_checked_on_id',
            { instanceCode:$('#data_menu_node').data('instanceCode'),
                menuNodeId:$('#data_menu_node').data('menuNodeId') } );

        changeOnLinkId(checkbox, route);
    });

    $(document).on('change', '#admin_head_isLinkOnId', function() {
        var checkbox = $(this);

        var route = Routing.generate('admin_instance_head_checked_on_id',
            { instanceCode:$('#data_head').data('instanceCode'),
                id:$('#data_head').data('headId') } );

        changeOnLinkId(checkbox, route);
    });
    $(document).on('change', '#document_form_isLinkOnId', function() {
        var checkbox = $(this);

        var route = Routing.generate('admin_instance_document_checked_on_id',
            { instanceCode:$('#data_document').data('instanceCode'),
                id:$('#data_document').data('documentId') } );

        changeOnLinkId(checkbox, route);
    });
});

function applyWidgetSort(sortOrder, instanceCode) {
    if (sortOrder != '') {
        $.post(Routing.generate('admin_instance_widget_sort', {'instanceCode': instanceCode}), {
                'sort_order': sortOrder,
            }, function (response) {
                if (response.status === true) {
                    adminPage.helper.successAlert(response.message, false, function () {});
                } else {
                    adminPage.helper.errorAlert(response);
                }
            }, 'json'
        );
    } else {
        adminPage.helper.errorAlert({
            message: Translator.trans('widget_to_panel_form.not_changes_for_sort', {}, 'messages')}
                );
    }
}

$(document).ready(function(){
    $('button').on('click', function(){
        var sortOrder = $('input[name=sort_order]').val();
        var instanceCode = $('input[name="instance_code"]').val();
        applyWidgetSort(sortOrder, instanceCode);
    });
});
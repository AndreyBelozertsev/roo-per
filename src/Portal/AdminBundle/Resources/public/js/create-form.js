let pageFormWrp = $('.page-form-wrp');
pageFormWrp.on('submit', 'form', function (e) {
    let submitButton = $('#page_form_submit');
    submitButton.prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_instance_inform_page_create', {
            'instanceCode': $(this).find('[name="instanceCode"]').val()
        }),
        data: $(this).serialize(),
        dataType: 'json',
        success: function (d) {
            if (d.status === false) {
                pageFormWrp.html(d.content);
                submitButton.prop('disabled', false);
            }
            if (d.status === true) {
                submitButton.remove();
                pageFormWrp.find('#page_form_templateId').prop('disabled', true).trigger("chosen:updated");
                pageFormWrp.after(d.content).hide().slideDown();
            }
        }
    });
    e.preventDefault();
});

$(document).ready(function () {
 //   var $collectionUserRoleHolder = $('.user-form-user-roles');
    $('body').on('click', '.add_user_role_link', function () {
        var roleIndex = $('.user-role-form').length;
        $.ajax({
            url: Routing.generate('admin_admin_user_add_new_role_form', {roleIndex: roleIndex}),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    $('.user-form-user-roles').append(response.content);
                } else {
                    adminPage.helper.successAlert(response.message, false, function () {
                        if (response.redirectUrl !== undefined)
                            window.location.href = response.redirectUrl;
                    });
                }

                return false;
            }
        });
        return false;
    });
    $('body').on('click', '.remove-role', function() {
        var currentBtn = this;
        var role2userid = $(currentBtn).closest('.user-role-form').find('.roleToUserId').val();
        if (typeof role2userid != "undefined" && role2userid != "") {
            $.ajax({
                url: Routing.generate('admin_admin_user_remove_role_form_item'),
                data: {id: role2userid},
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.status === true) {
                        $(currentBtn).closest('.user-role-form').remove();
                    } else {
                        adminPage.helper.successAlert(response.message, false, function () {
                            if (response.redirectUrl !== undefined)
                                window.location.href = response.redirectUrl;
                        });
                    }

                    return false;
                }
            });
        } else {
            $(this).closest('.user-role-form').remove();
        }

        return false;
    });
});

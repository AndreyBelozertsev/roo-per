function Helper() {
    this.showNotification = function (message) {
        if (typeof message !== 'undefined') {
            showPopup('notificationPopUp', 'green', message, 3000);
        }
    };

    this.successAlert = function (message, reload, func) {
        if (typeof reload === 'undefined') {
            reload = false;
        }
        if (!message) {
            message = Translator.trans('done');
        }
        BootstrapDialog.alert({
            message: message,
            closable: true,
            callback: function () {
                if (typeof func === 'function') {
                    func();
                }
                if (reload) {
                    location.reload();
                }
            }
        });
        return false;
    };

    this.errorAlert = function (response) {
        BootstrapDialog.alert(response.message, function () {
            if (response.redirectUrl !== undefined && response.redirectUrl !== '') {
                window.location.href = response.redirectUrl;
            } else {
                BootstrapDialog.closeAll();
            }
        });
        return false;
    };
}

function BootstrapRemoveDialog(data) {
    BootstrapDialog.show({
        title: data.title,
        message: Translator.trans('are_you_sure'),
        closable: true,
        buttons: [{
            id: 'btn-save',
            label: data.buttonText || Translator.trans('delete'),
            cssClass: 'btn btn-warning',
            autospin: false,
            action: function (dialog) {
                $.post(data.route, data.params,
                    function (response) {
                        dialog.close();
                        $('.alert.alert-danger').remove();
                        if (response.status === true) {
                            // Success
                            adminPage.helper.successAlert(response.message, false, function () {
                                if (response.redirectUrl !== undefined) {
                                    window.location.href = response.redirectUrl;
                                }
                                if (response.reload) {
                                    location.reload();
                                }
                            });
                        } else {
                            // Error
                            adminPage.helper.errorAlert(response);
                        }
                    }, 'json'
                );
            }
        }, {
            id: 'btn-close',
            label: Translator.trans('cancel'),
            cssClass: 'btn btn-primary',
            autospin: false,
            action: function (dialog) {
                dialog.close();
            }
        }]
    });
}

function User() {
    this.removeItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('users_form.delete_title'),
            route: Routing.generate('admin_admin_user_remove', {userId: itemId}),
            params: {}
        });
    };
}

// function Instance() {
//     this.removeItem = function (itemId) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('instance_form.remove_instance'),
//             route: Routing.generate('admin_admin_instance_remove', {instanceId: itemId}),
//             params: {}
//         });
//     };
// }

// function InstanceCategory() {
//     this.removeItem = function (itemId) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('instance_category_form.remove_instance_category'),
//             route: Routing.generate('admin_admin_instance_category_remove', {
//                 instanceCategoryId: itemId
//             }),
//             params: {}
//         });
//     };
// }

// function Document() {
//     this.removeItem = function (itemId, instanceCode, page) {
//         var params = {
//             id: itemId,
//             instanceCode: instanceCode
//         };
//         if (page) {
//             params.page = page;
//         }
//         BootstrapRemoveDialog({
//             title: Translator.trans('document.delete_title'),
//             route: Routing.generate('admin_instance_document_delete', params),
//             params: {}
//         });
//     };
//     this.restoreItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('document.restore_title'),
//             route: Routing.generate('admin_instance_document_restore', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {},
//             buttonText: Translator.trans('restore')
//         });
//     };
// }

// function InformPage() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('inform_page.delete_title'),
//             route: Routing.generate('admin_instance_inform_page_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function Event() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('event.delete_title'),
//             route: Routing.generate('admin_instance_event_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function Tag() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('tag.delete_title'),
//             route: Routing.generate('admin_instance_tag_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function Category() {
    this.removeItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('article_category.delete_title'),
            route: Routing.generate('admin_admin_category_delete', {
                id: itemId
            }),
            params: {}
        });
    };
}

function PhotoReport() {
    this.removeItem = function (itemId, instanceCode, page) {
        var params = {
            id: itemId,
            instanceCode: instanceCode
        };
        if (page) {
            params.page = page;
        }
        BootstrapRemoveDialog({
            title: Translator.trans('photo_report_form.delete_title'),
            route: Routing.generate('admin_instance_photo_report_delete', params),
            params: {}
        });
    };
    this.restoreItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('photo_report_form.restore_title'),
            route: Routing.generate('admin_instance_photo_report_restore', {
                id: itemId,
                instanceCode: instanceCode
            }),
            params: {},
            buttonText: Translator.trans('restore')
        });
    };
}

// function VideoReport() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('video_report_form.delete_title'),
//             route: Routing.generate('admin_instance_video_report_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function MenuNode() {
    this.removeItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('menu_page.delete_node_title'),
            route: Routing.generate('admin_instance_menu_node_delete', {
                menuNodeId: itemId,
                instanceCode: instanceCode
            }),
            params: {}
        });
    };
}

function Menu() {
    this.removeItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('menu_page.delete_title'),
            route: Routing.generate('admin_instance_menu_delete', {
                menuId: itemId,
                instanceCode: instanceCode
            }),
            params: {}
        });
    };
}

function Article() {
    this.removeItem = function (itemId, cat, page) {
        var params = {
            id: itemId,
            cat: cat
        };
        if (page) {
            params.page = page;
        }
        BootstrapRemoveDialog({
            title: Translator.trans('article_form.delete_title'),
            route: Routing.generate('admin_instance_article_delete', params),
            params: {}
        });
    };
    this.restoreItem = function (itemId, cat) {
        BootstrapRemoveDialog({
            title: Translator.trans('article_form.restore'),
            route: Routing.generate('admin_instance_article_restore', {
                id: itemId,
                cat: cat
            }),
            params: {},
            buttonText: Translator.trans('restore')
        });
    };
}

function SocialNetwork() {
    this.removeItem = function (itemId) {
        var params = {
            id: itemId
        };
        BootstrapRemoveDialog({
            title: Translator.trans('delete'),
            route: Routing.generate('admin_admin_socialnetwork_remove', params),
            params: {}
        });
    };
    this.restoreItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('restore'),
            route: Routing.generate('admin_admin_socialnetwork_restore', {
                id: itemId,
            }),
            params: {},
            buttonText: Translator.trans('restore')
        });
    };
}

// function Head() {
//     this.removeItem = function (itemId, instanceCode, page) {
//         var params = {
//             id: itemId,
//             instanceCode: instanceCode
//         };
//         if (page) {
//             params.page = page;
//         }
//         BootstrapRemoveDialog({
//             title: Translator.trans('head_form.delete_title'),
//             route: Routing.generate('admin_instance_head_form_delete', params),
//             params: {}
//         });
//     };
//     this.restoreItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('head_form.restore_title'),
//             route: Routing.generate('admin_instance_head_form_restore', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {},
//             buttonText: Translator.trans('restore')
//         });
//     };
// }

// function InternetResources() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('internet_resources.delete_title'),
//             route: Routing.generate('admin_instance_internet_resources_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function Attachment() {
    this.removeItem = function (itemIds, instanceCode, route) {
        BootstrapRemoveDialog({
            title: Translator.trans('files.delete_title'),
            route: Routing.generate('admin_instance_delete_file', {
                ids: itemIds,
                instanceCode: instanceCode,
                route: route
            }),
            params: {}
        });
    };
}

// function UserRole() {
//     this.removeItem = function (itemId) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('user_roles_form.delete_title'),
//             route: Routing.generate('admin_admin_role_remove', {id: itemId}),
//             params: {}
//         });
//     };
// }

// function UserPermission() {
//     this.removeItem = function (itemId) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('user_permissions_form.delete_title'),
//             route: Routing.generate('admin_admin_permission_remove', {id: itemId}),
//             params: {}
//         });
//     };
// }

// function Slider() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('slider.delete_title'),
//             route: Routing.generate('admin_instance_slider_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function Banner() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('banner.delete_title'),
//             route: Routing.generate('admin_instance_banner_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function WidgetToPanel() {
    this.removeItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('widget_to_panel_form.modal_message_delete'),
            route: Routing.generate('admin_instance_widget_form_delete', {
                id: itemId,
                instanceCode: instanceCode
            }),
            params: {}
        });
    };
}

// function Interview() {
//     this.removeItem = function (itemId, instanceCode, page) {
//         var params = {
//             id: itemId,
//             instanceCode: instanceCode
//         };
//         if (page) {
//             params.page = page;
//         }
//         BootstrapRemoveDialog({
//             title: Translator.trans('interview_form.modal_message_delete'),
//             route: Routing.generate('admin_instance_interview_form_delete', params),
//             params: {}
//         });
//     };
//     this.restoreItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('interview_form.restore_title'),
//             route: Routing.generate('admin_instance_interview_form_restore', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {},
//             buttonText: Translator.trans('restore')
//         });
//     };
// }

// function Quiz() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('quiz_form.modal_message_delete'),
//             route: Routing.generate('admin_instance_quiz_form_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function FeedbackForm() {
    this.removeItem = function (itemId, instanceCode) {
        BootstrapRemoveDialog({
            title: Translator.trans('feedback_form.delete_title'),
            route: Routing.generate('admin_instance_feedback_form_delete', {
                id: itemId,
                instanceCode: instanceCode
            }),
            params: {}
        });
    };
}

// function Structure() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('menu_page.delete_node_title'),
//             route: Routing.generate('admin_instance_structure_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function FeedbackCategory() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('feedback_category_form.delete_title'),
//             route: Routing.generate('admin_instance_feedback_category_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function DocumentTag() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('tag.delete_title'),
//             route: Routing.generate('admin_instance_doc_tag_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

// function Material() {
//     this.removeItem = function (itemId, instanceCode) {
//         BootstrapRemoveDialog({
//             title: Translator.trans('material.delete_title'),
//             route: Routing.generate('admin_instance_material_delete', {
//                 id: itemId,
//                 instanceCode: instanceCode
//             }),
//             params: {}
//         });
//     };
// }

function Comment() {
    this.removeItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('comment_form.delete_title'),
            route: Routing.generate('admin_admin_comment_remove', {
                id: itemId
            }),
            params: {}
        });
    };
}

function Post() {
    this.removeItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('post_form.delete_title'),
            route: Routing.generate('admin_instance_post_delete', {
                id: itemId
            }),
            params: {}
        });
    };
    this.restoreItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('post_form.restore'),
            route: Routing.generate('admin_instance_post_restore', {
                id: itemId
            }),
            params: {},
            buttonText: Translator.trans('restore')
        });
    };
}

function MagazineNewspaper() {
    this.removeItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('magazine_newspaper_form.delete_title'),
            route: Routing.generate('admin_instance_magazine_newspaper_delete', {
                id: itemId
            }),
            params: {}
        });
    };
    this.restoreItem = function (itemId) {
        BootstrapRemoveDialog({
            title: Translator.trans('magazine_newspaper_form.restore'),
            route: Routing.generate('admin_instance_magazine_newspaper_restore', {
                id: itemId
            }),
            params: {},
            buttonText: Translator.trans('restore')
        });
    };
}

function AdminPage() {
    this.helper = new Helper();
    this.photoReport = new PhotoReport();
    this.menuNode = new MenuNode();
    this.menu = new Menu();
    this.article = new Article();
    this.attachment = new Attachment();
    this.widgetToPanel = new WidgetToPanel();
    this.feedbackForm = new FeedbackForm();
    this.comment = new Comment();
    this.user = new User();
    this.socialNetwork = new SocialNetwork();
    // this.instance = new Instance();
    this.category = new Category();
    this.post = new Post();
    this.magazineNewspaper = new MagazineNewspaper();
    // this.instanceCategory = new InstanceCategory();
    // this.document = new Document();
    // this.informPage = new InformPage();
    // this.documentCategory = new DocumentCategory();
    // this.videoReport = new VideoReport();
    // this.event = new Event();
    // this.tag = new Tag();
    // this.head = new Head();
    // this.internetResources = new InternetResources();
    // this.userRole = new UserRole();
    // this.userPermission = new UserPermission();
    // this.slider = new Slider();
    // this.banner = new Banner();
    // this.interview = new Interview();
    // this.quiz = new Quiz();
    // this.structure = new Structure();
    // this.feedbackCategory = new FeedbackCategory();
    // this.documentTag = new DocumentTag();
    // this.material = new Material();
}

// global object
var adminPage;

$(function () {
    // init global object
    adminPage = new AdminPage();
});

/*global adminPage, BootstrapDialog*/
/*eslint camelcase: [2, {properties: "never"}]*/

// conditional select
(function ($) {
    'use strict';
    $.jstree.defaults.conditionalselect = function () {
        return true;
    };
    $.jstree.plugins.conditionalselect = function (options, parent) {
        this.activate_node = function (obj, e) {
            if (this.settings.conditionalselect.call(this, this.get_node(obj))) {
                parent.activate_node.call(this, obj, e);
            } else {
                $('#structures').jstree('deselect_all');
                $('#structure_content, #goods, #button_menu_node').html('');
            }
        };
    };
})($);

(function () {
// Application module
    var instanceCode = $('#instanceCode').val();
    var app = (function ($) {
        var app = {
            // Initialize the required variables
            moveStructure: Routing.generate(
                'admin_instance_structure_move', {instanceCode: instanceCode}
            ),
            renameStructure: Routing.generate(
                'admin_instance_structure_rename', {instanceCode: instanceCode}
            ),
            ajaxUrl: Routing.generate(
                'admin_instance_structure_json', {instanceCode: instanceCode}
            ),
            ui: {
                $structures: $('#structures'),
                $goods: $('#goods')
            },

            // Initializing the structure tree with jstree
            _initTree: function (data, selectedNode) {
                app.ui.$structures.jstree({
                    core: {
                        check_callback: true,
                        multiple: false,
                        data: data,
                        dblclick_toggle: false
                    },
                    conditionalselect: function (node) {
                        return !node.state.selected;
                    },
                    plugins: ['dnd', 'conditionalselect']
                }).bind('select_node.jstree', function (e, data) {
                    getAjaxContent(data);
                    if ($('#ref').val() === '1') {
                        location.hash = 'goods';
                    }
                    app.ui.$goods.html('<span>' + Translator.trans('bloc_structure.selected') +
                        '</span> ' + data.node.text);
                }).bind('move_node.jstree', function (e, data) {
                    var currentNode = (data.parent === '#') ? data.node.id : data.parent;
                    $(this).jstree('open_node', currentNode, function () {
                        var myParam = {
                            parent: data.parent,
                            tree: $('#' + data.node.id).parent().find('>li').map(function () {
                                return this.id;
                            }).get()
                        };
                        app._moveStructure(myParam);
                    });
                }).bind('rename_node.jstree', function (e, data) {
                    var params = {
                        id: Number(data.node.id),
                        oldName: data.old,
                        newName: data.text
                    };
                    app._renameStructure(params);
                }).bind('ready.jstree', function () {
                    $(this).jstree('select_node', selectedNode);
                });
            },

            // Moving structure
            _moveStructure: function (params) {
                $.ajax({
                    url: app.moveStructure,
                    data: params,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === true) {
                            adminPage.helper.successAlert(response.message, false, function () {
                                if (response.redirectUrl !== undefined) {
                                    window.location.href = response.redirectUrl;
                                }
                            });
                        } else {
                            adminPage.helper.successAlert(response.message, false, function () {
                                if (response.redirectUrl !== undefined) {
                                    window.location.href = response.redirectUrl;
                                }
                            });
                        }
                    }
                });
            },

            _renameStructure: function (params) {
                $.ajax({
                    url: app.renameStructure,
                    data: params,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === false) {
                            adminPage.helper.successAlert(response.message, false, function () {
                                if (response.redirectUrl !== undefined) {
                                    window.location.href = response.redirectUrl;
                                }
                            });
                        }
                    }
                });
            },

            // Loading categories from the server
            _loadData: function (selectedNode) {
                $.ajax({
                    url: app.ajaxUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        // Initializing the Tree of Structures
                        if (response.status === true) {
                            var selNode = selectedNode ||
                                getUrlParam('selected_node', window.location.search);
                            app._initTree(response.content, selNode);
                        } else {
                            adminPage.helper.successAlert(response.message, false, function () {
                                if (response.redirectUrl !== undefined) {
                                    window.location.href = response.redirectUrl;
                                }
                            });
                        }
                    }
                });
            },

            structureDelete: function () {
                var tree = app.ui.$structures.jstree(true);
                var sel = tree.get_selected();
                var node = tree.get_node(sel);
                if (!sel.length) {
                    return false;
                }
                BootstrapDialog.show({
                    title: Translator.trans('structure_form.delete_title'),
                    message: Translator.trans('are_you_sure'),
                    closable: true,
                    buttons: [{
                        id: 'btn-save',
                        label: Translator.trans('delete'),
                        cssClass: 'btn btn-warning',
                        autospin: false,
                        action: function (dialog) {
                            $.post(
                                Routing.generate(
                                    'admin_instance_structure_delete',
                                    {instanceCode: instanceCode}
                                ),
                                node,
                                function (response) {
                                    dialog.close();
                                    if (response.status === true) {
                                        // Success
                                        tree.destroy();
                                        app._loadData(tree.get_parent(node));
                                    }
                                    adminPage.helper.successAlert(response.message);
                                },
                                'json'
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
            },

            structureCreate: function () {
                $.post(
                    Routing.generate('admin_instance_structure_new', {instanceCode: instanceCode}),
                    {},
                    function (response) {
                        if (response.status === true) {
                            // Success
                            BootstrapDialog.show({
                                title: Translator.trans('structure_form.action_create'),
                                message: response.content,
                                closable: true,
                                nl2br: false,
                                buttons: [{
                                    id: 'btn-save',
                                    label: Translator.trans('save'),
                                    cssClass: 'btn btn-warning',
                                    autospin: false,
                                    action: function () {
                                        $('form[name="structure"]').trigger('submit');
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
                    },
                    'json'
                );
            },

            structureRename: function () {
                var tree = app.ui.$structures.jstree(true);
                var sel = tree.get_selected();
                if (!sel.length) {
                    return false;
                }
                tree.edit(sel[0]);
            },

            structureRestore: function (nodeId) {
                BootstrapDialog.show({
                    title: Translator.trans('structure_form.restore_title'),
                    message: Translator.trans('are_you_sure'),
                    closable: true,
                    buttons: [{
                        id: 'btn-save',
                        label: Translator.trans('restore'),
                        cssClass: 'btn btn-warning',
                        action: function (dialog) {
                            dialog.close();
                            $.post(
                                Routing.generate(
                                    'admin_instance_structure_restore',
                                    {instanceCode: instanceCode}
                                ),
                                {nodeId: nodeId},
                                function (response) {
                                    if (response.status === true) {
                                        var tree = app.ui.$structures.jstree(true);
                                        tree.destroy();
                                        app._loadData(nodeId);
                                    }
                                    adminPage.helper.successAlert(response.message);
                                }
                            );
                        }
                    }, {
                        id: 'btn-close',
                        label: Translator.trans('cancel'),
                        cssClass: 'btn btn-primary',
                        action: function (dialog) {
                            dialog.close();
                        }
                    }]
                });
            },

            // Initializing the application
            init: function (selectedNode) {
                app._loadData(selectedNode);
            }
        };
        // export outside
        return app;
    })($);

    function replaceUrlParam(param, newval, search) {
        var regex = new RegExp('([?;&])' + param + '[^&;]*[;&]?');
        var query = search.replace(regex, '$1').replace(/&$/, '');

        return (query.length > 2 ? query + '&' : '?') + (newval ? param + '=' + newval : '');
    }

    function getUrlParam(param, search) {
        var regex = new RegExp(param + '=([^&#=]*)');

        return regex.test(search) ? search.match(regex)[1] : 0;
    }

    function getAjaxContent(data) {
        var content = $('#structure_content');
        var pageHistory = JSON.parse(localStorage.getItem('pageHistory')) || {};
        var id = data.node.id;
        content.html('');
        $.post(Routing.generate('admin_instance_structure_content', {
            instanceCode: instanceCode,
            id: id,
            page: pageHistory[id] || null
        }), function (response) {
            if (response.status === true) {
                // show next node content
                content.html(response.content);

                // show additional buttons
                $('#button_menu_node').html(response.buttons);

                // change URL parameters by select tree node
                window.history.replaceState({}, '', window.location.pathname +
                    replaceUrlParam('selected_node', id, window.location.search)
                );
            }
        }, 'json');
    }

    $(document).ready(app.init());

    var body = $('body');

    body.on('submit', 'form[name="structure"]', function (e) {
        e.preventDefault();
        var tree = app.ui.$structures.jstree(true);
        var parent = Number(tree.get_selected());
        $(this).find('#structure_parent').val(parent);
        $.post(
            Routing.generate('admin_instance_structure_new', {instanceCode: instanceCode}),
            $(this).serialize(),
            function (response) {
                if (response.status === true) {
                    $('.bootstrap-dialog').modal('hide');
                    tree.destroy();
                    app.init(response.selected_node);
                    adminPage.helper.successAlert(response.message, false, function () {
                        if (response.redirectUrl !== undefined) {
                            window.location.href = response.redirectUrl;
                        }
                    });
                } else {
                    $('form[name="structure"]').html(response.content);
                }
            },
            'json'
        );
    });

    body.on('click', '.restore_btn', function () {
        app.structureRestore($(this).data('id'));
    });

    body.on('click', '.pagerfanta a', function (e) {
        e.preventDefault();
        $.post($(this).attr('href'), function (response) {
            if (response.status === true) {
                // show next page content
                $('#structure_content').html(response.content);

                // save last shown page
                var id = getUrlParam('id', this.url);
                var pageHistory = JSON.parse(localStorage.getItem('pageHistory')) || {};
                pageHistory[id] = getUrlParam('page', this.url);
                localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
            }
        }, 'json');
    });

    $('#structureCreateBtn').on('click', function () {
        app.structureCreate();
    });
    $('#structureRenameBtn').on('click', function () {
        app.structureRename();
    });
    $('#structureDeleteBtn').on('click', function () {
        app.structureDelete();
    });
})();

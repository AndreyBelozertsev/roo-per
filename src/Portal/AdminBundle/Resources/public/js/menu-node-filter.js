$('#filterBtn').on('click', function () {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterTitle = $('#filterTitle').val();
    document.location.href = Routing.generate('admin_instance_menu_node_viewall', arrData);
});

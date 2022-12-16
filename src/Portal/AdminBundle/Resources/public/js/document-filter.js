$('#filterArticle').on('click', function () {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterTitle = $('#filterTitle').val();
    arrData.filterDocType = $('#filterDocType').val();
    arrData.filterPublishFrom = $('#filterPublishFrom').val();
    arrData.filterPublishTo = $('#filterPublishTo').val();
    arrData.filterApprovFrom = $('#filterApprovFrom').val();
    arrData.filterApprovTo = $('#filterApprovTo').val();
    arrData.filterPublished = $('#filterPublished')[0].checked;
    arrData.filterNotPublished = $('#filterNotPublished')[0].checked;

    document.location.href = Routing.generate('admin_instance_document_view_all', arrData);
});

$('#filterArticle').on('click', function () {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterTitle = $('#filterTitle').val();
    arrData.filterCreateFrom = $('#filterCreateFrom').val();
    arrData.filterCreateTo = $('#filterCreateTo').val();
    arrData.filterPublishFrom = $('#filterPublishFrom').val();
    arrData.filterPublishTo = $('#filterPublishTo').val();
    arrData.filterPublished = $('#filterPublished')[0].checked;
    arrData.filterNotPublished = $('#filterNotPublished')[0].checked;

    document.location.href = Routing.generate('admin_instance_article_list', arrData);
});

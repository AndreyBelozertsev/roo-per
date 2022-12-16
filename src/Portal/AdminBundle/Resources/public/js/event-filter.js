$('#filterArticle').on('click', function () {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterPublished = $('#filterPublished')[0].checked;
    arrData.filterNotPublished = $('#filterNotPublished')[0].checked;

    document.location.href = Routing.generate('admin_instance_event_index', arrData);
});

$('#filterArticle').on('click', function () {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterTitle = $('#filterTitle').val();
    arrData.filterDateStartFrom = $('#filterDateStartFrom').val();
    arrData.filterDateStartTo = $('#filterDateStartTo').val();
    arrData.filterDateEndFrom = $('#filterDateEndFrom').val();
    arrData.filterDateEndTo = $('#filterDateEndTo').val();
    arrData.filterPublished = $('#filterPublished')[0].checked;
    arrData.filterNotPublished = $('#filterNotPublished')[0].checked;
    arrData.filterIsDeleted = $('#filterIsDeleted')[0].checked;

    document.location.href = Routing.generate('admin_instance_interview_list', arrData);
});

function removeAttachment(instanceCode, route) {
    var arrId = $.map( $(":checkbox:checked"), function(el){ return $(el).val(); });
    if(arrId.length) {

        adminPage.attachment.removeItem(arrId, instanceCode, route);
    }
}
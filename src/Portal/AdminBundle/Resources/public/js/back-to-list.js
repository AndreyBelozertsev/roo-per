$(function () {
    var listPageHistory = JSON.parse(localStorage.getItem('listPageHistory')) || {};
    var controller = $('.content').data('action').split('::')[0];
    var paginatePage = parseInt($('.pagerfanta .pagination .active').text(), 10) || 0;

    // save last shown paginate page
    if (paginatePage) {
        if (paginatePage === 1) {
            delete listPageHistory[controller];
        } else {
            listPageHistory[controller] = paginatePage;
        }
        localStorage.setItem('listPageHistory', JSON.stringify(listPageHistory));
    }

    // only for edit page
    var entity = $('form').attr('name');
    if (entity) {
        // modify url for back to list button
        if (listPageHistory[controller]) {
            var linkTag = $('.back-to-list');
            var link = linkTag.attr('href');
            linkTag.attr('href', link + '?page=' + listPageHistory[controller]);
        }

        // modify page parameter for delete button
        var btn = $('.soft-delete-btn');
        var url = btn.attr('onclick');
        var regex = /\(([\s\S]+?)\)/;
        if (regex.test(url)) {
            var subStr = url.match(regex)[1];
            var page = listPageHistory[controller] || 1;
            var newStr = url.replace(regex, '(' + subStr + ', ' + page + ')');
            btn.attr('onclick', newStr);
        }
    }
});

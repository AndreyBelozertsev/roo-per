// category tab buttons
$(function () {
    $('ul.tabs__caption').on('click', 'li:not(.active)', function () {
        $(this).addClass('active').siblings().removeClass('active');

        var category = $(this).data('category');
        var tabs = $(this).closest('div.tabs').find('div.tabs__content');
        var targetTab = tabs.eq($(this).index());
        var targetDiv = targetTab.find('.news');
        var url = $(this).parent('ul').data('url');

        tabs.removeClass('active');
        targetTab.addClass('active');
        if (!targetDiv.children().length) {
            $.post(url, {
                cat: category,
                page: 0
            }, function (data) {
                targetDiv.html(data.list);
            });
        }
    });
});

// show more button
$(function () {
    $('.tabs').on('click', '.get_more_article', function () {
        var targetDiv = $(this).closest('.tabs__content').find('.news');
        var btn = $(this);
        var url = btn.closest('.tabs').find('ul.tabs__caption').data('url');
        var page = btn.data('page');

        $.post(url, {
            cat: $(this).data('cat'),
            page: page
        }, function (data) {
            targetDiv.append(data.list);
            if (data.hideButton) {
                btn.hide();
            } else {
                btn.data('page', page + 1);
            }
        });
    });
});

modules.require(['jquery'], function ($) {
    $(document).ready(function () {
        $('body').on('click', '.get-more-posts', function () {
            var $buttom = $(this);

            $.ajax({
                url: Routing.generate('get_more_posts'),
                type: 'POST',
                dataType: 'json',
                data: {page: $buttom.attr('data-page')},
                success: function (response) {
                    $buttom.attr('data-page', response.page);
                    if (response.hideButton) {
                        $buttom.hide();
                    }
                    $('.pagination').empty().append(response.pagination);
                    $('.content_catalog').append(response.postList);

                    var searchParams = new URLSearchParams(window.location.search);
                    searchParams.set('page', response.page);
                    history.pushState({}, null, location.pathname + '?' + searchParams.toString());
                }
            });
        });
    });
});

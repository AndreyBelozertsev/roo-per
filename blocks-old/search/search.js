$(function () {
    $('.search__icon_open').on('click', function () {
        $(this)
            .toggleClass('search__icon_close')
            .closest('.header').toggleClass('header_search-open')
            .closest('.page').toggleClass('page_search-open')
            .find('.search_header')
            .toggleClass('search_header_open');
    });
    var url = $('input.search__pole').data('url');
    function search(str) {
        if (str.length) {
            window.location.href = url + '?str=' + str;
        }
    }
    $('input.search__icon_search').on('click', function () {
        search($(this).siblings('input.search__pole').val().trim());
    });
    $(document).on('keyup', function (e) {
        if (e.which === 13) {
            search($('input.search__pole:focus').val().trim());
        }
    });
    $(document).on('keyup', function (e) {
        if (e.key === 'Escape' && $('.search_header_open').length) {
            $('.search__icon_open').click();
        }
    });
    $('.get-more-search-results').on('click', function () {
        var btn = $(this);
        $.ajax({
            url: btn.data('url'),
            type: 'POST',
            dataType: 'json',
            data: {
                page: btn.attr('data-page'),
                str: btn.attr('data-str')
            },
            success: function (response) {
                btn.attr('data-page', response.page);
                if (response.hideButton) {
                    btn.hide();
                }
                $('.pagination').empty().append(response.pagination);
                $('.content_catalog').append(response.list);
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.set('page', response.page);
                searchParams.set('str', btn.attr('data-str'));
                history.pushState({}, null, location.pathname + '?' + searchParams.toString());
            }
        });
    });
});

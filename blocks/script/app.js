document.addEventListener('DOMContentLoaded', function () {
    var burger = document.querySelector('.sidebar-wrap__menu');
    var nav = document.querySelector('.menu');
    var body = document;
    var searchBtn = document.querySelector('.sidebar-wrap__search-btn');
    var search = document.querySelector('.search');

    if (burger) {
        burger.addEventListener('click', function () {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
            searchBtn.classList.remove('active');
            search.classList.remove('active');
            body.classList.toggle('lock');
            $('html').removeClass('lock');
        });
    }

    //var closeSearch = document.querySelector('.search-wrap__container-btn');

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            searchBtn.classList.toggle('active');
            search.classList.toggle('active');
            burger.classList.remove('active');
            nav.classList.remove('active');
            body.classList.remove('lock');
            $('html').toggleClass('lock');
        });
    }

    // closeSearch.addEventListener('click', () => {
    //     search.classList.remove('active');
    //     searchBtn.classList.toggle('active');
    //     $('html').toggleClass('lock');
    // });

    function Select() {
        var select = document.querySelector('.sidebar-wrap__lang');
        //var selected = document.querySelector('.selected');
        var menu = document.querySelector('.sidebar-wrap__lang-switch');
        //var option = document.querySelectorAll('.option');

        select.addEventListener('click', function () {
            select.classList.toggle('active');
            menu.classList.toggle('active');
        });

        // option.forEach(o => {
        //     o.addEventListener('click', () => {
        //         selected.innerHTML = o.innerHTML;
        //         select.classList.remove('active');
        //         menu.classList.remove('active');
        //         option.forEach(o => {
        //             o.classList.remove('active')
        //         });
        //         option.classList.add('active');
        //     });
        // });
    }

    Select();
});

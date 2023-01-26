document.addEventListener('DOMContentLoaded', function () {
    let burger = document.querySelector('.sidebar-wrap__menu');
    let nav = document.querySelector('.menu');
    let {body} = document;

    if (burger) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
            searchBtn.classList.remove('active');
            search.classList.remove('active');
            body.classList.toggle('lock');
            $('html').removeClass('lock');
        });
    };

    let searchBtn = document.querySelector('.sidebar-wrap__search-btn');
    let search = document.querySelector('.search');
    //let closeSearch = document.querySelector('.search-wrap__container-btn');

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            searchBtn.classList.toggle('active');
            search.classList.toggle('active');
            burger.classList.remove('active');
            nav.classList.remove('active');
            body.classList.remove('lock');
            $('html').toggleClass('lock');
        });
    };
    
    // closeSearch.addEventListener('click', () => {
    //     search.classList.remove('active');
    //     searchBtn.classList.toggle('active');
    //     $('html').toggleClass('lock');
    // });

    function Select() {
        const select = document.querySelector('.sidebar-wrap__lang');
        const selected = document.querySelector('.selected')
        const menu = document.querySelector('.sidebar-wrap__lang-switch');
        const option = document.querySelectorAll('.option')
    
        select.addEventListener('click', () => {
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
    };
    
    Select();
});

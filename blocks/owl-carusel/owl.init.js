$(document).ready(function () {
    $('.owl-carousel').owlCarousel({
        loop: true,
        dots: false,
        nav: true,
        margin: 15,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: true,
                loop: false
                // margin: 0
            },
            935: {
                items: 2,
                nav: true,
                loop: false
                // margin: 0
            },
            1140: {
                items: 3,
                nav: true,
                loop: false
                // margin: 0
            }
        }
    });
});

$(document).ready(function () {
    $('.gallery-wrap').slick({
        dots: false,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear'
    });

    $('.category-wrap__carousel').slick({
        infinite: false,
        slidesToShow: 5.5,
        slidesToScroll: 2,
        arrows: false,
        dots: true,
        responsive: [{
            breakpoint: 1366,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 2,
            }
        }, {
            breakpoint: 1024,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 2,
            }
        },
            {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            }
        }, {
            breakpoint: 460,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            }
        }, {
            breakpoint: 320,
            settings: {
                slidesToShow: 1.5,
                slidesToScroll: 2,
            }
        }
        ]
    });

})  

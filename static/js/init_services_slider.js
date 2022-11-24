$(function(){
    if ($(window).width() < 768){
        $('.services_wrapper-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: false,       
            autoplay: true,
            autoplaySpeed: 2000,
            infinite: true,
        }); 
    }
});
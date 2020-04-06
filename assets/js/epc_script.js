(function ($) {
    var cpt_addons = {
        initilaize: function () {
            $(document).ready(function () {
                cpt_addons.initAccordion();
            });
        },
        initAccordion: function () {
            $(document).on('click', '.woo_plus', function () {
                //$('.woo_nxt_lvl').slideUp();
                //$('.woo_plus').show();
                //$('.woo_minus').hide();
                $(this).next().next().slideDown();
                $(this).hide();
                $(this).next('.woo_minus').show();
            });

            $(document).on('click', '.woo_minus', function () {
                //$('.woo_nxt_lvl').slideUp();
                $(this).next().slideUp();
                //$(this).parents('.woo_subcategory_parent').find('.woo_nxt_lvl').slideDown();
                //$('.woo_plus').show();
                //$('.woo_minus').hide();
                $(this).hide();
                $(this).prev('.woo_plus').show();
            });
        }
    };

    cpt_addons.initilaize();
    var WidgetCarouselHandler = function ($scope, $) {
        var carousel_elem = $scope.find('.panavision_post_carousel').eq(0);
        if (carousel_elem.length > 0) {

            var settings = carousel_elem.data('settings');
            var arrows = settings['arrows'];
            var dots = settings['dots'];
            var autoplay = settings['autoplay'];
            var autoplay_speed = parseInt(settings['autoplay_speed']) || 3000;
            var animation_speed = parseInt(settings['animation_speed']) || 300;
            var fade = settings['fade'];
            var pause_on_hover = settings['pause_on_hover'];
            var display_columns = parseInt(settings['slides_to_show']) || 4;
            var scroll_columns = parseInt(settings['slides_to_scroll']) || 4;
            var tablet_width = parseInt(settings['tablet_width']) || 800;
            var tablet_display_columns = parseInt(settings['tablet_display_columns']) || 2;
            var tablet_scroll_columns = parseInt(settings['tablet_scroll_columns']) || 2;
            var mobile_width = parseInt(settings['mobile_width']) || 480;
            var mobile_display_columns = parseInt(settings['mobile_display_columns']) || 1;
            var mobile_scroll_columns = parseInt(settings['mobile_scroll_columns']) || 1;
            var infinite = settings['infinite'];
            var direction = settings['direction'];
            carousel_elem.slick({
                arrows: arrows,
                dots: dots,
                infinite: infinite,
                autoplay: autoplay,
                autoplaySpeed: autoplay_speed,
                speed: animation_speed,
                fade: false,
                pauseOnHover: pause_on_hover,
                slidesToShow: display_columns,
                slidesToScroll: scroll_columns,
                direction: direction,
                responsive: [
                    {
                        breakpoint: tablet_width,
                        settings: {
                            slidesToShow: tablet_display_columns,
                            slidesToScroll: tablet_scroll_columns
                        }
                    },
                    {
                        breakpoint: mobile_width,
                        settings: {
                            slidesToShow: mobile_display_columns,
                            slidesToScroll: mobile_scroll_columns
                        }
                    }
                ]
            });
        }

    };
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/elementor-carousel-posts.default', WidgetCarouselHandler);
    });
})(jQuery);
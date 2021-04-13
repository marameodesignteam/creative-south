(function($) {
    var windowWidth = $(window).width();
    var facetScrollTop = undefined;

    if ($(".facet-search").length > 0) {
        var facetScrollTop = $(".facet-search").offset().top;
    }

    $(window).on("load", function() {
        removeAttrExpanedMenu();
        internalAnchors();
        anchorHash();
        mobileMenu();
        clipboard();
    });



    $(document).ready(function($) {
        $('.post-list .description .read-more').remove();
        $('#wellcomeModal').modal('show');
        modal_searchForm();
        var carousel = $('body').find('.carousel-blocks');
        //console.log(carousel);
        if (carousel.length > 0) {
            $('.entry-header .entry-title').addClass('sr-only');
        } else {
            $('.site-header .custom-fixed-header').addClass('no-fixed');
            $('.the-header').addClass('active');
            $('.entry-header .entry-title').addClass('show');
        }
    });

    $(window).resize(function() {
        windowWidth = $(window).width();
    });

    $(window).scroll(function() {
        menu_fixed()
    });

    function clipboard() {
        var $temp = $("<input>");
        var $url = $(location).attr('href');
        $('.clipboard').on('click', function(e) {
            e.preventDefault();
            $("body").append($temp);
            $temp.val($url).select();
            document.execCommand("copy");
            $temp.remove();
            $(".copied").addClass('active');
            setTimeout(function() {
                $(".copied").removeClass('active');
            }, 2000);
        });
    }

    function modal_searchForm() {
        $('.js-search-form').on('click', function() {
            $('#search-form').addClass('active');
        });

        $('.bg-searchform').on('click', function() {
            $('#search-form').removeClass('active');
        });
    }

    function mobileMenu() {
        $("#menu-primary-menu > li.menu-item-has-children > a, #menu-primary-menu > li.menu-item-has-children li.menu-item-has-children > a").each(function() {
            //unbind
            $(this).unbind();
            $(this).removeAttr("aria-expanded");
            $(this).removeAttr("aria-haspopup");

            var ul = $(this).next();

            var li = $(this).closest("li")
            var id = li.attr("id");

            ul.attr("id", id + "_ul");

            var button = $("<button class='mega-indicator mega-indicator-button top-level d-xl-none'><span class='sr-only'>" + $(this).text() + " submenu</span></button>");

            button.attr("aria-controls", id + "_ul");
            button.attr("aria-expanded", "false");

            button.insertAfter($(this));
            li.addClass("mega-with-button");
            $(this).find(".mega-indicator:not(.mega-indicator-button)").remove();
            li.addClass("js");

        });


        $("body").on("click", ".mega-indicator-button", function() {
            // var paren = $(this).parent('.menu-item-has-children');
            // paren.find('.menu-link').toggleClass('active');
            $(this).parent('li').toggleClass('active');
            if ($(this).attr("aria-expanded") == "false") $(this).attr("aria-expanded", "true");
            else $(this).attr("aria-expanded", "false");
            $(this).next('.sub-menu').slideToggle();
        });

        //toggle
        $(".menu-toggle").on("click", function() {
            var menu = $("#menu-mobile");
            if (menu.hasClass("open")) {
                menu.removeClass("open");
                $("body").removeClass("open");
                $(this).attr("aria-expanded", "false");
                $('html,body').removeClass('hidden');
            } else {
                menu.addClass("open");
                $("body").addClass("open");
                $(this).attr("aria-expanded", "true");
                $('html,body').addClass('hidden');
            }

            closeAccordion();
        });

        $('.overlay').on("click", function() {
            $("#menu-mobile").removeClass('open');
            $('html,body').removeClass('hidden');
            closeAccordion();
            $('body').removeClass('open');
        });

        // if (windowWidth < 1200) {
        //     $("body").on("click", ".main-navigation ul li a", function() {
        //         $('html,body').removeClass('hidden');
        //         $('.mega-indicator-button').attr("aria-expanded", "false");
        //         $('body, #menu-mobile').removeClass('open');
        //     });
        // }

        function closeAccordion() {
            $(".mega-indicator-button").attr("aria-expanded", "false");
            $('.sub-menu').hide();
            $('.menu-item-has-children').removeClass('active');
        }
    }


    //remove attr-expaned Menu
    function removeAttrExpanedMenu() {
        $('#mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link').removeAttr("aria-expanded");
    }
    //https://css-tricks.com/snippets/jquery/smooth-scrolling/
    function internalAnchors() {
        $('a[href*="#"]')
            // Remove links that don't actually link to anything
            .not('[href="#"]')
            .not('[href="#0"]')
            .click(function(event) {
                // On-page links
                if (
                    location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
                    location.hostname == this.hostname
                ) {
                    // Figure out element to scroll to
                    var target = $(this.hash);
                    var offsetHeader = $('#masthead').outerHeight();

                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    // Does a scroll target exist?
                    if (target.length) {
                        // Only prevent default if animation is actually gonna happen
                        event.preventDefault();
                        var offsetTop = target.offset().top;
                        var scrolltop = offsetTop - offsetHeader - 20;
                        //var scrolltop = offsetTop
                        $('html, body').animate({
                            scrollTop: scrolltop
                        }, 600);
                    }
                }
            });
    }

    function anchorHash() {
        var has = window.location.hash;
        var offsetHeader = $('#masthead').outerHeight();
        if (has) {
            var nav = $(has);
            if (nav.length) {
                var contentNav = nav.offset().top;
                jQuery('html, body').animate({
                    scrollTop: contentNav - offsetHeader - 20
                }, 600);
            }
        }
    }

    function menu_fixed() {
        var carousel = $('body').find('.carousel-blocks');
        console.log(carousel);
        if (carousel.length > 0) {
            if ($(window).scrollTop() > $('.site-header').offset().top) $('.custom-fixed-header').addClass('fixed');
            else $('.custom-fixed-header').removeClass('fixed')
        } else {
            if ($(window).scrollTop() > $('.site-header').offset().top) $('.custom-fixed-header').addClass('fixed').removeClass('no-fixed');
            else $('.custom-fixed-header').removeClass('fixed').addClass('no-fixed');
        }
    }

})(jQuery);
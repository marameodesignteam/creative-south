(function($) {
    var windowWidth = $(window).width();
    var facetScrollTop = undefined;

    if ($(".facet-search").length > 0) {
        var facetScrollTop = $(".facet-search").offset().top;
    }

    $(window).on("load", function() {
        internalAnchors();
        mapMobile();
        anchorHash();
        mobileMenu();
        clipboard();
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function isVisited() {
        var visited = getCookie("visited");
        if (visited != "") {
            return true;
        } else {
            setCookie("visited", true, 30);
        }
    }

    $(document).ready(function($) {
        $('.post-list .description .read-more').remove();

        if ($('#welcomeModal').length) {
            if (!isVisited()) {
                $('#welcomeModal').modal('show');
            }
        }
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
        if (windowWidth < 1199) {
            $('#post-38 .wpgmp-map-2 .listing-map').removeClass('full-height');
        }
    });

    $(window).resize(function() {
        windowWidth = $(window).width();
    });

    $(window).scroll(function() {
        menu_fixed()
    });

    var $item = $('.full-height');
    var offsetHeader = $('.the-header').height();
    //console.log(offsetHeader);
    //var cateHeight = $('.wpgmp-map-2 .wpgmp_filter_wrappers .categories_filter').height();
    if (windowWidth < 375) {
        var $wHeight = $(window).height() - 67 - 44;
    } else if (windowWidth < 1199) {
        var $wHeight = $(window).height() - 73.1 - 44;
    } else {
        var $wHeight = $(window).height() - 112 - 41;
    }
    $item.height($wHeight);


    function mapMobile() {
        $('.nav-browse-link').on('click', function(e) {
            e.preventDefault();
            var neo = $(this).attr('href');
            var offsetTop = $(neo).offset().top;
            var offsetHeader = $('#masthead').outerHeight();
            var scrolltop = offsetTop - offsetHeader - 120;
            $('html, body').animate({
                scrollTop: scrolltop
            }, 600);
        });
    }


    function clipboard() {
        var temp = $("<input>");
        $('.clipboard').on('click', function() {
            console.log(1);
            $("body").append(temp);
            var url = $(this).attr('href');
            temp.val(url).select();
            document.execCommand("copy");
            temp.remove();
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
        //toggle
        $(".menu-toggle").on("click", function(e) {
            e.preventDefault();
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

        function closeAccordion() {
            $(".mega-indicator-button").attr("aria-expanded", "false");
            $('.sub-menu').hide();
            $('.menu-item-has-children').removeClass('active');
        }
    }

    //https://css-tricks.com/snippets/jquery/smooth-scrolling/
    function internalAnchors() {
        $('a[href*="#"]')
            // Remove links that don't actually link to anything
            .not('[href="#"]')
            .not('[href="#0"]')
            .click(function(event) {
                // On-page links
                $("#menu-mobile").removeClass("open");
                $("body").removeClass("open");
                $(".menu-toggle").attr("aria-expanded", "false");
                $('html,body').removeClass('hidden');
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
                        var scrolltop = offsetTop - offsetHeader - 100;
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
                    scrollTop: contentNav - offsetHeader - 120
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
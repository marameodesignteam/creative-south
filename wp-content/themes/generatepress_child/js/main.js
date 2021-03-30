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
    });


    $(document).ready(function($) {
        modal_searchForm();
        facetWP();
        facetReset();
        $('.post-list .description .read-more').remove();
    });

    $(window).resize(function() {
        windowWidth = $(window).width();
    });

    $(window).scroll(function() {

    });

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

        if (windowWidth < 1200) {
            $("body").on("click", ".main-navigation ul li a", function() {
                $('html,body').removeClass('hidden');
                $('.mega-indicator-button').attr("aria-expanded", "false");
                $('body, #menu-mobile').removeClass('open');
            });
        }

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
    //Facet
    function facetWP() {
        $(document).on('facetwp-loaded', function() {

            //accessibility improvements for facet WP

            //button
            // add role, label, attr and move it after the input search
            if (!($(".facetwp-btn").attr("role"))) {
                $(".facetwp-btn").attr("role", "button").attr("tabindex", "0").prepend("<span class='sr-only'>Search</span>").appendTo($(".facetwp-btn").parent());
            }

            //add keyboard func
            $(".facetwp-btn").on("keydown", function(e) {

                if (e.which == 32 || e.which == 13) {
                    e.preventDefault();
                    FWP.autoload();
                }

            });

            //add pagination
            if ($(".facetwp-pager").length > 0) {

                $(".facetwp-pager").find(".facetwp-page.active").attr("aria-current", "page");
            }

            //disabled to checkbox
            $(".facetwp-checkbox").removeAttr("aria-disabled");
            $(".facetwp-checkbox.disabled").attr("aria-disabled", "true");


            // add labels to select
            $(".find-keyword .facetwp-search-wrap .facetwp-search").wrapAll("<label class='search-label'></label>")
            $('.find-keyword .search-label').prepend("<span class='sr-only'>Search by keyword</span>");


            $(".find-state .facetwp-dropdown").wrapAll("<label class='search-label'></label>")
            $('.find-state .search-label').prepend("<span class='sr-only'>Search by state</span>");

            $(".find-experience .facetwp-dropdown").wrapAll("<label class='search-label'></label>")
            $('.find-experience .search-label').prepend("<span class='sr-only'>Search by experience</span>");


            //custom selections in order to rewrite
            var selections = '';
            $.each(FWP.facets, function(key, val) {
                if (val.length < 1 || 'undefined' === typeof FWP.settings.labels[key]) {
                    return true; // skip this facet
                }

                var choices = val;
                var facet_type = $('.facetwp-facet-' + key).attr('data-type');
                choices = FWP.hooks.applyFilters('facetwp/selections/' + facet_type, choices, {
                    'el': $('.facetwp-facet-' + key),
                    'selected_values': choices
                });

                if ('string' === typeof choices) {
                    choices = [{
                        value: '',
                        label: choices
                    }];
                } else if ('undefined' === typeof choices[0].label) {
                    choices = [{
                        value: '',
                        label: choices[0]
                    }];
                }

                var values = '';
                $.each(choices, function(idx, choice) {
                    values += '<button type="button" class="facetwp-selection-value" data-value="' + choice.value + '"><span class="sr-only">Remove the filter </span>' + FWP.helper.escape_html(choice.label) + '</span>';
                });

                selections += '<li data-facet="' + key + '"> ' + values + '</li>';
            });

            if ('' !== selections) {
                selections = '<ul>' + selections + '</ul>';
            }

            $('.facetwp-custom-selections').html(selections);

            // Click on a user selection
            $(document).on('click', '.facetwp-custom-selections button', function() {
                if (FWP.is_refresh) {
                    return;
                }

                var facet_name = $(this).closest('li').attr('data-facet');
                var facet_value = $(this).attr('data-value');

                if ('' != facet_value) {
                    var obj = {};
                    obj[facet_name] = facet_value;
                    FWP.reset(obj);
                } else {
                    FWP.reset(facet_name);
                }
            });

        });

    }
    //facetReset
    function facetReset() {
        $(document).on('facetwp-loaded', function() {
            var queryString = FWP.build_query_string();
            if ('' === queryString) { // no facets are selected
                $('.facet-reset').hide();
            } else {
                $('.facet-reset').show();
            }
        });
    }

})(jQuery);
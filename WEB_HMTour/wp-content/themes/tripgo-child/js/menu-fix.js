/**
 * HM Tour & Travel - Menu Dropdown Fix
 * Fixes the dropdown menu disappearing issue when cursor moves down
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        /**
         * Fix dropdown menu hover behavior
         */
        function fixDropdownMenu() {
            var $menuItems = $('.menu-item-has-children');
            
            $menuItems.each(function() {
                var $this = $(this);
                var $submenu = $this.find('.sub-menu').first();
                var hoverTimer;
                
                // Mouse enter on parent menu item
                $this.on('mouseenter', function() {
                    clearTimeout(hoverTimer);
                    
                    // Close other dropdowns
                    $('.menu-item-has-children').not($this).removeClass('is-open');
                    $('.menu-item-has-children .sub-menu').not($submenu).stop(true, true).fadeOut(200);
                    
                    // Open this dropdown
                    $this.addClass('is-open');
                    $submenu.stop(true, true).fadeIn(300);
                });
                
                // Mouse leave on parent menu item
                $this.on('mouseleave', function() {
                    hoverTimer = setTimeout(function() {
                        $this.removeClass('is-open');
                        $submenu.stop(true, true).fadeOut(200);
                    }, 300); // Delay before closing
                });
                
                // Keep dropdown open when hovering over submenu
                $submenu.on('mouseenter', function() {
                    clearTimeout(hoverTimer);
                    $this.addClass('is-open');
                });
                
                $submenu.on('mouseleave', function() {
                    hoverTimer = setTimeout(function() {
                        $this.removeClass('is-open');
                        $submenu.stop(true, true).fadeOut(200);
                    }, 300);
                });
            });
        }
        
        /**
         * Add smooth scroll to anchor links
         */
        function smoothScrollToAnchor() {
            $('a[href*="#"]:not([href="#"])').on('click', function(e) {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
                    location.hostname === this.hostname) {
                    
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    
                    if (target.length) {
                        e.preventDefault();
                        $('html, body').animate({
                            scrollTop: target.offset().top - 100
                        }, 800, 'swing');
                    }
                }
            });
        }
        
        /**
         * Add active class to current menu item
         */
        function highlightCurrentMenuItem() {
            var currentUrl = window.location.href;
            var $menuLinks = $('.menu-item a');
            
            $menuLinks.each(function() {
                var $link = $(this);
                if ($link.attr('href') === currentUrl) {
                    $link.parent('.menu-item').addClass('current-menu-item');
                }
            });
        }
        
        /**
         * Mobile menu toggle enhancement
         */
        function enhanceMobileMenu() {
            var $mobileMenuToggle = $('.mobile-menu-toggle, .menu-toggle');
            var $mobileMenu = $('.mobile-menu, .main-navigation');
            
            $mobileMenuToggle.on('click', function(e) {
                e.preventDefault();
                $(this).toggleClass('is-active');
                $mobileMenu.toggleClass('is-open');
                $('body').toggleClass('menu-open');
            });
            
            // Close mobile menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                    $mobileMenuToggle.removeClass('is-active');
                    $mobileMenu.removeClass('is-open');
                    $('body').removeClass('menu-open');
                }
            });
        }
        
        /**
         * Add loading animation for images
         */
        function lazyLoadImages() {
            var lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                var imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var image = entry.target;
                            image.classList.add('loaded');
                            imageObserver.unobserve(image);
                        }
                    });
                });
                
                lazyImages.forEach(function(image) {
                    imageObserver.observe(image);
                });
            } else {
                // Fallback for browsers that don't support IntersectionObserver
                lazyImages.forEach(function(image) {
                    image.classList.add('loaded');
                });
            }
        }
        
        /**
         * Sticky header on scroll
         */
        function stickyHeader() {
            var $header = $('.site-header, header');
            var headerHeight = $header.outerHeight();
            var scrollThreshold = 100;
            
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > scrollThreshold) {
                    $header.addClass('is-sticky');
                    $('body').css('padding-top', headerHeight + 'px');
                } else {
                    $header.removeClass('is-sticky');
                    $('body').css('padding-top', '0');
                }
            });
        }
        
        /**
         * Back to top button
         */
        function backToTopButton() {
            // Create back to top button if it doesn't exist
            if (!$('.back-to-top').length) {
                $('body').append('<button class="back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>');
            }
            
            var $backToTop = $('.back-to-top');
            
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > 300) {
                    $backToTop.addClass('is-visible');
                } else {
                    $backToTop.removeClass('is-visible');
                }
            });
            
            $backToTop.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 600, 'swing');
            });
        }
        
        /**
         * Initialize all functions
         */
        function init() {
            fixDropdownMenu();
            smoothScrollToAnchor();
            highlightCurrentMenuItem();
            enhanceMobileMenu();
            lazyLoadImages();
            stickyHeader();
            backToTopButton();
            
            console.log('HM Travel menu enhancements loaded successfully');
        }
        
        // Run initialization
        init();
        
        // Re-initialize on window resize (debounced)
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                fixDropdownMenu();
            }, 250);
        });
        
    });
    
})(jQuery);

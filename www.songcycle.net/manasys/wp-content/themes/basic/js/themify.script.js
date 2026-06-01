;// Themify Theme Scripts - https://themify.me/
(function($){
    'use strict';
    
     $(function() {
        /////////////////////////////////////////////
        // Scroll to top 							
        /////////////////////////////////////////////
        $('.back-top a').on('click',function (e) {
            e.preventDefault();
            Themify.scrollTo();
        });

        /////////////////////////////////////////////
        // Toggle main nav on mobile
        /////////////////////////////////////////////
        $("#menu-icon").on('click',function(){
                $("#main-nav").fadeToggle();
                $("#headerwrap #top-nav").hide();
                $(this).toggleClass("active");
        });

        if( Themify.isTouch && typeof $.fn.themifyDropdown !== 'function' ) {
                Themify.LoadAsync(themify_vars.url + '/js/themify.dropdown.js', function(){
                        $( '#main-nav' ).themifyDropdown();
                });
        }
        $( '#main-nav .menu-item-has-children' ).on( 'focusin focusout', function() {
                $( this ).toggleClass( 'dropdown-open' );
        });
    });
}(jQuery));
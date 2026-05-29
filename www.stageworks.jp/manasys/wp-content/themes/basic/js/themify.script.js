;
(($,Themify,doc)=>{
    'use strict';
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

    if( Themify.isTouch) {
        Themify.dropDown(doc.tfId( 'main-nav' ));
    }
    $( '#main-nav .menu-item-has-children' ).on( 'focusin focusout', function() {
            $( this ).toggleClass( 'dropdown-open' );
    });
})(jQuery,Themify,document);
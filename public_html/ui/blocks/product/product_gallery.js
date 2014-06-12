define( 'productGallery', ['jquery'], function() {
    'use strict';

    var $img = $( '.product__gallery__full__img' );

    $( '.product__gallery__thumbs' ).on( 'click', '.product__gallery__thumbs__link', function( e ) {
        e.preventDefault();

        $img[0].src = $( this ).attr( 'href' );
    } );
} );

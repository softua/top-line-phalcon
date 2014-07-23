var version = {
        jquery: (window.msie < 9) ? '1.11.1' : '2.1.1',
        jqueryui: '1.10.4',
        underscore: '1.6.0',
        backbone: '1.1.2'
    },
    isDev = window.location.hostname.search( '.dev' ) > -1;

require.config({
    urlArgs: 'v=' + (isDev ? Math.random() : 0.1),
    baseUrl: '/public_html/js/lib',
    paths: {
        jquery: [
//            '//yandex.st/jquery/' + version.jquery + '/jquery.min',
            'jquery-' + version.jquery + '.min'
        ],
        myselect: 'plugins/jquery.myselect',
        myslider: 'plugins/jquery.myslider',
        myslideropacity: 'plugins/jquery.myslideropacity',
        productGallery: '../../ui/blocks/product/product_gallery',
	    fancyBox: '../vendor/jquery.fancybox.pack'
    },
	shim: {
		fancyBox: {
			deps: ['jquery'],
			exports: 'fancybox'
		}
	}
} );

require( ['jquery'], function( $ ) {
    'use strict';

    var $jsSelect = $( '.js-select' );

    if ( $jsSelect.length ) {
        require( ['myselect'], function() {
            $jsSelect.mySelect();
        } );
    }

    if ( $( '.product__gallery' ).length ) {
        require( ['productGallery'] );
    }

    if ( $( '.js-slider' ).length ) {
        require( ['myslider', 'myslideropacity'], function() {
            $( '.slider-sales--outer-wrapper' ).mySlider( {
                slider: '.slider-sales',
                sliderItem: '.slider-sales__item',
                isResponsive: true,
                autoPlay: false,
                autoPlayDuration: 6000
            } );

            $( '.new-products--outer-wrapper' ).mySlider( {
                slider: '.new-products',
                sliderItem: '.new-products__item',
                isResponsive: true,
                autoPlay: false,
                autoPlayDuration: 6000
            } );

            $( '.partners--outer-wrapper' ).mySlider( {
                slider: '.partners',
                sliderItem: '.partners__item',
                isResponsive: true,
                autoPlay: false,
                autoPlayDuration: 6000
            } );

            $( '.slider--outer-wrapper' ).mySliderOpacity( {
                showArrows: true,
                autoPlay: false,
                autoPlayDuration: 6000
            } );
        } );
    }

	if ($('.fancybox').length) {
		require(['../../ui/blocks/fancybox/fancybox']);
	}

	if ($('.download-price').length) {
		require(['../../ui/blocks/download-price/download-price']);
	}

	if ($('.popup-form').length) {
		require(['../../ui/blocks/popup-form/popup-form']);
	}

    if ( !isDev ) {
        require( ['pluso'] );
    }

	if ($('[data-filter]').length) {
		require(['../../ui/blocks/data-filter/data-filter']);
	}
} );

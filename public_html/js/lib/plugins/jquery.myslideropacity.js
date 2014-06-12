define( ['jquery'], function( $ ) {
    'use strict';

    /*******************************************************************************
     * plugin: mySliderOpacity
     *******************************************************************************/
    var methods = {
        init: function( options ) {
            return this.each( function() {
                var
                    $this = $( this ),
                    settings = $.extend( {
                        autoPlay: false,
                        title: false,
                        autoPlayDuration: 6000,
                        showArrows: false,
                        switcherAppend: true,
                        switcher: '.switcher',
                        switcherItem: '.switcher__item',
                        slider: '.slider',
                        sliderItem: '.slider__item'
                    }, options ),

                    autoPlay = settings.autoPlay,
                    autoPlayDuration = settings.autoPlayDuration,

                    $title = $( '<div/>', {
                        'class': 'i-slider__title'
                    } ),

                    $slider = $this.find( settings.slider ),
                    $sliderItems = $slider.children( settings.sliderItem ),

                    $switcher, $switcherItems,

                    i, interval;


                // append arrows
                if ( settings.showArrows ) {
                    $this.append( '<div class="left-arrow"></div><div class="right-arrow"></div>' );
                }

                // append title
                if ( settings.title ) {
                    $this.append( $title );
                    $title.html( $sliderItems.eq( 0 ).data( 'title' ) );
                }

                // append switcher
                if ( settings.switcher ) {
                    if ( settings.switcherAppend ) {
                        $switcher = $( '<div class="' + settings.switcher.replace( '.', '' ) + '"></div>' );
                        $switcherItems = '';

                        for ( i = 0; i < $sliderItems.length; i++ ) {
                            $switcherItems += '<div class="' + settings.switcherItem.replace( '.', '' ) + '"></div>';
                        }

                        $switcher
                            .append( $switcherItems )
                            .appendTo( $this );

                    } else {
                        $switcher = $( settings.switcher );
                    }

                    $switcherItems = $switcher.find( settings.switcherItem );
                    $switcherItems.eq( 0 ).add( $sliderItems.eq( 0 ) ).addClass( 'active' );
                }

                if ( $switcherItems.length < 2 ) {
                    return;
                }

                $sliderItems.eq( 0 ).css( {
                    opacity: 1,
                    position: 'relative',
                    'z-index': 3
                } );

                if ( autoPlay ) {
                    interval = setTimeout( autoRotate, autoPlayDuration );
                }

                $this.find( '.left-arrow' ).on( {
                    click: function() {
                        var $active = $switcherItems.filter( '.active' ),
                            $prev = $active.prev();

                        if ( !$prev.length ) {
                            $prev = $switcherItems.eq( -1 );
                        }

                        $prev.click();

                        return false;
                    }
                } );

                $this.find( '.right-arrow' ).on( {
                    click: function() {
                        var $active = $switcherItems.filter( '.active' ),
                            $next = $active.next();

                        if ( !$next.length ) {
                            $next = $switcherItems.eq( 0 );
                        }

                        $next.click();

                        return false;
                    }
                } );

                $switcherItems.on( {
                    click: function( e ) {
                        e.preventDefault();

                        var $this = $( this ),
                            index = $this.index();

                        if ( autoPlay ) {
                            clearInterval( interval );
                            interval = setTimeout( autoRotate, autoPlayDuration );
                        }

                        if ( !$this.hasClass( 'active' ) ) {
                            $switcherItems.removeClass( 'active' );
                            $this.addClass( 'active' );

                            showSlide( index );
                        }
                    }
                } );

                function autoRotate() {
                    var $active = $switcher.find( '.active' );
                    var $next = $active.next();

                    if ( !$next.length ) {
                        $next = $switcherItems.eq( 0 );
                    }

                    $next.click();
                }

                function showSlide( index ) {
                    var $newSlide = $sliderItems.eq( index ),
                        $active = $sliderItems.filter( '.active' ),

                        newHeight = $newSlide.outerHeight( true );

                    $slider.animate( {height: newHeight}, 500 );

                    $active
                        .removeClass( 'active' )
                        .css( {
                            position: 'absolute',
                            'z-index': 2
                        } )
                        .fadeTo( 500, 0 );

                    $newSlide.addClass( 'active' ).css( {
                        opacity: 0,
                        position: 'relative',
                        'z-index': 3
                    } ).stop().fadeTo( 700, 1, function() {
                        $active.css( 'z-index', 1 );
                    } );

                    if ( settings.title ) {
                        $title.html( $newSlide.data( 'title' ) );
                    }
                }

            } );
        }
    };

    $.fn.mySliderOpacity = function( method ) {
        if ( !this.length ) { return this; }

        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
        } else if ( typeof method === 'object' || !method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Метод с именем ' + method + ' не существует' );
        }
    };


} );

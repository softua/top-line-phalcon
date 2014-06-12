define( ['jquery'], function( $ ) {
    'use strict';

    /*******************************************************************************
     * plugin: mySlider
     *******************************************************************************/
    $.fn.mySlider = function( options ) {

        if ( !this.length ) { return this; }

        return this.each( function() {

            var
                $this = $( this ),
                settings = $.extend( {
                    duration: 300,
                    autoPlay: false,
                    autoPlayDuration: 5000,
                    visibleItems: 1,
                    sliderItem: '.item',
                    switcher: null, // e.g. '.switcher'
                    switcherItems: null,  // e.g. '.switcher__item'
                    switcherAppend: true,
                    callback: null,
                    recheckWidth: true,
                    isResponsive: false

                }, options ),

                duration = settings.duration,
                autoPlay = settings.autoPlay === true,
                autoPlayDuration = settings.autoPlayDuration,
                $slider = $this.find( settings.slider ),
                $sliderItems = $slider.find( settings.sliderItem ),
                sliderSize = $sliderItems.length,
                sliderItemWidth = $sliderItems.eq( 0 ).outerWidth( true ),
                sliderFullItemsWidth = sliderSize * sliderItemWidth,
                $boundingBox = $( this ).find( '.bounding-box' ),

                $leftArrow, $rightArrow,
                $switcher, $switcherItems,

                interval, i,
                offsetMultiply = 1,
                memoMultiply = 1,
                isAnimate = false,

                delay = (function() {
                    var timer = 0;
                    return function( callback, ms ) {
                        clearTimeout( timer );
                        timer = setTimeout( callback, ms );
                    };
                })();

            if ( sliderSize <= settings.visibleItems ) {
                return false;
            }

            $.each( $sliderItems, function( i ) {
                $( this ).data( 'initId', i );
            } );

            $this.append( '<button class="left-arrow" type="button"></button><button class="right-arrow" type="button"></button>' );
            $leftArrow = $this.find( '.left-arrow' );
            $rightArrow = $this.find( '.right-arrow' );

            $leftArrow.on( 'click.mySlider', function( e ) {
                e.stopPropagation();
                resetInterval();
                moveLeftToRight();
            } );

            $rightArrow.on( 'click.mySlider', function( e ) {
                e.stopPropagation();
                resetInterval();
                moveRightToLeft();
            } );

            // append switcher
            if ( settings.switcher ) {

                if ( settings.switcherAppend ) {
                    $switcher = $( '<div class="' + settings.switcher.replace( '.', '' ) + '"></div>' );
                    $switcherItems = '';

                    for ( i = 0; i < $sliderItems.length; i++ ) {
                        $switcherItems += '<span class="' + settings.switcherItems.replace( '.', '' ) + '"></span>';
                    }

                    $switcher
                        .append( $switcherItems )
                        .appendTo( $this );
                } else {
                    $switcher = $( settings.switcher );
                }

                $switcherItems = $switcher.find( settings.switcherItems );
                $switcherItems.eq( 0 ).add( $sliderItems.eq( 0 ) ).addClass( 'active' );

                $switcherItems.on( 'click', function( e ) {
                    var $this = $( this ),
                        index, activeSlideIndex,
                        multiply;

                    e.stopPropagation();

                    if ( $this.hasClass( 'active' ) ) {
                        return false;
                    }

                    index = $this.index();
                    activeSlideIndex = $sliderItems.eq( 0 ).data( 'initId' );

                    multiply = index - activeSlideIndex;
                    memoMultiply = offsetMultiply = Math.abs( multiply );

                    if ( index > activeSlideIndex ) {
                        if ( offsetMultiply <= sliderSize / 2 ) {
                            $rightArrow.click();
                        } else {
                            memoMultiply = offsetMultiply = sliderSize - offsetMultiply;
                            $leftArrow.click();
                        }
                    } else {
                        if ( offsetMultiply > sliderSize / 2 ) {
                            memoMultiply = offsetMultiply = sliderSize - offsetMultiply;
                            $rightArrow.click();
                        } else {
                            $leftArrow.click();
                        }
                    }

                } );
            }

            if ( autoPlay ) {
                interval = setInterval( function() {
                    auto();
                }, autoPlayDuration );

                $this.on( {
                    mouseenter: function() {
                        clearInterval( interval );
                    },
                    mouseleave: function() {
                        interval = setInterval( function() {
                            auto();
                        }, autoPlayDuration );
                    }
                } );
            }

            if ( settings.isResponsive ) {
                $( window ).resize( function() {
                    delay( function() {
                        if ( sliderFullItemsWidth < $boundingBox.width() ) {
                            $leftArrow.add( $rightArrow ).css( 'display', 'none' );

                            if ( autoPlay ) {
                                clearInterval( interval );
                            }

                        } else {
                            $leftArrow.add( $rightArrow ).css( 'display', 'block' );

                            if ( autoPlay ) {
                                interval = setInterval( function() {
                                    auto();
                                }, autoPlayDuration );
                            }
                        }

                    }, 50 );
                } ).trigger('resize');
            }

            function moveLeftToRight() {
                if ( isAnimate ) {
                    return false;
                }
                isAnimate = true;

                var
                    $first = $sliderItems.eq( 0 ),
                    $last = $sliderItems.eq( -1 );

                $last.insertBefore( $first );
                $slider.css( 'left', -sliderItemWidth );

                $slider.animate( {left: 0}, (duration / memoMultiply | 0), 'linear', function() {
                    animateFinished();

                    if ( offsetMultiply > 1 ) {
                        --offsetMultiply;
                        $leftArrow.click();
                    } else {
                        memoMultiply = 1;
                    }
                } );
            }

            function moveRightToLeft() {
                if ( isAnimate ) {
                    return false;
                }
                isAnimate = true;

                var
                    $first = $sliderItems.eq( 0 ),
                    $last = $sliderItems.eq( -1 );

                $slider
                    .animate( {left: -sliderItemWidth}, (duration / memoMultiply | 0), 'linear', function() {
                        $first.insertAfter( $last );
                        $slider.css( 'left', 0 );
                        animateFinished();

                        if ( offsetMultiply > 1 ) {
                            --offsetMultiply;
                            $rightArrow.click();
                        } else {
                            memoMultiply = 1;
                        }
                    } );
            }

            function animateFinished() {
                isAnimate = false;
                $sliderItems = $slider.find( settings.sliderItem );

                if ( settings.switcher && offsetMultiply === 1 ) {
                    $switcherItems.removeClass( 'active' );
                    $switcherItems.eq( $sliderItems.eq( 0 ).data( 'initId' ) ).addClass( 'active' );
                }

                if ( settings.recheckWidth ) {
                    sliderItemWidth = $sliderItems.eq( 0 ).outerWidth( true );
                }
            }

            function resetInterval() {
                if ( settings.autoPlay ) {
                    clearInterval( interval );
                    interval = setInterval( function() {
                        auto();
                    }, autoPlayDuration );
                }
            }

            function auto() {
                $rightArrow.click();
            }

        } );

    };

} );

define( ['jquery'], function( $ ) {
    /**
     * jQuery mySelect plugin
     */

    'use strict';

    var $body = $( 'body' ),
        $options = {},
        isIe = typeof window.msie !== undefined && window.msie < 9,

        methods = {
            init: function( options ) {
                var settings = $.extend( {
                    value: '.js-select__value',
                    selectedAsPlaceholder: true
                }, options );


                $( window ).resize( function() {
                    destroyOptions();
                } );

                $body.on( {
                    click: function() {
                        destroyOptions();
                    }
                } )
                    .on( {
                        click: function() {
                            var $this = $( this ),
                                related = $options.data( 'related' ),
                                $select = $( '.' + related );

                            $select.find( settings.value ).html( $this.html() );
                            $select.find( 'option' ).eq( $this.index() ).prop( 'selected', true );
                            $select.find( 'select' ).change();
                        }
                    }, '.js-select-options .item' );

                return this.each( function() {
                    var
                        $this = $( this ),
                        $select = $this.find( 'select' ),
                        $value = $this.find( settings.value ),
                        initClass = $this.attr( 'class' );

                    // prevent from selection
                    $this
                        .attr( 'unselectable', 'on' )
                        .css( 'user-select', 'none' );

                    if ( settings.selectedAsPlaceholder ) {
                        $value.html( $select.find( 'option:selected' ).html() );
                    }

                    $this.on( {
                        click: showOptions
                    } );

                    function showOptions( e ) {
                        var $select = $( e.currentTarget );
                        var pos = $select.offset();
                        var newClass = 'optionsForThis' + (new Date().getTime());
                        var $clone, width = 0;

                        var height, scrollHeight, $firstItem, $handler, handlerHeight,
                            offset, maxOffset;

                        e.stopPropagation();

                        if ( $options.length && $select.hasClass( $options.data( 'related' ) ) ) {
                            destroyOptions();
                            return false;
                        }

                        destroyOptions();

                        $select.attr( 'class', initClass + ' ' + newClass );

                        $options = $( '<ul/>', {
                            'class': 'js-select-options'
                        } );

                        $.each( $select.find( 'option' ), function( i ) {
                            var $this = $( this );
                            $options.append( $( '<li/>', {
                                'class': 'item' + (i % 2 ? ' even ' : ' ') + ($this.prop( 'selected' ) ? 'active ' : ' '),
                                html: $this.text()
                            } ) );
                        } );

                        $clone = $options.clone();
                        $clone
                            .appendTo( $body )
                            .css( {
                                position: 'absolute',
                                visibility: 'hidden',
                                top: 100,
                                width: '100%'
                            } );

                        $.each( $clone.find( '.item' ), function() {
                            var $thisEach = $( this );


                            $thisEach.css( {
                                display: 'inline-block',
                                '*zoom': 1,
                                '*display': 'inline'
                            } );

                            width = Math.max( width, $thisEach.outerWidth( true ) );
                        } );


                        $options
                            .data( 'related', newClass )
                            .css( {
                                top: pos.top + $value.outerHeight( true ),
                                left: pos.left,
                                width: $select.outerWidth() - (isIe ? 2 : 0)
                            } )
                            .appendTo( $body );

                        $clone.remove();

                        // scroll: to be or not to be
                        height = $options.outerHeight();
                        scrollHeight = $options[0].scrollHeight;

                        if ( scrollHeight > height ) {
                            $firstItem = $options.find( '.item' ).eq( 0 );
                            handlerHeight = height * (height / scrollHeight) | 0;

                            $handler = $( '<li/>', {
                                'class': 'handler',
                                height: handlerHeight
                            } );

                            $options.append( $handler );

                            offset = 10;
                            maxOffset = height - scrollHeight;

                            // scroll by mouse wheel
                            $options.on( 'mousewheel', function( e, delta ) {
                                var currentOffset = parseInt( $firstItem.css( 'margin-top' ), 10 ),
                                    newOffset;

                                e.preventDefault();

                                if ( delta > 0 ) {
                                    newOffset = currentOffset + offset;
                                    newOffset = newOffset > 0 ? 0 : newOffset;

                                } else {
                                    newOffset = currentOffset - offset;
                                    newOffset = newOffset < maxOffset ? maxOffset : newOffset;
                                }

                                $handler.css( 'top', (height - handlerHeight) * (newOffset / maxOffset) );

                                $firstItem.css( 'margin-top', newOffset );
                            } );

                            // scroll by dragging
                            $handler.draggable( {
                                axis: 'y',
                                containment: 'parent',
                                drag: function( e, ui ) {
                                    void( e ); // -jshint- unused pass

                                    var top = ui.position.top;
                                    $firstItem.css( 'margin-top', maxOffset * top / maxOffset );
                                }
                            } );

                        }

                        return false;
                    }
                } );

                function destroyOptions() {
                    if ( $options.length ) {
                        $( '.js-select-options' ).remove();
                        $options = {};
                    }
                }
            }
        };

    /**
     * initialize plugin
     * @param method
     * @returns {*}
     */
    $.fn.mySelect = function( method ) {
        if ( !this.length ) { return this; }

        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
        } else if ( typeof method === 'object' || !method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method "' + method + '" does not exist' );
        }
    };


} );

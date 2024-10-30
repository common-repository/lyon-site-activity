jQuery( document ).ready( function() {
    if( jQuery( '#site-activity-navigation' ).length > 0 ) {
        var $navigation = jQuery( '#site-activity-navigation' );
        jQuery( 'a', $navigation ).on( 'click', function( e ) {
            e.preventDefault();

            var $id = jQuery( this ).attr( 'href' ), $offset = jQuery( $id ).offset().top - (
                parseInt( $navigation.outerHeight( true ) ) + parseInt( jQuery( '#wpadminbar' ).outerHeight( true ) ) + 18
            );
            if( ! $navigation.hasClass( 'fixed' ) ) {
                $offset -= parseInt( $navigation.outerHeight( true ) );
            }
            jQuery( 'html, body' ).animate( {
                scrollTop: $offset
            }, 1000 );
        } );

        function moveScroller() {
            var $anchor = jQuery( ".wrap > h1" );
            var $scroller = $navigation;

            var move = function() {
                var st = jQuery( window ).scrollTop();
                var ot = $anchor.offset().top;
                if( st > ot ) {
                    $scroller.addClass( 'fixed' );
                    jQuery( '.wrap section' ).each( function( i ) {
                        if( jQuery( this ).position().top <= st + 61 ) {
                            jQuery( 'a.active', $navigation ).removeClass( 'active' );
                            jQuery( 'a', $navigation ).eq( i ).addClass( 'active' );
                        }
                    } );
                } else {
                    $scroller.removeClass( 'fixed' );
                    jQuery( 'a.active', $navigation ).removeClass( 'active' );
                    jQuery( 'a:first', $navigation ).addClass( 'active' );
                }
            };
            jQuery( window ).scroll( move );
            move();
        }

        moveScroller();

    }

    // Helper for checkboxes to select all, or some, options
    if( jQuery( '.lsa_cpt_list' ).length > 0 ) {
        jQuery( '.lsa_cpt_list input[type="checkbox"]' ).change( function( e ) {

            var checked = jQuery( this ).prop( "checked" ),
                container = jQuery( this ).parent(),
                siblings = container.siblings();

            container.find( 'input[type="checkbox"]' ).prop( {
                indeterminate: false,
                checked: checked
            } );

            function checkSiblings( el ) {

                var parent = el.parent().parent(),
                    all = true;

                el.siblings().each( function() {
                    let returnValue = all = ( jQuery( this ).children( 'input[type="checkbox"]' ).prop( "checked" ) === checked );
                    return returnValue;
                } );

                if( all && checked ) {

                    parent.children( 'input[type="checkbox"]' ).prop( {
                        indeterminate: false,
                        checked: checked
                    } );

                    checkSiblings( parent );

                } else if( all && ! checked ) {

                    parent.children( 'input[type="checkbox"]' ).prop( "checked", checked );
                    parent.children( 'input[type="checkbox"]' ).prop( "indeterminate", ( parent.find( 'input[type="checkbox"]:checked' ).length > 0 ) );
                    checkSiblings( parent );

                } else {

                    el.parents( "li" ).children( 'input[type="checkbox"]' ).prop( {
                        indeterminate: true,
                        checked: false
                    } );

                }

            }

            checkSiblings( container );
        } );

        jQuery( '.lsa_cpt_list ul input[type="checkbox"]' ).trigger( 'change' );

    }

} );
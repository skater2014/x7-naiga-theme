jQuery( function( $ ) {
	'use strict';
	/* ----------------------------------------------------------------------
		# Sticky
	---------------------------------------------------------------------- */
	$( window ).on( 'scroll', function() {
		var header = $( '#header' );
		if ( $( this ).scrollTop() > 300 ) {
			header.sticky( {
				topSpacing: 0,
				zIndex: 300
			} );
			header.addClass( 'sticked sticked-slide' );
		} else {
			header.unstick();
			header.removeClass( 'sticked sticked-slide' );
		}
	} );
	/* ----------------------------------------------------------------------
		# Sidebar Fix
	---------------------------------------------------------------------- */
	$( '.fix-home-sidebar, .fix-blog-sidebar' ).stick_in_parent( {
		offset_top: 70
	} );
	/* ----------------------------------------------------------------------
		# wp-bootstrap-navwalker
		https://github.com/wp-bootstrap/wp-bootstrap-navwalker/issues/391
	---------------------------------------------------------------------- */
	// Add Submenu
	$( 'ul.navbar-nav > li > ul > li > ul.dropdown-menu' ).parent().addClass( 'dropdown-submenu' ).find( ' > .dropdown-item' ).attr( 'href', 'javascript:;' ).addClass( 'dropdown-toggle' );
	// Toggle in Mobile
	$( '.dropdown-submenu > a' ).on( 'click', function( e ) {
		var dropdown = submenu.parent().find( ' > .show' );
		$( '.dropdown-submenu .dropdown-menu' ).not(dropdown).removeClass( 'show' );
		$( this ).next( '.dropdown-menu' ).toggleClass( 'show' );
		e.stopPropagation();
	} );
	$( '.dropdown' ).on( 'hidden.bs.dropdown', function() {
		$( '.dropdown-menu.show' ).removeClass( 'show' );
	} );
	/* ----------------------------------------------------------------------
		# Simple Placeholder
	---------------------------------------------------------------------- */
	$( 'input[placeholder]' ).simplePlaceholder();
	$( 'textarea[placeholder]' ).simplePlaceholder();
	/* ----------------------------------------------------------------------
		# Back to Top
	---------------------------------------------------------------------- */
	$( window ).on( 'scroll', function() {
		if ( $( this ).scrollTop() > 100 ) {
			$( '#back-to-top' ).fadeIn();
		} else {
			$( '#back-to-top' ).fadeOut();
		}
	} );
	$( '#back-to-top' ).on( 'click', 'a', function() {
		$( 'html, body' ).animate( {
			scrollTop: 0
		}, 500 );
		return false;
	} );
	/* ----------------------------------------------------------------------
		# Animated (Waypoint)
	---------------------------------------------------------------------- */
	// Animated
	$( '.animated' ).css( 'opacity', '0' );
	$( '.animated' ).waypoint( function( direction ) {
		$( this.element ).css( 'opacity', '1' );
		$( this.element ).addClass( $( this.element ).data( 'animation' ) );
	}, {
		offset: '90%'
	} );
	// Delay
	$( '.delay' ).each( function() {
		$( this ).css( {
			'-webkit-animation-delay': $( this ).data( 'delay' ) + 's',
			'animation-delay': $( this ).data( 'delay' ) + 's'
		} );
	} );
	/* ----------------------------------------------------------------------
		# Masonry
	---------------------------------------------------------------------- */
	// Masonry
	var blogGrid = $( '.blog-grid' ).masonry( {
		itemSelector: 'article',
		visibleStyle: {
			transform: 'translateY( 0 )',
			opacity: 1
		},
		hiddenStyle: {
			transform: 'translateY( 0 )',
			opacity: 0
		}
	} );
	// ImagesLoaded
	blogGrid.imagesLoaded().progress( function() {
		blogGrid.masonry( 'layout' );
	} );
	/* ----------------------------------------------------------------------
		# End
	---------------------------------------------------------------------- */
});

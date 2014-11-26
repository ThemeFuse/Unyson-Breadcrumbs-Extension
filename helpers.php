<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Returns the breadcrumbs HTML
 *
 * @param string $separator, separator symbol that will be set between elements
 *
 * @return string
 */
function fw_ext_get_breadcrumbs( $separator = ">" ) {
	return fw()->extensions->get( 'breadcrumbs' )->render( $separator, false, 'breadcrumbs' );
}

/**
 * Displays the breadcrumbs HTML
 *
 * @param string $separator, separator symbol that will be set between elements
 */
function fw_ext_breadcrumbs( $separator = ">" ) {
	echo fw()->extensions->get( 'breadcrumbs' )->render( $separator, false, 'breadcrumbs' );
}
<?php
/**
 * Display PRO label or not
 */
function bpge_admin_header_title_pro() {
	if ( defined( 'BPGE_PRO' ) ) {
		echo ' [PRO] ';
	}
}

add_action( 'bpge_admin_header_title_pro', 'bpge_admin_header_title_pro', 1 );

/**
 * Parse and include all BPGE extensions
 *
 * @param string $dir
 */
function bpge_include_pro_files( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
	}

	if ( $handle = opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( $file == "." || $file == ".." ) {
				continue;
			}

			if ( is_dir( $dir . DS . $file ) ) {
				bpge_include_pro_files( $dir . DS . $file );
			} else {
				/** @noinspection PhpIncludeInspection */
				include( $dir . DS . $file );
			}
		}
		closedir( $handle );
	}
}
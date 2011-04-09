<?php

# Protect against register_globals
# This must be done before any globals are set by the code
if ( ini_get( 'register_globals' ) ) {
	if ( isset( $_REQUEST['GLOBALS'] ) ) {
		die( '<a href="http://www.hardened-php.net/globals-problem">$GLOBALS overwrite vulnerability</a>');
	}
	$verboten = array(
		'GLOBALS',
		'_SERVER',
		'HTTP_SERVER_VARS',
		'_GET',
		'HTTP_GET_VARS',
		'_POST',
		'HTTP_POST_VARS',
		'_COOKIE',
		'HTTP_COOKIE_VARS',
		'_FILES',
		'HTTP_POST_FILES',
		'_ENV',
		'HTTP_ENV_VARS',
		'_REQUEST',
		'_SESSION',
		'HTTP_SESSION_VARS'
	);
	foreach ( $_REQUEST as $name => $value ) {
		if( in_array( $name, $verboten ) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			echo "register_globals security paranoia: trying to overwrite superglobals, aborting.";
			die( -1 );
		}
		unset( $GLOBALS[$name] );
	}
}

define( 'PHP_API', true );

	# Get MWInit class
	require_once( "$IP/includes/Init.php" );

	# Start profiler
	# FIXME: rewrite wfProfileIn/wfProfileOut so that they can work in compiled mode
	if ( file_exists( "$IP/StartProfiler.php" ) ) {
		require_once( "$IP/StartProfiler.php" );
	} else {
		require_once( "$IP/includes/ProfilerStub.php" );
	}

	# Load up some global defines.
	require_once( "$IP/includes/Defines.php" );

	# Start the autoloader, so that extensions can derive classes from core files
	require_once( "$IP/includes/AutoLoader.php" );

# Initialise output buffering
# Check that there is no previous output or previously set up buffers, because
# that would cause us to potentially mix gzip and non-gzip output, creating a
# big mess.
if ( !defined( 'MW_NO_OUTPUT_BUFFER' ) && ob_get_level() == 0 ) {
	if ( !defined( 'MW_COMPILED' ) ) {
		require_once( "$IP/includes/OutputHandler.php" );
	}
	ob_start( 'wfOutputHandler' );
}

